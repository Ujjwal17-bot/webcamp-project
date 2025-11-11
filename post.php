<?php # DISPLAY POST MESSAGE FORM.

# Access session.
session_start() ;

# Redirect if not logged in.
if ( !isset( $_SESSION[ 'user_id' ] ) ) { require ( 'login_tools.php' ) ; load() ; }

# Generate CSRF token if not present
if (!isset($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

# Set page title and display header section.
$page_title = 'Post Message' ;
include ( 'includes/header.html' ) ;
?>

<!-- Display form with Bootstrap styling -->
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-lg-8">
      <div class="card shadow-lg border-0">
        <div class="card-body p-4 p-md-5">
          <div class="mb-4">
            <h1 class="h3 fw-bold mb-2">
              <i class="bi bi-pencil-square text-primary me-2" aria-hidden="true"></i>
              New Forum Post
            </h1>
            <p class="text-muted mb-0">Share your thoughts with the community</p>
          </div>
          
          <form action="post_action.php" method="post" accept-charset="utf-8">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf']); ?>">
            
            <div class="mb-4">
              <label for="subject" class="form-label fw-semibold">Subject <span class="text-danger">*</span></label>
              <input 
                type="text" 
                class="form-control form-control-lg" 
                id="subject" 
                name="subject" 
                placeholder="Enter post subject"
                maxlength="120"
                value="<?php echo isset($_GET['subject']) ? htmlspecialchars($_GET['subject']) : ''; ?>"
                required
                aria-describedby="subjectHelp"
              >
              <div id="subjectHelp" class="form-text">Maximum 120 characters</div>
            </div>
            
            <div class="mb-4">
              <label for="message" class="form-label fw-semibold">Message <span class="text-danger">*</span></label>
              <textarea 
                class="form-control form-control-lg" 
                id="message" 
                name="message" 
                rows="8"
                placeholder="Write your message here..."
                maxlength="2000"
                required
                aria-describedby="messageHelp"
              ><?php echo isset($_GET['message']) ? htmlspecialchars($_GET['message']) : ''; ?></textarea>
              <div id="messageHelp" class="form-text">Maximum 2000 characters</div>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-between">
              <a href="forum.php" class="btn btn-outline-secondary btn-lg">
                <i class="bi bi-arrow-left me-2" aria-hidden="true"></i>
                Back to Forum
              </a>
              <button type="submit" name="submit" class="btn btn-primary btn-lg fw-semibold">
                <i class="bi bi-send-fill me-2" aria-hidden="true"></i>
                Publish Post
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
# Display footer section.
include ( 'includes/footer.html' ) ;
?>
