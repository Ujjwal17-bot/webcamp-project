<?php # DISPLAY SHOPPING CART PAGE.

# Access session.
session_start() ;
#
# Set page title and display header section.
$page_title = 'Cart' ;
include ( 'includes/header.html' ) ;

# Check if form has been submitted for update.
if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
  # Update changed quantity field values.
  foreach ( $_POST['qty'] as $item_id => $item_qty )
  {
    # Ensure values are integers.
    $id = (int) $item_id;
    $qty = (int) $item_qty;

    # Change quantity or delete if zero.
    if ( $qty == 0 ) { unset ($_SESSION['cart'][$id]); } 
    elseif ( $qty > 0 ) { $_SESSION['cart'][$id]['quantity'] = $qty; }
  }
}

# Initialize grand total variable.
$total = 0; 

# Hero header
echo '<div class="container py-4">';
echo '  <div class="hero p-4">';
echo '    <h1 class="m-0">Your Shopping Cart</h1>';
echo '    <p class="mb-0">Review your items and proceed to checkout when ready.</p>';
echo '  </div>';
echo '</div>';

# Display the cart if not empty.
if (!empty($_SESSION['cart']))
{
  # Connect to the database.
  require ('connect_db.php');
  
  # Retrieve all items in the cart from the 'shop' database table.
  $q = "SELECT * FROM shop WHERE item_id IN (";
  foreach ($_SESSION['cart'] as $id => $value) { $q .= $id . ','; }
  $q = substr( $q, 0, -1 ) . ') ORDER BY item_id ASC';
  $r = mysqli_query ($dbc, $q);

  # Display body section with Bootstrap cards
  echo '<div class="container py-4">';
  echo '<form action="cart.php" method="post">';
  echo '<div class="row g-4">';
  
  while ($row = mysqli_fetch_array ($r, MYSQLI_ASSOC))
  {
    # Calculate sub-totals and grand total.
    $subtotal = $_SESSION['cart'][$row['item_id']]['quantity'] * $_SESSION['cart'][$row['item_id']]['price'];
    $total += $subtotal;

    # Display each item as a card
    echo '<div class="col-12">';
    echo '  <div class="card shadow-sm">';
    echo '    <div class="card-body">';
    echo '      <div class="row align-items-center">';
    echo '        <div class="col-md-4">';
    echo '          <img src="' . htmlspecialchars($row['restaurant']) . '/' . htmlspecialchars($row['item_img']) . '" class="img-fluid rounded" alt="' . htmlspecialchars($row['item_name']) . '" style="max-height:150px; object-fit:cover;">';
    echo '        </div>';
    echo '        <div class="col-md-8">';
    echo '          <h5 class="card-title">' . htmlspecialchars($row['item_name']) . '</h5>';
    echo '          <p class="card-text text-muted">' . htmlspecialchars($row['item_desc']) . '</p>';
    echo '          <div class="row align-items-center mt-3">';
    echo '            <div class="col-sm-4">';
    echo '              <label class="form-label">Quantity:</label>';
    echo '              <input type="number" class="form-control" min="0" name="qty[' . $row['item_id'] . ']" value="' . $_SESSION['cart'][$row['item_id']]['quantity'] . '">';
    echo '            </div>';
    echo '            <div class="col-sm-4">';
    echo '              <p class="mb-0"><strong>Price:</strong> $' . htmlspecialchars($row['item_price']) . '</p>';
    echo '            </div>';
    echo '            <div class="col-sm-4">';
    echo '              <p class="mb-0"><strong>Subtotal:</strong> $' . number_format($subtotal, 2) . '</p>';
    echo '            </div>';
    echo '          </div>';
    echo '        </div>';
    echo '      </div>';
    echo '    </div>';
    echo '  </div>';
    echo '</div>';
  }
  
  # Close the database connection.
  mysqli_close($dbc); 
  
  echo '</div>'; # close row
  
  # Display the total in a styled card
  echo '<div class="row mt-4">';
  echo '  <div class="col-12">';
  echo '    <div class="alert alert-success shadow-sm">';
  echo '      <div class="d-flex justify-content-between align-items-center">';
  echo '        <h4 class="mb-0">Grand Total:</h4>';
  echo '        <h4 class="mb-0">$' . number_format($total, 2) . '</h4>';
  echo '      </div>';
  echo '    </div>';
  echo '  </div>';
  echo '</div>';
  
  # Action buttons
  echo '<div class="row mt-3 mb-4">';
  echo '  <div class="col-12 d-flex gap-2 justify-content-center">';
  echo '    <button type="submit" name="submit" class="btn btn-primary">Update Cart</button>';
  echo '    <a href="checkout.php?total=' . $total . '" class="btn btn-success">Proceed to Checkout</a>';
  echo '  </div>';
  echo '</div>';
  
  echo '</form>';
  echo '</div>'; # close container
}
else
{
  # Display empty cart message
  echo '<div class="container py-5">';
  echo '  <div class="row justify-content-center">';
  echo '    <div class="col-md-6">';
  echo '      <div class="alert alert-info text-center shadow-sm">';
  echo '        <h5 class="alert-heading">Your cart is empty</h5>';
  echo '        <p class="mb-3">Looks like you haven\'t added any items to your cart yet.</p>';
  echo '        <a href="shop.php" class="btn btn-primary">Return to Shop</a>';
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