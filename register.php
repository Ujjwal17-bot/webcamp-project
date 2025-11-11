<?php
/**
 * DISPLAY COMPLETE REGISTRATION PAGE
 * 
 * Handles user registration with CSRF protection, password hashing,
 * prepared statements, and Bootstrap UI.
 */

# Start session for CSRF token
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

# Generate CSRF token if not present
if (!isset($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

# Initialize variables
$errors = array();
$success = false;

# Check form submitted.
if ( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' )
{
  # Verify CSRF token
  if (!isset($_POST['csrf']) || !isset($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
    $errors[] = 'Invalid security token. Please refresh the page and try again.';
  }
  else
  {
    # Connect to the database.
    require ('connect_db.php'); 
    
    # Sanitize and validate first name
    $fn = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
    if (empty($fn)) {
      $errors[] = 'Please enter your first name.';
    } elseif (strlen($fn) > 50) {
      $errors[] = 'First name must be 50 characters or less.';
    }
    
    # Sanitize and validate last name
    $ln = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
    if (empty($ln)) {
      $errors[] = 'Please enter your last name.';
    } elseif (strlen($ln) > 50) {
      $errors[] = 'Last name must be 50 characters or less.';
    }
    
    # Sanitize and validate email address
    $e = isset($_POST['email']) ? trim($_POST['email']) : '';
    if (empty($e)) {
      $errors[] = 'Please enter your email address.';
    } else {
      # Sanitize email
      $e = filter_var($e, FILTER_SANITIZE_EMAIL);
      # Validate email format
      if (!filter_var($e, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
      }
    }
    
    # Validate passwords
    $p1 = isset($_POST['pass1']) ? $_POST['pass1'] : '';
    $p2 = isset($_POST['pass2']) ? $_POST['pass2'] : '';
    
    if (empty($p1)) {
      $errors[] = 'Please enter a password.';
    } elseif (strlen($p1) < 8) {
      $errors[] = 'Password must be at least 8 characters long.';
    } elseif (!preg_match('/[A-Z]/', $p1)) {
      $errors[] = 'Password must contain at least one uppercase letter.';
    } elseif (!preg_match('/[a-z]/', $p1)) {
      $errors[] = 'Password must contain at least one lowercase letter.';
    } elseif (!preg_match('/[0-9]/', $p1)) {
      $errors[] = 'Password must contain at least one number.';
    }
    
    if (empty($p2)) {
      $errors[] = 'Please confirm your password.';
    } elseif ($p1 !== $p2) {
      $errors[] = 'Passwords do not match.';
    }
    
    # Check if email address already registered (only if no validation errors so far)
    if (empty($errors))
    {
      # Use prepared statement to check for existing email
      $stmt = mysqli_prepare($dbc, "SELECT user_id FROM users WHERE email = ?");
      mysqli_stmt_bind_param($stmt, 's', $e);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_store_result($stmt);
      
      if (mysqli_stmt_num_rows($stmt) > 0) {
        $errors[] = 'This email address is already registered. <a href="login.php" class="alert-link">Login instead</a>?';
      }
      
      mysqli_stmt_close($stmt);
    }
    
    # On success, register user with password_hash
    if (empty($errors)) 
    {
      # Hash password using modern bcrypt algorithm
      $hashed_password = password_hash($p1, PASSWORD_DEFAULT);
      
      # Use prepared statement for insert
      $stmt = mysqli_prepare($dbc, "INSERT INTO users (first_name, last_name, email, pass, reg_date) VALUES (?, ?, ?, ?, NOW())");
      mysqli_stmt_bind_param($stmt, 'ssss', $fn, $ln, $e, $hashed_password);
      
      if (mysqli_stmt_execute($stmt))
      {
        $success = true;
        # Regenerate CSRF token after successful registration
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
      }
      else
      {
        $errors[] = 'Registration failed due to a system error. Please try again later.';
      }
      
      mysqli_stmt_close($stmt);
    }
    
    # Close database connection.
    mysqli_close($dbc);
  }
}

# Set page title and display header section.
$page_title = 'Register';
include ('includes/header.html');
?>

<!-- Display success or registration form -->
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-5">
      
      <?php if ($success) { ?>
        <!-- Success Card -->
        <div class="card shadow-lg border-0">
          <div class="card-body p-4 p-md-5 text-center">
            <div class="mb-4">
              <div class="success-checkmark">
                <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" class="bi bi-check-circle-fill text-success" viewBox="0 0 16 16" aria-hidden="true">
                  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                </svg>
              </div>
              <h1 class="h3 fw-bold mb-3 mt-3">Registration Successful!</h1>
              <p class="text-muted mb-4">Your account has been created successfully. You can now login with your credentials.</p>
              <div class="d-grid">
                <a href="login.php" class="btn btn-primary btn-lg fw-semibold">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-box-arrow-in-right me-2" viewBox="0 0 16 16" aria-hidden="true">
                    <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0v-2z"/>
                    <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                  </svg>
                  Login Now
                </a>
              </div>
            </div>
          </div>
        </div>
      <?php } else { ?>
        <!-- Registration Form Card -->
        <div class="card shadow-lg border-0">
          <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" fill="currentColor" class="bi bi-person-plus-fill text-primary mb-3" viewBox="0 0 16 16" aria-hidden="true">
                <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                <path fill-rule="evenodd" d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5z"/>
              </svg>
              <h1 class="h3 fw-bold mb-2">Create Your Account</h1>
              <p class="text-muted mb-0">Join us and start shopping today</p>
            </div>
            
            <?php
            # Display error messages if present
            if (!empty($errors)) {
              echo '<div class="alert alert-danger alert-dismissible fade show" role="alert" aria-live="polite" aria-atomic="true">';
              echo '  <h5 class="alert-heading"><i class="bi bi-exclamation-triangle-fill me-2" aria-hidden="true"></i>Registration Failed</h5>';
              echo '  <p class="mb-2">Please correct the following issues:</p>';
              echo '  <ul class="mb-0">';
              foreach ($errors as $msg) {
                echo '<li>' . $msg . '</li>';
              }
              echo '  </ul>';
              echo '  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
              echo '</div>';
            }
            ?>
            
            <form action="register.php" method="post" id="registerForm" novalidate>
              <!-- CSRF Token -->
              <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf']); ?>">
              
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="first_name" class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                  <input 
                    type="text" 
                    class="form-control form-control-lg" 
                    id="first_name" 
                    name="first_name" 
                    placeholder="John"
                    value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>"
                    required
                    maxlength="50"
                    autocomplete="given-name"
                  >
                </div>
                
                <div class="col-md-6 mb-3">
                  <label for="last_name" class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                  <input 
                    type="text" 
                    class="form-control form-control-lg" 
                    id="last_name" 
                    name="last_name" 
                    placeholder="Doe"
                    value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>"
                    required
                    maxlength="50"
                    autocomplete="family-name"
                  >
                </div>
              </div>
              
              <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                <input 
                  type="email" 
                  class="form-control form-control-lg" 
                  id="email" 
                  name="email" 
                  placeholder="john.doe@example.com"
                  value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                  required
                  inputmode="email"
                  autocomplete="email"
                  aria-describedby="emailHelp"
                >
                <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
              </div>
              
              <div class="mb-3">
                <label for="pass1" class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input 
                    type="password" 
                    class="form-control form-control-lg" 
                    id="pass1" 
                    name="pass1" 
                    placeholder="Enter your password"
                    required
                    autocomplete="new-password"
                    aria-describedby="passHelp"
                  >
                  <button 
                    class="btn btn-outline-secondary" 
                    type="button" 
                    id="togglePassword1" 
                    aria-label="Toggle password visibility"
                    aria-pressed="false"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16" id="eyeIcon1" aria-hidden="true">
                      <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                      <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                    </svg>
                  </button>
                </div>
                <div id="passHelp" class="form-text">
                  Must be at least 8 characters with uppercase, lowercase, and number.
                </div>
                <!-- Password strength indicator -->
                <div class="mt-2">
                  <div class="progress" style="height: 5px;">
                    <div id="passwordStrength" class="progress-bar" role="progressbar" style="width: 0%"></div>
                  </div>
                  <small id="passwordStrengthText" class="text-muted"></small>
                </div>
              </div>
              
              <div class="mb-4">
                <label for="pass2" class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input 
                    type="password" 
                    class="form-control form-control-lg" 
                    id="pass2" 
                    name="pass2" 
                    placeholder="Confirm your password"
                    required
                    autocomplete="new-password"
                  >
                  <button 
                    class="btn btn-outline-secondary" 
                    type="button" 
                    id="togglePassword2" 
                    aria-label="Toggle password visibility"
                    aria-pressed="false"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16" id="eyeIcon2" aria-hidden="true">
                      <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                      <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                    </svg>
                  </button>
                </div>
                <div id="passwordMatch" class="form-text"></div>
              </div>
              
              <div class="d-grid mb-3">
                <button type="submit" class="btn btn-primary btn-lg fw-semibold">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person-check-fill me-2" viewBox="0 0 16 16" aria-hidden="true">
                    <path fill-rule="evenodd" d="M15.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L12.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0z"/>
                    <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                  </svg>
                  Create Account
                </button>
              </div>
              
              <div class="text-center">
                <p class="text-muted mb-0">Already have an account? <a href="login.php" class="text-decoration-none fw-semibold">Login here</a></p>
              </div>
            </form>
          </div>
        </div>
      <?php } ?>
      
    </div>
  </div>
</div>

<!-- Password toggle and validation scripts -->
<script>
  // Password toggle functionality for first password
  const togglePassword1 = document.getElementById('togglePassword1');
  const passwordInput1 = document.getElementById('pass1');
  const eyeIcon1 = document.getElementById('eyeIcon1');
  
  if (togglePassword1) {
    togglePassword1.addEventListener('click', function() {
      const type = passwordInput1.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput1.setAttribute('type', type);
      
      const isPressed = type === 'text';
      togglePassword1.setAttribute('aria-pressed', isPressed.toString());
      
      if (type === 'text') {
        eyeIcon1.innerHTML = '<path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/><path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/><path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12-.708.708z"/>';
      } else {
        eyeIcon1.innerHTML = '<path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>';
      }
    });
  }
  
  // Password toggle functionality for confirm password
  const togglePassword2 = document.getElementById('togglePassword2');
  const passwordInput2 = document.getElementById('pass2');
  const eyeIcon2 = document.getElementById('eyeIcon2');
  
  if (togglePassword2) {
    togglePassword2.addEventListener('click', function() {
      const type = passwordInput2.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput2.setAttribute('type', type);
      
      const isPressed = type === 'text';
      togglePassword2.setAttribute('aria-pressed', isPressed.toString());
      
      if (type === 'text') {
        eyeIcon2.innerHTML = '<path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/><path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/><path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12-.708.708z"/>';
      } else {
        eyeIcon2.innerHTML = '<path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>';
      }
    });
  }
  
  // Password strength indicator
  if (passwordInput1) {
    passwordInput1.addEventListener('input', function() {
      const password = this.value;
      const strengthBar = document.getElementById('passwordStrength');
      const strengthText = document.getElementById('passwordStrengthText');
      
      let strength = 0;
      let feedback = '';
      
      if (password.length >= 8) strength++;
      if (password.match(/[a-z]/)) strength++;
      if (password.match(/[A-Z]/)) strength++;
      if (password.match(/[0-9]/)) strength++;
      if (password.match(/[^a-zA-Z0-9]/)) strength++;
      
      strengthBar.style.width = (strength * 20) + '%';
      
      if (strength === 0) {
        strengthBar.className = 'progress-bar';
        feedback = '';
      } else if (strength <= 2) {
        strengthBar.className = 'progress-bar bg-danger';
        feedback = 'Weak';
      } else if (strength === 3) {
        strengthBar.className = 'progress-bar bg-warning';
        feedback = 'Fair';
      } else if (strength === 4) {
        strengthBar.className = 'progress-bar bg-info';
        feedback = 'Good';
      } else {
        strengthBar.className = 'progress-bar bg-success';
        feedback = 'Strong';
      }
      
      strengthText.textContent = feedback;
    });
  }
  
  // Password match validation
  if (passwordInput2) {
    passwordInput2.addEventListener('input', function() {
      const pass1 = passwordInput1.value;
      const pass2 = this.value;
      const matchDiv = document.getElementById('passwordMatch');
      
      if (pass2.length === 0) {
        matchDiv.textContent = '';
        matchDiv.className = 'form-text';
      } else if (pass1 === pass2) {
        matchDiv.textContent = '✓ Passwords match';
        matchDiv.className = 'form-text text-success';
      } else {
        matchDiv.textContent = '✗ Passwords do not match';
        matchDiv.className = 'form-text text-danger';
      }
    });
  }
</script>

<?php
# Display footer section.
include ('includes/footer.html');
?>
