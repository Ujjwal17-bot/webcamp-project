<?php # DISPLAY COMPLETE LOGIN PAGE.

# Start session for CSRF token
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

# Generate CSRF token if not present
if (!isset($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

# Set page title and display header section.
$page_title = 'Login' ;
include ( './includes/header.html' ) ;

# Display any error messages if present.
if ( isset( $errors ) && !empty( $errors ) )
{
 echo '<div class="container mt-4">';
 echo '  <div class="alert alert-danger alert-dismissible fade show" role="alert" aria-live="polite" aria-atomic="true">';
 echo '    <h5 class="alert-heading"><i class="bi bi-exclamation-triangle-fill me-2" aria-hidden="true"></i>Login Failed</h5>';
 echo '    <p class="mb-2">Please correct the following:</p>';
 echo '    <ul class="mb-0">';
 foreach ( $errors as $msg ) { 
   echo '<li>' . htmlspecialchars($msg) . '</li>'; 
 }
 echo '    </ul>';
 echo '    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
 echo '  </div>';
 echo '</div>';
}
?>

<!-- Display body section with Bootstrap styling -->
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-5">
      <div class="card shadow-lg border-0">
        <div class="card-body p-4 p-md-5">
          <div class="text-center mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" fill="currentColor" class="bi bi-person-circle text-primary mb-3" viewBox="0 0 16 16" aria-hidden="true">
              <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
              <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
            </svg>
            <h1 class="h3 fw-bold mb-2">Welcome Back</h1>
            <p class="text-muted mb-0">Please login to your account</p>
          </div>
          
          <form action="login_action.php" method="post" novalidate>
            <!-- CSRF Token -->
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf']); ?>">
            
            <div class="mb-3">
              <label for="email" class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
              <input 
                type="email" 
                class="form-control form-control-lg" 
                id="email" 
                name="email" 
                placeholder="Enter your email"
                value="<?php echo htmlspecialchars($email ?? ''); ?>"
                required
                inputmode="email"
                autocomplete="email"
                aria-describedby="emailHelp"
              >
              <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
            </div>
            
            <div class="mb-3">
              <label for="pass" class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
              <div class="input-group">
                <input 
                  type="password" 
                  class="form-control form-control-lg" 
                  id="pass" 
                  name="pass" 
                  placeholder="Enter your password"
                  required
                  autocomplete="current-password"
                  aria-describedby="passHelp"
                >
                <button 
                  class="btn btn-outline-secondary" 
                  type="button" 
                  id="togglePassword" 
                  aria-label="Toggle password visibility"
                  aria-pressed="false"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16" id="eyeIcon" aria-hidden="true">
                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                  </svg>
                </button>
              </div>
              <div id="passHelp" class="form-text">
                <a href="#" class="text-decoration-none">Forgot password?</a>
              </div>
            </div>
            
            <div class="mb-4 form-check">
              <input type="checkbox" class="form-check-input" id="rememberMe">
              <label class="form-check-label" for="rememberMe">Remember me</label>
            </div>
            
            <div class="d-grid mb-3">
              <button type="submit" class="btn btn-primary btn-lg fw-semibold">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-box-arrow-in-right me-2" viewBox="0 0 16 16" aria-hidden="true">
                  <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0v-2z"/>
                  <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                </svg>
                Login
              </button>
            </div>
            
            <div class="text-center">
              <p class="text-muted mb-0">Don't have an account? <a href="register.php" class="text-decoration-none fw-semibold">Register here</a></p>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Password toggle script -->
<script>
  const togglePassword = document.getElementById('togglePassword');
  const passwordInput = document.getElementById('pass');
  const eyeIcon = document.getElementById('eyeIcon');
  
  if (togglePassword) {
    togglePassword.addEventListener('click', function() {
      // Toggle password visibility
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      
      // Update aria-pressed attribute
      const isPressed = type === 'text';
      togglePassword.setAttribute('aria-pressed', isPressed.toString());
      
      // Toggle eye icon
      if (type === 'text') {
        eyeIcon.innerHTML = '<path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/><path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/><path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12-.708.708z"/>';
      } else {
        eyeIcon.innerHTML = '<path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>';
      }
    });
  }
</script>

<?php 

# Display footer section.
include ( 'includes/footer.html' ) ; 

?>
