<?php
/**
 * Google reCAPTCHA v2 Helper Functions
 */

// Load configuration
$config = require 'config.php';

/**
 * Verify reCAPTCHA response
 * @param string $response The reCAPTCHA response from the form
 * @return bool True if valid, false otherwise
 */
function verifyRecaptcha($response) {
    global $config;
    
    if (!$config['recaptcha']['enabled']) {
        return true; // Skip verification if disabled
    }
    
    if (empty($response)) {
        return false;
    }
    
    $secretKey = $config['recaptcha']['secret_key'];
    $verifyURL = 'https://www.google.com/recaptcha/api/siteverify';
    
    $data = [
        'secret' => $secretKey,
        'response' => $response,
        'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
    ];
    
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    
    $context = stream_context_create($options);
    $result = file_get_contents($verifyURL, false, $context);
    
    if ($result === false) {
        return false;
    }
    
    $resultJson = json_decode($result, true);
    return isset($resultJson['success']) && $resultJson['success'] === true;
}

/**
 * Get reCAPTCHA site key
 * @return string
 */
function getRecaptchaSiteKey() {
    global $config;
    return $config['recaptcha']['site_key'];
}

/**
 * Check if reCAPTCHA is enabled
 * @return bool
 */
function isRecaptchaEnabled() {
    global $config;
    return $config['recaptcha']['enabled'];
}

/**
 * Generate reCAPTCHA HTML
 * @param string $theme 'light' or 'dark'
 * @param string $size 'normal' or 'compact'
 * @return string HTML for reCAPTCHA widget
 */
function generateRecaptchaHTML($theme = 'light', $size = 'normal') {
    if (!isRecaptchaEnabled()) {
        return '';
    }
    
    $siteKey = getRecaptchaSiteKey();
    
    return sprintf(
        '<div class="g-recaptcha" data-sitekey="%s" data-theme="%s" data-size="%s"></div>',
        htmlspecialchars($siteKey),
        htmlspecialchars($theme),
        htmlspecialchars($size)
    );
}
?>
