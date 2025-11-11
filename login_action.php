<?php # PROCESS LOGIN ATTEMPT.

# Check form submitted.
if ( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' )
{
  # Access session for throttling and CSRF verification.
  session_start();
  
  # Verify CSRF token
  if (!isset($_POST['csrf']) || !isset($_SESSION['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
    $errors = array('Invalid security token. Please try again.');
    $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
  }
  else
  {
    # Initialize login attempts tracking
    if (!isset($_SESSION['login_attempts'])) {
      $_SESSION['login_attempts'] = array();
    }
    
    # Clean up old login attempts (older than 10 minutes)
    $current_time = time();
    $_SESSION['login_attempts'] = array_filter($_SESSION['login_attempts'], function($timestamp) use ($current_time) {
      return ($current_time - $timestamp) < 600; // 10 minutes = 600 seconds
    });
    
    # Check if user has exceeded login attempts (5 in 10 minutes)
    if (count($_SESSION['login_attempts']) >= 5) {
      $errors = array('Too many failed login attempts. Please wait 10 minutes and try again.');
      $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
    }
    else
    {
      # Open database connection.
      require ( 'connect_db.php' ) ;

      # Get connection, load, and validate functions.
      require ( 'login_tools.php' ) ;

      # Check login.
      list ( $check, $data ) = validate ( $dbc, $_POST[ 'email' ], $_POST[ 'pass' ] ) ;

      # On success set session data and display logged in page.
      if ( $check )  
      {
        # Clear failed login attempts on success
        $_SESSION['login_attempts'] = array();
        
        # Regenerate session ID and CSRF token for security
        session_regenerate_id(true);
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
        
        # Set session data
        $_SESSION[ 'user_id' ] = $data[ 'user_id' ] ;
        $_SESSION[ 'first_name' ] = $data[ 'first_name' ] ;
        $_SESSION[ 'last_name' ] = $data[ 'last_name' ] ;
        
        # Close database connection before redirect
        mysqli_close( $dbc ) ;
        
        # Redirect to home page
        load ( 'home.php' ) ;
      }
      # Or on failure set errors and record attempt.
      else 
      { 
        $errors = $data;
        # Record failed login attempt
        $_SESSION['login_attempts'][] = time();
        # Preserve email for user convenience
        $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
      } 

      # Close database connection.
      mysqli_close( $dbc ) ; 
    }
  }
}

# Continue to display login page on failure.
include ( 'login.php' ) ;

?>