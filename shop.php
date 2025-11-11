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

# Get selected category from URL parameter
$selected_category = isset($_GET['category']) ? trim($_GET['category']) : 'all';

# Define valid categories
$valid_categories = ['all', 'vegan', 'vegetarian', 'seafood', 'meat', 'dessert'];

# Validate category parameter - ensure lowercase for database comparison
$selected_category = strtolower($selected_category);
if (!in_array($selected_category, $valid_categories)) {
  $selected_category = 'all';
}

# Retrieve items from 'shop' database table with category filter
if ($selected_category === 'all') {
  # Get all items
  $q = "SELECT * FROM shop ORDER BY item_id DESC";
  $stmt = mysqli_prepare($dbc, $q);
} else {
  # Filter by category using prepared statement
  $q = "SELECT * FROM shop WHERE category = ? ORDER BY item_id DESC";
  $stmt = mysqli_prepare($dbc, $q);
  mysqli_stmt_bind_param($stmt, 's', $selected_category);
}

# Execute query
mysqli_stmt_execute($stmt);
$r = mysqli_stmt_get_result($stmt);

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

# Category filter pills
echo '<div class="container py-3">';
echo '  <div class="d-flex justify-content-center">';
echo '    <ul class="nav nav-pills gap-2" role="tablist">';

# Define categories with display names and icons
$categories = [
  'all' => ['name' => 'All', 'icon' => 'bi-grid-fill'],
  'vegan' => ['name' => 'Vegan', 'icon' => 'bi-leaf-fill'],
  'vegetarian' => ['name' => 'Vegetarian', 'icon' => 'bi-egg-fill'],
  'seafood' => ['name' => 'Seafood', 'icon' => 'bi-water'],
  'meat' => ['name' => 'Meat', 'icon' => 'bi-trophy-fill'],
  'dessert' => ['name' => 'Dessert', 'icon' => 'bi-cake2-fill']
];

# Display filter pills
foreach ($categories as $cat_key => $cat_info) {
  $active_class = ($selected_category === $cat_key) ? 'active' : '';
  $aria_current = ($selected_category === $cat_key) ? 'aria-current="page"' : '';
  
  echo '<li class="nav-item" role="presentation">';
  echo '  <a class="nav-link ' . $active_class . '" href="shop.php?category=' . htmlspecialchars($cat_key) . '" ' . $aria_current . '>';
  echo '    <i class="bi ' . htmlspecialchars($cat_info['icon']) . ' me-2" aria-hidden="true"></i>';
  echo '    ' . htmlspecialchars($cat_info['name']);
  echo '  </a>';
  echo '</li>';
}

echo '    </ul>';
echo '  </div>';
echo '</div>';

# Page wrapper.
echo '<div class="container py-4">';

# Display selected category info
if ($selected_category !== 'all') {
  $category_display = ucfirst(htmlspecialchars($selected_category));
  echo '  <div class="mb-3">';
  echo '    <h5 class="text-muted">Showing: <span class="text-primary">' . $category_display . '</span></h5>';
  echo '  </div>';
}

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

  mysqli_stmt_close($stmt);
  mysqli_close($dbc);
}
else
{
  # Friendly empty state with category-specific message
  echo '<div class="text-center py-5">';
  echo '  <div class="mb-4">';
  echo '    <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" class="bi bi-inbox text-muted" viewBox="0 0 16 16" aria-hidden="true">';
  echo '      <path d="M4.98 4a.5.5 0 0 0-.39.188L1.54 8H6a.5.5 0 0 1 .5.5 1.5 1.5 0 1 0 3 0A.5.5 0 0 1 10 8h4.46l-3.05-3.812A.5.5 0 0 0 11.02 4H4.98zm9.954 5H10.45a2.5 2.5 0 0 1-4.9 0H1.066l.32 2.562a.5.5 0 0 0 .497.438h12.234a.5.5 0 0 0 .496-.438L14.933 9zM3.809 3.563A1.5 1.5 0 0 1 4.981 3h6.038a1.5 1.5 0 0 1 1.172.563l3.7 4.625a.5.5 0 0 1 .105.374l-.39 3.124A1.5 1.5 0 0 1 14.117 13H1.883a1.5 1.5 0 0 1-1.489-1.314l-.39-3.124a.5.5 0 0 1 .106-.374l3.7-4.625z"/>';
  echo '    </svg>';
  echo '  </div>';
  
  if ($selected_category === 'all') {
     echo '  <div class="display-6 mb-3">No items found</div>';
     echo '  <p class="text-secondary mb-4">No items found for this selection.</p>';
  } else {
    $category_display = ucfirst(htmlspecialchars($selected_category));
     echo '  <div class="display-6 mb-3">No items found</div>';
     echo '  <p class="text-secondary mb-4">No items found for this selection.</p>';
    echo '  <a href="shop.php?category=all" class="btn btn-primary mb-2">View All Meals</a>';
  }
  
  echo '  <div class="mt-3">';
  echo '    <a href="forum.php" class="btn btn-outline-secondary me-2">Go to Forum</a>';
  echo '    <a href="home.php" class="btn btn-outline-secondary">Back to Home</a>';
  echo '  </div>';
  echo '</div>';
  
  mysqli_stmt_close($stmt);
  mysqli_close($dbc);
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
