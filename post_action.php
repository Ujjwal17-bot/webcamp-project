<?php
/**
 * PROCESS MESSAGE POST
 * 
 * Handles forum post submissions with CSRF protection, validation,
 * rate limiting, and prepared statements for security.
 */

# Access session.
session_start();

# Make load function available.
require ( 'login_tools.php' ) ;

# Redirect if not logged in.
if ( !isset( $_SESSION[ 'user_id' ] ) ) { load() ; }

# Initialize errors array.
$errors = array();

# Check form submitted.
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
  # Verify CSRF token
  if (!isset($_POST['csrf']) || !isset($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
    $errors[] = 'Invalid security token. Please try again.';
  }
  else
  {
    # Sanitize and validate inputs
    $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    
    # Validate subject
    if (empty($subject)) {
      $errors[] = 'Please enter a subject for your post.';
    } elseif (strlen($subject) > 120) {
      $errors[] = 'Subject must be 120 characters or less.';
    }
    
    # Validate message
    if (empty($message)) {
      $errors[] = 'Please enter a message for your post.';
    } elseif (strlen($message) > 2000) {
      $errors[] = 'Message must be 2000 characters or less.';
    }
    
    # Rate limiting: Check if user is posting too frequently
    if (empty($errors)) {
      if (!isset($_SESSION['last_posts'])) {
        $_SESSION['last_posts'] = array();
      }
      
      # Clean up old post timestamps (older than 60 seconds)
      $current_time = time();
      $_SESSION['last_posts'] = array_filter($_SESSION['last_posts'], function($timestamp) use ($current_time) {
        return ($current_time - $timestamp) < 60;
      });
      
      # Check if user has posted 3 or more times in the last 60 seconds
      if (count($_SESSION['last_posts']) >= 3) {
        $errors[] = 'You are posting too frequently. Please wait a moment before posting again.';
      }
    }
    
    # On success, insert post into database
    if (empty($errors))
    {
      # Open database connection.
      require ( 'connect_db.php' ) ;
      
      # Prepare data from session
      $firstname = $_SESSION['first_name'];
      $lastname = $_SESSION['last_name'];
      
      # Strip HTML tags for security (keep plain text only)
      $subject_clean = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
      $message_clean = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
      
      # Execute insert using prepared statement
      $stmt = mysqli_prepare($dbc, "INSERT INTO forum (first_name, last_name, subject, message, post_date) VALUES (?, ?, ?, ?, NOW())");
      
      if ($stmt) {
        # Bind parameters
        mysqli_stmt_bind_param($stmt, 'ssss', $firstname, $lastname, $subject_clean, $message_clean);
        
        # Execute statement
        if (mysqli_stmt_execute($stmt)) {
          # Record successful post timestamp
          $_SESSION['last_posts'][] = time();
          
          # Close statement
          mysqli_stmt_close($stmt);
          
          # Close database connection
          mysqli_close($dbc);
          
          # Regenerate CSRF token
          $_SESSION['csrf'] = bin2hex(random_bytes(32));
          
          # Set success message in session
          $_SESSION['post_success'] = 'Your post has been published successfully!';
          
          # Redirect to forum
          load('forum.php');
          exit();
        } else {
          $errors[] = 'Database error: Could not publish post. Please try again.';
          mysqli_stmt_close($stmt);
        }
      } else {
        $errors[] = 'Database error: Could not prepare statement. Please try again.';
      }
      
      # Close database connection
      mysqli_close($dbc);
    }
  }
}

# If we reach here, there were validation errors
# Set page title and display header section.
$page_title = 'Post Error';
include ( 'includes/header.html' );
?>

<!-- Display error feedback with Bootstrap styling -->
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-lg-8">
      <div class="card shadow-lg border-0">
        <div class="card-body p-4 p-md-5">
          <div class="alert alert-danger alert-dismissible fade show" role="alert" aria-live="polite" aria-atomic="true">
            <h5 class="alert-heading">
              <i class="bi bi-exclamation-triangle-fill me-2" aria-hidden="true"></i>
              Unable to Publish Post
            </h5>
            <p class="mb-2">Please correct the following issues:</p>
            <ul class="mb-0">
              <?php foreach ($errors as $error) { 
                echo '<li>' . htmlspecialchars($error) . '</li>'; 
              } ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          
          <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
            <a href="post.php?subject=<?php echo isset($_POST['subject']) ? urlencode($_POST['subject']) : ''; ?>&message=<?php echo isset($_POST['message']) ? urlencode($_POST['message']) : ''; ?>" class="btn btn-primary btn-lg fw-semibold">
              <i class="bi bi-arrow-left me-2" aria-hidden="true"></i>
              Back to Post Form
            </a>
            <a href="forum.php" class="btn btn-outline-secondary btn-lg">
              <i class="bi bi-list-ul me-2" aria-hidden="true"></i>
              View Forum
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
# Display footer section.
include ( 'includes/footer.html' );
?>
