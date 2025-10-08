# 🔒 Google reCAPTCHA Enterprise Setup Guide

## 📋 Overview

Google reCAPTCHA Enterprise telah diimplementasikan dengan Google Cloud API untuk proteksi yang lebih canggih.

## 🚀 Features

- ✅ reCAPTCHA Enterprise dengan Google Cloud API
- ✅ Action-based verification (LOGIN, REGISTER)
- ✅ Risk scoring dan analysis
- ✅ Advanced threat detection
- ✅ Fallback ke standard API jika Google Cloud library tidak tersedia
- ✅ Comprehensive error handling

## 🔧 Setup Requirements

### 1. Google Cloud Project
- **Project ID**: `shaped-network-470412-q5`
- **reCAPTCHA Key**: `6LdNueIrAAAAALRKnwvzFYSWJDZU64Q4dxYtVJP4`
- **Secret Key**: `6LdNueIrAAAAADsck-GbReXrMMZAaod6hjYuTLMl`

### 2. Install Dependencies
```bash
# Run the installation script
./install-dependencies.sh

# Or manually install
composer install --no-dev --optimize-autoloader
```

### 3. Google Cloud Authentication

#### Option A: Service Account Key (Recommended for Production)
```bash
# Download service account key from Google Cloud Console
# Set environment variable
export GOOGLE_APPLICATION_CREDENTIALS=/path/to/service-account-key.json
```

#### Option B: Application Default Credentials (Development)
```bash
# Install Google Cloud CLI
# Authenticate
gcloud auth application-default login
```

## 📱 Implementation Details

### Configuration
```php
'recaptcha' => [
    'site_key' => '6LdNueIrAAAAALRKnwvzFYSWJDZU64Q4dxYtVJP4',
    'secret_key' => '6LdNueIrAAAAADsck-GbReXrMMZAaod6hjYuTLMl',
    'enabled' => true,
    'type' => 'enterprise',
    'project_id' => 'shaped-network-470412-q5'
],
```

### Frontend Integration
```html
<!-- Enterprise Script -->
<script src="https://www.google.com/recaptcha/enterprise.js?render=6LdNueIrAAAAALRKnwvzFYSWJDZU64Q4dxYtVJP4"></script>

<!-- Enterprise Button -->
<button class="g-recaptcha" 
        data-sitekey="6LdNueIrAAAAALRKnwvzFYSWJDZU64Q4dxYtVJP4" 
        data-callback="onRecaptchaSubmit" 
        data-action="LOGIN">
    Submit
</button>
```

### Backend Verification
```php
// Verify with action
$isValid = verifyRecaptcha($token, 'LOGIN');

// Enterprise API provides risk score
// Score > 0.5 is considered valid (adjustable)
```

## 🎯 Actions Supported

- **LOGIN**: User login attempts
- **REGISTER**: User registration
- **SUBMIT**: General form submissions

## 🔍 Risk Analysis

Enterprise reCAPTCHA provides risk scores:
- **0.0 - 0.3**: High risk (likely bot)
- **0.3 - 0.7**: Medium risk
- **0.7 - 1.0**: Low risk (likely human)

Current threshold: **0.5** (adjustable in code)

## 🛠️ Functions Available

### `verifyRecaptcha($response, $action)`
- Main verification function
- Supports v2, v3, and Enterprise
- Returns: `true` if valid, `false` if not

### `verifyRecaptchaEnterprise($token, $action)`
- Enterprise-specific verification
- Uses Google Cloud API
- Provides risk scoring

### `verifyRecaptchaEnterpriseHTTP($token, $action)`
- Fallback HTTP verification
- Used when Google Cloud library unavailable

## 🚨 Troubleshooting

### "Invalid key type" Error
- Ensure using Enterprise keys with Enterprise script
- Check key format: `6Ld...` for Enterprise

### Authentication Errors
- Verify Google Cloud authentication
- Check service account permissions
- Ensure project ID is correct

### Library Not Found
- Run `composer install`
- Check `vendor/autoload.php` exists
- Verify Google Cloud library installation

### API Errors
- Check Google Cloud Console for API errors
- Verify project billing is enabled
- Check API quotas and limits

## 📊 Monitoring

### Logs
```php
// Enterprise verification logs
error_log('reCAPTCHA Enterprise: Score - ' . $score);
error_log('reCAPTCHA Enterprise: Token invalid - ' . $reason);
```

### Google Cloud Console
- View assessments in reCAPTCHA Enterprise console
- Monitor risk scores and patterns
- Analyze bot detection effectiveness

## 🔐 Security Best Practices

1. **Authentication**: Use service account keys for production
2. **Environment Variables**: Store credentials securely
3. **Rate Limiting**: Implement additional rate limiting
4. **Monitoring**: Monitor risk scores and patterns
5. **Thresholds**: Adjust risk score thresholds based on data

## 📝 Production Checklist

- [ ] Install Google Cloud dependencies
- [ ] Set up service account authentication
- [ ] Configure production domains
- [ ] Test Enterprise functionality
- [ ] Monitor risk scores
- [ ] Set up alerting for high-risk attempts
- [ ] Document incident response procedures

## 🆘 Support

### Google Cloud Resources
- [reCAPTCHA Enterprise Documentation](https://cloud.google.com/recaptcha-enterprise/docs)
- [PHP Client Library](https://cloud.google.com/php/docs/reference/recaptcha-enterprise/latest)
- [Authentication Guide](https://cloud.google.com/docs/authentication)

### Common Issues
1. **403 Forbidden**: Authentication required
2. **Invalid Key**: Wrong key type for implementation
3. **Library Missing**: Run `composer install`
4. **High Risk Scores**: Adjust threshold or investigate

## 🔄 Migration from v2

If migrating from reCAPTCHA v2:
1. Update configuration to Enterprise
2. Install Google Cloud dependencies
3. Set up authentication
4. Update frontend scripts
5. Test thoroughly
6. Monitor performance

## 📈 Performance Considerations

- **Caching**: Cache Google Cloud client instances
- **Timeouts**: Set appropriate API timeouts
- **Fallbacks**: Implement fallback mechanisms
- **Monitoring**: Track API response times
