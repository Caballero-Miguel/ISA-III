[CmdletBinding()]
param(
    [switch] $Reseed
)

$ErrorActionPreference = 'Stop'
$projectRoot = Split-Path -Parent $PSScriptRoot
$runtimeDir = Join-Path $projectRoot '.runtime'
$dataDir = Join-Path $runtimeDir 'mariadb-data'
$mariaRoot = 'C:\xampp\mysql'
$installDb = Join-Path $mariaRoot 'bin\mysql_install_db.exe'
$server = Join-Path $mariaRoot 'bin\mysqld.exe'
$client = Join-Path $mariaRoot 'bin\mysql.exe'
$admin = Join-Path $mariaRoot 'bin\mysqladmin.exe'
$port = 3307

foreach ($path in @($installDb, $server, $client, $admin)) {
    if (-not (Test-Path -LiteralPath $path -PathType Leaf)) {
        throw "Required MariaDB executable not found: $path"
    }
}

New-Item -ItemType Directory -Force -Path $runtimeDir | Out-Null

if (-not (Test-Path -LiteralPath (Join-Path $dataDir 'mysql') -PathType Container)) {
    Write-Host "Initializing project-local MariaDB data at $dataDir" -ForegroundColor Cyan
    New-Item -ItemType Directory -Force -Path $dataDir | Out-Null
    & $installDb --datadir=$dataDir --port=$port --allow-remote-root-access
    if ($LASTEXITCODE -ne 0) { throw 'MariaDB data initialization failed.' }
    $Reseed = $true
}

& $admin --protocol=tcp -h 127.0.0.1 -P $port -u root ping --silent 2>$null
if ($LASTEXITCODE -ne 0) {
    $errorLog = Join-Path $runtimeDir 'mariadb-error.log'
    $process = Start-Process -WindowStyle Hidden -FilePath $server -ArgumentList @(
        '--no-defaults',
        "--basedir=$mariaRoot",
        "--datadir=$dataDir",
        "--port=$port",
        '--bind-address=127.0.0.1',
        '--character-set-server=utf8mb4',
        '--collation-server=utf8mb4_unicode_ci',
        "--log-error=$errorLog"
    ) -PassThru
    Set-Content -LiteralPath (Join-Path $runtimeDir 'mariadb-process.pid') -Value $process.Id

    $ready = $false
    for ($attempt = 0; $attempt -lt 30; $attempt++) {
        Start-Sleep -Milliseconds 250
        & $admin --protocol=tcp -h 127.0.0.1 -P $port -u root ping --silent 2>$null
        if ($LASTEXITCODE -eq 0) { $ready = $true; break }
    }
    if (-not $ready) {
        throw "MariaDB did not become ready. Inspect $errorLog"
    }
    Write-Host "Project MariaDB started on 127.0.0.1:$port (PID $($process.Id))." -ForegroundColor Green
} else {
    Write-Host "Project MariaDB is already running on 127.0.0.1:$port." -ForegroundColor Green
}

$sqlFiles = @('001_create_databases.sql', '002_create_tables.sql')
if ($Reseed -or -not (Test-Path -LiteralPath (Join-Path $runtimeDir 'database-initialized.flag'))) {
    $sqlFiles += '003_seed_products.sql'
}

foreach ($file in $sqlFiles) {
    $fullPath = (Join-Path $projectRoot "database\$file").Replace('\', '/')
    & $client --protocol=tcp -h 127.0.0.1 -P $port -u root --default-character-set=utf8mb4 -e "SOURCE $fullPath;"
    if ($LASTEXITCODE -ne 0) { throw "Failed to apply $file" }
    Write-Host "Applied database/$file"
}

Set-Content -LiteralPath (Join-Path $runtimeDir 'database-initialized.flag') -Value (Get-Date -Format o)
& $client --protocol=tcp -h 127.0.0.1 -P $port -u root -N -e "SELECT 'alpha', COUNT(*) FROM market_alpha.products UNION ALL SELECT 'beta', COUNT(*) FROM market_beta.products UNION ALL SELECT 'gamma', COUNT(*) FROM market_gamma.products;"

