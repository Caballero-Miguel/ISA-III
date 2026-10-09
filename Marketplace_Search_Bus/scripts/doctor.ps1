[CmdletBinding()]
param()

$ErrorActionPreference = 'Stop'
$script:RequiredFailures = 0

function Write-Check {
    param(
        [Parameter(Mandatory)] [string] $Name,
        [Parameter(Mandatory)] [bool] $Ok,
        [Parameter(Mandatory)] [string] $Detail,
        [bool] $Required = $true
    )

    $label = if ($Ok) { 'OK' } elseif ($Required) { 'FAIL' } else { 'WARN' }
    $color = if ($Ok) { 'Green' } elseif ($Required) { 'Red' } else { 'Yellow' }
    Write-Host ("[{0}] {1}: {2}" -f $label, $Name, $Detail) -ForegroundColor $color
    if (-not $Ok -and $Required) {
        $script:RequiredFailures++
    }
}

function Resolve-Executable {
    param(
        [Parameter(Mandatory)] [string[]] $Names,
        [string[]] $Candidates = @()
    )

    foreach ($name in $Names) {
        $command = Get-Command $name -ErrorAction SilentlyContinue
        if ($command) {
            return $command.Source
        }
    }

    foreach ($candidate in $Candidates) {
        if (Test-Path -LiteralPath $candidate -PathType Leaf) {
            return $candidate
        }
    }

    return $null
}

Write-Host 'Marketplace Search Bus - environment doctor' -ForegroundColor Cyan
Write-Host ("Checked at: {0}" -f (Get-Date -Format 'yyyy-MM-dd HH:mm:ss zzz'))

$php = Resolve-Executable -Names @('php') -Candidates @(
    'C:\xampp\php\php.exe',
    'C:\laragon\bin\php\php-8.4.0-Win32-vs17-x64\php.exe',
    'C:\php\php.exe'
)

if ($php) {
    $phpVersion = (& $php -r 'echo PHP_VERSION;' 2>&1 | Out-String).Trim()
    Write-Check 'PHP' $true "$phpVersion at $php"

    $modules = & $php -m 2>&1
    Write-Check 'PDO MySQL' ($modules -contains 'pdo_mysql') 'PHP extension pdo_mysql'
    Write-Check 'cURL' ($modules -contains 'curl') 'PHP extension curl'
} else {
    Write-Check 'PHP' $false 'php.exe was not found in PATH or common local locations'
    Write-Check 'PDO MySQL' $false 'cannot inspect extensions without PHP'
    Write-Check 'cURL' $false 'cannot inspect extensions without PHP'
}

$mysql = Resolve-Executable -Names @('mysql', 'mariadb') -Candidates @(
    'C:\xampp\mysql\bin\mysql.exe',
    'C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe',
    'C:\Program Files\MariaDB 11.4\bin\mariadb.exe'
)

if ($mysql) {
    $mysqlVersion = (& $mysql --version 2>&1 | Out-String).Trim()
    Write-Check 'MySQL/MariaDB client' $true "$mysqlVersion at $mysql"

    $projectRoot = Split-Path -Parent $PSScriptRoot
    $envFile = Join-Path $projectRoot '.env'
    $dbPort = 3306
    $dbUser = 'root'
    if (Test-Path -LiteralPath $envFile) {
        foreach ($line in Get-Content -LiteralPath $envFile) {
            if ($line -match '^DB_PORT=(\d+)$') { $dbPort = [int] $Matches[1] }
            if ($line -match '^DB_USER=(.+)$') { $dbUser = $Matches[1].Trim() }
        }
    }
    $dbProbe = & $mysql --connect-timeout=2 --protocol=tcp -h 127.0.0.1 -P $dbPort -u $dbUser -N -e 'SELECT VERSION()' 2>&1
    Write-Check 'Project database connectivity' ($LASTEXITCODE -eq 0) ($(if ($LASTEXITCODE -eq 0) { "$dbUser@127.0.0.1:$dbPort -> $(($dbProbe | Out-String).Trim())" } else { ($dbProbe | Out-String).Trim() })) $false
} else {
    Write-Check 'MySQL/MariaDB client' $false 'mysql or mariadb client was not found'
    Write-Check 'Database connectivity' $false 'not attempted because no client was found' $false
}

$powerShellVersion = $PSVersionTable.PSVersion.ToString()
$powerShellEdition = if ($PSVersionTable.PSEdition) { $PSVersionTable.PSEdition } else { 'Desktop' }
Write-Check 'PowerShell' $true "$powerShellVersion ($powerShellEdition) at $($PSHOME)"

$java = Resolve-Executable -Names @('java')
if ($java) {
    $javaVersion = (& $java -version 2>&1 | Select-Object -First 1 | Out-String).Trim()
    Write-Check 'Java (optional PlantUML rendering)' $true "$javaVersion at $java" $false
} else {
    Write-Check 'Java (optional PlantUML rendering)' $false 'not found; PlantUML sources can still be delivered' $false
}

Write-Host ''
if ($script:RequiredFailures -eq 0) {
    Write-Host 'Required runtime checks passed.' -ForegroundColor Green
    exit 0
}

Write-Host ("Required runtime checks failed: {0}" -f $script:RequiredFailures) -ForegroundColor Red
exit 1
