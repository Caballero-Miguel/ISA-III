[CmdletBinding()]
param(
    [int] $SearchTtlSeconds = 0,
    [switch] $Reseed
)

$ErrorActionPreference = 'Stop'
$projectRoot = Split-Path -Parent $PSScriptRoot
$runtimeDir = Join-Path $projectRoot '.runtime'
$php = 'C:\xampp\php\php.exe'
if (-not (Test-Path -LiteralPath $php -PathType Leaf)) { throw "PHP not found at $php" }

& (Join-Path $PSScriptRoot 'init-db.ps1') -Reseed:$Reseed

if ($SearchTtlSeconds -gt 0) {
    $env:SEARCH_TTL_SECONDS = [string] $SearchTtlSeconds
    Write-Host "Runtime TTL override: $SearchTtlSeconds seconds" -ForegroundColor Yellow
}

$services = @(
    @{ Name = 'provider-alpha'; Port = 8101; Public = 'services\provider-alpha\public'; Router = 'services\provider-alpha\public\index.php' },
    @{ Name = 'provider-beta'; Port = 8102; Public = 'services\provider-beta\public'; Router = 'services\provider-beta\public\index.php' },
    @{ Name = 'provider-gamma'; Port = 8103; Public = 'services\provider-gamma\public'; Router = 'services\provider-gamma\public\index.php' },
    @{ Name = 'bus'; Port = 8000; Public = 'apps\bus\public'; Router = 'apps\bus\public\index.php' }
)

foreach ($service in $services) {
    $pidFile = Join-Path $runtimeDir "$($service.Name).pid"
    if (Test-Path -LiteralPath $pidFile) {
        $oldPid = [int] (Get-Content -LiteralPath $pidFile -Raw)
        if (Get-Process -Id $oldPid -ErrorAction SilentlyContinue) {
            throw "$($service.Name) is already running with PID $oldPid. Run scripts/stop-dev.ps1 first."
        }
    }
    $stdout = Join-Path $runtimeDir "$($service.Name).out.log"
    $stderr = Join-Path $runtimeDir "$($service.Name).err.log"
    $public = Join-Path $projectRoot $service.Public
    $router = Join-Path $projectRoot $service.Router
    $process = Start-Process -WindowStyle Hidden -FilePath $php -WorkingDirectory $projectRoot -ArgumentList @('-S', "127.0.0.1:$($service.Port)", '-t', $public, $router) -RedirectStandardOutput $stdout -RedirectStandardError $stderr -PassThru
    Set-Content -LiteralPath $pidFile -Value $process.Id
    Write-Host "$($service.Name) -> http://127.0.0.1:$($service.Port) (PID $($process.Id))" -ForegroundColor Green
}

$workerPidFile = Join-Path $runtimeDir 'cleanup-worker.pid'
if (Test-Path -LiteralPath $workerPidFile) {
    $oldWorkerPid = [int] (Get-Content -LiteralPath $workerPidFile -Raw)
    if (Get-Process -Id $oldWorkerPid -ErrorAction SilentlyContinue) {
        throw "cleanup-worker is already running with PID $oldWorkerPid."
    }
}
$worker = Start-Process -WindowStyle Hidden -FilePath $php -WorkingDirectory $projectRoot -ArgumentList (Join-Path $projectRoot 'scripts\cleanup-worker.php') -RedirectStandardOutput (Join-Path $runtimeDir 'cleanup-worker.out.log') -RedirectStandardError (Join-Path $runtimeDir 'cleanup-worker.err.log') -PassThru
Set-Content -LiteralPath $workerPidFile -Value $worker.Id
Write-Host "cleanup-worker (PID $($worker.Id))" -ForegroundColor Green

$ready = $false
for ($attempt = 0; $attempt -lt 30; $attempt++) {
    Start-Sleep -Milliseconds 250
    try {
        $health = Invoke-RestMethod -Uri 'http://127.0.0.1:8000/health' -TimeoutSec 2 -UseBasicParsing
        if ($health.status -eq 'ok') { $ready = $true; break }
    } catch { }
}
if (-not $ready) { throw 'Bus did not become ready. Inspect .runtime/bus.err.log.' }

Write-Host ''
Write-Host 'Demo ready:' -ForegroundColor Cyan
Write-Host '  Search: http://127.0.0.1:8000/'
Write-Host '  Admin:  http://127.0.0.1:8000/admin'
Write-Host '  Login:  admin / demo-isa3-2026'
