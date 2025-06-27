#!/usr/bin/env node

/**
 * MCP Server for Moritz's Learning Log
 * This server implements the Model Context Protocol (MCP) to allow Claude Desktop
 * to interact with Moritz's Kirby CMS learning log.
 */

import { Server } from '@modelcontextprotocol/sdk/server/index.js';
import { StdioServerTransport } from '@modelcontextprotocol/sdk/server/stdio.js';
import {
  CallToolRequestSchema,
  ErrorCode,
  ListToolsRequestSchema,
  McpError,
} from '@modelcontextprotocol/sdk/types.js';
import fs from 'fs/promises';
import path from 'path';
import yaml from 'js-yaml';

// Configuration
const CONFIG = {
  // Set to 'local' for local file access, 'remote' for API calls
  mode: process.env.MCP_MODE || 'local',
  
  // Local file path (when mode is 'local')
  localFilePath: '/Users/moritzkindler/Documents/5-portfolio-24/content/learning-log/today.txt',
  
  // Remote API settings (when mode is 'remote')
  remote: {
    apiUrl: 'https://moritzkindler.com/api/learning-log',
    authToken: 'hi-my-love-mattie'
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
            description: 'Add a new learning log entry to your Kirby CMS learning log',
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
            description: 'Get recent learning log entries from your Kirby CMS',
            inputSchema: {
              type: 'object',
              properties: {
                limit: {
                  type: 'number',
                  description: 'Number of recent entries to retrieve (default: 10)',
                  default: 10,
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
                text: `Successfully added learning entry: "${args.title}"\n\nMode: ${CONFIG.mode}\nTimestamp: ${result.timestamp}`,
              },
            ],
          };
        } else if (name === 'get_recent_learning') {
          const result = await this.getRecentLearning(args.limit || 10);
          return {
            content: [
              {
                type: 'text',
                text: `Recent Learning Log Entries (${result.entries.length} of ${result.total}):\n\n${result.entries
                  .map((entry, index) => {
                    const date = new Date(entry.post_date).toLocaleDateString('en-US', {
                      year: 'numeric',
                      month: 'short',
                      day: 'numeric',
                      hour: '2-digit',
                      minute: '2-digit'
                    });
                    return `${index + 1}. ${entry.title}\n   Date: ${date}`;
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
    console.log(`[DEBUG] Adding learning entry: "${title}" (mode: ${CONFIG.mode})`);
    
    if (CONFIG.mode === 'remote') {
      return await this.addLearningEntryRemote(title);
    } else {
      return await this.addLearningEntryLocal(title);
    }
  }

  async addLearningEntryLocal(title) {
    try {
      console.log(`[DEBUG] Reading local file: ${CONFIG.localFilePath}`);
      const content = await fs.readFile(CONFIG.localFilePath, 'utf8');
      console.log(`[DEBUG] File read successfully, length: ${content.length}`);
      
      // Split into sections
      const sections = content.split('----');
      if (sections.length < 2) {
        throw new Error('Invalid file format: missing YAML section');
      }

      // Parse the YAML frontmatter (second section)
      const yamlContent = sections[1].trim();
      console.log('[DEBUG] Parsing YAML content...');
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

      console.log(`[DEBUG] Added entry, total entries: ${data.Today.length}`);

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
      console.log('[DEBUG] Writing updated content to file...');
      await fs.writeFile(CONFIG.localFilePath, newContent, 'utf8');
      console.log('[DEBUG] File written successfully');

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
      console.log(`[DEBUG] Making remote API call to: ${CONFIG.remote.apiUrl}`);
      
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
      console.log('[DEBUG] Remote API response:', result);

      if (!result.success) {
        throw new Error(`API error: ${result.error || 'Unknown error'}`);
      }

      return {
        success: true,
        entry: result.entry,
        timestamp: result.entry.post_date
      };
    } catch (error) {
      console.error('[DEBUG] Error in addLearningEntryRemote:', error);
      throw error;
    }
  }

  async getRecentLearning(limit = 10) {
    console.log(`[DEBUG] Getting recent learning entries (limit: ${limit}, mode: ${CONFIG.mode})`);
    
    if (CONFIG.mode === 'remote') {
      return await this.getRecentLearningRemote(limit);
    } else {
      return await this.getRecentLearningLocal(limit);
    }
  }

  async getRecentLearningLocal(limit) {
    try {
      console.log(`[DEBUG] Reading local file: ${CONFIG.localFilePath}`);
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
      console.log(`[DEBUG] Found ${entries.length} total entries`);

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
      console.log(`[DEBUG] Making remote API call to: ${CONFIG.remote.apiUrl}`);
      
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
      console.log('[DEBUG] Remote API response:', result);

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