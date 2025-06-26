
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

-- Admin users table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE
);

-- Settings table for configurable options
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Quiz questions table
CREATE TABLE IF NOT EXISTS quiz_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id VARCHAR(50) NOT NULL,
    title TEXT NOT NULL,
    icon VARCHAR(100) NOT NULL,
    question_order INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_order (question_order),
    INDEX idx_active (is_active)
);

-- Quiz options table
CREATE TABLE IF NOT EXISTS quiz_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id VARCHAR(50) NOT NULL,
    option_value VARCHAR(100) NOT NULL,
    option_label VARCHAR(255) NOT NULL,
    option_order INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_question (question_id),
    INDEX idx_order (option_order)
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
('success_message', 'Thank you! Your information has been submitted successfully.', 'Success message after form submission'),
('step1_search_placeholder', 'Enter address, city, or ZIP code', 'Placeholder text for address search input'),
('step1_start_button_text', 'Start Your Search', 'Text for the start search button'),
('step1_powered_by_text', 'Powered by Google Maps • Trusted by thousands of buyers', 'Footer text on step 1'),
('step2_back_button_text', 'Back', 'Text for quiz back button'),
('step2_next_button_text', 'Next', 'Text for quiz next button'),
('step2_continue_button_text', 'Continue', 'Text for quiz continue button on last question'),
('step3_title', 'Almost There!', 'Title for lead capture form'),
('step3_subtitle', 'Let us know how to reach you with the perfect property matches', 'Subtitle for lead capture form'),
('step3_name_label', 'Full Name *', 'Label for name field'),
('step3_name_placeholder', 'Enter your full name', 'Placeholder for name field'),
('step3_email_label', 'Email Address *', 'Label for email field'),
('step3_email_placeholder', 'Enter your email address', 'Placeholder for email field'),
('step3_phone_label', 'Phone Number *', 'Label for phone field'),
('step3_phone_placeholder', 'Enter your phone number', 'Placeholder for phone field'),
('step3_privacy_title', 'Your Privacy Matters', 'Title for privacy notice'),
('step3_back_button_text', 'Back', 'Text for form back button'),
('step3_next_button_text', 'View Results', 'Text for form next button'),
('step4_title', 'Your Property Search Results', 'Title for results page'),
('step4_subtitle_prefix', 'Based on your preferences for', 'Prefix for results subtitle'),
('step4_preferences_title', 'Your Preferences', 'Title for preferences summary'),
('step4_back_button_text', 'Back', 'Text for results back button'),
('step4_submit_button_text', 'Submit Information', 'Text for final submit button'),
('success_modal_title_prefix', 'Thank You,', 'Prefix for success modal title'),
('success_modal_subtitle', 'Your property search preferences have been submitted successfully.', 'Subtitle for success modal'),
('success_modal_next_steps_title', 'What happens next?', 'Title for next steps section'),
('success_modal_step1_text', 'You''ll receive matching properties via email within 24 hours', 'Text for first next step'),
('success_modal_step2_text', 'Our expert agent will call you to discuss your needs', 'Text for second next step'),
('success_modal_step3_text', 'We''ll schedule property viewings that match your criteria', 'Text for third next step'),
('success_modal_button_text', 'Start Another Search', 'Text for new search button')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- Insert default quiz questions
INSERT INTO quiz_questions (question_id, title, icon, question_order) VALUES
('propertyType', 'What type of property are you looking for?', 'fas fa-home', 1),
('bedrooms', 'How many bedrooms do you need?', 'fas fa-bed', 2),
('bathrooms', 'How many bathrooms do you prefer?', 'fas fa-bath', 3),
('priceRange', 'What is your budget range?', 'fas fa-dollar-sign', 4),
('timeline', 'When are you looking to buy?', 'fas fa-calendar', 5),
('financing', 'How will you finance your purchase?', 'fas fa-credit-card', 6)
ON DUPLICATE KEY UPDATE title = VALUES(title);

-- Insert default quiz options
INSERT INTO quiz_options (question_id, option_value, option_label, option_order) VALUES
-- Property Type Options
('propertyType', 'single-family', 'Single Family Home', 1),
('propertyType', 'condo', 'Condominium', 2),
('propertyType', 'townhouse', 'Townhouse', 3),
('propertyType', 'multi-family', 'Multi-Family', 4),
-- Bedrooms Options
('bedrooms', '1', '1 Bedroom', 1),
('bedrooms', '2', '2 Bedrooms', 2),
('bedrooms', '3', '3 Bedrooms', 3),
('bedrooms', '4+', '4+ Bedrooms', 4),
-- Bathrooms Options
('bathrooms', '1', '1 Bathroom', 1),
('bathrooms', '1.5', '1.5 Bathrooms', 2),
('bathrooms', '2', '2 Bathrooms', 3),
('bathrooms', '3+', '3+ Bathrooms', 4),
-- Price Range Options
('priceRange', 'under-300k', 'Under $300,000', 1),
('priceRange', '300k-500k', '$300,000 - $500,000', 2),
('priceRange', '500k-750k', '$500,000 - $750,000', 3),
('priceRange', 'over-750k', 'Over $750,000', 4),
-- Timeline Options
('timeline', 'immediately', 'Immediately', 1),
('timeline', '1-3-months', '1-3 Months', 2),
('timeline', '3-6-months', '3-6 Months', 3),
('timeline', '6-months-plus', '6+ Months', 4),
-- Financing Options
('financing', 'mortgage', 'Mortgage/Loan', 1),
('financing', 'cash', 'Cash Purchase', 2),
('financing', 'pre-approved', 'Pre-approved', 3),
('financing', 'need-help', 'Need Help with Financing', 4)
ON DUPLICATE KEY UPDATE option_label = VALUES(option_label);

-- Insert default admin user (username: admin, password: admin123)
-- Note: Change this password immediately after setup
INSERT INTO admin_users (username, email, password_hash) VALUES
('admin', 'admin@yourdomain.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
ON DUPLICATE KEY UPDATE username = VALUES(username);
