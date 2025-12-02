<?php
// Start the session at the very beginning for tasks 4, 5, 6, 9, 10, 11, 14, 15, and 16.
// This is mandatory for using $_SESSION.
session_start();

// --- Configuration and Helper Function for Output ---
$current_time = time();
$app_name = "PHP State Management Demo";

// Function to safely display a value from a superglobal array
function display_value($array_name, $key) {
    if (isset($GLOBALS[$array_name][$key])) {
        $value = is_array($GLOBALS[$array_name][$key]) 
                 ? print_r($GLOBALS[$array_name][$key], true) 
                 : $GLOBALS[$array_name][$key];
        return htmlspecialchars($value);
    }
    return "Not set or expired.";
}

// Check if a specific task request is simulated
$requested_action = $_GET['action'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $app_name; ?></title>
    <style>
        body { font-family: 'Inter', sans-serif; line-height: 1.6; margin: 20px; background-color: #f7f7f7; color: #333; }
        .container { max-width: 900px; margin: 0 auto; background: #fff; padding: 20px 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
        h1 { color: #0056b3; border-bottom: 3px solid #0056b3; padding-bottom: 10px; margin-bottom: 25px; }
        h2 { color: #28a745; margin-top: 25px; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
        pre { background: #e9ecef; padding: 15px; border-radius: 8px; overflow-x: auto; font-size: 0.9em; white-space: pre-wrap; word-break: break-all; }
        .result { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; border-radius: 6px; margin-top: 10px; }
        .info { background: #e2e3e5; color: #383d41; border: 1px solid #d6d8db; padding: 10px; border-radius: 6px; margin-top: 10px; }
        code { background: #f8f9fa; padding: 2px 4px; border-radius: 4px; color: #c82333; }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo $app_name; ?></h1>

        <!-- Task 1: Set "username" Cookie with Expiry -->
        <h2>1. Set "username" Cookie with Expiry (1 hour)</h2>
        <?php
        $cookie_name = "username";
        $cookie_value = "Gulnara Serik";
        $expiry_time = $current_time + 3600; // 1 hour (3600 seconds)

        // The setcookie() function must be called before any output is sent.
        $cookie_set = setcookie($cookie_name, $cookie_value, [
            'expires' => $expiry_time,
            'path' => '/',
            'httponly' => true, // Recommended for security
            'samesite' => 'Lax' // Recommended for modern browsers
        ]);

        if ($cookie_set) {
            echo "<div class='result'>Cookie '{$cookie_name}' set successfully. Value: '{$cookie_value}'. Expires: " . date('Y-m-d H:i:s', $expiry_time) . "</div>";
        } else {
            echo "<div class='info'>Could not set cookie (output already sent).</div>";
        }
        ?>
        <pre>// setcookie(name, value, expiry_timestamp, path, domain, secure, httponly)
setcookie("username", "Gulnara Serik", time() + 3600, "/");</pre>

        <!-- Task 2: Retrieve "username" Cookie -->
        <h2>2. Retrieve "username" Cookie</h2>
        <?php
        // Note: The cookie set in Task 1 will usually only be available in the $_COOKIE array on the *next* request.
        // We display the current state:
        $retrieved_username = display_value('_COOKIE', 'username');
        ?>
        <div class="result">
            Value of 'username' from <code>$_COOKIE</code> (after page load): 
            <strong><?php echo $retrieved_username; ?></strong>
            <br><small>(You must reload this page to see the value set in Task 1.)</small>
        </div>
        <pre>echo $_COOKIE['username'];</pre>

        <!-- Task 3: Delete "username" Cookie -->
        <h2>3. Delete "username" Cookie</h2>
        <?php
        // To delete a cookie, set its value to empty and its expiration time to a time in the past.
        $cookie_deleted = setcookie("username", "", $current_time - 3600); // 1 hour ago
        ?>
        <div class="info">
            <?php echo $cookie_deleted ? "Deletion instruction for 'username' cookie sent." : "Failed to send deletion instruction."; ?> 
            <br><small>(Reload the page twice to confirm deletion in your browser.)</small>
        </div>
        <pre>setcookie("username", "", time() - 3600);</pre>

        <!-- Task 4: Set "userid" Session Variable -->
        <h2>4. Set "userid" Session Variable</h2>
        <?php
        $_SESSION['userid'] = 10020;
        ?>
        <div class="result">
            Session variable 'userid' set successfully.
            <br>Current Session ID: <code><?php echo session_id(); ?></code>
        </div>
        <pre>session_start();
$_SESSION['userid'] = 10020;</pre>

        <!-- Task 5: Retrieve "userid" Session Variable -->
        <h2>5. Retrieve "userid" Session Variable</h2>
        <?php
        $retrieved_userid = display_value('_SESSION', 'userid');
        ?>
        <div class="result">
            Value of 'userid' from <code>$_SESSION</code>: 
            <strong><?php echo $retrieved_userid; ?></strong>
        </div>
        <pre>session_start();
echo $_SESSION['userid'];</pre>

        <!-- Task 6: Destroy Session and Unset Variables -->
        <h2>6. Destroy Session and Unset Variables</h2>
        <?php
        // Unset all session variables
        $_SESSION = [];

        // Destroy the session (client-side cookie and server-side file)
        session_destroy();

        // Note: The session is actually destroyed after the script finishes, 
        // but $_SESSION is now empty.
        ?>
        <div class="info">
            All session variables unset and session destroyed.
            <br><small>(On the next request, a new session ID will be generated.)</small>
        </div>
        <pre>// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();</pre>

        <!-- Restart session for subsequent tasks (simulating a new request) -->
        <?php session_start(); ?>

        <!-- Task 7: Set Secure Cookie Over HTTPS -->
        <h2>7. Set Secure Cookie Over HTTPS</h2>
        <?php
        $secure_cookie_name = "secure_token";
        $is_secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';

        // Secure flag is set to TRUE. This cookie will only be sent over HTTPS.
        setcookie($secure_cookie_name, "my_secret_data", [
            'expires' => $current_time + 3600,
            'path' => '/',
            'secure' => true, // THE KEY FLAG
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        ?>
        <div class="info">
            Cookie '<?php echo $secure_cookie_name; ?>' set with <code>'secure' => true</code>. 
            <br><strong>It will only be transmitted if this page is loaded via HTTPS.</strong>
        </div>
        <pre>// The 'secure' parameter (5th argument or in the options array) is set to true.
setcookie("secure_token", "my_secret_data", time() + 3600, "/", "", true, true);</pre>

        <!-- Task 8: Check for "visited" Cookie -->
        <h2>8. Check for "visited" Cookie</h2>
        <?php
        $visited_cookie_name = "visited";
        
        if (isset($_COOKIE[$visited_cookie_name])) {
            $message = "Welcome back! You have visited before.";
        } else {
            $message = "Hello! This appears to be your first visit.";
            // Set the cookie now so the welcome back message appears on the next visit
            setcookie($visited_cookie_name, $current_time, $current_time + 86400 * 30); // 30 days
        }
        ?>
        <div class="<?php echo isset($_COOKIE[$visited_cookie_name]) ? 'result' : 'info'; ?>">
            <?php echo $message; ?>
        </div>
        <pre>if (isset($_COOKIE['visited'])) {
    echo "Welcome back!";
} else {
    setcookie('visited', time(), time() + 86400 * 30);
    echo "Hello! First visit.";
}</pre>

        <!-- Task 9: Store Array in Session Variable -->
        <h2>9. Store Array in Session Variable</h2>
        <?php
        $user_preferences = [
            'theme' => 'dark',
            'notifications' => true,
            'language' => 'en-US'
        ];
        $_SESSION['preferences'] = $user_preferences;
        ?>
        <div class="result">
            Array stored in <code>$_SESSION['preferences']</code> successfully.
        </div>
        <pre>$_SESSION['preferences'] = [
    'theme' => 'dark',
    'notifications' => true,
    'language' => 'en-US'
];</pre>

        <!-- Task 10: Retrieve Session User Preferences -->
        <h2>10. Retrieve Session User Preferences</h2>
        <?php
        $retrieved_prefs = display_value('_SESSION', 'preferences');
        ?>
        <div class="result">
            Retrieved preferences:
            <pre><?php echo $retrieved_prefs; ?></pre>
        </div>
        <pre>// Accessing the array
$prefs = $_SESSION['preferences'];
echo "Theme: " . $prefs['theme'];</pre>
        
        <!-- Task 11: Session Timeout After 30 Minutes (Demonstration) -->
        <h2>11. Session Timeout After 30 Minutes (Inactivity Check)</h2>
        <?php
        $timeout_limit = 1800; // 30 minutes in seconds
        $last_activity = $_SESSION['LAST_ACTIVITY'] ?? $current_time;
        
        if ($current_time - $last_activity > $timeout_limit) {
            $timeout_message = "Session has timed out due to 30 minutes of inactivity. Session reset.";
            // Clear session data
            $_SESSION = [];
        } else {
            $timeout_message = "Session active. Remaining time before timeout on next load: " . ($timeout_limit - ($current_time - $last_activity)) . " seconds.";
        }
        
        // Update last activity time
        $_SESSION['LAST_ACTIVITY'] = $current_time;
        ?>
        <div class="<?php echo ($current_time - $last_activity > $timeout_limit) ? 'info' : 'result'; ?>">
            <?php echo $timeout_message; ?>
            <br><small>This check must be run on every page load to enforce timeout logic.</small>
        </div>
        <pre>// Check for inactivity timeout on every request
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    session_unset();
    session_destroy();
}
$_SESSION['LAST_ACTIVITY'] = time();</pre>

        <!-- Task 12: Display Number of Active Sessions (Explanation) -->
        <h2>12. Display Number of Active Sessions (Explanation)</h2>
        <div class="info">
            <p>This cannot be reliably done within a standard PHP script without access to the server's session save path (usually a temporary directory) or a custom session handler (e.g., using a database).</p>
            <p><strong>Method:</strong> If sessions are stored in files, you would iterate over the files in the session directory and count the non-expired ones. If using a database, you count records that haven't expired.</p>
        </div>
        
        <!-- Task 13: Limit Maximum Concurrent Sessions (Simulation) -->
        <h2>13. Limit Maximum Concurrent Sessions (Simulation)</h2>
        <div class="info">
            <p>Limiting concurrent sessions per *user* requires persistent storage (like a database) to track how many active sessions (tokens) a user currently has. In a simple session-file environment, this is difficult.</p>
            <p>The **maximum number of sessions on the server** is typically limited by PHP configuration (<code>session.gc_maxlifetime</code>) and available server resources.</p>
        </div>

        <!-- Task 14: Regenerate Session ID to Prevent Fixation -->
        <h2>14. Regenerate Session ID to Prevent Fixation</h2>
        <?php
        $old_session_id = session_id();
        
        // This command regenerates the session ID and deletes the old session file.
        // It's recommended after a successful login.
        session_regenerate_id(true); 
        $new_session_id = session_id();
        ?>
        <div class="result">
            Old Session ID: <code><?php echo $old_session_id; ?></code>
            <br>New Session ID: <code><?php echo $new_session_id; ?></code>
            <br><strong>Session ID successfully regenerated.</strong>
        </div>
        <pre>session_regenerate_id(true);</pre>

        <!-- Task 15: Display Last Session Access Time -->
        <h2>15. Display Last Session Access Time</h2>
        <?php
        $last_access = $_SESSION['LAST_ACCESS'] ?? 'First access in this session.';
        
        $display_last_access = is_numeric($last_access) 
                             ? date('Y-m-d H:i:s', $last_access) 
                             : $last_access;
                             
        // Update the access time for the *next* request
        $_SESSION['LAST_ACCESS'] = $current_time;
        ?>
        <div class="result">
            Last Session Access Time: <strong><?php echo $display_last_access; ?></strong>
            <br><small>(Updated to <?php echo date('Y-m-d H:i:s', $current_time); ?> for the next request.)</small>
        </div>
        <pre>// Store the current time on every request
$_SESSION['LAST_ACCESS'] = time();
echo date('Y-m-d H:i:s', $_SESSION['LAST_ACCESS']);</pre>

        <!-- Task 16: Set Cookie and Session Variable with Same Name -->
        <h2>16. Set Cookie and Session Variable with Same Name</h2>
        <?php
        $common_name = "data_source";
        $session_value = "SessionData_123";
        $cookie_value = "CookieData_456";

        // Set the session variable
        $_SESSION[$common_name] = $session_value;
        
        // Set the cookie (available on the next request in $_COOKIE)
        setcookie($common_name, $cookie_value, $current_time + 300); // 5 minutes
        ?>
        <div class="result">
            Data set for key: <code><?php echo $common_name; ?></code>
            <ul>
                <li>Session Value (from <code>$_SESSION['<?php echo $common_name; ?>']</code>): <strong><?php echo $_SESSION[$common_name]; ?></strong></li>
                <li>Cookie Value (from <code>$_COOKIE['<?php echo $common_name; ?>']</code>): 
                    <strong><?php echo display_value('_COOKIE', $common_name); ?></strong>
                    <small>(Will be '<?php echo $cookie_value; ?>' on the next page load)</small>
                </li>
            </ul>
            <p><strong>Comparison:</strong> Cookies and Session variables use separate storage mechanisms (<code>$_COOKIE</code> and <code>$_SESSION</code> superglobals), so having the same key name does not cause a conflict.</p>
        </div>
        <pre>// Set both
$_SESSION['data_source'] = "SessionData_123";
setcookie('data_source', "CookieData_456", time() + 300);

// Retrieve both
echo "Session: " . $_SESSION['data_source'];
echo "Cookie: " . $_COOKIE['data_source'];</pre>

    </div>
</body>
</html>
<?php
// Ensure the session is closed gracefully at the end of the script
session_write_close();
?>