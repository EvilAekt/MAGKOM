#!/usr/bin/env node
/**
 * MAGKOM - Branch Ruleset Setup Script (for 'main' & 'test-2')
 * 
 * Script ini mengonfigurasi GitHub Repository Ruleset untuk memproteksi
 * branch 'main' dan branch 'test-2' dengan:
 * - Wajib lolos seluruh CI Status Checks (Quality Gate, Frontend, Gitleaks, Semgrep, Actionlint/Zizmor, In-Tree Tests, MySQL Concurrency).
 * - Blokir Force Push.
 * - Blokir Penghapusan Branch.
 * - Require branches to be up to date before merging.
 * 
 * Penggunaan:
 *   node scripts/setup-branch-protection.mjs [--mode active|evaluate]
 */

import { existsSync, readFileSync } from 'node:fs';
import { resolve } from 'node:path';

let token = process.env.GITHUB_TOKEN || process.env.GH_TOKEN;

if (!token) {
  const mcpPath = resolve(process.env.USERPROFILE || process.env.HOME || '', '.gemini/antigravity/mcp_config.json');
  if (existsSync(mcpPath)) {
    try {
      const mcp = JSON.parse(readFileSync(mcpPath, 'utf8'));
      token = mcp?.mcpServers?.github?.env?.GITHUB_PERSONAL_ACCESS_TOKEN;
    } catch {}
  }
}

if (!token) {
  console.error('[ERROR] Token GitHub tidak ditemukan di environment maupun MCP config!');
  process.exit(1);
}

const OWNER = 'EvilAekt';
const REPO = 'MAGKOM';
const RULESET_NAME = 'MAGKOM Quality Gate & Branch Protection (main & test-2)';

const enforcementMode = process.argv.includes('--mode') 
  ? process.argv[process.argv.indexOf('--mode') + 1] 
  : 'active'; // 'active' atau 'evaluate'

async function rest(endpoint, options = {}) {
  const url = endpoint.startsWith('https://') ? endpoint : `https://api.github.com${endpoint}`;
  const res = await fetch(url, {
    ...options,
    headers: {
      'Authorization': `Bearer ${token}`,
      'User-Agent': 'MAGKOM-DevSecOps',
      'Accept': 'application/vnd.github.v3+json',
      'Content-Type': 'application/json',
      ...(options.headers || {})
    }
  });
  if (!res.ok) {
    const text = await res.text();
    throw new Error(`REST Error ${res.status}: ${text}`);
  }
  return await res.json();
}

async function main() {
  console.log(`[INFO] Memeriksa ruleset pada ${OWNER}/${REPO}...`);
  const existingRulesets = await rest(`/repos/${OWNER}/${REPO}/rulesets`);
  const existing = existingRulesets.find(r => r.name === RULESET_NAME);

  const payload = {
    name: RULESET_NAME,
    target: 'branch',
    enforcement: enforcementMode,
    conditions: {
      ref_name: {
        include: [
          'refs/heads/main',
          'refs/heads/test-2'
        ],
        exclude: []
      }
    },
    rules: [
      {
        type: 'deletion'
      },
      {
        type: 'non_fast_forward'
      },
      {
        type: 'required_status_checks',
        parameters: {
          strict_required_status_checks_policy: true,
          required_status_checks: [
            { context: 'PHP 8.3 Quality Gate & Test Suite' },
            { context: 'Frontend Quality & Asset Compilation' },
            { context: 'Secret Scanning (Gitleaks)' },
            { context: 'PHP & Laravel Static Security Analysis (Semgrep)' },
            { context: 'Workflow Security & Linter Audit (actionlint & zizmor)' },
            { context: 'Laravel In-Tree Security Test Suites' },
            { context: 'MySQL 8 Database Concurrency & Quota Locking' }
          ]
        }
      }
    ]
  };

  if (existing) {
    console.log(`[FOUND] Ruleset sudah ada (ID: ${existing.id}). Memperbarui konfigurasi...`);
    const updated = await rest(`/repos/${OWNER}/${REPO}/rulesets/${existing.id}`, {
      method: 'PUT',
      body: JSON.stringify(payload)
    });
    console.log(`[SUCCESS] Ruleset diperbarui: ID ${updated.id}, Status: ${updated.enforcement}`);
  } else {
    console.log('[INFO] Membuat Ruleset baru untuk branch main & test-2...');
    const created = await rest(`/repos/${OWNER}/${REPO}/rulesets`, {
      method: 'POST',
      body: JSON.stringify(payload)
    });
    console.log(`[SUCCESS] Ruleset dibuat: ID ${created.id}, Status: ${created.enforcement}`);
  }

  console.log('Target branch terlindungi:');
  console.log(' - refs/heads/main');
  console.log(' - refs/heads/test-2');
  console.log('Aturan yang aktif:');
  console.log(' - Cegah penghapusan branch (deletion: blocked)');
  console.log(' - Cegah force push (non_fast_forward: blocked)');
  console.log(' - 7 Required CI Status Checks wajib Passed sebelum merge/push');
}

main().catch(err => {
  console.error('[FATAL]', err);
  process.exit(1);
});

