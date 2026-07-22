$ErrorActionPreference = "Stop"

$workspace = "d:\Antarman\code\palm-reading"
$pluginDir = Join-Path $workspace "plugin"
$buildDir = Join-Path $workspace "build"
$tempDir = Join-Path $buildDir "palm-reading"
$zipPath = Join-Path $buildDir "palm-reading-beta1.zip"

Write-Host "Starting build process..."

# 1. Create build directory if it doesn't exist
if (!(Test-Path $buildDir)) {
    New-Item -ItemType Directory -Force -Path $buildDir | Out-Null
}

# 2. Clean previous build
if (Test-Path $tempDir) {
    Remove-Item -Recurse -Force $tempDir
}
if (Test-Path $zipPath) {
    Remove-Item -Force $zipPath
}

# 3. Create temp directory
New-Item -ItemType Directory -Force -Path $tempDir | Out-Null

# 4. Copy plugin files to temp directory
Write-Host "Copying plugin files..."
Copy-Item -Path "$pluginDir\*" -Destination $tempDir -Recurse -Force

# 4.5 Remove development files
Write-Host "Removing development files..."
$devFiles = @(
    "$tempDir\engine\tests",
    "$tempDir\engine\.github",
    "$tempDir\engine\phpunit.xml",
    "$tempDir\engine\tools",
    "$tempDir\engine\samples",
    "$tempDir\engine\bin",
    "$tempDir\engine\composer.phar",
    "$tempDir\engine\composer-setup.php"
)
foreach ($file in $devFiles) {
    if (Test-Path $file) {
        Remove-Item -Recurse -Force $file
    }
}

# 5. Compress to zip
Write-Host "Creating zip archive at $zipPath..."
Compress-Archive -Path "$tempDir" -DestinationPath $zipPath -Force

# 6. Cleanup
Write-Host "Cleaning up..."
Remove-Item -Recurse -Force $tempDir

Write-Host "Build complete! Plugin packaged at: $zipPath"
