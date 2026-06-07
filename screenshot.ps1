# BHRC Site Screenshot Tool
# Usage: .\screenshot.ps1 [url] [output-path]
# Default: captures behappyretired.com homepage to STAGING/other/
param(
    [string]$Url = "https://behappyretired.com",
    [string]$Output = "E:\CCWS\STAGING\other\site-screenshot.png"
)
npx playwright screenshot $Url $Output --viewport-size=1280,900 --timeout=20000
Write-Host "Screenshot saved to $Output"
