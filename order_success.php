<?php # ORDER SUCCESS PAGE - DISPLAYS ORDER CONFIRMATION

# Access session.
session_start() ;

# Redirect if not logged in.
if ( !isset( $_SESSION[ 'user_id' ] ) ) { require ( 'login_tools.php' ) ; load() ; }

# Set page title and display header section.
$page_title = 'Order Success' ;
include ( 'includes/header.html' ) ;

# Check if order_id is provided
if ( !isset( $_GET['order_id'] ) || empty($_GET['order_id']) )
{
  # Redirect to shop if no order ID
  header('Location: shop.php');
  exit();
}

$order_id = (int)$_GET['order_id'];

# Open database connection.
require ('connect_db.php');

# Retrieve order details
$q = "SELECT o.order_id, o.total, o.order_date, u.first_name, u.last_name 
      FROM orders o 
      JOIN users u ON o.user_id = u.user_id 
      WHERE o.order_id = $order_id AND o.user_id = " . $_SESSION['user_id'];
$r = mysqli_query($dbc, $q);

if ( mysqli_num_rows($r) == 1 )
{
  $order = mysqli_fetch_array($r, MYSQLI_ASSOC);
  
  # Retrieve order items
  $q = "SELECT oc.quantity, oc.price, s.item_name, s.item_img 
        FROM order_contents oc 
        JOIN shop s ON oc.item_id = s.item_id 
        WHERE oc.order_id = $order_id";
  $r = mysqli_query($dbc, $q);
  
  # Display success page with Bootstrap styling
  echo '<div class="container py-5">';
  echo '  <div class="row justify-content-center">';
  echo '    <div class="col-12 col-lg-8">';
  
  # Success Card Header
  echo '      <div class="card shadow-lg border-0 mb-4">';
  echo '        <div class="card-body text-center p-5">';
  echo '          <div class="success-icon mb-4">';
  echo '            <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" fill="currentColor" class="bi bi-check-circle-fill text-success" viewBox="0 0 16 16">';
  echo '              <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>';
  echo '            </svg>';
  echo '          </div>';
  echo '          <h1 class="display-5 fw-bold text-success mb-3">Order Placed Successfully!</h1>';
  echo '          <p class="lead text-muted mb-4">Thank you for your order, ' . htmlspecialchars($order['first_name']) . '!</p>';
  echo '          <div class="alert alert-primary d-inline-block px-5 py-3">';
  echo '            <p class="mb-1 text-muted small">Order Number</p>';
  echo '            <h3 class="mb-0 fw-bold">#' . str_pad($order['order_id'], 6, '0', STR_PAD_LEFT) . '</h3>';
  echo '          </div>';
  echo '          <p class="text-muted mt-3 mb-0"><small>Ordered on ' . date('F j, Y - g:i A', strtotime($order['order_date'])) . '</small></p>';
  echo '        </div>';
  echo '      </div>';
  
  # Order Summary Card
  echo '      <div class="card shadow-lg border-0 mb-4">';
  echo '        <div class="card-header bg-primary text-white py-3">';
  echo '          <h5 class="mb-0"><i class="bi bi-bag-check me-2"></i>Order Summary</h5>';
  echo '        </div>';
  echo '        <div class="card-body p-4">';
  
  # Order Items
  if ( mysqli_num_rows($r) > 0 )
  {
    echo '        <div class="list-group list-group-flush mb-3">';
    while ( $item = mysqli_fetch_array($r, MYSQLI_ASSOC) )
    {
      $subtotal = $item['quantity'] * $item['price'];
      echo '          <div class="list-group-item d-flex align-items-center border-0 px-0">';
      echo '            <img src="' . htmlspecialchars($item['item_img']) . '" alt="' . htmlspecialchars($item['item_name']) . '" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">';
      echo '            <div class="flex-grow-1">';
      echo '              <h6 class="mb-1">' . htmlspecialchars($item['item_name']) . '</h6>';
      echo '              <small class="text-muted">Quantity: ' . $item['quantity'] . ' × $' . number_format($item['price'], 2) . '</small>';
      echo '            </div>';
      echo '            <strong>$' . number_format($subtotal, 2) . '</strong>';
  echo '          </div>';
    }
    echo '        </div>';
  }
  
  # Order Total
  echo '        <div class="border-top pt-3">';
  echo '          <div class="d-flex justify-content-between align-items-center">';
  echo '            <h4 class="mb-0">Order Total:</h4>';
  echo '            <h3 class="mb-0 text-success fw-bold">$' . number_format($order['total'], 2) . '</h3>';
  echo '          </div>';
  echo '        </div>';
  echo '        </div>';
  echo '      </div>';
  
  # Next Steps Card
  echo '      <div class="card shadow-lg border-0 mb-4">';
  echo '        <div class="card-body p-4">';
  echo '          <h5 class="card-title mb-3"><i class="bi bi-info-circle me-2"></i>What'\''s Next?</h5>';
  echo '          <ul class="list-unstyled mb-0">';
  echo '            <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>You will receive an order confirmation email shortly</li>';
  echo '            <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>We are preparing your order for delivery</li>';
  echo '            <li class="mb-0"><i class="bi bi-check2 text-success me-2"></i>Track your order status in your account</li>';
  echo '          </ul>';
  echo '        </div>';
  echo '      </div>';
  
  # Action Buttons
  echo '      <div class="d-grid gap-2 d-md-flex justify-content-md-center mb-4">';
  echo '        <a href="shop.php" class="btn btn-primary btn-lg px-5">';
  echo '          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-shop me-2" viewBox="0 0 16 16">';
  echo '            <path d="M2.97 1.35A1 1 0 0 1 3.73 1h8.54a1 1 0 0 1 .76.35l2.609 3.044A1.5 1.5 0 0 1 16 5.37v.255a2.375 2.375 0 0 1-4.25 1.458A2.371 2.371 0 0 1 9.875 8 2.37 2.37 0 0 1 8 7.083 2.37 2.37 0 0 1 6.125 8a2.37 2.37 0 0 1-1.875-.917A2.375 2.375 0 0 1 0 5.625V5.37a1.5 1.5 0 0 1 .361-.976l2.61-3.045zm1.78 4.275a1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0 1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0 1.375 1.375 0 1 0 2.75 0V5.37a.5.5 0 0 0-.12-.325L12.27 2H3.73L1.12 5.045A.5.5 0 0 0 1 5.37v.255a1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0zM1.5 8.5A.5.5 0 0 1 2 9v6h1v-5a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1v5h6V9a.5.5 0 0 1 1 0v6h.5a.5.5 0 0 1 0 1H.5a.5.5 0 0 1 0-1H1V9a.5.5 0 0 1 .5-.5zM4 15h3v-5H4v5zm5-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-3zm3 0h-2v3h2v-3z"/>';
  echo '          </svg>';
  echo '          Continue Shopping';
  echo '        </a>';
  echo '        <a href="home.php" class="btn btn-outline-secondary btn-lg px-5">';
  echo '          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-house me-2" viewBox="0 0 16 16">';
  echo '            <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.646.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.707 1.5ZM13 7.207V13.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V7.207l5-5 5 5Z"/>';
  echo '          </svg>';
  echo '          Go to Home';
  echo '        </a>';
  echo '      </div>';
  
  echo '    </div>';
  echo '  </div>';
  echo '</div>';
}
else
{
  # Order not found or doesn'\''t belong to user
  echo '<div class="container py-5">';
  echo '  <div class="row justify-content-center">';
  echo '    <div class="col-md-6">';
  echo '      <div class="alert alert-danger text-center">';
  echo '        <h5 class="alert-heading">Order Not Found</h5>';
  echo '        <p class="mb-3">We couldn'\''t find this order or you don'\''t have permission to view it.</p>';
  echo '        <a href="shop.php" class="btn btn-primary">Back to Shop</a>';
  echo '      </div>';
  echo '    </div>';
  echo '  </div>';
  echo '</div>';
}

# Close database connection.
mysqli_close($dbc);

# Site footer
echo '<footer class="site-footer text-center py-4 mt-5">';
echo '  <div class="container">';
echo '    <div class="mb-3">';
echo '      <a href="shop.php" class="btn btn-outline-primary mx-1">Shop</a>';
echo '      <a href="forum.php" class="btn btn-outline-secondary mx-1">Forum</a>';
echo '      <a href="home.php" class="btn btn-outline-secondary mx-1">Home</a>';
echo '      <a href="goodbye.php" class="btn btn-outline-danger mx-1">Logout</a>';
echo '    </div>';
echo '    <p class="text-muted mb-0">&copy; 2025 Our Shop. All rights reserved.</p>';
echo '  </div>';
echo '</footer>';

# Display footer section.
include ( 'includes/footer.html' ) ;

?>
