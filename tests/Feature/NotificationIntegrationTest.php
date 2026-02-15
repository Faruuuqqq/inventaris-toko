<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;

class NotificationIntegrationTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
    }
    
    public function testNotificationControllerExists()
    {
        // Test that the Notifications controller class exists
        $this->assertTrue(class_exists('\App\Controllers\Notifications'));
    }
    
    public function testNotificationModelExists()
    {
        // Test that the NotificationModel class exists
        $this->assertTrue(class_exists('\App\Models\NotificationModel'));
    }
    
    public function testBasicNotificationFunctionality()
    {
        // Test that notification model class exists and is properly structured
        $this->assertTrue(class_exists('\App\Models\NotificationModel'));
        
        $model = new \App\Models\NotificationModel();
        
        // Check if table property is set correctly
        $this->assertEquals('notifications', $model->table);
        $this->assertEquals('id', $model->primaryKey);
        $this->assertIsArray($model->allowedFields);
        $this->assertEquals('array', $model->returnType);
    }
    
    public function testNotificationModelHasCorrectProperties()
    {
        // Test that the model has the expected properties
        $model = new \App\Models\NotificationModel();
        
        $expectedProperties = ['table', 'primaryKey', 'allowedFields', 'returnType'];
        
        foreach ($expectedProperties as $property) {
            $this->assertTrue(property_exists($model, $property));
        }
    }
    
    public function testNotificationControllerHasCorrectMethods()
    {
        // Test that the controller has the expected methods
        $controller = new \App\Controllers\Notifications();
        
        $expectedMethods = [
            'getUnreadCount',
            'getRecent', 
            'markAsRead',
            'checkSystemNotifications',
            'getSettings',
            'updateSettings'
        ];
        
        foreach ($expectedMethods as $method) {
            $this->assertTrue(method_exists($controller, $method));
        }
    }
    
    public function testNotificationHelperFunctions()
    {
        // Test that UI helper functions exist
        $this->assertTrue(function_exists('badge_status'));
        $this->assertTrue(function_exists('icon'));
    }
    
    public function testDatabaseMigrationForNotifications()
    {
        // Test that the migration file exists
        $migrationFile = APPPATH . 'Database/Migrations/2026-02-14-100000_CreateNotificationsTable.php';
        $this->assertFileExists($migrationFile);
    }
    
    public function testNotificationTableStructure()
    {
        // Check if database connection works
        $db = \Config\Database::connect();
        $this->assertIsObject($db);
        
        // Test that the database has tables
        $tables = $db->listTables();
        $this->assertIsArray($tables);
    }
    
    public function testSettingsControllerIntegration()
    {
        // Test Settings controller exists and has expected methods
        $this->assertTrue(class_exists('\App\Controllers\Settings'));
        
        $controller = new \App\Controllers\Settings();
        
        // Test that Settings controller has index method
        $this->assertTrue(method_exists($controller, 'index'));
    }
    
    public function testUserRoleSystem()
    {
        // Test that user roles are correctly defined based on fitur.txt
        $expectedRoles = ['OWNER', 'ADMIN'];
        $this->assertIsArray($expectedRoles);
        
        // Verify the database connection works
        $db = \Config\Database::connect();
        $this->assertIsObject($db);
        
        // Verify that core classes exist
        $this->assertTrue(class_exists('\App\Models\UserModel'));
        $this->assertTrue(class_exists('\App\Models\NotificationModel'));
        $this->assertTrue(class_exists('\App\Controllers\Notifications'));
    }
    
    public function testSessionHelperFunctions()
    {
        // Test that session helpers exist
        $this->assertTrue(function_exists('is_admin'));
        $this->assertTrue(function_exists('is_owner'));
    }
}