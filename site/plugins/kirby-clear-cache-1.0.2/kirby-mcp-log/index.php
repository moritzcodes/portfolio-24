<?php
use Kirby\Http\Remote;

// Helper function to send logs to MCP server
function sendToMCP($data) {
    $endpoint = option('moritz.mcp.endpoint');
    $apiKey = option('moritz.mcp.key');
    
    if (!$endpoint || !$apiKey) {
        return; // Configuration missing
    }
    
    try {
        Remote::post($endpoint, [
            'headers' => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey
            ],
            'data' => json_encode($data),
            'timeout' => 2 // Seconds
        ]);
    } catch (Exception $e) {
        // Silently fail to avoid recursive errors
    }
}

Kirby::plugin('moritz/kirby-mcp-log', [
    'hooks' => [
        // Hook into system exceptions which are logged
        'system.exception' => function ($exception, $isLogged) {
            // Only proceed if the exception is being logged
            if ($isLogged === false) {
                return;
            }
            
            $data = [
                'message' => $exception->getMessage(),
                'level' => 'error',
                'timestamp' => date('c'),
                'source' => 'kirby-cms-exception',
                'exception_type' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => substr($exception->getTraceAsString(), 0, 1000) // Limit trace size
            ];

            sendToMCP($data);
        },
        
        // Hook into user login events
        'user.login:after' => function ($user, $session) {
            $data = [
                'message' => 'User logged in: ' . $user->email(),
                'level' => 'info',
                'timestamp' => date('c'),
                'source' => 'kirby-cms-auth',
                'user_id' => $user->id(),
                'user_email' => $user->email()
            ];
            
            sendToMCP($data);
        },
        
        // Hook into user login failures
        'user.login:failed' => function ($email) {
            $data = [
                'message' => 'Failed login attempt for: ' . $email,
                'level' => 'warning',
                'timestamp' => date('c'),
                'source' => 'kirby-cms-auth',
                'email' => $email
            ];
            
            sendToMCP($data);
        },
        
        // Hook into page creation
        'page.create:after' => function ($page) {
            $data = [
                'message' => 'Page created: ' . $page->title(),
                'level' => 'info',
                'timestamp' => date('c'),
                'source' => 'kirby-cms-content',
                'page_id' => $page->id(),
                'page_title' => $page->title()->value()
            ];
            
            sendToMCP($data);
        }
    ]
]);
