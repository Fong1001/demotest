<#
.SYNOPSIS
    Cleans up temporary files, unused Docker images/volumes, and lists the largest files in your user directory.
    
.DESCRIPTION
    Running out of space is usually caused by Docker caches, leftover node_modules, or hidden temp files.
    This script safely deletes known temp/cache files and provides a report of the largest files taking up space so you can manually review them.
#>

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "      System Space Cleanup Utility      " -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# 1. Clean Docker System
Write-Host "[1/4] Cleaning unused Docker Images, Containers, and Volumes..." -ForegroundColor Yellow
try {
    # This removes unused containers, networks, images, and volumes
    docker system prune -a --volumes -f
    Write-Host "Docker cleanup complete!" -ForegroundColor Green
} catch {
    Write-Host "Docker is not running or encountered an error." -ForegroundColor Red
}
Write-Host ""

# 2. Clean Windows Temp Files
Write-Host "[2/4] Cleaning Windows Temporary Files..." -ForegroundColor Yellow
$tempPath = $env:TEMP
try {
    Get-ChildItem -Path $tempPath -Recurse -Force -ErrorAction SilentlyContinue | 
        Where-Object { -not $_.PSIsContainer } | 
        Remove-Item -Force -ErrorAction SilentlyContinue
    Write-Host "Temp files cleared!" -ForegroundColor Green
} catch {
    Write-Host "Some temp files are currently in use and were skipped (this is normal)." -ForegroundColor Green
}
Write-Host ""

# 3. Clean Windows Prefetch (Requires Admin, so we wrap in try/catch)
Write-Host "[3/4] Cleaning Windows Prefetch..." -ForegroundColor Yellow
try {
    $prefetch = "$env:WINDIR\Prefetch"
    Get-ChildItem -Path $prefetch -Force -ErrorAction Stop | Remove-Item -Force -ErrorAction SilentlyContinue
    Write-Host "Prefetch cleared!" -ForegroundColor Green
} catch {
    Write-Host "Skipped Prefetch (requires Administrator privileges)." -ForegroundColor DarkGray
}
Write-Host ""

# 4. Find the Largest Files in User Directory
Write-Host "[4/4] Scanning for the 15 LARGEST files in your User folder (Downloads, Documents, etc.)..." -ForegroundColor Yellow
Write-Host "Please wait, this may take a minute..." -ForegroundColor DarkGray

$userProfile = $env:USERPROFILE
# Exclude AppData to avoid breaking applications, focus on personal files
$largestFiles = Get-ChildItem -Path $userProfile -Recurse -File -ErrorAction SilentlyContinue |
    Where-Object { $_.FullName -notmatch "\\AppData\\" } |
    Sort-Object Length -Descending |
    Select-Object -First 15

Write-Host ""
Write-Host "--- TOP 15 LARGEST FILES ---" -ForegroundColor Cyan
foreach ($file in $largestFiles) {
    $sizeMB = [math]::Round($file.Length / 1MB, 2)
    Write-Host "$sizeMB MB`t- $($file.FullName)" -ForegroundColor White
}
Write-Host "----------------------------" -ForegroundColor Cyan
Write-Host ""
Write-Host "Cleanup script finished! Please review the large files listed above and manually delete any you no longer need." -ForegroundColor Green
