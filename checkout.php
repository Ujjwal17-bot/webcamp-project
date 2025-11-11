<?php # DISPLAY CHECKOUT PAGE.

# Access session.
session_start() ;

# Redirect if not logged in.
if ( !isset( $_SESSION[ 'user_id' ] ) ) { require ( 'login_tools.php' ) ; load() ; }

# Set page title and display header section.
$page_title = 'Checkout' ;
include ( 'includes/header.html' ) ;

# Check for passed total and cart.
if ( isset( $_GET['total'] ) && ( $_GET['total'] > 0 ) && (!empty($_SESSION['cart']) ) )
{
  # Open database connection.
  require ('connect_db.php');
  
  # Store buyer and order total in 'orders' database table.
  $q = "INSERT INTO orders ( user_id, total, order_date ) VALUES (". $_SESSION['user_id'].",".$_GET['total'].", NOW() ) ";
  $r = mysqli_query ($dbc, $q);
  
  # Retrieve current order number.
  $order_id = mysqli_insert_id($dbc) ;
  
  # Retrieve cart items from 'shop' database table.
  $q = "SELECT * FROM shop WHERE item_id IN (";
  foreach ($_SESSION['cart'] as $id => $value) { $q .= $id . ','; }
  $q = substr( $q, 0, -1 ) . ') ORDER BY item_id ASC';
  $r = mysqli_query ($dbc, $q);

  # Store order contents in 'order_contents' database table.
  while ($row = mysqli_fetch_array ($r, MYSQLI_ASSOC))
  {
    $query = "INSERT INTO order_contents ( order_id, item_id, quantity, price )
    VALUES ( $order_id, ".$row['item_id'].",".$_SESSION['cart'][$row['item_id']]['quantity'].",".$_SESSION['cart'][$row['item_id']]['price'].")" ;
    $result = mysqli_query($dbc,$query);
  }
  
  # Close database connection.
  mysqli_close($dbc);

  # Display success message with Bootstrap styling
  echo '<div class="container py-5">';
  echo '  <div class="row justify-content-center">';
  echo '    <div class="col-12 col-md-8 col-lg-6">';
  echo '      <div class="card success-card text-center shadow-sm">';
  echo '        <div class="card-body">';
  echo '          <div class="mb-3">';
  echo '            <div class="success-icon">&#10004;</div>';
  echo '          </div>';
  echo '          <h3 class="card-title text-success mb-3">Order Placed Successfully!</h3>';
  echo '          <p class="lead mb-3">Thank you for shopping with us!</p>';
  echo '          <div class="alert alert-success mb-4">';
  echo '            <strong>Your Order Number:</strong> <span class="h4">#' . htmlspecialchars($order_id) . '</span>';
  echo '          </div>';
  echo '          <p class="text-muted mb-4">Your order has been placed successfully. We will process it shortly.</p>';
  echo '          <div class="d-flex justify-content-center gap-2">';
  echo '            <a href="shop.php" class="btn btn-primary">Return to Shop</a>';
  echo '            <a href="home.php" class="btn btn-outline-secondary">Go to Home</a>';
  echo '          </div>';
  echo '        </div>';
  echo '      </div>';
  echo '    </div>';
  echo '  </div>';
  echo '</div>';

  # Remove cart items.  
  $_SESSION['cart'] = NULL ;
}
else
{
  # Display empty cart message
  echo '<div class="container py-5">';
  echo '  <div class="row justify-content-center">';
  echo '    <div class="col-md-6">';
  echo '      <div class="alert alert-warning text-center shadow-sm">';
  echo '        <h5 class="alert-heading">Your cart is empty</h5>';
  echo '        <p class="mb-3">You need to add items to your cart before checking out.</p>';
  echo '        <a href="shop.php" class="btn btn-primary">Back to Shop</a>';
  echo '      </div>';
  echo '    </div>';
  echo '  </div>';
  echo '</div>';
}

# Navigation buttons
echo '<div class="container pb-4">';
echo '  <div class="text-center">';
echo '    <a href="shop.php" class="btn btn-outline-secondary me-2">Shop</a>';
echo '    <a href="forum.php" class="btn btn-outline-secondary me-2">Forum</a>';
echo '    <a href="home.php" class="btn btn-outline-secondary me-2">Home</a>';
echo '    <a href="goodbye.php" class="btn btn-danger">Logout</a>';
echo '  </div>';
echo '</div>';

# Site footer
echo '<footer class="site-footer">';
echo '  <div class="container">';
echo '    <div class="row">';
echo '      <div class="col text-center">';
echo '        <p class="mb-0">&copy; ' . date('Y') . ' FreshMeals. All rights reserved.</p>';
echo '      </div>';
echo '    </div>';
echo '  </div>';
echo '</footer>';

# Display footer section.
include ( 'includes/footer.html' ) ;

?>