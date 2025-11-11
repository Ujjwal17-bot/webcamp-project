<?php # DISPLAY COMPLETE PRODUCTS PAGE.

# Access session.
session_start();

# Redirect if not logged in.
if (!isset($_SESSION['user_id'])) { require('login_tools.php'); load(); }

# Set page title and display header section.
$page_title = 'Shop';
include('includes/header.html');

# Open database connection.
require('connect_db.php');
# Retrieve items from 'shop' database table.
$q = "SELECT * FROM shop";
$r = mysqli_query($dbc, $q);

# Hero header
echo '<div class="container py-4">';
echo '  <div class="hero p-4">';
echo '    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">';
echo '      <div class="mb-3 mb-md-0">';
echo '        <h1 class="m-0">Discover Our Fresh Meals</h1>';
echo '        <p class="mb-0">Handcrafted daily from quality ingredients — choose what moves you.</p>';
echo '      </div>';
echo '      <div>';
echo '        <a href="cart.php" class="btn btn-outline-primary">View Cart</a>';
echo '      </div>';
echo '    </div>';
echo '  </div>';
echo '</div>';

# Page wrapper.
echo '<div class="container py-4">';
echo '  <div class="mb-4">';
echo '    <!-- Section heading could be here if desired -->';
echo '  </div>';

if (mysqli_num_rows($r) > 0)
{
  # Use responsive grid with equal gaps and auto columns.
  echo '<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">';

  while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC))
  {
    # Safe values
    $id   = htmlspecialchars($row['item_id']);
    $name = htmlspecialchars($row['item_name']);
    $desc = htmlspecialchars($row['item_desc']);
    $img  = htmlspecialchars($row['item_img']);
    $rest = htmlspecialchars($row['restaurant']);

  # Card
  echo '<div class="col">';
  echo '  <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden">';
    
  # Consistent image ratio (4:3) with object-fit and hover-zoom.
  echo '    <div class="ratio ratio-4x3 overflow-hidden">';
  echo '      <img loading="lazy" src="' . $rest . '/' . $img . '" alt="' . $name . '" class="w-100 h-100" style="object-fit: cover;">';
  echo '    </div>';

    echo '    <div class="card-body">';
    echo '      <div class="d-flex justify-content-between align-items-start mb-2">';
    echo '        <h5 class="card-title mb-0">' . $name . '</h5>';
    echo '        <span class="badge text-bg-light border">' . $rest . '</span>';
    echo '      </div>';
    # Clamp long text for tidy cards.
    echo '      <p class="card-text text-secondary" style="display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;">' . $desc . '</p>';
    echo '    </div>';

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
  # Friendly empty state
  echo '<div class="text-center py-5">';
  echo '  <div class="display-6 mb-3">No meals yet</div>';
  echo '  <p class="text-secondary mb-4">Please check back later or explore the forum for recommendations.</p>';
  echo '  <a href="forum.php" class="btn btn-outline-secondary me-2">Go to Forum</a>';
  echo '  <a href="home.php" class="btn btn-primary">Back to Home</a>';
  echo '</div>';
}

// Footer (site-wide)
echo '<footer class="site-footer">';
echo '  <div class="container">';
echo '    <div class="row">';
echo '      <div class="col text-center">';
echo '        <p class="mb-0">&copy; ' . date('Y') . ' FreshMeals. All rights reserved.</p>';
echo '      </div>';
echo '    </div>';
echo '  </div>';
echo '</footer>';

echo '</div>'; # /container

# Display footer section (scripts)
include('includes/footer.html');
?>
