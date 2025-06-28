
# Real Estate Lead Capture Template - Professional Edition

A complete, professional lead capture funnel template designed for real estate brokers and agents. This template features a multi-step interactive process, comprehensive admin panel, and automated installation system to help generate qualified leads effectively.

## 🚀 Key Features

### Frontend Lead Capture System
- **Step 1: Address Search** - Google Maps integrated address autocomplete
- **Step 2: Property Quiz** - Interactive questionnaire about property preferences  
- **Step 3: Lead Capture** - Contact information collection with validation
- **Step 4: Results Map** - Visual map display with user preferences summary
- **Responsive Design** - Works perfectly on desktop, tablet, and mobile devices

### Complete Admin Panel
- **Lead Management** - View, search, filter, and delete leads
- **CSV Export** - Export leads with pagination and filtering options
- **Analytics Dashboard** - Lead statistics and conversion metrics
- **Dynamic Content Management** - Edit all text content, quiz questions, and settings
- **Background Customization** - Change background images from admin panel
- **Quiz Builder** - Create and modify quiz questions and answer options
- **Google Maps Integration** - Manage API keys through admin settings

### Automated Installation System
- **System Requirements Check** - Automatic validation of server requirements
- **Database Connection Testing** - Verify database credentials before installation
- **One-Click Setup** - Automated database table creation and configuration
- **Admin Account Creation** - Set up admin credentials during installation
- **Auto-Cleanup** - Remove installation files after successful setup

### Technical Features
- **PHP/MySQL Backend** - Complete database integration for lead storage
- **Security Features** - Input sanitization, SQL injection prevention, XSS protection
- **Session Management** - Secure admin authentication system
- **CSRF Protection** - Built-in security for admin forms
- **Debug Mode** - Development-friendly error reporting

## 📋 System Requirements

- **Web Server**: Apache/Nginx with PHP support
- **PHP Version**: 7.4 or higher
- **Database**: MySQL 5.7+ or MariaDB 10.2+
- **PHP Extensions**: PDO, PDO_MySQL, Session support
- **File Permissions**: Write access to root directory
- **Mail Function**: For email notifications (optional)

## 🎯 For CodeCanyon Buyers

### What You Get
- Complete source code (PHP, HTML, CSS, JavaScript)
- Automated installation system
- Professional admin panel
- Comprehensive documentation
- Database schema with sample data
- Ready-to-deploy package

### Download Instructions
1. Download the ZIP file from your CodeCanyon account
2. Extract the contents to your local machine
3. Upload all files to your web server's document root
4. Navigate to `yourdomain.com/install/` to begin installation

## 🛠️ Installation Instructions

### Quick Installation (Recommended)

1. **Upload Files**
   - Extract the downloaded ZIP file
   - Upload all files to your web server's document root (usually `public_html`)
   - Ensure proper file permissions (644 for files, 755 for directories)

2. **Run Installer**
   - Navigate to `yourdomain.com/install/` in your browser
   - The installer will automatically check system requirements
   - Follow the step-by-step installation wizard

3. **Database Setup**
   - Create a MySQL database in your hosting control panel
   - Enter database credentials in the installer
   - The system will automatically create all required tables

4. **Admin Account**
   - Create your admin username and password during installation
   - Set your admin email address for notifications

