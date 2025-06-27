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
    'moritz.mcp.key' => 'hi-my-love-mattie',
    'moritz.mcp.endpoint' => 'http://localhost:3001/log',

    // Panel configuration
    'panel' =>[
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
          // Check authorization
          $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
          $expectedAuth = 'Bearer ' . option('moritz.mcp.key');
          
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
          
          try {
              $page = page('learning-log');
              if (!$page) {
                  throw new Exception('Learning log page not found');
              }
              
              $contentFile = $page->root() . '/today.txt';
              
              if ($input['action'] === 'add_entry') {
                  if (!isset($input['title'])) {
                      throw new Exception('Title is required');
                  }
                  
                  // Read current content
                  $content = file_get_contents($contentFile);
                  $sections = explode('----', $content);
                  
                  // Parse YAML frontmatter
                  $yamlContent = trim($sections[1]);
                  $data = yaml($yamlContent);
                  
                  // Add new entry
                  $newEntry = [
                      'title' => $input['title'],
                      'post_date' => date('Y-m-d H:i:s')
                  ];
                  
                  if (!isset($data['Today'])) {
                      $data['Today'] = [];
                  }
                  
                  $data['Today'][] = $newEntry;
                  
                  // Convert back to YAML
                  $newYaml = yaml($data, true);
                  $sections[1] = $newYaml;
                  
                  // Write back to file
                  file_put_contents($contentFile, implode('----', $sections));
                  
                  return new Kirby\Cms\Response([
                      'success' => true,
                      'message' => 'Entry added successfully',
                      'entry' => $newEntry
                  ], 'application/json');
                  
              } elseif ($input['action'] === 'get_recent') {
                  // Read current content
                  $content = file_get_contents($contentFile);
                  $sections = explode('----', $content);
                  
                  // Parse YAML frontmatter
                  $yamlContent = trim($sections[1]);
                  $data = yaml($yamlContent);
                  
                  $entries = $data['Today'] ?? [];
                  $limit = $input['limit'] ?? 10;
                  
                  // Get recent entries
                  $recentEntries = array_slice(array_reverse($entries), 0, $limit);
                  
                  return new Kirby\Cms\Response([
                      'success' => true,
                      'entries' => $recentEntries,
                      'total' => count($entries)
                  ], 'application/json');
              }
              
          } catch (Exception $e) {
              return new Kirby\Cms\Response([
                  'error' => $e->getMessage()
              ], 'application/json', 500);
          }
      }
    ]
  ]
];





