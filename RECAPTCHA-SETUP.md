# 🔒 Google reCAPTCHA v2 Setup Guide

## 📋 Overview

Google reCAPTCHA v2 (Checkbox) telah ditambahkan ke form login dan register untuk meningkatkan keamanan aplikasi.

## 🚀 Features

- ✅ reCAPTCHA v2 Checkbox di form login
- ✅ reCAPTCHA v2 Checkbox di form register  
- ✅ Server-side verification
- ✅ Test keys untuk development
- ✅ Responsive design
- ✅ Error handling

## 🔧 Setup reCAPTCHA Keys

### 1. Dapatkan Keys dari Google

1. Kunjungi: https://www.google.com/recaptcha/admin
2. Login dengan Google account
3. Klik "Create" untuk membuat site baru
4. Pilih **reCAPTCHA v2** → "I'm not a robot" Checkbox
5. Masukkan domain:
   - **Development**: `localhost`, `127.0.0.1`, `showroom.local`
   - **Production**: `yourdomain.com`, `www.yourdomain.com`
6. Copy Site Key dan Secret Key

### 2. Update Configuration

#### Option A: Environment Variables (Recommended)
```bash
# Set environment variables
export RECAPTCHA_SITE_KEY="your_site_key_here"
export RECAPTCHA_SECRET_KEY="your_secret_key_here"
```

#### Option B: Update config.php
```php
'recaptcha' => [
    'site_key' => 'your_site_key_here',
    'secret_key' => 'your_secret_key_here',
    'enabled' => true,
    'type' => 'v2'
],
```

## 🧪 Test Keys (Development)

Untuk development, Anda bisa menggunakan Google's test keys:

```php
'site_key' => '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI',
'secret_key' => '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe',
```

**Note**: Test keys akan selalu return success, cocok untuk development.

## 📱 Usage

### Form Login
- User harus centang "I'm not a robot" sebelum login
- Verification dilakukan server-side
- Error message jika reCAPTCHA gagal

### Form Register  
- User harus centang "I'm not a robot" sebelum register
- Verification dilakukan server-side
- Error message jika reCAPTCHA gagal

## 🎨 Customization

### Theme Options
```php
// Light theme (default)
generateRecaptchaHTML('submit', 'light', 'normal');

// Dark theme
generateRecaptchaHTML('submit', 'dark', 'normal');

// Compact size
generateRecaptchaHTML('submit', 'light', 'compact');
```

### CSS Styling
```css
.g-recaptcha {
    margin: 15px 0;
    display: flex;
    justify-content: center;
}

.g-recaptcha > div {
    transform: scale(0.9);
    transform-origin: 0 0;
}
```

## 🔍 Verification Process

1. **Client-side**: User completes reCAPTCHA
2. **Form submission**: reCAPTCHA response dikirim ke server
3. **Server-side**: `verifyRecaptcha()` function memverifikasi dengan Google
4. **Response**: Success atau error message

## 🛠️ Functions Available

### `verifyRecaptcha($response)`
- Verifies reCAPTCHA response dengan Google API
- Returns: `true` jika valid, `false` jika tidak

### `getRecaptchaSiteKey()`
- Returns site key untuk frontend

### `isRecaptchaEnabled()`
- Returns: `true` jika reCAPTCHA enabled

### `generateRecaptchaHTML($action, $theme, $size)`
- Generates HTML untuk reCAPTCHA widget
- Parameters: action, theme ('light'/'dark'), size ('normal'/'compact')

## 🚨 Troubleshooting

### reCAPTCHA tidak muncul
1. Cek internet connection
2. Cek site key di config
3. Cek domain di Google reCAPTCHA admin

### "Invalid key type" error
1. Pastikan menggunakan v2 keys untuk v2 reCAPTCHA
2. Cek tipe reCAPTCHA di config.php
3. Verifikasi keys di Google reCAPTCHA admin

### Verification selalu gagal
1. Cek secret key di config
2. Cek domain configuration
3. Cek server internet access

### Error: "reCAPTCHA verification failed"
1. User belum complete reCAPTCHA
2. reCAPTCHA expired (2 menit)
3. Network issue saat verification

## 📊 Monitoring

### Log reCAPTCHA attempts
```php
// Add to recaptcha.php
error_log("reCAPTCHA attempt: " . ($success ? 'SUCCESS' : 'FAILED') . " - IP: " . $_SERVER['REMOTE_ADDR']);
```

### Analytics
- Monitor reCAPTCHA success rate
- Track failed attempts
- Analyze user behavior

## 🔐 Security Best Practices

1. **Never expose secret key** di frontend
2. **Use HTTPS** untuk production
3. **Rate limiting** untuk form submissions
4. **Monitor** failed attempts
5. **Update keys** secara berkala

## 📝 Production Checklist

- [ ] Replace test keys dengan production keys
- [ ] Add production domains ke Google reCAPTCHA admin
- [ ] Enable HTTPS
- [ ] Test di production environment
- [ ] Monitor error logs
- [ ] Setup rate limiting

## 🆘 Support

Jika ada masalah:
1. Cek Google reCAPTCHA documentation
2. Verify domain configuration
3. Test dengan curl/Postman
4. Check server logs

## 🔄 Key Types Reference

### reCAPTCHA v2 Keys
- **Format**: `6Le...` (starts with 6Le)
- **Usage**: Checkbox "I'm not a robot"
- **API**: `https://www.google.com/recaptcha/api/siteverify`

### reCAPTCHA v3 Keys
- **Format**: `6Lc...` (starts with 6Lc)
- **Usage**: Invisible scoring
- **API**: `https://www.google.com/recaptcha/api/siteverify`

### reCAPTCHA Enterprise Keys
- **Format**: `6Ld...` (starts with 6Ld)
- **Usage**: Enterprise features
- **API**: `https://recaptchaenterprise.googleapis.com/v1/`
