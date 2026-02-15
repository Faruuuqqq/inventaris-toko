<?php

namespace Tests\Feature;

use Tests\Support\DatabaseTestCase;
use App\Models\UserModel;
use App\Models\RoleModel;

class AuthIntegrationTest extends DatabaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\App\Database\Seeds\UserSeeder::class);
        $this->seed(\App\Database\Seeds\RoleSeeder::class);
    }

    /** @test */
    public function it_authenticates_user_with_valid_credentials()
    {
        // Arrange: Get seeded user
        $user = $this->userModel->where('email', 'admin@example.com')->first();

        // Act: Attempt login
        $response = $this->post('auth/login', [
            'email' => $user->email,
            'password' => 'password123', // Default seeded password
            'remember' => false
        ]);

        // Assert: Login successful
        $response->assertStatus(200);
        $data = $response->getJSON();
        
        $this->assertArrayHasKey('user', $data);
        $this->assertArrayHasKey('token', $data);
        $this->assertEquals($user->id, $data['user']['id']);
        $this->assertNotNull($data['token']);

        // Assert: Session created
        $this->assertTrue(isset($_SESSION['logged_in']));
        $this->assertEquals($user->id, $_SESSION['user_id']);
    }

    /** @test */
    public function it_prevents_login_with_invalid_credentials()
    {
        // Act: Attempt login with wrong password
        $response = $this->post('auth/login', [
            'email' => 'admin@example.com',
            'password' => 'wrongpassword'
        ]);

        // Assert: Login failed
        $response->assertStatus(401);
        $data = $response->getJSON();
        
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals('Invalid credentials', $data['error']);

        // Assert: No session created
        $this->assertFalse(isset($_SESSION['logged_in']));
    }

    /** @test */
    public function it_validates_login_input()
    {
        // Act: Attempt login with invalid email
        $response = $this->post('auth/login', [
            'email' => 'invalid-email',
            'password' => 'password123'
        ]);

        // Assert: Validation error
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function it_logs_user_out_correctly()
    {
        // Arrange: First login user
        $user = $this->userModel->first();
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_role'] = $user->role;

        // Act: Logout
        $response = $this->post('auth/logout');

        // Assert: Logout successful
        $response->assertStatus(200);
        $data = $response->getJSON();
        
        $this->assertEquals('success', $data['status']);
        $this->assertEquals('Logged out successfully', $data['message']);

        // Assert: Session destroyed
        $this->assertFalse(isset($_SESSION['logged_in']));
        $this->assertFalse(isset($_SESSION['user_id']));
    }

    /** @test */
    public function it_prevents_access_to_protected_routes_without_auth()
    {
        // Clear any existing session
        unset($_SESSION['logged_in']);
        unset($_SESSION['user_id']);

        // Act: Try to access protected routes
        $protectedRoutes = [
            '/master/products',
            '/transactions/sales',
            '/finance/expenses',
            '/dashboard'
        ];

        foreach ($protectedRoutes as $route) {
            $response = $this->get($route);
            
            // Should redirect to login
            $this->assertEquals(302, $response->getStatusCode());
            $this->assertStringContains('/auth/login', $response->getHeaderLine('Location'));
        }
    }

    /** @test */
    public function it_allows_access_based_on_user_role()
    {
        // Arrange: Create users with different roles
        $adminUser = $this->createTestUser(['role' => 'admin']);
        $userUser = $this->createTestUser(['role' => 'user']);

        // Test admin access
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $adminUser;
        $_SESSION['user_role'] = 'admin';

        $response = $this->get('/users'); // Admin-only route
        $this->assertEquals(200, $response->getStatusCode());

        // Test regular user access (should fail)
        $_SESSION['user_id'] = $userUser;
        $_SESSION['user_role'] = 'user';

        $response = $this->get('/users');
        $this->assertEquals(403, $response->getStatusCode());
    }

    /** @test */
    public function it_handles_password_reset_flow()
    {
        // Arrange: Create user with known email
        $user = $this->createTestUser([
            'email' => 'test@example.com'
        ]);

        // Act: Request password reset
        $response = $this->post('auth/forgot-password', [
            'email' => 'test@example.com'
        ]);

        // Assert: Reset token generated
        $response->assertStatus(200);
        
        // Check database for reset token
        $this->assertDatabaseHas('password_resets', [
            'email' => 'test@example.com'
        ]);
    }

    /** @test */
    public function it_validates_session_timeout()
    {
        // Arrange: Login user
        $user = $this->userModel->first();
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $user->id;
        $_SESSION['last_activity'] = time() - 3600; // 1 hour ago (expired)

        // Act: Try to access protected route
        $response = $this->get('/dashboard');

        // Assert: Should redirect to login due to timeout
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContains('/auth/login', $response->getHeaderLine('Location'));
    }

    /** @test */
    public function it_prevents_brute_force_login_attempts()
    {
        // Arrange: Get user credentials
        $user = $this->userModel->first();

        // Act: Attempt multiple failed logins
        for ($i = 1; $i <= 6; $i++) {
            $this->post('auth/login', [
                'email' => $user->email,
                'password' => 'wrongpassword' . $i
            ]);
        }

        // Assert: Account should be locked
        $response = $this->post('auth/login', [
            'email' => $user->email,
            'password' => 'password123' // Correct password
        ]);

        $response->assertStatus(423); // Locked
        $data = $response->getJSON();
        $this->assertArrayHasKey('error', $data);
        $this->assertStringContains('locked', $data['error']);
    }

    /** @test */
    public function it_updates_last_activity_timestamp()
    {
        // Arrange: Login user
        $user = $this->userModel->first();
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $user->id;
        $_SESSION['last_activity'] = time() - 1800; // 30 minutes ago

        // Act: Make a request
        $this->get('/dashboard');

        // Assert: Last activity should be updated
        $this->assertGreaterThan($_SESSION['last_activity'], time() - 60);
    }

    /** @test */
    public function it_handles_concurrent_session_limit()
    {
        // Arrange: Create user
        $user = $this->createTestUser(['email' => 'concurrent@example.com']);

        // Act: Login from first device
        $response1 = $this->post('auth/login', [
            'email' => 'concurrent@example.com',
            'password' => 'password123'
        ]);

        $this->assertEquals(200, $response1->getStatusCode());
        $session1 = $_SESSION;

        // Clear session and login from second device
        unset($_SESSION);
        $response2 = $this->post('auth/login', [
            'email' => 'concurrent@example.com',
            'password' => 'password123'
        ]);

        $this->assertEquals(200, $response2->getStatusCode());

        // Assert: First session should be invalidated
        $this->restoreSession($session1);
        $response = $this->get('/dashboard');
        $this->assertEquals(302, $response->getStatusCode());
    }

    private function restoreSession($session)
    {
        $_SESSION = $session;
    }
}