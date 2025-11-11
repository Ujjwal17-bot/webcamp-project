<?php # DISPLAY COMPLETE FORUM PAGE.

# Access session.
session_start() ;

# Redirect if not logged in.
if ( !isset( $_SESSION[ 'user_id' ] ) ) { require ( 'login_tools.php' ) ; load() ; }

# Set page title and display header section.
$page_title = 'Community Forum' ;
include ( 'includes/header.html' ) ;

# Open database connection.
require ( 'connect_db.php' ) ;

# Display success message if post was just published
if (isset($_SESSION['post_success'])) {
  echo '<div class="container mt-4">';
  echo '  <div class="alert alert-success alert-dismissible fade show" role="alert" aria-live="polite" aria-atomic="true">';
  echo '    <i class="bi bi-check-circle-fill me-2" aria-hidden="true"></i>';
  echo '    <strong>Success!</strong> ' . htmlspecialchars($_SESSION['post_success']);
  echo '    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
  echo '  </div>';
  echo '</div>';
  # Clear the message so it doesn't display again
  unset($_SESSION['post_success']);
}

# Display hero section with title and post button
echo '<div class="hero-section text-center mb-4">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-md-8">
              <h1 class="display-4 fw-bold">Community Forum</h1>
              <p class="lead text-muted">Share your thoughts with others</p>
            </div>
            <div class="col-md-4 text-md-end">
              <a href="post.php" class="btn btn-primary btn-lg">
                <i class="bi bi-plus-circle"></i> Post a Message
              </a>
            </div>
          </div>
        </div>
      </div>';

echo '<div class="container mb-5">';

# Display body section, retrieving from 'forum' database table.
$q = "SELECT * FROM forum ORDER BY post_date DESC" ;
$r = mysqli_query( $dbc, $q ) ;
if ( mysqli_num_rows( $r ) > 0 )
{
  echo '<div class="row">';
  while ( $row = mysqli_fetch_array( $r, MYSQLI_ASSOC ))
  {
    echo '<div class="col-12 mb-3">
            <div class="card shadow-sm">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <div>
                    <h5 class="card-title mb-1">' . htmlspecialchars($row['subject']) . '</h5>
                    <p class="text-muted small mb-0">
                      <strong>' . htmlspecialchars($row['first_name']) . ' ' . htmlspecialchars($row['last_name']) . '</strong>
                      <span class="mx-1">•</span>
                      ' . date('F j, Y - g:i A', strtotime($row['post_date'])) . '
                    </p>
                  </div>
                </div>
                <p class="card-text mt-3">' . nl2br(htmlspecialchars($row['message'])) . '</p>
              </div>
            </div>
          </div>';
  }
  echo '</div>';
}
else 
{ 
  echo '<div class="alert alert-info text-center" role="alert">
          <h5 class="alert-heading">No messages yet. Be the first to post!</h5>
          <hr>
          <a href="post.php" class="btn btn-primary">Post a Message</a>
        </div>' ; 
}

echo '</div>'; # Close container

# Create navigation links with Bootstrap outline buttons
echo '<footer class="site-footer text-center py-4 mt-5">
        <div class="container">
          <div class="mb-3">
            <a href="shop.php" class="btn btn-outline-primary mx-1">Shop</a>
            <a href="home.php" class="btn btn-outline-secondary mx-1">Home</a>
            <a href="goodbye.php" class="btn btn-outline-danger mx-1">Logout</a>
          </div>
          <p class="text-muted mb-0">&copy; 2025 Our Shop. All rights reserved.</p>
        </div>
      </footer>';

# Close database connection.
mysqli_close( $dbc ) ;
  
# Display footer section.
include ( 'includes/footer.html' ) ;

?>