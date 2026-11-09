[CmdletBinding()]
param (
    [string] $OutputPath = ''
)

$ErrorActionPreference = 'Stop'

if ([string]::IsNullOrWhiteSpace($OutputPath)) {
    $OutputPath = Join-Path $PSScriptRoot 'infoscreening-cpanel.zip'
}

$projectRoot = (Resolve-Path (Join-Path $PSScriptRoot '..\..')).Path
$archivePath = [System.IO.Path]::GetFullPath($OutputPath)

Add-Type -AssemblyName System.IO.Compression.FileSystem

if (Test-Path -LiteralPath $archivePath) {
    Remove-Item -LiteralPath $archivePath -Force
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

    foreach ($file in @('artisan', 'composer.json', 'composer.lock', '.htaccess', '.env.example')) {
        $sourceFile = Join-Path $projectRoot $file
        if (Test-Path -LiteralPath $sourceFile -PathType Leaf) {
            Copy-Item -LiteralPath $sourceFile -Destination $releaseRoot
        }
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

    foreach ($file in Get-ChildItem -LiteralPath (Join-Path $projectRoot 'public') -Force | Where-Object { $_.Name -notin @('storage', 'hot', 'build', 'build.zip', 'build (2).zip') }) {
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

    if (-not (Test-Path -LiteralPath (Join-Path $releaseRoot 'vendor\autoload.php') -PathType Leaf)) {
        throw 'Production Composer autoloader was not created.'
    }

    if (-not (Test-Path -LiteralPath (Join-Path $publicBuildRoot 'manifest.json') -PathType Leaf)) {
        throw 'Vite production manifest was not created.'
    }

    $archiveDirectory = Split-Path -Parent $archivePath
    New-Item -ItemType Directory -Path $archiveDirectory -Force | Out-Null

    Add-Type -AssemblyName System.IO.Compression
    Add-Type -AssemblyName System.IO.Compression.FileSystem

    $archiveFile = [System.IO.File]::Create($archivePath)
    $zipArchive = New-Object System.IO.Compression.ZipArchive($archiveFile, [System.IO.Compression.ZipArchiveMode]::Create)

    try {
        $files = Get-ChildItem -LiteralPath $releaseRoot -Recurse -Force
        foreach ($item in $files) {
            $relativePath = $item.FullName.Substring($releaseRoot.Length).TrimStart('\', '/')
            $entryName = $relativePath.Replace('\', '/')
            if ($item.PSIsContainer) {
                if (-not [string]::IsNullOrEmpty($entryName)) {
                    [void]$zipArchive.CreateEntry($entryName + '/')
                }
            } else {
                [void][System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile(
                    $zipArchive,
                    $item.FullName,
                    $entryName,
                    [System.IO.Compression.CompressionLevel]::Optimal
                )
            }
        }
    } finally {
        $zipArchive.Dispose()
        $archiveFile.Dispose()
    }

    $rootDeployZip = Join-Path $projectRoot 'deploy.zip'
    Copy-Item -LiteralPath $archivePath -Destination $rootDeployZip -Force

    Write-Output "cPanel update package created: $archivePath"
    Write-Output "Root deploy package updated: $rootDeployZip"
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
