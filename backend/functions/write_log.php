<?php
function write_log($message, $log) {
    // Attempt to write to project log file, but always send to PHP error log as fallback.
    $log_dir = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'student024' . DIRECTORY_SEPARATOR . 'Shop' . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR;
    $log_file = $log_dir . $log;
    $timestamp = date('Y-m-d H:i:s');
    $entry = "[$timestamp] $message" . PHP_EOL;

    if (!is_dir($log_dir)) {
        @mkdir($log_dir, 0755, true);
    }

    $written = false;
    if (is_writable($log_dir) || (!file_exists($log_file) && is_writable(dirname($log_file)))) {
        $connection_log = @fopen($log_file, 'a');
        if ($connection_log) {
            @fwrite($connection_log, $entry);
            @fclose($connection_log);
            $written = true;
        }
    }

    // Always send at least to PHP error log so remotehost admin logs capture it
    if (!$written) {
        error_log($entry);
    }
}


