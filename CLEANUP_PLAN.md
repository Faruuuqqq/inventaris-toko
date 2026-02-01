# Project Root Cleanup Plan

## Files to Keep in Root
- .env (keep - configuration)
- .gitignore (keep - git config)
- LICENSE (keep - license)
- composer.json (keep - dependencies)
- composer.lock (keep - dependencies)
- spark (keep - CI4 CLI)
- preload.php (keep - performance)

## Files to Move to /docs folder
- COLOR_PALETTE.md
- COMPONENT_PATTERNS.md
- CONTINUATION_GUIDE.md
- DESIGN_SYSTEM.md
- IMPLEMENTATION_COMPLETE.md
- LOGIN_PAGE_REDESIGN.md
- PHASE_1_PROGRESS.md
- PHASE_1_SESSION_SUMMARY.md
- PHASE_2_IMPLEMENTATION.md
- PHASE_2_IMPROVEMENTS.md
- PHASE_3_API_DOCUMENTATION.md
- PHASE_3_COMPLETION.md
- PHASE_4_TESTING_COMPLETE.md
- TESTING_SESSION.md

## Files to Delete (Temp/Utility Scripts)
- add_missing_tables.php
- create_missing_api_controllers.php
- database_clean.sql
- database_complete.php
- database_final.sql
- database_schema.sql
- database_setup_simple.php
- generate_test_report.php
- import_database.sql
- migration_fix.sql

## Folders
- Create /docs folder for documentation
- Keep app/, public/, writable/, vendor/ as is