5. **Google Maps API** (Optional)
   - Get your API key from [Google Cloud Console](https://console.cloud.google.com/)
   - Enable "Maps JavaScript API" and "Places API"
   - Enter the API key during installation or later in admin settings

6. **Complete Installation**
   - Review your settings and complete the installation
   - The installer will automatically remove itself for security

### Manual Installation (Advanced Users)

If you prefer manual installation or need custom configuration:

1. **Database Setup**
   ```sql
   -- Create your database
   CREATE DATABASE your_database_name;
   
   -- Import the schema
   -- Use the install/database.sql file in phpMyAdmin or command line
   ```

2. **Configuration**
   - Copy `config.php.sample` to `config.php` (if provided)
   - Edit database credentials and other settings
   - Set `INSTALLATION_COMPLETE` to `true`

3. **Admin Account**
   - Manually insert admin user into `admin_users` table
   - Use password_hash() function for secure password storage

## 🎨 Customization Guide

### Admin Panel Customization

Access your admin panel at `yourdomain.com/admin/` using the credentials created during installation.

#### Content Management
- **Page Titles**: Edit main heading, subheadings, and descriptions
- **Button Text**: Customize all button labels and call-to-action text
- **Success Messages**: Modify confirmation and thank you messages
- **Email Templates**: Customize notification email content

#### Quiz Builder
- **Add Questions**: Create new quiz questions with custom icons
- **Edit Options**: Modify answer choices for each question
- **Reorder Questions**: Change the sequence of quiz steps
- **Question Types**: Support for radio button selections

#### Visual Customization
- **Background Images**: Upload and change background images for each step
- **Google Maps**: Configure API settings and map appearance
- **Branding**: Update company information and contact details

#### Lead Management
- **View Leads**: Access complete lead information with search and filtering
- **Export Data**: Download leads in CSV format with date range filtering
- **Lead Analytics**: View conversion statistics and lead sources
- **Delete Leads**: Remove individual or bulk leads as needed

### Code Customization

#### Styling
- Edit `styles.css` to match your brand colors and fonts
- All colors use CSS custom properties for easy theming
- Responsive breakpoints can be adjusted in media queries

#### JavaScript Functionality
- Modify `script.js` for custom form behavior
- Quiz questions and logic can be customized
- Google Maps integration settings

#### PHP Backend
- Customize email templates in `submit.php`
- Modify lead storage logic and validation rules
- Add custom fields to the database schema

## 📁 File Structure

```
/
├── install/                    # Installation system (auto-removed)
│   ├── index.php              # Installation wizard
│   ├── check_requirements.php # System requirements check
│   ├── test_connection.php    # Database connection test
│   ├── install.php            # Installation processor
│   ├── delete_install.php     # Installation cleanup
│   └── database.sql           # Database schema
├── admin/                     # Admin panel
│   ├── index.php             # Admin dashboard
│   ├── login.php             # Admin login
│   ├── logout.php            # Admin logout
│   └── api/                  # Admin API endpoints
│       ├── get_leads.php     # Lead retrieval
│       ├── export_csv.php    # CSV export
│       ├── delete_lead.php   # Lead deletion
│       ├── get_settings.php  # Settings retrieval
│       └── save_settings.php # Settings management
├── index.html                # Main landing page
├── styles.css               # All CSS styling
├── script.js                # JavaScript functionality
├── submit.php               # Form submission handler
├── get_content.php          # Dynamic content API
├── config.php               # Configuration settings
└── database.sql             # Database schema (backup)
```

## 🔧 Configuration Options

### Database Settings
The installer will automatically configure these, but they can be modified in `config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_database_name');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### Google Maps API
1. Visit [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing one
3. Enable "Maps JavaScript API" and "Places API"
4. Create credentials (API Key)
5. Add the API key in admin settings or `config.php`
6. Restrict the API key to your domain for security

### Email Configuration
Configure SMTP settings in admin panel or `config.php`:
```php
define('SMTP_HOST', 'your_smtp_host');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your_smtp_username');
define('SMTP_PASS', 'your_smtp_password');
```

## 🔒 Security Features

- **Input Sanitization**: All user input is properly sanitized
- **SQL Injection Prevention**: Prepared statements used throughout
- **XSS Protection**: Output encoding prevents cross-site scripting
- **CSRF Protection**: Admin forms include CSRF token validation
- **Session Security**: Secure session handling with timeout
- **Password Hashing**: Admin passwords use PHP's password_hash()
- **File Upload Security**: Restricted file types and validation

## 📊 Analytics & Reporting

### Lead Analytics
- Total leads captured
- Conversion rates by time period
- Lead source tracking
- Property preference analysis
- Geographic distribution

### Export Options
- CSV export with date filtering
- Paginated exports for large datasets
- Custom field selection
- Bulk data operations

## 🌐 Browser Support

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+
- Mobile browsers (iOS Safari, Chrome Mobile)
- Internet Explorer 11 (basic functionality)

## 📱 Mobile Optimization

- Fully responsive design
- Touch-friendly interface
- Optimized form fields for mobile
- Fast loading on mobile networks
- Mobile-specific user experience enhancements

## 🎓 Usage Tips

### Best Practices
1. **Test Installation**: Always test on a staging environment first
2. **Backup Database**: Regular backups of your leads database
3. **Monitor Performance**: Check Google Maps API usage to avoid overages
4. **Update Content**: Regularly refresh quiz questions and content
5. **Security Updates**: Keep PHP and database software updated

### Common Issues
- **Google Maps Not Loading**: Check API key and enabled services
- **Form Not Submitting**: Verify database connection and file permissions
- **Admin Login Issues**: Check session configuration and password
- **Email Not Sending**: Configure SMTP settings properly

## 🚀 Deployment Guide

### Shared Hosting (cPanel)
1. Upload files via File Manager or FTP
2. Create MySQL database in cPanel
3. Run the installer at yourdomain.com/install/
4. Configure email settings if needed

### VPS/Dedicated Server
1. Upload files to document root
2. Set proper file permissions
3. Configure PHP settings if needed
4. Run installer and configure

### WordPress Integration
- Can be installed in a subdirectory
- Use separate database or prefix tables
- Ensure no conflicts with WordPress plugins

## 📞 Support & Updates

### What's Included
- Free installation support
- 6 months of bug fixes
- Documentation updates
- Basic customization guidance

### Extended Support Available
- Custom modifications
- Advanced integrations
- Performance optimization
- Additional features development

## 📄 License & Usage

This template is licensed for commercial use with the following terms:
- Single domain license per purchase
- Can be used for client projects with extended license
- Resale or redistribution not permitted
- Modification and customization allowed

## 🔄 Version History

### v2.0.0 - Latest Version
- Complete admin panel with lead management
- Automated installation system
- Dynamic content management
- Quiz builder functionality
- Advanced analytics and reporting
- Enhanced security features
- Mobile optimization improvements

### v1.0.0 - Initial Release
- Basic lead capture funnel
- Google Maps integration
- PHP/MySQL backend
- Responsive design

## 🆘 Troubleshooting

### Common Installation Issues

**Database Connection Failed**
- Verify database credentials
- Check database server status
- Ensure database exists and user has proper permissions

**File Permission Errors**
- Set directories to 755 permissions
- Set files to 644 permissions
- Ensure web server can write to root directory

**Google Maps Not Working**
- Verify API key is correct
- Check that Maps JavaScript API and Places API are enabled
- Ensure API key restrictions match your domain

**Admin Panel Access Issues**
- Clear browser cache and cookies
- Check admin credentials
- Verify session configuration in PHP

### Getting Help

1. Check this documentation first
2. Review error logs in your hosting control panel
3. Test on a fresh installation to isolate issues
4. Contact support with specific error messages and server details

---

**🎉 Thank you for purchasing our Real Estate Lead Capture Template!**

This professional-grade solution will help you capture and convert more leads for your real estate business. The combination of an engaging user experience and powerful admin tools makes this the complete solution for real estate lead generation.

For additional support, customization requests, or feature suggestions, please don't hesitate to reach out through your CodeCanyon purchase page.

**Happy lead capturing!** 🏠✨
