<?php

/**
 * The config file is optional. It accepts a return array with config options
 * Note: Never include more than one return statement, all options go within this single return array
 * In this example, we set debugging to true, so that errors are displayed onscreen. 
 * This setting must be set to false in production.
 * All config options: https://getkirby.com/docs/reference/system/options
 */
return [
    'debug' => true,
    'home' => 'start',

    // MCP server configuration
    // Use environment variables for sensitive data
    'moritz.mcp.key' => $_ENV['MCP_LOG_KEY'] ?? 'hi-my-love-mattie',
    'moritz.mcp.endpoint' => $_ENV['MCP_LOG_ENDPOINT'] ?? 'http://localhost:3001/log',
    
    // Learning Log API key (separate from MCP logging)
    'moritz.learning.api.key' => $_ENV['LEARNING_API_KEY'] ?? 'learning-log-secret-key-2025',

    // Panel configuration
    'panel' => [
        'install' => true
    ],
    'routes' => [
    [
      'pattern' => 'sitemap.xml',
      'action'  => function() {
          $pages = site()->pages()->index();

          // fetch the pages to ignore from the config settings,
          // if nothing is set, we ignore the error page
          $ignore = kirby()->option('sitemap.ignore', ['error']);

          $content = snippet('sitemap', compact('pages', 'ignore'), true);

          // return response with correct header type
          return new Kirby\Cms\Response($content, 'application/xml');
      }
    ],
    [
      'pattern' => 'sitemap',
      'action'  => function() {
        return go('sitemap.xml', 301);
      }
    ],
    [
      'pattern' => 'api/learning-log',
      'method' => 'POST',
      'action' => function() {
          // Check authorization using the dedicated Learning API key
          $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
          $expectedAuth = 'Bearer ' . option('moritz.learning.api.key');
          
          if ($authHeader !== $expectedAuth) {
              return new Kirby\Cms\Response([
                  'error' => 'Unauthorized',
                  'code' => 401
              ], 'application/json', 401);
          }
          
          $input = json_decode(file_get_contents('php://input'), true);
          
          if (!$input || !isset($input['action'])) {
              return new Kirby\Cms\Response([
                  'error' => 'Invalid request - action required',
                  'code' => 400
              ], 'application/json', 400);
          }
          
          try {
              $page = page('learning-log');
              if (!$page) {
                  throw new Exception('Learning log page not found');
              }
              
              $contentFile = $page->root() . '/today.txt';
              
              if (!file_exists($contentFile)) {
                  throw new Exception('Learning log file not found: ' . $contentFile);
              }
              
              if ($input['action'] === 'add_entry') {
                  if (!isset($input['title']) || empty(trim($input['title']))) {
                      throw new Exception('Title is required and cannot be empty');
                  }
                  
                  // Read current content
                  $content = file_get_contents($contentFile);
                  if ($content === false) {
                      throw new Exception('Could not read learning log file');
                  }
                  
                  $sections = explode('----', $content);
                  if (count($sections) < 2) {
                      throw new Exception('Invalid file format: missing YAML section');
                  }
                  
                  // Parse YAML frontmatter
                  $yamlContent = trim($sections[1]);
                  $data = yaml($yamlContent);
                  
                  if ($data === false) {
                      throw new Exception('Could not parse YAML content');
                  }
                  
                  // Add new entry
                  $newEntry = [
                      'title' => trim($input['title']),
                      'post_date' => date('Y-m-d H:i:s')
                  ];
                  
                  if (!isset($data['Today'])) {
                      $data['Today'] = [];
                  }
                  
                  $data['Today'][] = $newEntry;
                  
                  // Convert back to YAML
                  $newYaml = yaml($data, true);
                  if ($newYaml === false) {
                      throw new Exception('Could not convert data to YAML');
                  }
                  
                  $sections[1] = $newYaml;
                  
                  // Write back to file
                  $result = file_put_contents($contentFile, implode('----', $sections));
                  if ($result === false) {
                      throw new Exception('Could not write to learning log file');
                  }
                  
                  return new Kirby\Cms\Response([
                      'success' => true,
                      'message' => 'Entry added successfully',
                      'entry' => $newEntry,
                      'total_entries' => count($data['Today'])
                  ], 'application/json');
                  
              } elseif ($input['action'] === 'get_recent') {
                  // Read current content
                  $content = file_get_contents($contentFile);
                  if ($content === false) {
                      throw new Exception('Could not read learning log file');
                  }
                  
                  $sections = explode('----', $content);
                  if (count($sections) < 2) {
                      throw new Exception('Invalid file format: missing YAML section');
                  }
                  
                  // Parse YAML frontmatter
                  $yamlContent = trim($sections[1]);
                  $data = yaml($yamlContent);
                  
                  if ($data === false) {
                      throw new Exception('Could not parse YAML content');
                  }
                  
                  $entries = $data['Today'] ?? [];
                  $limit = min(max((int)($input['limit'] ?? 10), 1), 100); // Limit between 1-100
                  
                  // Get recent entries (newest first)
                  $recentEntries = array_slice(array_reverse($entries), 0, $limit);
                  
                  return new Kirby\Cms\Response([
                      'success' => true,
                      'entries' => $recentEntries,
                      'total' => count($entries),
                      'returned' => count($recentEntries)
                  ], 'application/json');
                  
              } else {
                  throw new Exception('Invalid action. Supported actions: add_entry, get_recent');
              }
              
          } catch (Exception $e) {
              return new Kirby\Cms\Response([
                  'error' => $e->getMessage(),
                  'code' => 500
              ], 'application/json', 500);
          }
      }
    ]
  ]
];





