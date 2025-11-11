<?php # DISPLAY COMPLETE LOGGED IN PAGE.

# Access session.
session_start() ; 

# Redirect if not logged in.
if ( !isset( $_SESSION[ 'user_id' ] ) ) { require ( 'login_tools.php' ) ; load() ; }

# Set page title and display header section.
$page_title = 'Home' ;
include ( 'includes/header.html' ) ;

# Display hero section with welcome message
echo '<div class="hero-section text-center py-5 mb-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <div class="container">
          <h1 class="display-3 fw-bold mb-3">Welcome to Our Meal Shop</h1>
          <p class="lead mb-4">Hello, ' . htmlspecialchars($_SESSION['first_name']) . ' ' . htmlspecialchars($_SESSION['last_name']) . '! Discover delicious meals and connect with our community.</p>
          <p class="fs-5 mb-0">Your one-stop destination for quality food and great conversations</p>
        </div>
      </div>';

# Display feature cards section
echo '<div class="container mb-5">
        <div class="row g-4">
          
          <!-- Shop Feature Card -->
          <div class="col-md-6">
            <div class="card h-100 shadow-lg border-0 hover-card">
              <div class="card-body text-center p-5">
                <div class="feature-icon mb-4">
                  <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="#667eea" class="bi bi-shop" viewBox="0 0 16 16">
                    <path d="M2.97 1.35A1 1 0 0 1 3.73 1h8.54a1 1 0 0 1 .76.35l2.609 3.044A1.5 1.5 0 0 1 16 5.37v.255a2.375 2.375 0 0 1-4.25 1.458A2.371 2.371 0 0 1 9.875 8 2.37 2.37 0 0 1 8 7.083 2.37 2.37 0 0 1 6.125 8a2.37 2.37 0 0 1-1.875-.917A2.375 2.375 0 0 1 0 5.625V5.37a1.5 1.5 0 0 1 .361-.976l2.61-3.045zm1.78 4.275a1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0 1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0 1.375 1.375 0 1 0 2.75 0V5.37a.5.5 0 0 0-.12-.325L12.27 2H3.73L1.12 5.045A.5.5 0 0 0 1 5.37v.255a1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0zM1.5 8.5A.5.5 0 0 1 2 9v6h1v-5a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1v5h6V9a.5.5 0 0 1 1 0v6h.5a.5.5 0 0 1 0 1H.5a.5.5 0 0 1 0-1H1V9a.5.5 0 0 1 .5-.5zM4 15h3v-5H4v5zm5-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-3zm3 0h-2v3h2v-3z"/>
                  </svg>
                </div>
                <h2 class="card-title fw-bold mb-3">Browse Our Shop</h2>
                <p class="card-text text-muted mb-4">Explore our wide selection of fresh and delicious meals. Add your favorites to the cart and enjoy convenient ordering.</p>
                <a href="shop.php" class="btn btn-primary btn-lg">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-cart-plus me-2" viewBox="0 0 16 16">
                    <path d="M9 5.5a.5.5 0 0 0-1 0V7H6.5a.5.5 0 0 0 0 1H8v1.5a.5.5 0 0 0 1 0V8h1.5a.5.5 0 0 0 0-1H9V5.5z"/>
                    <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1H.5zm3.915 10L3.102 4h10.796l-1.313 7h-8.17zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                  </svg>
                  Start Shopping
                </a>
              </div>
            </div>
          </div>

          <!-- Forum Feature Card -->
          <div class="col-md-6">
            <div class="card h-100 shadow-lg border-0 hover-card">
              <div class="card-body text-center p-5">
                <div class="feature-icon mb-4">
                  <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="#764ba2" class="bi bi-chat-dots" viewBox="0 0 16 16">
                    <path d="M5 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                    <path d="m2.165 15.803.02-.004c1.83-.363 2.948-.842 3.468-1.105A9.06 9.06 0 0 0 8 15c4.418 0 8-3.134 8-7s-3.582-7-8-7-8 3.134-8 7c0 1.76.743 3.37 1.97 4.6a10.437 10.437 0 0 1-.524 2.318l-.003.011a10.722 10.722 0 0 1-.244.637c-.079.186.074.394.273.362a21.673 21.673 0 0 0 .693-.125zm.8-3.108a1 1 0 0 0-.287-.801C1.618 10.83 1 9.468 1 8c0-3.192 3.004-6 7-6s7 2.808 7 6c0 3.193-3.004 6-7 6a8.06 8.06 0 0 1-2.088-.272 1 1 0 0 0-.711.074c-.387.196-1.24.57-2.634.893a10.97 10.97 0 0 0 .398-2z"/>
                  </svg>
                </div>
                <h2 class="card-title fw-bold mb-3">Join the Forum</h2>
                <p class="card-text text-muted mb-4">Connect with other food lovers! Share your thoughts, ask questions, and be part of our growing community.</p>
                <a href="forum.php" class="btn btn-primary btn-lg">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-chat-left-text me-2" viewBox="0 0 16 16">
                    <path d="M14 1a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H4.414A2 2 0 0 0 3 11.586l-2 2V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12.793a.5.5 0 0 0 .854.353l2.853-2.853A1 1 0 0 1 4.414 12H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
                    <path d="M3 3.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zM3 6a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 6zm0 2.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5z"/>
                  </svg>
                  Visit Forum
                </a>
              </div>
            </div>
          </div>

        </div>
      </div>';

# Display footer with navigation
echo '<footer class="site-footer text-center py-4 mt-5">
        <div class="container">
          <div class="mb-3">
            <a href="shop.php" class="btn btn-outline-primary mx-1">Shop</a>
            <a href="forum.php" class="btn btn-outline-secondary mx-1">Forum</a>
            <a href="goodbye.php" class="btn btn-outline-danger mx-1">Logout</a>
          </div>
          <p class="text-muted mb-0">&copy; 2025 Our Shop. All rights reserved.</p>
        </div>
      </footer>';

# Display footer section.
include ( 'includes/footer.html' ) ;
?>