<?php

/**
 * Simple test config to debug API route issues
 */
return [
    'debug' => true,
    'home' => 'start',

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
        
        // Simple test route first
        [
            'pattern' => 'api/test',
            'action' => function() {
                return new Kirby\Cms\Response([
                    'status' => 'success',
                    'message' => 'Test route works!'
                ], 'application/json');
            }
        ],
        
        // Learning Log API with explicit method
        [
            'pattern' => 'api/learning-log',
            'method' => 'POST',
            'action' => function() {
                // Simple response first
                return new Kirby\Cms\Response([
                    'status' => 'success',
                    'message' => 'Learning Log API route works!',
                    'method' => 'POST'
                ], 'application/json');
            }
        ]
    ]
]; 