#!/usr/bin/env node

/**
 * MCP Server for Moritz's Learning Log
 * This server implements the Model Context Protocol (MCP) to allow Claude Desktop
 * to interact with Moritz's Kirby CMS learning log.
 */

const { Server } = require('@modelcontextprotocol/sdk/server/index.js');
const { StdioServerTransport } = require('@modelcontextprotocol/sdk/server/stdio.js');
const {
  CallToolRequestSchema,
  ErrorCode,
  ListToolsRequestSchema,
  McpError,
} = require('@modelcontextprotocol/sdk/types.js');
const fs = require('fs').promises;
const path = require('path');
const yaml = require('js-yaml');

// Configuration
const CONFIG = {
  // Set to 'local' for local file access, 'remote' for API calls
  mode: process.env.MCP_MODE || 'local',
  
  // Local file path (when mode is 'local')
  localFilePath: process.env.LOCAL_LEARNING_LOG_PATH || '/Users/moritzkindler/Documents/5-portfolio-24/content/learning-log/today.txt',
  
  // Remote API settings (when mode is 'remote')
  remote: {
    apiUrl: process.env.REMOTE_API_URL || 'https://moritzkindler.com/api/learning-log',
    authToken: process.env.LEARNING_API_KEY || 'learning-log-secret-key-2025'
  }
};

class LearningLogMCPServer {
  constructor() {
    this.server = new Server(
      {
        name: 'learning-log-server',
        version: '0.1.0',
      },
      {
        capabilities: {
          tools: {},
        },
      }
    );

    this.setupToolHandlers();
    
    // Error handling
    this.server.onerror = (error) => console.error('[MCP Error]', error);
    process.on('SIGINT', async () => {
      await this.server.close();
      process.exit(0);
    });
  }

  setupToolHandlers() {
    this.server.setRequestHandler(ListToolsRequestSchema, async () => {
      return {
        tools: [
          {
            name: 'add_learning_entry',
            description: `Add a new learning log entry to your Kirby CMS learning log (${CONFIG.mode} mode)`,
            inputSchema: {
              type: 'object',
              properties: {
                title: {
                  type: 'string',
                  description: 'The learning entry title/content',
                },
              },
              required: ['title'],
            },
          },
          {
            name: 'get_recent_learning',
            description: `Get recent learning log entries from your Kirby CMS (${CONFIG.mode} mode)`,
            inputSchema: {
              type: 'object',
              properties: {
                limit: {
                  type: 'number',
                  description: 'Number of recent entries to retrieve (default: 10, max: 50)',
                  default: 10,
                  minimum: 1,
                  maximum: 50,
                },
              },
              required: [],
            },
          },
        ],
      };
    });

    this.server.setRequestHandler(CallToolRequestSchema, async (request) => {
      const { name, arguments: args } = request.params;

      try {
        if (name === 'add_learning_entry') {
          const result = await this.addLearningEntry(args.title);
          return {
            content: [
              {
                type: 'text',
                text: `✅ Successfully added learning entry: "${args.title}"\n\n📍 Mode: ${CONFIG.mode}\n⏰ Timestamp: ${result.timestamp}\n📊 Total entries: ${result.total || 'unknown'}`,
              },
            ],
          };
        } else if (name === 'get_recent_learning') {
          const result = await this.getRecentLearning(args.limit || 10);
          return {
            content: [
              {
                type: 'text',
                text: `📚 Recent Learning Log Entries (${result.entries.length} of ${result.total})\n📍 Mode: ${CONFIG.mode}\n\n${result.entries
                  .map((entry, index) => {
                    const date = new Date(entry.post_date).toLocaleDateString('en-US', {
                      year: 'numeric',
                      month: 'short',
                      day: 'numeric',
                      hour: '2-digit',
                      minute: '2-digit'
                    });
                    return `${index + 1}. ${entry.title}\n   📅 ${date}`;
                  })
                  .join('\n\n')}`,
              },
            ],
          };
        } else {
          throw new McpError(
            ErrorCode.MethodNotFound,
            `Unknown tool: ${name}`
          );
        }
      } catch (error) {
        console.error(`Error in tool ${name}:`, error);
        throw new McpError(
          ErrorCode.InternalError,
          `Failed to execute ${name}: ${error.message}`
        );
      }
    });
  }

  async addLearningEntry(title) {
    console.error(`[DEBUG] Adding learning entry: "${title}" (mode: ${CONFIG.mode})`);
    
    if (CONFIG.mode === 'remote') {
      return await this.addLearningEntryRemote(title);
    } else {
      return await this.addLearningEntryLocal(title);
    }
  }

