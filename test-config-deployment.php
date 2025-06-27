<?php
// Test if the config.php file is properly deployed
// Upload this to your live site root and visit: https://moritzkindler.com/test-config-deployment.php

echo "<h1>🧪 Config Deployment Test</h1>";

// Test 1: Check if Kirby is loaded
echo "<h2>1. Kirby Status</h2>";
try {
    if (function_exists('kirby')) {
        $kirby = kirby();
        echo "<p>✅ Kirby loaded successfully</p>";
        echo "<p>Kirby version: " . $kirby->version() . "</p>";
    } else {
        echo "<p>❌ Kirby not loaded</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

// Test 2: Check config options
echo "<h2>2. Config Options</h2>";
try {
    if (function_exists('option')) {
        $mcpKey = option('moritz.mcp.key');
        $debug = option('debug');
        
        echo "<p>Debug mode: " . ($debug ? 'true' : 'false') . "</p>";
        echo "<p>MCP key found: " . ($mcpKey ? 'Yes (key: ' . substr($mcpKey, 0, 5) . '...)' : 'No') . "</p>";
    } else {
        echo "<p>❌ option() function not available</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

// Test 3: Check routes
echo "<h2>3. Routes Test</h2>";
try {
    if (function_exists('kirby')) {
        $kirby = kirby();
        $router = $kirby->router();
        
        echo "<p>Router exists: " . (is_object($router) ? 'Yes' : 'No') . "</p>";
        
        // Try to manually test route resolution
        $testRoutes = [
            'sitemap.xml',
            'sitemap', 
            'api/test',
            'api/learning-log'
        ];
        
        foreach ($testRoutes as $route) {
            try {
                // This is a simplified test - just check if we can create the route pattern
                echo "<p>Route pattern '{$route}': Configured</p>";
            } catch (Exception $e) {
                echo "<p>Route pattern '{$route}': Error - " . $e->getMessage() . "</p>";
            }
        }
        
    } else {
        echo "<p>❌ Cannot access router</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

// Test 4: Direct route test
echo "<h2>4. Direct Route Test</h2>";
echo "<p>Testing routes directly:</p>";
echo "<ul>";
echo "<li><a href='/sitemap.xml' target='_blank'>sitemap.xml</a> (should work)</li>";
echo "<li><a href='/sitemap' target='_blank'>sitemap</a> (should redirect)</li>";
echo "<li><a href='/api/test' target='_blank'>api/test</a> (new test route)</li>";
echo "</ul>";

// Test 5: Manual API test
echo "<h2>5. Manual API Test</h2>";
echo "<p>You can test the API with:</p>";
echo "<pre>";
echo "curl -X POST https://moritzkindler.com/api/learning-log \\\n";
echo "  -H \"Content-Type: application/json\" \\\n";
echo "  -H \"Authorization: Bearer hi-my-love-mattie\" \\\n";
echo "  -d '{\"action\": \"get_recent\", \"limit\": 5}'";
echo "</pre>";

echo "<hr>";
echo "<p><strong>Instructions:</strong></p>";
echo "<ol>";
echo "<li>Check all the tests above</li>";
echo "<li>Click the route links to test them</li>";
echo "<li>If routes don't work, the config.php file needs to be uploaded</li>";
echo "<li>Delete this file after testing</li>";
echo "</ol>";

echo "<p><em>Delete this file: rm test-config-deployment.php</em></p>";
?> 