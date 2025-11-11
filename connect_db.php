<?php
/**
 * Database Connection Script
 * 
 * This file establishes a secure connection to the MySQL database.
 * It automatically switches between production (University server) and local (XAMPP/MAMP) environments.
 */

# Detect environment and establish database connection
if (strpos($_SERVER['SERVER_NAME'], "pedro") !== false) 
{
    # PRODUCTION ENVIRONMENT: University Server (pedro.cs.herts.ac.uk)
    $db_host = "pedro.cs.herts.ac.uk";
    $db_user = "UrPedroId";
    $db_pass = "UrPedroPassWord";
    $db_name = "dbUrUHid";
    $environment = "production";
} 
else 
{
    # LOCAL ENVIRONMENT: XAMPP or MAMP
    # Note: Use empty string "" for XAMPP, "root" for MAMP
    $db_host = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "site_db";
    $environment = "local";
}

# Attempt database connection
$dbc = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);

# Handle connection errors with user-friendly message
if (!$dbc) {
    # Display error only in local environment for debugging
    if ($environment === "local") {
        echo '<div class="alert alert-danger" role="alert">';
        echo '<strong>Database Connection Failed:</strong> ' . mysqli_connect_error();
        echo '</div>';
    } else {
        # Production: Generic error message (don't expose database details)
        echo '<div class="alert alert-danger" role="alert">';
        echo '<strong>Service Unavailable:</strong> Unable to connect to the database. Please try again later.';
        echo '</div>';
    }
    exit;
}

# Set character encoding to UTF-8 for proper data handling
mysqli_set_charset($dbc, 'utf8');

# Optional: Display success message in local environment only (for testing)
if ($environment === "local" && isset($_GET['debug'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
    echo '<strong>Database Connected:</strong> Successfully connected to ' . htmlspecialchars($db_name) . ' on ' . htmlspecialchars($db_host);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
}

/**
 * Helper Function: Execute SQL query and display results in HTML table format
 * 
 * @param string $sql The SQL query to execute
 * @return void Outputs results directly to the browser
 */
function get_results_string($sql)
{
    global $dbc;
    
    # Execute query
    $result = $dbc->query($sql);
    
    # Display executed query
    echo '<div class="alert alert-info">' . htmlspecialchars($sql) . '</div>';
    
    # Check if query returned any rows
    if (!$result || $result->num_rows == 0) {
        echo '<div class="alert alert-warning">No rows returned.</div>';
        return;
    }
    
    # Display row count
    echo '<p class="text-muted">Returned ' . $result->num_rows . ' rows</p>';
    
    # Display results in Bootstrap-styled table
    echo '<div class="table-responsive">';
    echo '<table class="table table-striped table-bordered">';
    echo '<thead class="table-dark"><tr><th>Field</th><th>Value</th></tr></thead>';
    echo '<tbody>';
    
    foreach ($result->fetch_all(MYSQLI_ASSOC) as $row) {
        foreach ($row as $key => $value) {
            echo '<tr>';
            echo '<td><strong>' . htmlspecialchars($key) . '</strong></td>';
            echo '<td>' . htmlspecialchars($value) . '</td>';
            echo '</tr>';
        }
        echo '<tr class="table-secondary"><td colspan="2" class="text-center">***</td></tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
    echo '<hr class="my-4">';
}
?>