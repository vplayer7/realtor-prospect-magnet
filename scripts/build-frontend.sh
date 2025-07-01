
#!/bin/bash

echo "📦 Building React application..."
npm run build

# Check if build was successful
if [ ! -d "dist" ]; then
    echo "❌ Build failed - dist directory not found"
    echo "Make sure 'npm run build' completes successfully"
    exit 1
fi

echo "✅ React build completed successfully"
