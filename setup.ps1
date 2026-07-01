$dirs = @(
    "database/schema",
    "database/master",
    "database/generated_sql",
    "database/seed",
    "plugin/admin",
    "plugin/public",
    "plugin/includes",
    "plugin/assets/css",
    "plugin/assets/js",
    "plugin/assets/images",
    "plugin/templates",
    "plugin/languages",
    "docs",
    "scripts",
    "tests",
    ".github"
)

foreach ($dir in $dirs) {
    New-Item -ItemType Directory -Force -Path $dir | Out-Null
}

$readmeContent = @"
# AI Palm Reading Engine
WordPress Plugin
MeraRashifal.com

## Features
- ✔ Gemini Vision
- ✔ Palm Feature Extraction
- ✔ Rule Engine
- ✔ Free Report
- ✔ Premium Report
- ✔ Razorpay Integration
- ✔ PDF Report
- ✔ Cloud Storage
"@
Set-Content -Path "README.md" -Value $readmeContent

$gitignoreContent = @"
/vendor/
node_modules/
.env
.idea/
.vscode/
uploads/
logs/
*.zip
"@
Set-Content -Path ".gitignore" -Value $gitignoreContent

$files = @(
    "database/schema/palm_tables.sql",
    "database/master/palm_feature_values.json",
    "database/master/palm_report_sections.json",
    "database/master/prompts.json",
    "scripts/json_to_sql.php"
)

foreach ($file in $files) {
    New-Item -ItemType File -Force -Path $file | Out-Null
}

$pluginFileContent = @"
<?php
/**
 * Plugin Name: AI Palm Reading
 * Version: 1.0.0
 * Author: Your Name
 */
"@
Set-Content -Path "plugin/palm-reading.php" -Value $pluginFileContent

$docFiles = @(
    "docs/Architecture.md",
    "docs/Database.md",
    "docs/Gemini.md",
    "docs/RuleEngine.md",
    "docs/API.md",
    "docs/Roadmap.md"
)

foreach ($doc in $docFiles) {
    New-Item -ItemType File -Force -Path $doc | Out-Null
}

git add .
git commit -m "Initial project structure"
git push
