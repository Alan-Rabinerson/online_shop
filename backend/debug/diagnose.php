<?php
// Simple diagnostic script. Access with ?key=DEBUG123 and remove after use.
$key = $_GET['key'] ?? '';
if ($key !== 'DEBUG123') {
    http_response_code(403);
    echo "Forbidden\n";
    exit;
}
header('Content-Type: text/plain; charset=utf-8');
echo "DIAGNOSTIC REPORT\n";
echo "Time: " . date('c') . "\n";
echo "PHP version: " . PHP_VERSION . "\n";
echo "Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'unknown') . "\n";
echo "Document root: " . ($_SERVER['DOCUMENT_ROOT'] ?? '') . "\n";
echo "Script filename: " . (__FILE__) . "\n";
echo "CWD: " . getcwd() . "\n";
echo "Owner of script (get_current_user): " . get_current_user() . "\n";

// Test writing to project logs
$log_dir = realpath(__DIR__ . '/../logs');
echo "\n-- Log directory: " . ($log_dir ?: '(not found)') . "\n";
$test_log = __DIR__ . '/../logs/diagnose_test.txt';
$entry = "[" . date('c') . "] diagnose write test from " . ($_SERVER['REMOTE_ADDR'] ?? 'cli') . "\\n";
$can_write = false;
if ($log_dir && is_writable($log_dir)) {
    $res = @file_put_contents($test_log, $entry, FILE_APPEND | LOCK_EX);
    if ($res !== false) {
        $can_write = true;
    }
}
echo "Log dir writable: " . ($can_write ? 'yes' : 'no') . "\n";
if ($can_write) {
    echo "Wrote test entry to: " . $test_log . "\n";
}

// Attempt to include DB switch and test connection
$db_switch = __DIR__ . '/../config/db_connect_switch.php';
echo "\n-- DB connect switch file: " . (file_exists($db_switch) ? 'found' : 'missing') . "\n";
$have_conn = false;
$mysqli_error = '';
if (file_exists($db_switch)) {
    // include in isolated scope
    try {
        include $db_switch;
        if (isset($conn) && $conn) {
            // try ping
            if (function_exists('mysqli_ping') && @mysqli_ping($conn)) {
                $have_conn = true;
            } else {
                $mysqli_error = @mysqli_error($conn) ?: 'cannot ping connection';
            }
        } else {
            $mysqli_error = 'no $conn variable after include';
        }
    } catch (Throwable $e) {
        $mysqli_error = 'include threw: ' . $e->getMessage();
    }
}

echo "DB connection available: " . ($have_conn ? 'yes' : 'no') . "\n";
if ($mysqli_error) echo "DB error: " . $mysqli_error . "\n";

// Check response headers of one of the failing pages (my_account)
$self = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/student024/Shop/backend/views/my_account.php';
echo "\n-- Remote request to: " . $self . "\n";
$headers = @get_headers($self, 1);
if ($headers === false) {
    echo "get_headers failed or blocked\n";
} else {
    echo "Response headers:\n";
    foreach ($headers as $k => $v) {
        if (is_array($v)) $v = implode('; ', $v);
        echo "  $k: $v\n";
    }
}

// Does .htaccess exist in backend?
$ht = realpath(__DIR__ . '/../.htaccess');
echo "\n.htaccess in backend: " . ($ht ?: '(not found)') . "\n";

echo "\n-- End of report\n";
