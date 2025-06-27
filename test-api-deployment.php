<?php
// Simple test file to check if API routes are working
// Upload this to your live site root and access it via: https://moritzkindler.com/test-api-deployment.php

echo "<h1>🧪 API Deployment Test</h1>";
echo "<p>Testing if Kirby routes are working...</p>";

// Test 1: Check if we can access Kirby
try {
    // Try to get the site object
    if (function_exists('site')) {
        $site = site();
        echo "<p>✅ Kirby is loaded successfully</p>";
        echo "<p>Site title: " . $site->title() . "</p>";
    } else {
        echo "<p>❌ Kirby is not loaded</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error loading Kirby: " . $e->getMessage() . "</p>";
}

// Test 2: Check if config options are loaded
try {
    if (function_exists('option')) {
        $mcpKey = option('moritz.mcp.key');
        if ($mcpKey) {
            echo "<p>✅ MCP config found (key: " . substr($mcpKey, 0, 5) . "...)</p>";
        } else {
            echo "<p>❌ MCP config not found</p>";
        }
    } else {
        echo "<p>❌ option() function not available</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error checking config: " . $e->getMessage() . "</p>";
}

// Test 3: Try to manually call the learning log API logic
echo "<hr>";
echo "<h2>🔍 Direct API Test</h2>";

try {
    // Simulate the API call
    $input = [
        'action' => 'get_recent',
        'limit' => 5
    ];
    
    echo "<p>Testing learning log access...</p>";
    
    if (function_exists('page')) {
        $page = page('learning-log');
        if ($page) {
            echo "<p>✅ Learning log page found</p>";
            $contentFile = $page->root() . '/today.txt';
            
            if (file_exists($contentFile)) {
                echo "<p>✅ Learning log file exists: " . $contentFile . "</p>";
                
                // Try to read the file
                $content = file_get_contents($contentFile);
                $sections = explode('----', $content);
                
                if (count($sections) >= 2) {
                    echo "<p>✅ File has correct structure (" . count($sections) . " sections)</p>";
                    
                    // Try to parse YAML
                    if (function_exists('yaml')) {
                        $yamlContent = trim($sections[1]);
                        $data = yaml($yamlContent);
                        
                        if (isset($data['Today'])) {
                            $entryCount = count($data['Today']);
                            echo "<p>✅ Found {$entryCount} learning entries</p>";
                            
                            // Show recent entries
                            $recentEntries = array_slice(array_reverse($data['Today']), 0, 3);
                            echo "<p><strong>Recent entries:</strong></p>";
                            echo "<ul>";
                            foreach ($recentEntries as $entry) {
                                echo "<li>" . htmlspecialchars($entry['title']) . " (" . $entry['post_date'] . ")</li>";
                            }
                            echo "</ul>";
                        } else {
                            echo "<p>❌ No 'Today' section found in YAML</p>";
                        }
                    } else {
                        echo "<p>❌ yaml() function not available</p>";
                    }
                } else {
                    echo "<p>❌ File structure incorrect</p>";
                }
            } else {
                echo "<p>❌ Learning log file not found: " . $contentFile . "</p>";
            }
        } else {
            echo "<p>❌ Learning log page not found</p>";
        }
    } else {
        echo "<p>❌ page() function not available</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error in direct test: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><em>Delete this file after testing: rm test-api-deployment.php</em></p>";
?> 