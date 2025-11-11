<?php
/**
 * TEST CONNECTION TO MySQL DATABASE
 * 
 * Tests database connectivity and displays server information
 * with Bootstrap-styled feedback.
 */

# Set page title and display header section.
$page_title = 'Database Connection Test';
include ('includes/header.html');

# Incorporate the MySQL connection script.
require ('connect_db.php');

# Initialize connection status
$connection_success = false;
$error_message = '';
$server_info = '';
$host_info = '';

# Test the connection
if (isset($dbc) && $dbc instanceof mysqli) {
  # Suppress warnings and test with mysqli_ping
  if (@mysqli_ping($dbc)) {
    $connection_success = true;
    # Get server information with safe output escaping
    $server_info = htmlspecialchars(mysqli_get_server_info($dbc), ENT_QUOTES, 'UTF-8');
    $host_info = htmlspecialchars(mysqli_get_host_info($dbc), ENT_QUOTES, 'UTF-8');
  } else {
    # Connection exists but ping failed
    $error_message = mysqli_error($dbc) ? htmlspecialchars(mysqli_error($dbc), ENT_QUOTES, 'UTF-8') : 'Database connection is not responding.';
  }
} else {
  # Connection object doesn't exist
  $error_message = mysqli_connect_error() ? htmlspecialchars(mysqli_connect_error(), ENT_QUOTES, 'UTF-8') : 'Failed to create database connection object.';
}
?>

