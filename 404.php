<?php
/**
 * 404 Error Page
 * Redirect to home page
 */

// Redirect to home page
wp_redirect(home_url(), 301);
exit;
?>
