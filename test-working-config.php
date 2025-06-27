<?php

/**
 * Test config with proper Kirby route syntax
 */
return [
    'debug' => true,
    'home' => 'start',

    // Learning Log API key
    'moritz.learning.api.key' => 'learning-log-secret-key-2025',

    // Panel configuration
    'panel' => [
        'install' => true
    ],
    
    'routes' => [
        // Existing working routes
        [
            'pattern' => 'sitemap.xml',
            'action'  => function() {
                $pages = site()->pages()->index();
                $ignore = kirby()->option('sitemap.ignore', ['error']);
                $content = snippet('sitemap', compact('pages', 'ignore'), true);
                return new Kirby\Cms\Response($content, 'application/xml');
            }
        ],
        [
            'pattern' => 'sitemap',
            'action'  => function() {
                return go('sitemap.xml', 301);
            }
        ],
        
        // Simple test route (GET - should work)
        [
            'pattern' => 'api/test',
            'action' => function() {
                return new Kirby\Cms\Response([
                    'status' => 'success',
                    'message' => 'API test route works!',
                    'timestamp' => date('c')
                ], 'application/json');
            }
        ],
        
        // Learning Log API - POST method (the problematic one)
        [
            'pattern' => 'api/learning-log',
            'method' => 'POST',
            'action' => function() {
                // Check authorization
                $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
                $expectedAuth = 'Bearer ' . option('moritz.learning.api.key');
                
                if ($authHeader !== $expectedAuth) {
                    return new Kirby\Cms\Response([
                        'error' => 'Unauthorized'
                    ], 'application/json', 401);
                }
                
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (!$input || !isset($input['action'])) {
                    return new Kirby\Cms\Response([
                        'error' => 'Invalid request'
                    ], 'application/json', 400);
                }
                
                return new Kirby\Cms\Response([
                    'status' => 'success',
                    'message' => 'Learning Log API works!',
                    'action' => $input['action'],
                    'timestamp' => date('c')
                ], 'application/json');
            }
        ]
    ]
]; 