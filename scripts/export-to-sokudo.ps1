<#
.SYNOPSIS
    Export and sync all Skills, MCP servers, and Memory/Context to Sokudo IDE.
.DESCRIPTION
    1. Skills: Syncs workspace skills, global skills, and plugin skills into ~/.agents/skills (Sokudo global)
       and creates a portable export folder at ./export-sokudo/skills.
    2. MCP: Syncs mcp_config.json to .agents/mcp_config.json and updates ~/.config/opencode/opencode.json.
    3. Memory: Exports codebase graph, GRAPH_REPORT.md, rules, and architecture docs into Sokudo's Wiki engine.
#>

[CmdletBinding()]
param (
    [switch]$SyncDirectly = $true,
    [switch]$CreateExportBundle = $true
)

$ErrorActionPreference = "Stop"

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "  SOKUDO EXPORT & SYNC TOOL (Antigravity) " -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan

$WorkspaceRoot = "g:\MAGKOM"
$UserHome = $env:USERPROFILE
$GlobalGeminiConfig = "$UserHome\.gemini\config"
$SokudoGlobalSkillsDir = "$UserHome\.agents\skills"
$OpenCodeConfigPath = "$UserHome\.config\opencode\opencode.json"
$ExportBundleDir = "$WorkspaceRoot\export-sokudo"

# -------------------------------------------------------------
# 1. EXPORT & SYNC SKILLS
# -------------------------------------------------------------
Write-Host "`n[1/3] Memproses Skills..." -ForegroundColor Yellow

$SkillSources = @(
    "$WorkspaceRoot\.agents\skills",
    "$GlobalGeminiConfig\skills"
)

# Tambahkan skills dari plugins
$PluginDir = "$GlobalGeminiConfig\plugins"
if (Test-Path $PluginDir) {
    Get-ChildItem -Path $PluginDir -Directory | ForEach-Object {
        $pSkill = Join-Path $_.FullName "skills"
        if (Test-Path $pSkill) {
            $SkillSources += $pSkill
        }
    }
}

if ($SyncDirectly) {
    if (-not (Test-Path $SokudoGlobalSkillsDir)) {
        New-Item -ItemType Directory -Path $SokudoGlobalSkillsDir -Force | Out-Null
    }
}

if ($CreateExportBundle) {
    $BundleSkillsDir = "$ExportBundleDir\skills"
    if (-not (Test-Path $BundleSkillsDir)) {
        New-Item -ItemType Directory -Path $BundleSkillsDir -Force | Out-Null
    }
}

$CopiedSkills = @{}
foreach ($src in $SkillSources) {
    if (Test-Path $src) {
        Get-ChildItem -Path $src -Directory | ForEach-Object {
            $skillName = $_.Name
            if (-not $CopiedSkills.ContainsKey($skillName)) {
                $CopiedSkills[$skillName] = $true
                
                # Sync ke ~/.agents/skills (Global Sokudo)
                if ($SyncDirectly) {
                    $targetPath = Join-Path $SokudoGlobalSkillsDir $skillName
                    Copy-Item -Path $_.FullName -Destination $targetPath -Recurse -Force
                }
                
                # Bundle ke export folder
                if ($CreateExportBundle) {
                    $bundleTarget = Join-Path $BundleSkillsDir $skillName
                    Copy-Item -Path $_.FullName -Destination $bundleTarget -Recurse -Force
                }
                
                Write-Host "  [+] Skill: $skillName" -ForegroundColor Green
            }
        }
    }
}

Write-Host "  -> Total $($CopiedSkills.Count) skills berhasil diproses." -ForegroundColor Cyan

# -------------------------------------------------------------
# 2. EXPORT & SYNC MCP (MODEL CONTEXT PROTOCOL)
# -------------------------------------------------------------
Write-Host "`n[2/3] Memproses MCP Config..." -ForegroundColor Yellow

