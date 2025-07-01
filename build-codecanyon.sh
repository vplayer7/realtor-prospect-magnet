
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

# Create build directory and copy frontend files
mkdir -p codecanyon-build
cd codecanyon-build

echo "📂 Copying frontend files..."
cp -r ../dist/* .

# Verify files were copied
if [ ! -f "index.html" ]; then
    echo "❌ Frontend files not copied properly"
    echo "Contents of dist directory:"
    ls -la ../dist/
    exit 1
fi

echo "✅ Frontend files copied successfully"

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

# Package everything
../scripts/package-codecanyon.sh
