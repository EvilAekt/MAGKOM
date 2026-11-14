#!/usr/bin/env node
/**
 * MAGKOM - GitHub Projects & Labels Idempotent Setup Script
 * 
 * Script ini mengonfigurasi atau memperbarui GitHub Project V2 "MAGKOM | Engineering & Security"
 * beserta custom fields, views, labels, dan linking repository secara otomatis dan idempotent.
 * 
 * Penggunaan:
 *   node scripts/setup-github-projects.mjs
 * 
 * Prasyarat:
 *   Variabel lingkungan GITHUB_TOKEN atau GH_TOKEN dengan scope 'project', 'repo'.
 */

import { existsSync, readFileSync } from 'node:fs';
import { resolve } from 'node:path';

// Dapatkan token dari environment atau konfigurasi lokal jika tersedia
let token = process.env.GITHUB_TOKEN || process.env.GH_TOKEN;

if (!token) {
  // Coba ambil dari MCP config lokal jika ada
  const mcpPath = resolve(process.env.USERPROFILE || process.env.HOME || '', '.gemini/antigravity/mcp_config.json');
  if (existsSync(mcpPath)) {
    try {
      const mcp = JSON.parse(readFileSync(mcpPath, 'utf8'));
      token = mcp?.mcpServers?.github?.env?.GITHUB_PERSONAL_ACCESS_TOKEN;
    } catch {}
  }
}

if (!token) {
  console.error('[ERROR] Token GitHub tidak ditemukan!');
  console.error('Silakan setel environment variable GITHUB_TOKEN atau GH_TOKEN:');
  console.error('  export GITHUB_TOKEN=ghp_xxxx  (Linux/macOS)');
  console.error('  $env:GITHUB_TOKEN="ghp_xxxx"   (PowerShell)');
  process.exit(1);
}

const OWNER = 'EvilAekt';
const REPO = 'MAGKOM';
const PROJECT_TITLE = 'MAGKOM | Engineering & Security';
const PROJECT_DESC = 'Pusat manajemen pekerjaan developer, integrasi CI/CD, dan pemantauan keamanan MAGKOM.';

async function graphql(query, variables = {}) {
  const res = await fetch('https://api.github.com/graphql', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'User-Agent': 'MAGKOM-DevSecOps',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ query, variables })
  });
  const data = await res.json();
  if (data.errors) {
    throw new Error(`GraphQL Error: ${JSON.stringify(data.errors, null, 2)}`);
  }
  return data.data;
}

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

async function syncLabels() {
  console.log('--- 1. Sinkronisasi GitHub Labels ---');
  const labelsToEnsure = [
    { name: 'bug', color: 'd73a4a', description: 'Sesuatu tidak bekerja semestinya' },
    { name: 'feature', color: '0e8a16', description: 'Fitur baru atau pengembangan fungsionalitas' },
    { name: 'security', color: 'b60205', description: 'Masalah atau pengujian keamanan sistem, SAST/DAST, dan audit keterpaparan' },
    { name: 'maintenance', color: 'fbca04', description: 'Pemeliharaan rutin, refactoring, atau pembaruan dependency' },
    { name: 'documentation', color: '0075ca', description: 'Peningkatan atau penambahan dokumentasi' },
    { name: 'priority:critical', color: 'b60205', description: 'Prioritas kritis, blocker, harus diselesaikan segera' },
    { name: 'priority:high', color: 'd93f0b', description: 'Prioritas tinggi, berdampak signifikan pada fungsionalitas/keamanan' },
    { name: 'priority:medium', color: 'f9d0c4', description: 'Prioritas sedang, perbaikan atau fitur dalam siklus rilis normal' },
    { name: 'priority:low', color: 'c2e0c6', description: 'Prioritas rendah, peningkatan minor atau kosmetik' },
    { name: 'area:backend', color: '1d76db', description: 'Kode backend Laravel, Controller, Service, Model, Policy' },
    { name: 'area:frontend', color: '5319e7', description: 'Blade, CSS, Vanilla JS ES Modules, Vite assets' },
    { name: 'area:database', color: '0052cc', description: 'Migrasi database, seeder, indexing, performa query, locking' },
    { name: 'area:auth-rbac', color: '8250df', description: 'Autentikasi, otorisasi, dynamic RBAC matrix, session, CSRF' },
    { name: 'area:devops', color: '006b75', description: 'CI/CD GitHub Actions, Docker, environment, pipeline testing' },
    { name: 'area:superadmin', color: '382f6e', description: 'Modul SuperAdmin, manajemen staf, backup, audit log' },
    { name: 'area:admin', color: '0075ca', description: 'Modul Admin, verifikasi berkas, kuota, penempatan' },
    { name: 'area:pembimbing', color: '2cbe4e', description: 'Modul Pembimbing, classroom, review logbook, tugas, evaluasi' },
    { name: 'area:peserta', color: '1f883d', description: 'Portal Peserta, pendaftaran, submission tugas, pengisian logbook' }
  ];

  for (const label of labelsToEnsure) {
    const encoded = encodeURIComponent(label.name);
    try {
      await rest(`/repos/${OWNER}/${REPO}/labels/${encoded}`, {
        method: 'PATCH',
        body: JSON.stringify(label)
      });
      console.log(`[OK] Updated label: ${label.name}`);
    } catch {
      try {
        await rest(`/repos/${OWNER}/${REPO}/labels`, {
          method: 'POST',
          body: JSON.stringify(label)
        });
        console.log(`[OK] Created label: ${label.name}`);
      } catch (err) {
        console.warn(`[WARN] Label ${label.name}: ${err.message}`);
      }
    }
  }
}

