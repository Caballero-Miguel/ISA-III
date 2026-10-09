[CmdletBinding()]
param(
    [switch] $KeepDatabase
)

$ErrorActionPreference = 'Continue'
$projectRoot = Split-Path -Parent $PSScriptRoot
$runtimeDir = Join-Path $projectRoot '.runtime'

foreach ($name in @('bus', 'provider-alpha', 'provider-beta', 'provider-gamma', 'cleanup-worker')) {
    $pidFile = Join-Path $runtimeDir "$name.pid"
    if (-not (Test-Path -LiteralPath $pidFile)) { continue }
    $processId = [int] (Get-Content -LiteralPath $pidFile -Raw)
    $process = Get-Process -Id $processId -ErrorAction SilentlyContinue
    if ($process) {
        Stop-Process -Id $processId -Force
        Write-Host "Stopped $name (PID $processId)."
    }
    Remove-Item -LiteralPath $pidFile -Force
}

if (-not $KeepDatabase) {
    $admin = 'C:\xampp\mysql\bin\mysqladmin.exe'
    if (Test-Path -LiteralPath $admin) {
        & $admin --protocol=tcp -h 127.0.0.1 -P 3307 -u root shutdown 2>$null
        if ($LASTEXITCODE -eq 0) { Write-Host 'Stopped project MariaDB on port 3307.' }
    }
    $dbPidFile = Join-Path $runtimeDir 'mariadb-process.pid'
    if (Test-Path -LiteralPath $dbPidFile) { Remove-Item -LiteralPath $dbPidFile -Force }
}

