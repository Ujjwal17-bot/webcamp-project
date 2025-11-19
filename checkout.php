<?php # DISPLAY CHECKOUT PAGE.

# Access session.
session_start() ;
#
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
  $purchased_items = array();
  $total_amount = 0.0;

  while ($row = mysqli_fetch_array ($r, MYSQLI_ASSOC))
  {
    $item_id = (int) $row['item_id'];
    $qty = isset($_SESSION['cart'][$item_id]['quantity']) ? (int) $_SESSION['cart'][$item_id]['quantity'] : 1;
    $price = isset($_SESSION['cart'][$item_id]['price']) ? floatval($_SESSION['cart'][$item_id]['price']) : floatval($row['item_price'] ?? 0);

    # Keep existing insert logic for order_contents
    $query = "INSERT INTO order_contents ( order_id, item_id, quantity, price ) VALUES ( $order_id, " . $item_id . "," . $qty . "," . $price . ")" ;
    $result = mysqli_query($dbc,$query);

    # Collect item details for confirmation display
    $line_total = $qty * $price;
    $total_amount += $line_total;
    $purchased_items[] = array(
      'name' => $row['item_name'],
      'quantity' => $qty,
      'price' => $price,
      'line_total' => $line_total
    );
  }

  # Close database connection.
  mysqli_close($dbc);

  # Render confirmation card with order summary
  echo '<div class="container py-5">';
  echo '  <div class="row justify-content-center">';
  echo '    <div class="col-12 col-md-10 col-lg-8">';
  echo '      <div class="card shadow-sm">';
  echo '        <div class="card-body">';
  echo '          <div class="d-flex align-items-center mb-3">';
  echo '            <div class="me-3">';
  echo '              <div class="success-icon display-6 text-success">&#10004;</div>';
  echo '            </div>';
  echo '            <div>';
  echo '              <h3 class="mb-0">Order Placed Successfully!</h3>';
  echo '              <p class="text-muted mb-0">Thank you for your purchase.</p>';
  echo '            </div>';
  echo '          </div>';

  echo '          <div class="alert alert-success my-3">';
  echo '            <strong>Order Number:</strong> <span class="h5 ms-2">#' . htmlspecialchars($order_id) . '</span>';
  echo '          </div>';

  if (!empty($purchased_items)) {
    echo '          <div class="table-responsive">';
    echo '            <table class="table table-borderless">';
    echo '              <thead>'; 
    echo '                <tr class="text-muted small">';
    echo '                  <th>Item</th>'; 
    echo '                  <th class="text-end">Qty</th>'; 
    echo '                  <th class="text-end">Price</th>'; 
    echo '                  <th class="text-end">Total</th>'; 
    echo '                </tr>'; 
    echo '              </thead>'; 
    echo '              <tbody>';
    foreach ($purchased_items as $pitem) {
      echo '                <tr>'; 
      echo '                  <td>' . htmlspecialchars($pitem['name']) . '</td>'; 
      echo '                  <td class="text-end">' . (int)$pitem['quantity'] . '</td>'; 
      echo '                  <td class="text-end">$' . number_format($pitem['price'], 2) . '</td>'; 
      echo '                  <td class="text-end">$' . number_format($pitem['line_total'], 2) . '</td>'; 
      echo '                </tr>';
    }
    echo '                <tr class="border-top">';
    echo '                  <td></td><td></td>'; 
    echo '                  <td class="text-end fw-semibold">Total</td>'; 
    echo '                  <td class="text-end fw-bold">$' . number_format($total_amount, 2) . '</td>'; 
    echo '                </tr>';
    echo '              </tbody>';
    echo '            </table>';
    echo '          </div>';
  } else {
    echo '          <p class="text-muted">No item details available for this order.</p>';
  }

  echo '          <div class="d-flex justify-content-center gap-2 mt-4">';
  echo '            <a href="shop.php" class="btn btn-primary">Return to Shop</a>';
  echo '            <a href="home.php" class="btn btn-outline-secondary">Go Home</a>';
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