$SourceMcpConfigPath = "$GlobalGeminiConfig\mcp_config.json"
if (Test-Path $SourceMcpConfigPath) {
    $mcpRaw = Get-Content -Path $SourceMcpConfigPath -Raw | ConvertFrom-Json
    
    # 2a. Buat workspace .agents/mcp_config.json
    $WorkspaceAgentsDir = "$WorkspaceRoot\.agents"
    if (-not (Test-Path $WorkspaceAgentsDir)) {
        New-Item -ItemType Directory -Path $WorkspaceAgentsDir -Force | Out-Null
    }
    $WorkspaceMcpTarget = "$WorkspaceAgentsDir\mcp_config.json"
    $mcpRaw | ConvertTo-Json -Depth 10 | Set-Content -Path $WorkspaceMcpTarget -Encoding UTF8
    Write-Host "  [+] Disimpan ke Workspace: $WorkspaceMcpTarget" -ForegroundColor Green
    
    # 2b. Buat standard .mcp.json di project root untuk kompatibilitas universal
    $UniversalMcpTarget = "$WorkspaceRoot\.mcp.json"
    $mcpRaw | ConvertTo-Json -Depth 10 | Set-Content -Path $UniversalMcpTarget -Encoding UTF8
    Write-Host "  [+] Disimpan ke Root: $UniversalMcpTarget" -ForegroundColor Green

    # 2c. Bundle ke export folder
    if ($CreateExportBundle) {
        $BundleMcpDir = "$ExportBundleDir\mcp"
        if (-not (Test-Path $BundleMcpDir)) {
            New-Item -ItemType Directory -Path $BundleMcpDir -Force | Out-Null
        }
        $mcpRaw | ConvertTo-Json -Depth 10 | Set-Content -Path "$BundleMcpDir\mcp_config.json" -Encoding UTF8
    }

    # 2d. Update config internal OpenCode Sokudo (~/.config/opencode/opencode.json)
    if ($SyncDirectly -and (Test-Path $OpenCodeConfigPath)) {
        try {
            $openCodeConfig = Get-Content -Path $OpenCodeConfigPath -Raw | ConvertFrom-Json
            if (-not $openCodeConfig.PSObject.Properties['mcp']) {
                $openCodeConfig | Add-Member -MemberType NoteProperty -Name "mcp" -Value ([PSCustomObject]@{})
            }

            # Backup file asli jika belum pernah di-backup
            $backupPath = "$OpenCodeConfigPath.bak"
            if (-not (Test-Path $backupPath)) {
                Copy-Item -Path $OpenCodeConfigPath -Destination $backupPath -Force
                Write-Host "  [i] Backup dibuat di: $backupPath" -ForegroundColor DarkGray
            }

            foreach ($prop in $mcpRaw.mcpServers.PSObject.Properties) {
                $serverName = $prop.Name
                $serverDef = $prop.Value

                # Lewati server proxy internal antigravityide yang khusus IDE
                if ($serverName -in @("notebooks", "visualization", "data-agent-kit")) {
                    continue
                }

                $newEntry = [PSCustomObject]@{
                    enabled = $true
                }

                if ($serverDef.PSObject.Properties['serverUrl']) {
                    $newEntry | Add-Member -MemberType NoteProperty -Name "type" -Value "remote"
                    $newEntry | Add-Member -MemberType NoteProperty -Name "url" -Value $serverDef.serverUrl
                    if ($serverDef.PSObject.Properties['headers']) {
                        $newEntry | Add-Member -MemberType NoteProperty -Name "headers" -Value $serverDef.headers
                    }
                } else {
                    $newEntry | Add-Member -MemberType NoteProperty -Name "type" -Value "local"
                    $cmdList = @($serverDef.command)
                    if ($serverDef.PSObject.Properties['args']) {
                        $cmdList += $serverDef.args
                    }
                    $newEntry | Add-Member -MemberType NoteProperty -Name "command" -Value $cmdList
                    if ($serverDef.PSObject.Properties['env'] -and $serverDef.env.PSObject.Properties.Count -gt 0) {
                        $newEntry | Add-Member -MemberType NoteProperty -Name "environment" -Value $serverDef.env
                    }
                }

                $openCodeConfig.mcp | Add-Member -MemberType NoteProperty -Name $serverName -Value $newEntry -Force
                Write-Host "  [+] Synced MCP to OpenCode/Sokudo: $serverName" -ForegroundColor Green
            }

            $openCodeConfig | ConvertTo-Json -Depth 10 | Set-Content -Path $OpenCodeConfigPath -Encoding UTF8
            Write-Host "  [+] OpenCode config updated: $OpenCodeConfigPath" -ForegroundColor Green
        } catch {
            Write-Warning "Gagal mengupdate OpenCode config otomatis: $_"
        }
    }
} else {
    Write-Warning "mcp_config.json tidak ditemukan di $SourceMcpConfigPath"
}

# -------------------------------------------------------------
# 3. EXPORT & SYNC MEMORY & KNOWLEDGE CONTEXT
# -------------------------------------------------------------
Write-Host "`n[3/3] Memproses Memory & Knowledge Graph..." -ForegroundColor Yellow

$DocsWikiDir = "$WorkspaceRoot\docs\wiki"
if (-not (Test-Path $DocsWikiDir)) {
    New-Item -ItemType Directory -Path $DocsWikiDir -Force | Out-Null
}

if ($CreateExportBundle) {
    $BundleMemoryDir = "$ExportBundleDir\memory"
    if (-not (Test-Path $BundleMemoryDir)) {
        New-Item -ItemType Directory -Path $BundleMemoryDir -Force | Out-Null
    }
}