<!-- Display connection test results -->
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
      
      <?php if ($connection_success) { ?>
        <!-- Success Card -->
        <div class="card shadow-lg border-0 border-start border-success border-5">
          <div class="card-body p-4 p-md-5 text-center">
            <div class="mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" class="bi bi-database-check text-success mb-3" viewBox="0 0 16 16" aria-hidden="true">
                <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm1.679-4.493-1.335 2.226a.75.75 0 0 1-1.174.144l-.774-.773a.5.5 0 0 1 .708-.708l.547.548 1.17-1.951a.5.5 0 1 1 .858.514Z"/>
                <path d="M12.096 6.223A4.92 4.92 0 0 0 13 5.698V7c0 .289-.213.654-.753 1.007a4.493 4.493 0 0 1 1.753.25V4c0-1.007-.875-1.755-1.904-2.223C11.022 1.289 9.573 1 8 1s-3.022.289-4.096.777C2.875 2.245 2 2.993 2 4v9c0 1.007.875 1.755 1.904 2.223C4.978 15.71 6.427 16 8 16c.536 0 1.058-.034 1.555-.097a4.525 4.525 0 0 1-.813-.927C8.5 14.992 8.252 15 8 15c-1.464 0-2.766-.27-3.682-.687C3.356 13.875 3 13.373 3 13v-1.302c.271.202.58.378.904.525C4.978 12.71 6.427 13 8 13h.027a4.552 4.552 0 0 1 0-1H8c-1.464 0-2.766-.27-3.682-.687C3.356 10.875 3 10.373 3 10V8.698c.271.202.58.378.904.525C4.978 9.71 6.427 10 8 10c.262 0 .52-.008.774-.024a4.525 4.525 0 0 1 1.102-1.132C9.298 8.944 8.666 9 8 9c-1.464 0-2.766-.27-3.682-.687C3.356 7.875 3 7.373 3 7V5.698c.271.202.58.378.904.525C4.978 6.711 6.427 7 8 7s3.022-.289 4.096-.777ZM3 4c0-.374.356-.875 1.318-1.313C5.234 2.271 6.536 2 8 2s2.766.27 3.682.687C12.644 3.125 13 3.627 13 4c0 .374-.356.875-1.318 1.313C10.766 5.729 9.464 6 8 6s-2.766-.27-3.682-.687C3.356 4.875 3 4.373 3 4Z"/>
              </svg>
              <h1 class="h3 fw-bold text-success mb-3">Connection Successful!</h1>
              <p class="text-muted mb-4">Database connection is working properly.</p>
            </div>
            
            <div class="bg-light rounded p-3 mb-4">
              <div class="mb-2">
                <strong class="text-muted d-block mb-1">MySQL Server Version</strong>
                <code class="fs-5"><?php echo $server_info; ?></code>
              </div>
              <hr class="my-3">
              <div>
                <strong class="text-muted d-block mb-1">Host Information</strong>
                <code class="fs-6"><?php echo $host_info; ?></code>
              </div>
            </div>
            
            <div class="d-grid">
              <a href="home.php" class="btn btn-primary btn-lg fw-semibold">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-house-door-fill me-2" viewBox="0 0 16 16" aria-hidden="true">
                  <path d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5Z"/>
                </svg>
                Back to Home
              </a>
            </div>
          </div>
        </div>
        
      <?php } else { ?>
        <!-- Error Card -->
        <div class="card shadow-lg border-0 border-start border-danger border-5">
          <div class="card-body p-4 p-md-5 text-center">
            <div class="mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" class="bi bi-database-x text-danger mb-3" viewBox="0 0 16 16" aria-hidden="true">
                <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm-.646-4.854.646.647.646-.647a.5.5 0 0 1 .708.708l-.647.646.647.646a.5.5 0 0 1-.708.708l-.646-.647-.646.647a.5.5 0 0 1-.708-.708l.647-.646-.647-.646a.5.5 0 0 1 .708-.708Z"/>
                <path d="M12.096 6.223A4.92 4.92 0 0 0 13 5.698V7c0 .289-.213.654-.753 1.007a4.493 4.493 0 0 1 1.753.25V4c0-1.007-.875-1.755-1.904-2.223C11.022 1.289 9.573 1 8 1s-3.022.289-4.096.777C2.875 2.245 2 2.993 2 4v9c0 1.007.875 1.755 1.904 2.223C4.978 15.71 6.427 16 8 16c.536 0 1.058-.034 1.555-.097a4.525 4.525 0 0 1-.813-.927C8.5 14.992 8.252 15 8 15c-1.464 0-2.766-.27-3.682-.687C3.356 13.875 3 13.373 3 13v-1.302c.271.202.58.378.904.525C4.978 12.71 6.427 13 8 13h.027a4.552 4.552 0 0 1 0-1H8c-1.464 0-2.766-.27-3.682-.687C3.356 10.875 3 10.373 3 10V8.698c.271.202.58.378.904.525C4.978 9.71 6.427 10 8 10c.262 0 .52-.008.774-.024a4.525 4.525 0 0 1 1.102-1.132C9.298 8.944 8.666 9 8 9c-1.464 0-2.766-.27-3.682-.687C3.356 7.875 3 7.373 3 7V5.698c.271.202.58.378.904.525C4.978 6.711 6.427 7 8 7s3.022-.289 4.096-.777ZM3 4c0-.374.356-.875 1.318-1.313C5.234 2.271 6.536 2 8 2s2.766.27 3.682.687C12.644 3.125 13 3.627 13 4c0 .374-.356.875-1.318 1.313C10.766 5.729 9.464 6 8 6s-2.766-.27-3.682-.687C3.356 4.875 3 4.373 3 4Z"/>
              </svg>
              <h1 class="h3 fw-bold text-danger mb-3">Connection Failed!</h1>
              <p class="text-muted mb-4">Unable to connect to the database.</p>
            </div>
            
            <div class="alert alert-danger text-start" role="alert">
              <h5 class="alert-heading">
                <i class="bi bi-exclamation-triangle-fill me-2" aria-hidden="true"></i>
                Error Details
              </h5>
              <hr>
              <p class="mb-0"><strong>Message:</strong> <?php echo $error_message; ?></p>
            </div>
            
            <div class="bg-light rounded p-3 mb-4 text-start">
              <h6 class="fw-bold mb-2">Troubleshooting Tips:</h6>
              <ul class="mb-0 small">
                <li>Check if MySQL/XAMPP server is running</li>
                <li>Verify database credentials in <code>connect_db.php</code></li>
                <li>Ensure the database exists and user has proper permissions</li>
                <li>Check firewall settings and port availability (default: 3306)</li>
              </ul>
            </div>
            
            <div class="d-grid">
              <a href="home.php" class="btn btn-outline-primary btn-lg fw-semibold">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-house-door-fill me-2" viewBox="0 0 16 16" aria-hidden="true">
                  <path d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5Z"/>
                </svg>
                Back to Home
              </a>
            </div>
          </div>
        </div>
      <?php } ?>
      
    </div>
  </div>
</div>

<?php
# Close database connection if it exists
if (isset($dbc) && $dbc instanceof mysqli) {
  mysqli_close($dbc);
}

# Display footer section.
include ('includes/footer.html');
?>
