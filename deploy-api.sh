#!/bin/bash

# Learning Log MCP API Deployment Script
# This script helps you deploy the API endpoint to your live site

echo "🚀 Learning Log MCP API Deployment"
echo "=================================="
echo ""

# Check if config file exists
if [ ! -f "site/config/config.php" ]; then
    echo "❌ Error: site/config/config.php not found!"
    echo "   Make sure you're running this from the project root."
    exit 1
fi

echo "✅ Found site/config/config.php"
echo ""

# Show deployment options
echo "📤 Deployment Options:"
echo ""
echo "1. 📋 Show file contents (for manual copy/paste)"
echo "2. 💾 Save as backup for manual upload"
echo "3. 🔧 Show rsync command (if you have SSH access)"
echo "4. ℹ️  Show deployment instructions"
echo ""

read -p "Choose an option (1-4): " choice

case $choice in
    1)
        echo ""
        echo "📋 File contents to copy:"
        echo "========================="
        echo ""
        cat site/config/config.php
        echo ""
        echo "📍 Upload this to: your-live-site/site/config/config.php"
        ;;
    2)
        backup_file="config-backup-$(date +%Y%m%d-%H%M%S).php"
        cp site/config/config.php "$backup_file"
        echo ""
        echo "💾 Backup saved as: $backup_file"
        echo "📍 Upload this file to: your-live-site/site/config/config.php"
        ;;
    3)
        echo ""
        echo "🔧 rsync command (replace with your details):"
        echo "=============================================="
        echo ""
        echo "rsync -avz site/config/config.php username@moritzkindler.com:/path/to/your/site/site/config/"
        echo ""
        echo "📝 Replace 'username' and '/path/to/your/site' with your actual details"
        ;;
    4)
        echo ""
        echo "📖 Deployment Instructions:"
        echo "==========================="
        echo ""
        echo "1. 📁 Access your live site's file manager or FTP client"
        echo "2. 📂 Navigate to: site/config/"
        echo "3. 📤 Upload: site/config/config.php"
        echo "4. ✅ Overwrite the existing config.php file"
        echo "5. 🔄 Test the API:"
        echo ""
        echo "   curl -X POST https://moritzkindler.com/api/learning-log \\"
        echo "     -H \"Content-Type: application/json\" \\"
        echo "     -H \"Authorization: Bearer hi-my-love-mattie\" \\"
        echo "     -d '{\"action\": \"get_recent\", \"limit\": 5}'"
        echo ""
        echo "6. 🔄 Restart Claude Desktop"
        echo "7. 🎉 Test with: \"Add a learning log entry: Testing live API\""
        ;;
    *)
        echo ""
        echo "❌ Invalid option. Please run the script again."
        exit 1
        ;;
esac

echo ""
echo "🎯 Next Steps:"
echo "1. Deploy the config.php file to your live site"
echo "2. Restart Claude Desktop"
echo "3. Test both local and live modes!"
echo ""
echo "📚 Full documentation: LEARNING-LOG-MCP-SETUP.md" 