# Security Features - Kaleni Catering Services

## 🔒 Security Measures Implemented

### 1. Contact Form Bot Prevention

#### Honeypot Field
- Hidden field that bots will fill but humans won't see
- If filled, submission is silently rejected
- No indication to bots that they were caught

#### Rate Limiting
- **3 messages per hour** per IP address
- Prevents spam and abuse
- Clear error messages when limit exceeded

#### Input Validation
- Strict validation rules for all fields
- Minimum/maximum length requirements
- Email format validation
- HTML tag stripping to prevent XSS

#### Spam Detection
- Pattern matching for suspicious content (URLs, scripts, etc.)
- Keyword filtering for common spam terms
- Automatic logging of suspicious activity

#### Content Sanitization
- All user input is sanitized before storage
- HTML tags stripped
- Email addresses filtered
- IP addresses and user agents logged for security

---

### 2. Security Headers

All responses include security headers:

- **X-Content-Type-Options: nosniff** - Prevents MIME type sniffing
- **X-Frame-Options: SAMEORIGIN** - Prevents clickjacking
- **X-XSS-Protection: 1; mode=block** - XSS protection
- **Referrer-Policy: strict-origin-when-cross-origin** - Controls referrer information
- **Content-Security-Policy** - Restricts resource loading
- **Permissions-Policy** - Restricts browser features

Server information headers are removed:
- X-Powered-By header removed
- Server header removed

---

### 3. Admin Panel Security

#### Hidden Admin Path
- The panel is served under a private, unguessable URL prefix (`ADMIN_PATH` in `.env`),
  not the conventional `/admin`.
- Not listed in `robots.txt`; every admin response carries `X-Robots-Tag: noindex, nofollow`.
- Unknown paths return a plain 404, revealing nothing about the panel's existence.

#### Two-Factor Authentication (mandatory)
- TOTP (Google Authenticator-compatible), required for every admin/editor account.
- Password is verified first but does **not** create a session — a second, separately
  rate-limited step is required before login completes.
- One-time recovery codes for lost-device access.

#### Login Rate Limiting & Lockout
- **5 login attempts per 15 minutes** per IP (`AuthController`).
- **5 failed attempts per account** also triggers a 15-minute account-level lockout,
  independent of the IP throttle.
- Clear error messages.

#### Audit Log
- Logins, 2FA events, password resets, and user management actions are recorded with
  user, IP, user agent, and timestamp — viewable in the admin panel (Security → Audit Log).

#### Session Security
- Session regeneration on login
- Session invalidation on logout
- Secure session cookies (when HTTPS enabled)
- SameSite cookie protection

#### Password Requirements
- Minimum 8 characters enforced
- Validation on login attempts

#### Role-Based Access Control
- Admin middleware protection
- Role verification on all admin routes
- Automatic logout for non-admin users

---

### 4. General Application Security

#### CSRF Protection
- Laravel's built-in CSRF token protection
- All forms include CSRF tokens
- Automatic token validation

#### SQL Injection Prevention
- Eloquent ORM with parameter binding
- No raw SQL queries with user input
- Prepared statements used throughout

#### XSS Prevention
- Blade templating engine auto-escapes output
- User input sanitized before display
- HTML tags stripped from stored content

#### Input Validation
- Server-side validation on all forms
- Client-side validation for better UX
- Sanitization before database storage

---

## 📋 Security Best Practices

### For Administrators

1. **Admin credentials**
   - The seeder generates a random password and prints it once to the terminal — there is
     no fixed default password.
   - Two-factor authentication (authenticator app) is required on first login.
   - Use strong, unique passwords for any additional admin/editor accounts you create.

2. **Keep Laravel Updated**
   - Regularly update Laravel framework
   - Update dependencies via Composer
   - Monitor security advisories

3. **Environment Variables**
   - Never commit `.env` file
   - Use strong database passwords
   - Keep DPO credentials secure

4. **Regular Backups**
   - Backup database regularly
   - Store backups securely
   - Test backup restoration

5. **Monitor Logs**
   - Check `storage/logs/laravel.log` regularly
   - Watch for suspicious activity
   - Review contact form submissions

---

## 🛡️ Additional Security Recommendations

### Production Deployment

1. **Enable HTTPS**
   - Use SSL/TLS certificates
   - Update `SESSION_SECURE_COOKIE=true` in `.env`
   - Force HTTPS redirects

2. **Update APP_DEBUG**
   - Set `APP_DEBUG=false` in production
   - Hide error details from users
   - Log errors securely

3. **Database Security**
   - Use strong database passwords
   - Limit database user permissions
   - Regular database backups

4. **Server Security**
   - Keep server software updated
   - Configure firewall rules
   - Use secure SSH keys

5. **File Permissions**
   - Set proper file permissions
   - Protect sensitive directories
   - Restrict upload file types

---

## 📊 Security Monitoring

### Logged Events

The system logs:
- Bot detection attempts
- Suspicious content in forms
- Spam keyword detection
- Successful contact form submissions
- Admin login attempts (failed and successful)
- Rate limit violations

### Log Location
- `storage/logs/laravel.log`

### Review Regularly
- Check logs weekly
- Monitor for patterns
- Investigate suspicious activity

---

## 🔧 Configuration

### Rate Limiting

**Contact Form:**
- 3 submissions per hour per IP
- Configured in `ContactController`

**Admin Login:**
- 5 attempts per 15 minutes per IP
- Configured in `AdminAuthController`

### Security Headers

Configured in `SecurityHeadersMiddleware`
- Applied to all requests automatically
- Can be customized per route if needed

---

## ✅ Security Checklist

- [x] Honeypot field on contact form
- [x] Rate limiting on contact form
- [x] Rate limiting on admin login
- [x] Input validation and sanitization
- [x] Spam detection
- [x] Security headers
- [x] CSRF protection
- [x] XSS prevention
- [x] SQL injection prevention
- [x] Session security
- [x] Role-based access control
- [x] Activity logging

---

## 🆘 Security Issues

If you discover a security vulnerability:

1. **Do NOT** create a public issue
2. Email security concerns privately
3. Include steps to reproduce
4. Wait for fix before disclosure

---

**Last Updated:** {{ date('Y-m-d') }}
