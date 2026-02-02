@echo off
echo ========================================
echo TokoManager - Route Integration Tests
echo ========================================
echo.

echo Running Feature Tests...
echo.

REM Run all feature tests
php spark test --testdox Tests\Feature

echo.
echo ========================================
echo Test Suite Complete!
echo ========================================
pause