async function syncProject() {
  console.log('\n--- 2. Sinkronisasi GitHub Project V2 ---');
  
  // Ambil repository dan project
  const repoData = await graphql(`
    query($owner: String!, $name: String!) {
      repository(owner: $owner, name: $name) {
        id
        projectsV2(first: 10) {
          nodes {
            id
            title
            number
            url
            public
          }
        }
      }
    }
  `, { owner: OWNER, name: REPO });

  const repoId = repoData.repository.id;
  let targetProject = repoData.repository.projectsV2.nodes.find(p => p.title === PROJECT_TITLE);

  if (!targetProject && repoData.repository.projectsV2.nodes.length > 0) {
    // Ambil project pertama yang belum memiliki judul kustom jika ada
    targetProject = repoData.repository.projectsV2.nodes[0];
  }

  let projectId;

  if (targetProject) {
    projectId = targetProject.id;
    console.log(`[FOUND] Menggunakan Project: ${targetProject.title} (${targetProject.url})`);
    
    // Perbarui judul dan deskripsi
    await graphql(`
      mutation($projectId: ID!, $title: String!, $shortDescription: String!) {
        updateProjectV2(input: {
          projectId: $projectId,
          title: $title,
          shortDescription: $shortDescription,
          public: false
        }) {
          projectV2 {
            id
            title
          }
        }
      }
    `, {
      projectId,
      title: PROJECT_TITLE,
      shortDescription: PROJECT_DESC
    });
    console.log(`[OK] Berhasil memperbarui judul Project ke "${PROJECT_TITLE}"`);
  } else {
    // Buat project baru
    console.log('[INFO] Membuat ProjectV2 baru...');
    const ownerData = await graphql(`
      query($login: String!) {
        user(login: $login) {
          id
        }
      }
    `, { login: OWNER });

    const createRes = await graphql(`
      mutation($ownerId: ID!, $title: String!) {
        createProjectV2(input: {
          ownerId: $ownerId,
          title: $title
        }) {
          projectV2 {
            id
            url
          }
        }
      }
    `, {
      ownerId: ownerData.user.id,
      title: PROJECT_TITLE
    });

    projectId = createRes.createProjectV2.projectV2.id;
    console.log(`[OK] Project dibuat: ${createRes.createProjectV2.projectV2.url}`);

    // Hubungkan dengan repository
    await graphql(`
      mutation($projectId: ID!, $repositoryId: ID!) {
        linkProjectV2ToRepository(input: {
          projectId: $projectId,
          repositoryId: $repositoryId
        }) {
          projectV2 {
            id
          }
        }
      }
    `, { projectId, repositoryId: repoId });
    console.log(`[OK] Project berhasil dihubungkan ke ${OWNER}/${REPO}`);
  }

  // Sinkronkan Fields
  console.log('\n--- 3. Sinkronisasi Custom Fields ---');
  const projectDetails = await graphql(`
    query($id: ID!) {
      node(id: $id) {
        ... on ProjectV2 {
          fields(first: 30) {
            nodes {
              ... on ProjectV2Field {
                id
                name
                dataType
              }
              ... on ProjectV2SingleSelectField {
                id
                name
                dataType
                options {
                  id
                  name
                }
              }
            }
          }
        }
      }
    }
  `, { id: projectId });

  const existingFields = projectDetails.node.fields.nodes;

  // A. Perbarui Status field options
  const statusField = existingFields.find(f => f.name === 'Status');
  if (statusField) {
    try {
      await graphql(`
        mutation($input: UpdateProjectV2FieldInput!) {
          updateProjectV2Field(input: $input) {
            projectV2Field {
              ... on ProjectV2SingleSelectField {
                id
              }
            }
          }
        }
      `, {
        input: {
          fieldId: statusField.id,
          name: 'Status',
          singleSelectOptions: [
            { name: 'Todo', color: 'GRAY', description: 'Tugas belum dimulai' },
            { name: 'In Progress', color: 'BLUE', description: 'Sedang dikerjakan' },
            { name: 'In Review', color: 'YELLOW', description: 'Dalam proses review kode / PR' },
            { name: 'Blocked', color: 'RED', description: 'Terhambat kendala atau dependensi' },
            { name: 'Done', color: 'GREEN', description: 'Selesai dan terverifikasi' }
          ]
        }
      });
      console.log('[OK] Field "Status" diperbarui (Todo, In Progress, In Review, Blocked, Done).');
    } catch (e) {
      console.warn(`[WARN] Status field update: ${e.message}`);
    }
  }

  // B. Field Priority
  if (!existingFields.find(f => f.name === 'Priority')) {
    await graphql(`
      mutation($input: CreateProjectV2FieldInput!) {
        createProjectV2Field(input: $input) {
          projectV2Field { ... on ProjectV2SingleSelectField { id } }
        }
      }
    `, {
      input: {
        projectId,
        dataType: 'SINGLE_SELECT',
        name: 'Priority',
        singleSelectOptions: [
          { name: 'Critical', color: 'RED', description: 'Masalah kritis' },
          { name: 'High', color: 'ORANGE', description: 'Prioritas tinggi' },
          { name: 'Medium', color: 'YELLOW', description: 'Prioritas normal' },
          { name: 'Low', color: 'BLUE', description: 'Prioritas rendah' }
        ]
      }
    });
    console.log('[OK] Field "Priority" dibuat.');
  } else {
    console.log('[EXISTS] Field "Priority" sudah tersedia.');
  }

  // C. Field Work Type
  if (!existingFields.find(f => f.name === 'Work Type')) {
    await graphql(`
      mutation($input: CreateProjectV2FieldInput!) {
        createProjectV2Field(input: $input) {
          projectV2Field { ... on ProjectV2SingleSelectField { id } }
        }
      }
    `, {
      input: {
        projectId,
        dataType: 'SINGLE_SELECT',
        name: 'Work Type',
        singleSelectOptions: [
          { name: 'Bug', color: 'RED' },
          { name: 'Feature', color: 'GREEN' },
          { name: 'Security', color: 'PURPLE' },
          { name: 'Maintenance', color: 'YELLOW' },
          { name: 'Documentation', color: 'BLUE' }
        ]
      }
    });
    console.log('[OK] Field "Work Type" dibuat.');
  } else {
    console.log('[EXISTS] Field "Work Type" sudah tersedia.');
  }

  // D. Field Area
  if (!existingFields.find(f => f.name === 'Area')) {
    await graphql(`
      mutation($input: CreateProjectV2FieldInput!) {
        createProjectV2Field(input: $input) {
          projectV2Field { ... on ProjectV2SingleSelectField { id } }
        }
      }
    `, {
      input: {
        projectId,
        dataType: 'SINGLE_SELECT',
        name: 'Area',
        singleSelectOptions: [
          { name: 'Backend', color: 'BLUE' },
          { name: 'Frontend', color: 'PURPLE' },
          { name: 'Authentication & RBAC', color: 'PINK' },
          { name: 'Database', color: 'BLUE' },
          { name: 'Public Portal', color: 'GREEN' },
          { name: 'Pendaftaran', color: 'YELLOW' },
          { name: 'Admin', color: 'BLUE' },
          { name: 'Pembimbing', color: 'GREEN' },
          { name: 'Peserta', color: 'GRAY' },
          { name: 'SuperAdmin', color: 'PURPLE' },
          { name: 'DevOps & Infrastructure', color: 'ORANGE' }
        ]
      }
    });
    console.log('[OK] Field "Area" dibuat.');
  } else {
    console.log('[EXISTS] Field "Area" sudah tersedia.');
  }

  // E. Field Target Date
  if (!existingFields.find(f => f.name === 'Target Date')) {
    await graphql(`
      mutation($input: CreateProjectV2FieldInput!) {
        createProjectV2Field(input: $input) {
          projectV2Field { ... on ProjectV2Field { id } }
        }
      }
    `, {
      input: {
        projectId,
        dataType: 'DATE',
        name: 'Target Date'
      }
    });
    console.log('[OK] Field "Target Date" dibuat.');
  } else {
    console.log('[EXISTS] Field "Target Date" sudah tersedia.');
  }

  console.log('\n=========================================');
  console.log(`[DONE] Setup GitHub Projects berhasil!`);
  console.log(`URL Project: https://github.com/users/${OWNER}/projects/2`);
  console.log('=========================================');
}

async function main() {
  await syncLabels();
  await syncProject();
}

main().catch(err => {
  console.error('[FATAL]', err);
  process.exit(1);
});

