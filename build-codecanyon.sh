
#!/bin/bash

echo "🚀 Building CodeCanyon-ready package for Real Estate Lead Capture..."

# Clean up any previous builds
rm -rf codecanyon-build
rm -f real-estate-lead-capture-v2.0.zip

# Make all scripts executable
chmod +x scripts/*.sh

# Build the React application
./scripts/build-frontend.sh
if [ $? -ne 0 ]; then
    echo "❌ Frontend build failed"
    exit 1
fi

# Create build directory and copy files
mkdir -p codecanyon-build
cd codecanyon-build

echo "📂 Copying PHP files..."
# Copy PHP files directly (not from dist)
cp ../index.php .
cp ../get_content.php .
cp ../submit.php .
cp ../styles.css .
cp ../script.js .

# Copy React build files if they exist
if [ -d "../dist" ]; then
    echo "📂 Copying React build assets..."
    # Only copy assets directory from React build
    if [ -d "../dist/assets" ]; then
        cp -r ../dist/assets .
    fi
    
    # Copy any other static files from dist (excluding index.html since we use index.php)
    find ../dist -maxdepth 1 -type f ! -name "index.html" -exec cp {} . \;
fi

# Verify main files were copied
if [ ! -f "index.php" ]; then
    echo "❌ PHP files not copied properly"
    echo "Current directory contents:"
    ls -la
    exit 1
fi

echo "✅ PHP and static files copied successfully"

# Copy installation files
echo "📂 Copying installation files..."
mkdir -p install
cp -r ../install/* install/
cp ../simple-install.php .
cp ../config.php .

# Create PHP backend files
../scripts/create-php-files.sh
if [ $? -ne 0 ]; then
    echo "❌ PHP backend creation failed"
    exit 1
fi

# Create documentation
../scripts/create-documentation.sh
if [ $? -ne 0 ]; then
    echo "❌ Documentation creation failed"
    exit 1
fi

# Create quick start guide for cPanel
cat > CPANEL-QUICKSTART.md << 'EOF'
# cPanel Quick Start Guide

## Option 1: Simple Installation (Recommended)
1. Upload all files to your cPanel's `public_html` directory
2. Create a MySQL database in cPanel
3. Visit `yourdomain.com/simple-install.php`
4. Fill in database details and admin credentials
5. Click "Install System"
6. Delete `simple-install.php` after installation

## Option 2: Manual Installation
1. Upload files to `public_html`
2. Import `install/database.sql` into your MySQL database
3. Edit `config.php` with your database credentials
4. Access admin panel at `yourdomain.com/admin/`

## Default Admin Login
- Username: admin
- Password: admin123
- Change these after first login!

## Support
Contact seller through CodeCanyon for support.
EOF

# Package everything
../scripts/package-codecanyon.sh
