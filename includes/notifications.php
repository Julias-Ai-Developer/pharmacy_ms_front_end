<?php
/**
 * Notification Display Component
 * Shows success/error messages based on URL status parameter
 * Uses JavaScript toaster notifications
 */

if (isset($_GET['status'])) {
    $status = $_GET['status'];
    $messages = [
        'created' => ['success', 'Success!', 'Record created successfully!'],
        'updated' => ['success', 'Success!', 'Record updated successfully!'],
        'deleted' => ['success', 'Success!', 'Record deleted successfully!'],
        'saved' => ['success', 'Success!', 'Changes saved successfully!'],
        'error' => ['error', 'Error!', 'An error occurred. Please try again.'],
        'invalid' => ['warning', 'Warning!', 'Invalid data submitted.']
    ];
    
    if (isset($messages[$status])) {
        list($type, $title, $msg) = $messages[$status];
        // Output JavaScript to show toaster notification
        echo '<script>';
        echo 'document.addEventListener("DOMContentLoaded", function() {';
        echo '  if (typeof showNotification === "function") {';
        echo '    showNotification("' . addslashes($title) . '", "' . addslashes($msg) . '", "' . $type . '");';
        echo '  }';
        echo '});';
        echo '</script>';
    }
}
?>
