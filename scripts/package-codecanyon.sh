
#!/bin/bash

echo "📦 Creating final ZIP package..."

# Show what we've created
echo ""
echo "📋 Files created in codecanyon-build:"
ls -la

echo ""
echo "📊 Directory sizes:"
du -sh *

# Create the final ZIP package
cd ..
zip -r "real-estate-lead-capture-v2.0.zip" codecanyon-build/ -x "codecanyon-build/.git/*" "codecanyon-build/node_modules/*"

# Verify ZIP was created
if [ -f "real-estate-lead-capture-v2.0.zip" ]; then
    echo "✅ CodeCanyon package created: real-estate-lead-capture-v2.0.zip"
    echo "📦 Package size: $(du -sh real-estate-lead-capture-v2.0.zip | cut -f1)"
    
    echo ""
    echo "📋 Package Contents:"
    echo "   ✓ Built React frontend (production-ready)"
    echo "   ✓ PHP backend with lead capture"
    echo "   ✓ Admin panel for lead management"
    echo "   ✓ Database schema"
    echo "   ✓ cPanel .htaccess configuration"
    echo "   ✓ Compatibility test script"
    echo "   ✓ Complete installation documentation"
    echo ""
    echo "🎉 Ready for CodeCanyon submission!"
else
    echo "❌ Failed to create ZIP package"
    exit 1
fi
