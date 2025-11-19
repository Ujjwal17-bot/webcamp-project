<?php # DISPLAY COMPLETE PRODUCTS PAGE.

# Access session.
session_start();


# Set page title and display header section.
$page_title = 'Shop';
include('includes/header.html');

# Open database connection.
require('connect_db.php');

# Retrieve items from 'shop' database table.
$q = "SELECT * FROM shop";
$r = mysqli_query($dbc, $q);

# Page wrapper.
echo '<div class="container py-5">';
echo '  <div class="d-flex align-items-center justify-content-between mb-4">';
echo '    <h1 class="h3 m-0">Our Meals</h1>';
echo '    <a href="cart.php" class="btn btn-outline-primary"><i class="bi bi-bag"></i> View Cart</a>';
echo '  </div>';

if (mysqli_num_rows($r) > 0)
{
  # Use responsive grid.
  echo '<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">';

  while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC))
  {
    # Safe values
    $id    = htmlspecialchars($row['item_id']);
    $name  = htmlspecialchars($row['item_name']);
    $desc  = htmlspecialchars($row['item_desc']);
    $img   = htmlspecialchars($row['item_img']);
    $rest  = htmlspecialchars($row['restaurant']);
    $price = isset($row['item_price']) ? number_format($row['item_price'], 2) : '5.99'; // fallback price
    $rating = rand(3, 5); // random 3–5 stars if not in DB

    # Card
    echo '<div class="col">';
    echo '  <div class="card h-100 shadow-sm border-0">';

    # Image
    echo '    <div class="ratio ratio-4x3 overflow-hidden">';
    echo '      <img loading="lazy" src="' . $rest . '/' . $img . '" alt="' . $name . '" class="w-100 h-100" style="object-fit: cover;">';
    echo '    </div>';

    echo '    <div class="card-body">';
    echo '      <div class="d-flex justify-content-between align-items-start mb-2">';
    echo '        <h5 class="card-title mb-0">' . $name . '</h5>';
    echo '        <span class="badge bg-success fs-6">$' . $price . '</span>';
    echo '      </div>';

    # Rating stars
    echo '      <div class="text-warning mb-2">';
    for ($i = 0; $i < $rating; $i++) echo '⭐';
    for ($i = $rating; $i < 5; $i++) echo '☆';
    echo '      </div>';

    # Description
    echo '      <p class="card-text text-secondary" style="display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;">' . $desc . '</p>';
    echo '    </div>';

    # Button
    echo '    <div class="card-footer bg-white border-0 pt-0 pb-4 px-3">';
    echo '      <a href="added.php?id=' . $id . '" class="btn btn-primary w-100">Add To Cart</a>';
    echo '    </div>';

    echo '  </div>';
    echo '</div>';
  }

  echo '</div>'; # /row

  mysqli_close($dbc);
}
else
{
  echo '<div class="text-center py-5">';
  echo '  <div class="display-6 mb-3">No meals yet</div>';
  echo '  <p class="text-secondary mb-4">Please check back later or explore the forum for recommendations.</p>';
  echo '  <a href="forum.php" class="btn btn-outline-secondary me-2">Go to Forum</a>';
  echo '  <a href="home.php" class="btn btn-primary">Back to Home</a>';
  echo '</div>';
}

# Navigation
echo '<div class="text-center mt-5">';
echo '  <a href="cart.php" class="btn btn-outline-secondary me-2">Cart</a>';
echo '  <a href="forum.php" class="btn btn-outline-secondary me-2">Forum</a>';
echo '  <a href="home.php" class="btn btn-outline-secondary me-2">Home</a>';
echo '  <a href="goodbye.php" class="btn btn-danger">Logout</a>';
echo '</div>';

echo '</div>'; # /container

include('includes/footer.html');
?>
