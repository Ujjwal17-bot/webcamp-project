<?php # DISPLAY SHOPPING CART ADDITIONS PAGE.

# Access session.
session_start() ;

# Redirect if not logged in.
if ( !isset( $_SESSION[ 'user_id' ] ) ) { require ( 'login_tools.php' ) ; load() ; }

# Set page title and display header section.
$page_title = 'Cart Addition' ;
include ( 'includes/header.html' ) ;

# Get passed product id and assign it to a variable.
if ( isset( $_GET['id'] ) ) $id = $_GET['id'] ; 

# Open database connection.
require ( 'connect_db.php' ) ;

# Retrieve selective item data from 'shop' database table. 
$q = "SELECT * FROM shop WHERE item_id = $id" ;
$r = mysqli_query( $dbc, $q ) ;
if ( mysqli_num_rows( $r ) == 1 )
{
  $row = mysqli_fetch_array( $r, MYSQLI_ASSOC );

  # Check if cart already contains one of this product id.
  if ( isset( $_SESSION['cart'][$id] ) )
  { 
    # Add one more of this product.
    $_SESSION['cart'][$id]['quantity']++; 
    $message = 'Another ' . htmlspecialchars($row['item_name']) . ' has been added to your cart.';
  } 
  else
  {
    # Or add one of this product to the cart.
    $_SESSION['cart'][$id]= array ( 'quantity' => 1, 'price' => $row['item_price'] ) ;
    $message = 'A ' . htmlspecialchars($row['item_name']) . ' has been added to your cart.';
  }
}

# Close database connection.
mysqli_close($dbc);

// Render a centered success card showing the added product
echo '<div class="container">';
echo '  <div class="row justify-content-center">';
echo '    <div class="col-12 col-md-8 col-lg-6">';
echo '      <div class="card success-card text-center shadow-sm">';
echo '        <div class="card-body">';
echo '          <div class="mb-3">';
echo '            <div class="success-icon">&#10004;</div>'; // checkmark
echo '          </div>';
echo '          <h4 class="card-title">' . (isset($row['item_name']) ? htmlspecialchars($row['item_name']) : 'Product') . '</h4>';
echo '          <div class="mb-3">';
echo '            <img src="' . (isset($row['restaurant']) ? htmlspecialchars($row['restaurant']) . '/' . htmlspecialchars($row['item_img']) : '') . '" alt="' . (isset($row['item_name']) ? htmlspecialchars($row['item_name']) : '') . '" class="img-fluid rounded" style="max-height:220px; object-fit:cover;">';
echo '          </div>';
echo '          <p class="text-success fw-medium">' . (isset($message) ? htmlspecialchars($message) : 'Item added to cart.') . '</p>';
echo '          <div class="d-flex justify-content-center gap-2">';
echo '            <a href="shop.php" class="btn btn-outline-primary">Continue Shopping</a>';
echo '            <a href="cart.php" class="btn btn-success">View Cart</a>';
echo '          </div>';
echo '        </div>';
echo '      </div>';
echo '    </div>';
echo '  </div>';
echo '</div>';

# Display footer section.
include ( 'includes/footer.html' ) ;

?>