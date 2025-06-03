
# Real Estate Lead Capture Template

A professional, responsive lead capture page template designed for real estate brokers and agents. This template helps generate qualified leads through an interactive multi-step process.

## Features

- **Step 1: Address Search** - Google Maps integrated address autocomplete
- **Step 2: Property Quiz** - Interactive questionnaire about property preferences
- **Step 3: Lead Capture** - Contact information collection with validation
- **Step 4: Results Map** - Visual map display with user preferences summary
- **Responsive Design** - Works perfectly on desktop, tablet, and mobile devices
- **PHP/MySQL Backend** - Complete database integration for lead storage
- **Admin Ready** - Built with future admin panel expansion in mind

## Installation Instructions

### Prerequisites
- Web server with PHP 7.4+ support
- MySQL 5.7+ or MariaDB 10.2+
- Google Maps API key

### Quick Setup (cPanel)

1. **Upload Files**
   - Download/extract the template files
   - Upload all files to your cPanel's `public_html` directory
   - Ensure proper file permissions (644 for files, 755 for directories)

2. **Database Setup**
   - Create a new MySQL database in cPanel
   - Import the `database.sql` file using phpMyAdmin
   - Note your database credentials

3. **Configuration**
   - Edit `config.php` with your database credentials
   - Add your Google Maps API key in `config.php`
   - Update email settings for notifications

4. **Google Maps API**
   - Get your API key from [Google Cloud Console](https://console.cloud.google.com/)
   - Enable "Maps JavaScript API" and "Places API"
   - Replace `YOUR_GOOGLE_MAPS_API_KEY` in `index.html` and `config.php`

### Manual Installation

1. **Database Setup**
   ```sql
   -- Run the contents of database.sql in your MySQL database
   CREATE DATABASE real_estate_leads;
   -- Import the schema and default data
   ```

2. **File Configuration**
   ```php
   // Edit config.php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'real_estate_leads');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('GOOGLE_MAPS_API_KEY', 'your_api_key');
   ```

3. **Web Server**
   - Upload files to your web server
   - Ensure PHP has MySQL/PDO support enabled
   - Set appropriate file permissions

## File Structure

```
/
├── index.html          # Main landing page
├── styles.css          # All CSS styling
├── script.js           # JavaScript functionality
├── submit.php          # Form submission handler
├── config.php          # Configuration settings
├── database.sql        # Database schema
└── README.md           # This file
```

## Configuration Options

### Google Maps API Key
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing
3. Enable "Maps JavaScript API" and "Places API"
4. Create credentials (API Key)
5. Restrict the API key to your domain for security

### Database Settings
Edit `config.php` to match your hosting environment:
```php
define('DB_HOST', 'localhost');        // Usually 'localhost' for cPanel
define('DB_NAME', 'your_db_name');     // Your database name
define('DB_USER', 'your_db_user');     // Your database username
define('DB_PASS', 'your_db_password'); // Your database password
```

### Email Notifications
Configure email settings in `config.php`:
```php
define('ADMIN_EMAIL', 'your@email.com');
define('FROM_EMAIL', 'noreply@yourdomain.com');
```

## Customization

### Styling
- Edit `styles.css` to match your brand colors and fonts
- All colors use CSS custom properties for easy theming
- Responsive breakpoints can be adjusted in the media queries

### Content
- Update text content in `index.html`
- Modify quiz questions in `script.js` (quizQuestions array)
- Change background images by updating the URLs in CSS

### Database Schema
- The `leads` table stores all captured information
- Additional fields can be added to the database and form
- The `settings` table allows for dynamic configuration

## Browser Support

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+
- Mobile browsers (iOS Safari, Chrome Mobile)

## Security Features

- Input sanitization and validation
- SQL injection prevention with prepared statements
- XSS protection with output encoding
- CSRF protection ready (can be enabled)

## Support & Documentation

For support and customization services, please contact:
- Email: support@yourdomain.com
- Documentation: [Your documentation URL]

## License

This template is licensed for commercial use. Please refer to your purchase agreement for specific terms and conditions.

## Version History

- v1.0.0 - Initial release
  - Multi-step lead capture process
  - Google Maps integration
  - PHP/MySQL backend
  - Responsive design
  - Basic admin preparation

## Future Enhancements

The template is designed to support these future additions:
- Admin dashboard for lead management
- Email marketing integration
- Property listing integration
- Advanced analytics and reporting
- Multi-language support
- A/B testing capabilities

---

**Important:** Remember to update all placeholder text, API keys, and configuration settings before deploying to production.
