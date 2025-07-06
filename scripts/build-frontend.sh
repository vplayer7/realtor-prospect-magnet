
#!/bin/bash

echo "📦 Building React application..."
npm run build

# Check if build was successful
if [ ! -d "dist" ]; then
    echo "❌ Build failed - dist directory not found"
    echo "Make sure 'npm run build' completes successfully"
    exit 1
fi

echo "🔧 Fixing CSS and JS paths for cPanel compatibility..."

# Fix absolute paths in index.html to relative paths
if [ -f "dist/index.html" ]; then
    # Replace absolute paths with relative paths
    sed -i 's|href="/assets/|href="assets/|g' dist/index.html
    sed -i 's|src="/assets/|src="assets/|g' dist/index.html
    echo "✅ Fixed asset paths in index.html"
else
    echo "⚠️  Warning: dist/index.html not found"
fi

echo "✅ React build completed successfully"
