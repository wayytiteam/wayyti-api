<?php

namespace Tests\Feature;

use App\Mail\OTPSent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_successful_email_login_should_return_auth_token()
    {
        $user = User::factory()->create();
        $response = $this->post('api/users/email', [
            'email' => $user->email,
            'password' => 'password'
        ]);
        $response->assertStatus(200)->assertJson(
            fn (AssertableJson $json) => 
            $json->has('token')
            ->etc()
        );
    }

    public function test_failed_email_login_should_return_unauthorized_status_code()
    {
        $user = User::factory()->create();
        $response = $this->post('api/users/email', [
            'email' => $user->email,
            'password' => 'wrong_password'
        ]);
        $response->assertStatus(401);
    }

    public function test_store_endpoint_should_return_successful_status_code()
    {
        $response = $this->post('api/users', [
            'email' => 'jdo@gmail.com',
            'password' => 'password'
        ]);
        $response->assertStatus(200);
    }

    public function test_request_verification_code_sends_mail_and_stores_cache()
    {
        Mail::fake();
        $email = 'jdoe@gmail.com';
        $response = $this->post('api/users/request-verification', [
            'email' => $email,
            'action' => 'sign-up'
        ]);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'verification_code_timestamp'
            ]);
        Mail::assertSent(OTPSent::class, function ($mail) use ($email) {
            return $mail->hasTo($email);
        });
        $this->assertNotNull(Cache::get('verification_code'.$email));
    }

    public function test_verfy_otp_return_succeeds_with_valid_code()
    {
        $user = User::factory()->create(['email_verified_at' => null]);
        $code = 12345;
        Cache::put('verification_code'.$user->email, $code, now()->addHours(24));
        Cache::put('verification_code_timestamp'.$user->email, now()->addHours(24));

        $response = $this->post('api/users/verify', [
            'email' => $user->email,
            'verification_code' => $code
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['user', 'token']);
        $this->assertNotNull($user->fresh()->email_verified_at);
        $this->assertNull(Cache::get('verification_code'.$user->email));
    }

        public function test_verify_otp_fails_with_invalid_code()
    {
        $user = User::factory()->create(['email_verified_at' => null]);
        $code = 12345;

        Cache::put('verification_code' . $user->email, $code, now()->addHours(24));

        $response = $this->post('api/users/verify', [
            'email' => $user->email,
            'verification_code' => 99999 // invalid code
        ]);

        $response->assertStatus(400)
            ->assertJsonFragment(['error' => 'Invalid Code']);
    }
    public function test_verify_otp_fails_with_expired_code()
    {
        $user = User::factory()->create(['email_verified_at' => null]);
        $response = $this->post('api/users/verify', [
            'email' => $user->email,
            'verification_code' => 12345
        ]);
        $response->assertStatus(400)
            ->assertJsonFragment([
                'error' => 'Verification code is either expired or already used. Please do note that verification code is only available for 24 hours',
            ]);
    }
}
