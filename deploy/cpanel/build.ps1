[CmdletBinding()]
param (
    [string] $OutputPath = (Join-Path $PSScriptRoot 'infoscreening-cpanel.zip')
)

$ErrorActionPreference = 'Stop'

$projectRoot = (Resolve-Path (Join-Path $PSScriptRoot '..\..')).Path
$archivePath = [System.IO.Path]::GetFullPath($OutputPath)

if (Test-Path -LiteralPath $archivePath) {
    throw "Output already exists; move or remove it before rebuilding: $archivePath"
}

$requiredFiles = @(
    'artisan',
    'composer.json',
    'composer.lock',
    'package.json',
    'package-lock.json'
)

foreach ($file in $requiredFiles) {
    if (-not (Test-Path -LiteralPath (Join-Path $projectRoot $file) -PathType Leaf)) {
        throw "Required project file is missing: $file"
    }
}

$releaseRoot = Join-Path $env:TEMP "infoscreening-cpanel-$([guid]::NewGuid().ToString('N'))"
$publicRoot = Join-Path $releaseRoot 'public'
$publicBuildRoot = Join-Path $publicRoot 'build'

try {
    New-Item -ItemType Directory -Path $releaseRoot, $publicRoot, $publicBuildRoot -Force | Out-Null

    foreach ($file in @('artisan', 'composer.json', 'composer.lock')) {
        Copy-Item -LiteralPath (Join-Path $projectRoot $file) -Destination $releaseRoot
    }

    foreach ($directory in @('app', 'config', 'resources', 'routes')) {
        Copy-Item -LiteralPath (Join-Path $projectRoot $directory) -Destination (Join-Path $releaseRoot $directory) -Recurse
    }

    $bootstrapRoot = Join-Path $releaseRoot 'bootstrap'
    New-Item -ItemType Directory -Path $bootstrapRoot -Force | Out-Null

    foreach ($file in Get-ChildItem -LiteralPath (Join-Path $projectRoot 'bootstrap') -Force | Where-Object { $_.Name -ne 'cache' }) {
        Copy-Item -LiteralPath $file.FullName -Destination $bootstrapRoot -Recurse
    }

    New-Item -ItemType Directory -Path (Join-Path $bootstrapRoot 'cache') -Force | Out-Null
    $databaseRoot = Join-Path $releaseRoot 'database'
    New-Item -ItemType Directory -Path $databaseRoot -Force | Out-Null

    foreach ($directory in @('factories', 'migrations', 'seeders')) {
        $databaseDestination = Join-Path $databaseRoot $directory
        Copy-Item -LiteralPath (Join-Path $projectRoot "database\$directory") -Destination $databaseDestination -Recurse
    }

    foreach ($file in Get-ChildItem -LiteralPath (Join-Path $projectRoot 'public') -Force | Where-Object { $_.Name -notin @('storage', 'hot', 'build') }) {
        Copy-Item -LiteralPath $file.FullName -Destination $publicRoot -Recurse
    }

    $runtimeDirectories = @(
        'storage\app\private',
        'storage\app\public',
        'storage\framework\cache\data',
        'storage\framework\sessions',
        'storage\framework\views',
        'storage\logs'
    )

    foreach ($directory in $runtimeDirectories) {
        New-Item -ItemType Directory -Path (Join-Path $releaseRoot $directory) -Force | Out-Null
    }

    $deployFiles = Join-Path $releaseRoot 'deploy\cpanel'
    New-Item -ItemType Directory -Path $deployFiles -Force | Out-Null
    Copy-Item -LiteralPath (Join-Path $PSScriptRoot 'README.md') -Destination $deployFiles
    Copy-Item -LiteralPath (Join-Path $PSScriptRoot '.env.production.example') -Destination $releaseRoot

    Push-Location $projectRoot
    try {
        & npm.cmd run build -- --outDir $publicBuildRoot --emptyOutDir
        if ($LASTEXITCODE -ne 0) {
            throw "Frontend build failed with exit code $LASTEXITCODE."
        }
    } finally {
        Pop-Location
    }

    & composer install --working-dir=$releaseRoot --no-dev --optimize-autoloader --no-interaction --prefer-dist
    if ($LASTEXITCODE -ne 0) {
        throw "Production Composer install failed with exit code $LASTEXITCODE."
    }

    $archiveDirectory = Split-Path -Parent $archivePath
    New-Item -ItemType Directory -Path $archiveDirectory -Force | Out-Null
    Compress-Archive -Path (Join-Path $releaseRoot '*') -DestinationPath $archivePath

    Write-Output "cPanel package created: $archivePath"
} catch {
    if (Test-Path -LiteralPath $archivePath) {
        Remove-Item -LiteralPath $archivePath -Force
    }

    throw
} finally {
    if (Test-Path -LiteralPath $releaseRoot) {
        Remove-Item -LiteralPath $releaseRoot -Recurse -Force
    }
}