# 3a. Graphify Memory (Knowledge Graph)
$GraphReportPath = "$WorkspaceRoot\graphify-out\GRAPH_REPORT.md"
$GraphJsonPath = "$WorkspaceRoot\graphify-out\graph.json"

if (Test-Path $GraphReportPath) {
    Copy-Item -Path $GraphReportPath -Destination "$DocsWikiDir\CODEBASE_GRAPH_REPORT.md" -Force
    Write-Host "  [+] Graphify Report disinkronkan ke Sokudo Wiki: docs\wiki\CODEBASE_GRAPH_REPORT.md" -ForegroundColor Green
    if ($CreateExportBundle) {
        Copy-Item -Path $GraphReportPath -Destination "$BundleMemoryDir\CODEBASE_GRAPH_REPORT.md" -Force
    }
}

if (Test-Path $GraphJsonPath) {
    if ($CreateExportBundle) {
        Copy-Item -Path $GraphJsonPath -Destination "$BundleMemoryDir\graph.json" -Force
        Write-Host "  [+] Knowledge Graph JSON diexport ke bundle: memory\graph.json" -ForegroundColor Green
    }
}

# 3b. Project Rules & Context Memory
$RulesDir = "$WorkspaceRoot\.agents\rules"
if (Test-Path $RulesDir) {
    # Salin semua rules ke docs/wiki/rules agar Sokudo Wiki Engine langsung mengindeksnya
    $WikiRulesDir = "$DocsWikiDir\project-rules"
    if (-not (Test-Path $WikiRulesDir)) {
        New-Item -ItemType Directory -Path $WikiRulesDir -Force | Out-Null
    }
    Get-ChildItem -Path $RulesDir -Filter "*.md" | ForEach-Object {
        Copy-Item -Path $_.FullName -Destination $WikiRulesDir -Force
    }
    Write-Host "  [+] Rules context disalin ke Wiki Sokudo: docs\wiki\project-rules\" -ForegroundColor Green

    if ($CreateExportBundle) {
        $BundleRulesDir = "$BundleMemoryDir\rules"
        if (-not (Test-Path $BundleRulesDir)) {
            New-Item -ItemType Directory -Path $BundleRulesDir -Force | Out-Null
        }
        Copy-Item -Path "$RulesDir\*" -Destination $BundleRulesDir -Recurse -Force
    }
}

# 3c. Generate Sokudo Project Memory / Wiki Index (README_SOKUDO.md)
$SokudoWikiReadme = @"
# Project Knowledge & Memory Base: MAGKOM

Dokumen ini adalah ringkasan memori dan konteks proyek untuk AI Agents di **Sokudo IDE**.

## 1. Arsitektur & Aturan Proyek
- **Tech Stack**: Laravel 11, PHP 8.2+, MySQL (Laragon local runtime), Blade, Tailwind / Gentelella Admin.
- **Project Rules**: Tersedia di folder `.agents/rules/` dan `docs/wiki/project-rules/`.
- **Knowledge Graph Codebase**: Lihat [CODEBASE_GRAPH_REPORT.md](./CODEBASE_GRAPH_REPORT.md) untuk relasi modul, controller, view, dan komunitas node.

## 2. Model & Relasi Utama
- **User** (Roles: Superadmin, Admin, Instruktur, Peserta, Reviewer)
- **Activity & Audit Logs**: Riwayat aksi user di `/superadmin/audit-logs`
- **Dashboard Modul**: Dilengkapi cockpit monitoring, metric cards, dan layout Gentelella modern.

## 3. Skills yang Terhubung
Semua agent skills telah diekspor ke global `~/.agents/skills/` dan workspace `.agents/skills/`.
Gunakan skill seperti `web-planning`, `web-implementation`, `ui-ux-pro-max`, `impeccable`, dan security scanning Strix sesuai kebutuhan.
"@

Set-Content -Path "$DocsWikiDir\index.md" -Value $SokudoWikiReadme -Encoding UTF8
Write-Host "  [+] Sokudo Wiki Index dibuat: docs\wiki\index.md" -ForegroundColor Green

if ($CreateExportBundle) {
    Set-Content -Path "$BundleMemoryDir\README.md" -Value $SokudoWikiReadme -Encoding UTF8
}

Write-Host "`n==========================================" -ForegroundColor Cyan
Write-Host "  SELESAI! SEMUA ASSET BERHASIL DI-EXPORT " -ForegroundColor Green
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "Lokasi Global Sokudo: $SokudoGlobalSkillsDir" -ForegroundColor White
Write-Host "Lokasi Workspace MCP: $WorkspaceMcpTarget" -ForegroundColor White
Write-Host "Lokasi Bundle Export: $ExportBundleDir" -ForegroundColor White
