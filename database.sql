
-- database.sql - Database schema for real estate lead capture

CREATE DATABASE IF NOT EXISTS real_estate_leads;
USE real_estate_leads;

-- Leads table
CREATE TABLE IF NOT EXISTS leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    address TEXT NOT NULL,
    coordinates_lat DECIMAL(10, 8) NULL,
    coordinates_lng DECIMAL(11, 8) NULL,
    property_type VARCHAR(50) NULL,
    bedrooms VARCHAR(10) NULL,
    bathrooms VARCHAR(10) NULL,
    price_range VARCHAR(50) NULL,
    timeline VARCHAR(50) NULL,
    financing VARCHAR(50) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_created_at (created_at),
    INDEX idx_property_type (property_type),
    INDEX idx_price_range (price_range)
);

-- Admin users table (for future admin panel)
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE
);

-- Settings table (for configurable options)
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default settings
INSERT INTO settings (setting_key, setting_value, description) VALUES
('google_maps_api_key', 'YOUR_GOOGLE_MAPS_API_KEY', 'Google Maps API key for address autocomplete and maps'),
('admin_email', 'admin@yourdomain.com', 'Email address to receive lead notifications'),
('site_title', 'Find Your Dream Home', 'Main title displayed on the landing page'),
('site_subtitle', 'Enter your desired location to get started', 'Subtitle displayed on the landing page'),
('background_image', 'https://images.unsplash.com/photo-1487958449943-2429e8be8625?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80', 'Background image URL for the landing page'),
('company_name', 'Real Estate Lead Magnet', 'Company name for branding'),
('privacy_text', 'We''ll only use your information to send you relevant property listings and market updates. You can unsubscribe at any time.', 'Privacy notice text'),
('success_message', 'Thank you! Your information has been submitted successfully.', 'Success message after form submission')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- Sample data (optional - remove in production)
-- INSERT INTO leads (name, email, phone, address, property_type, bedrooms, bathrooms, price_range, timeline, financing) VALUES
-- ('John Doe', 'john@example.com', '555-1234', '123 Main St, Anytown, USA', 'single-family', '3', '2', '300k-500k', '1-3-months', 'mortgage'),
-- ('Jane Smith', 'jane@example.com', '555-5678', '456 Oak Ave, Somewhere, USA', 'condo', '2', '2', 'under-300k', 'immediately', 'pre-approved');