  async addLearningEntryLocal(title) {
    try {
      console.error(`[DEBUG] Reading local file: ${CONFIG.localFilePath}`);
      const content = await fs.readFile(CONFIG.localFilePath, 'utf8');
      console.error(`[DEBUG] File read successfully, length: ${content.length}`);
      
      // Split into sections
      const sections = content.split('----');
      if (sections.length < 2) {
        throw new Error('Invalid file format: missing YAML section');
      }

      // Parse the YAML frontmatter (second section)
      const yamlContent = sections[1].trim();
      console.error('[DEBUG] Parsing YAML content...');
      const data = yaml.load(yamlContent);

      // Create new entry
      const newEntry = {
        title: title,
        post_date: new Date().toISOString().replace('T', ' ').substring(0, 19)
      };

      // Add to Today array
      if (!data.Today) {
        data.Today = [];
      }
      data.Today.push(newEntry);

      console.error(`[DEBUG] Added entry, total entries: ${data.Today.length}`);

      // Convert back to YAML
      const newYaml = yaml.dump(data, {
        defaultFlowStyle: false,
        lineWidth: -1,
        quotingType: '"',
        forceQuotes: false
      });

      // Rebuild the content
      sections[1] = newYaml;
      const newContent = sections.join('----');

      // Write back to file
      console.error('[DEBUG] Writing updated content to file...');
      await fs.writeFile(CONFIG.localFilePath, newContent, 'utf8');
      console.error('[DEBUG] File written successfully');

      return {
        success: true,
        entry: newEntry,
        timestamp: newEntry.post_date,
        total: data.Today.length
      };
    } catch (error) {
      console.error('[DEBUG] Error in addLearningEntryLocal:', error);
      throw error;
    }
  }

  async addLearningEntryRemote(title) {
    try {
      console.error(`[DEBUG] Making remote API call to: ${CONFIG.remote.apiUrl}`);
      
      // Use dynamic import for fetch in Node.js
      const fetch = (await import('node-fetch')).default;
      
      const response = await fetch(CONFIG.remote.apiUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${CONFIG.remote.authToken}`
        },
        body: JSON.stringify({
          action: 'add_entry',
          title: title
        })
      });

      if (!response.ok) {
        const errorText = await response.text();
        throw new Error(`API request failed: ${response.status} ${response.statusText} - ${errorText}`);
      }

      const result = await response.json();
      console.error('[DEBUG] Remote API response:', result);

      if (!result.success) {
        throw new Error(`API error: ${result.error || 'Unknown error'}`);
      }

      return {
        success: true,
        entry: result.entry,
        timestamp: result.entry.post_date,
        total: result.total_entries
      };
    } catch (error) {
      console.error('[DEBUG] Error in addLearningEntryRemote:', error);
      throw error;
    }
  }

  async getRecentLearning(limit = 10) {
    console.error(`[DEBUG] Getting recent learning entries (limit: ${limit}, mode: ${CONFIG.mode})`);
    
    if (CONFIG.mode === 'remote') {
      return await this.getRecentLearningRemote(limit);
    } else {
      return await this.getRecentLearningLocal(limit);
    }
  }

  async getRecentLearningLocal(limit) {
    try {
      console.error(`[DEBUG] Reading local file: ${CONFIG.localFilePath}`);
      const content = await fs.readFile(CONFIG.localFilePath, 'utf8');
      
      // Split into sections
      const sections = content.split('----');
      if (sections.length < 2) {
        throw new Error('Invalid file format: missing YAML section');
      }

      // Parse the YAML frontmatter
      const yamlContent = sections[1].trim();
      const data = yaml.load(yamlContent);

      const entries = data.Today || [];
      console.error(`[DEBUG] Found ${entries.length} total entries`);

      // Get recent entries (reverse to get newest first)
      const recentEntries = entries.slice(-limit).reverse();

      return {
        success: true,
        entries: recentEntries,
        total: entries.length
      };
    } catch (error) {
      console.error('[DEBUG] Error in getRecentLearningLocal:', error);
      throw error;
    }
  }

  async getRecentLearningRemote(limit) {
    try {
      console.error(`[DEBUG] Making remote API call to: ${CONFIG.remote.apiUrl}`);
      
      // Use dynamic import for fetch in Node.js
      const fetch = (await import('node-fetch')).default;
      
      const response = await fetch(CONFIG.remote.apiUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${CONFIG.remote.authToken}`
        },
        body: JSON.stringify({
          action: 'get_recent',
          limit: limit
        })
      });

      if (!response.ok) {
        const errorText = await response.text();
        throw new Error(`API request failed: ${response.status} ${response.statusText} - ${errorText}`);
      }

      const result = await response.json();
      console.error('[DEBUG] Remote API response:', result);

      if (!result.success) {
        throw new Error(`API error: ${result.error || 'Unknown error'}`);
      }

      return {
        success: true,
        entries: result.entries,
        total: result.total
      };
    } catch (error) {
      console.error('[DEBUG] Error in getRecentLearningRemote:', error);
      throw error;
    }
  }

  async run() {
    const transport = new StdioServerTransport();
    await this.server.connect(transport);
    console.error(`Learning Log MCP server running on stdio (mode: ${CONFIG.mode})`);
  }
}

async function main() {
  const server = new LearningLogMCPServer();
  await server.run();
}

if (require.main === module) {
    main().catch(console.error);
}

module.exports = LearningLogMCPServer; 