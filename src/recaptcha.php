<?php
/**
 * Google reCAPTCHA Enterprise Helper Functions
 */

// Load configuration
$config = require 'config.php';

// Load Google Cloud dependencies if available
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

/**
 * Verify reCAPTCHA response (supports v2, v3, and Enterprise)
 * @param string $response The reCAPTCHA response from the form
 * @param string $action The action name (for v3/Enterprise)
 * @return bool True if valid, false otherwise
 */
function verifyRecaptcha($response, $action = 'submit') {
    global $config;
    
    if (!$config['recaptcha']['enabled']) {
        return true; // Skip verification if disabled
    }
    
    if (empty($response)) {
        return false;
    }
    
    $type = $config['recaptcha']['type'] ?? 'v2';
    
    if ($type === 'enterprise') {
        return verifyRecaptchaEnterprise($response, $action);
    } else {
        return verifyRecaptchaStandard($response, $action);
    }
}

/**
 * Verify reCAPTCHA using Google Cloud Enterprise API
 * @param string $token The reCAPTCHA token
 * @param string $action The action name
 * @return bool True if valid, false otherwise
 */
function verifyRecaptchaEnterprise($token, $action) {
    global $config;
    
    // Check if Google Cloud library is available
    if (!class_exists('Google\Cloud\RecaptchaEnterprise\V1\Client\RecaptchaEnterpriseServiceClient')) {
        // Fallback to simple HTTP request
        return verifyRecaptchaEnterpriseHTTP($token, $action);
    }
    
    try {
        // Use Google Cloud Enterprise API
        $client = new Google\Cloud\RecaptchaEnterprise\V1\Client\RecaptchaEnterpriseServiceClient();
        $projectName = $client->projectName($config['recaptcha']['project_id']);
        
        // Set the properties of the event to be tracked
        $event = (new Google\Cloud\RecaptchaEnterprise\V1\Event())
            ->setSiteKey($config['recaptcha']['site_key'])
            ->setToken($token);
        
        // Build the assessment request
        $assessment = (new Google\Cloud\RecaptchaEnterprise\V1\Assessment())
            ->setEvent($event);
        
        $request = (new Google\Cloud\RecaptchaEnterprise\V1\CreateAssessmentRequest())
            ->setParent($projectName)
            ->setAssessment($assessment);
        
        $response = $client->createAssessment($request);
        
        // Check if the token is valid
        if ($response->getTokenProperties()->getValid() == false) {
            error_log('reCAPTCHA Enterprise: Token invalid - ' . 
                     Google\Cloud\RecaptchaEnterprise\V1\TokenProperties\InvalidReason::name(
                         $response->getTokenProperties()->getInvalidReason()
                     ));
            return false;
        }
        
        // Check if the expected action was executed
        if ($response->getTokenProperties()->getAction() == $action) {
            // Get the risk score
            $score = $response->getRiskAnalysis()->getScore();
            error_log('reCAPTCHA Enterprise: Score - ' . $score);
            
            // Consider score > 0.5 as valid (adjust threshold as needed)
            return $score > 0.5;
        } else {
            error_log('reCAPTCHA Enterprise: Action mismatch - expected: ' . $action . 
                     ', got: ' . $response->getTokenProperties()->getAction());
            return false;
        }
        
    } catch (Exception $e) {
        error_log('reCAPTCHA Enterprise error: ' . $e->getMessage());
        return false;
    }
}

/**
 * Fallback HTTP verification for Enterprise (when Google Cloud library not available)
 * @param string $token The reCAPTCHA token
 * @param string $action The action name
 * @return bool True if valid, false otherwise
 */
function verifyRecaptchaEnterpriseHTTP($token, $action) {
    global $config;
    
    $verifyURL = 'https://www.google.com/recaptcha/api/siteverify';
    
    $data = [
        'secret' => $config['recaptcha']['secret_key'],
        'response' => $token,
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
 * Verify reCAPTCHA using standard API (v2/v3)
 * @param string $response The reCAPTCHA response
 * @param string $action The action name
 * @return bool True if valid, false otherwise
 */
function verifyRecaptchaStandard($response, $action) {
    global $config;
    
    $secretKey = $config['recaptcha']['secret_key'];
    $type = $config['recaptcha']['type'] ?? 'v2';
    
    $verifyURL = 'https://www.google.com/recaptcha/api/siteverify';
    
    $data = [
        'secret' => $secretKey,
        'response' => $response,
        'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
    ];
    
    if ($type === 'v3') {
        $data['action'] = $action;
    }
    
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
 * Generate reCAPTCHA HTML (supports v2, v3, and Enterprise)
 * @param string $action Action name for v3/Enterprise
 * @param string $theme 'light' or 'dark' (v2 only)
 * @param string $size 'normal' or 'compact' (v2 only)
 * @return string HTML for reCAPTCHA widget
 */
function generateRecaptchaHTML($action = 'submit', $theme = 'light', $size = 'normal') {
    global $config;
    
    if (!isRecaptchaEnabled()) {
        return '';
    }
    
    $siteKey = getRecaptchaSiteKey();
    $type = $config['recaptcha']['type'] ?? 'v2';
    
    if ($type === 'enterprise') {
        // Enterprise reCAPTCHA with invisible button
        return sprintf(
            '<button class="g-recaptcha btn btn-primary" 
                     data-sitekey="%s" 
                     data-callback="onRecaptchaSubmit" 
                     data-action="%s"
                     style="width: 100%%; padding: 15px; border: none; border-radius: 5px; font-size: 16px; font-weight: bold; cursor: pointer; background: #000; color: white; transition: all 0.3s;"
                     onmouseover="this.style.background=\'#333\'"
                     onmouseout="this.style.background=\'#000\'">
                Submit
            </button>',
            htmlspecialchars($siteKey),
            htmlspecialchars($action)
        );
    } elseif ($type === 'v3') {
        // v3 invisible reCAPTCHA
        return sprintf(
            '<input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response-%s">',
            htmlspecialchars($action)
        );
    } else {
        // v2 checkbox
        return sprintf(
            '<div class="g-recaptcha" data-sitekey="%s" data-theme="%s" data-size="%s"></div>',
            htmlspecialchars($siteKey),
            htmlspecialchars($theme),
            htmlspecialchars($size)
        );
    }
}

/**
 * Generate reCAPTCHA script (for v3/Enterprise)
 * @param string $action Action name
 * @return string JavaScript code
 */
function generateRecaptchaScript($action = 'submit') {
    global $config;
    
    if (!isRecaptchaEnabled()) {
        return '';
    }
    
    $siteKey = getRecaptchaSiteKey();
    $type = $config['recaptcha']['type'] ?? 'v2';
    
    if ($type === 'enterprise') {
        return sprintf(
            '<script>
                function onRecaptchaSubmit(token) {
                    // Add token to form
                    var form = document.querySelector("form");
                    var tokenInput = document.createElement("input");
                    tokenInput.type = "hidden";
                    tokenInput.name = "g-recaptcha-response";
                    tokenInput.value = token;
                    form.appendChild(tokenInput);
                    
                    // Submit form
                    form.submit();
                }
            </script>',
            htmlspecialchars($siteKey),
            htmlspecialchars($action)
        );
    } elseif ($type === 'v3') {
        return sprintf(
            '<script>
                grecaptcha.ready(function() {
                    grecaptcha.execute("%s", {action: "%s"}).then(function(token) {
                        document.getElementById("g-recaptcha-response-%s").value = token;
                    });
                });
            </script>',
            htmlspecialchars($siteKey),
            htmlspecialchars($action),
            htmlspecialchars($action)
        );
    }
    
    return '';
}
?>
