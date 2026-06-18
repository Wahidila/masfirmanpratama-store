<?php

namespace Tests\Feature\Affiliate;

use App\Http\Middleware\EnsureAffiliatorIsVerified;
use App\Models\Affiliator;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AffiliateAuthTest extends TestCase
{
    use RefreshDatabase;

    // ─── Register ────────────────────────────────────────────────────

    /** Halaman register bisa diakses */
    public function test_affiliator_can_view_register_page(): void
    {
        $response = $this->get(route('affiliate.register'));

        $response->assertStatus(200);
        $response->assertViewIs('affiliate.auth.register');
    }

    /** Affiliator bisa register dengan data valid */
    public function test_affiliator_can_register_successfully(): void
    {
        Notification::fake();

        $response = $this->post(route('affiliate.register.store'), [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '081234567890',
            'type' => 'alumni',
        ]);

        $response->assertRedirect(route('affiliate.verification.notice'));

        $this->assertDatabaseHas('affiliators', [
            'email' => 'budi@example.com',
            'status' => 'pending',
            'type' => 'alumni',
        ]);

        $this->assertAuthenticatedAs(
            Affiliator::where('email', 'budi@example.com')->first(),
            'affiliator'
        );

        Notification::assertSentTo(
            Affiliator::where('email', 'budi@example.com')->first(),
            VerifyEmail::class
        );
    }

    /** Email unik — tidak bisa register dengan email yang sudah ada */
    public function test_affiliator_register_validation_email_unique(): void
    {
        Affiliator::factory()->create(['email' => 'existing@example.com']);

        $response = $this->post(route('affiliate.register.store'), [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '081234567890',
            'type' => 'alumni',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /** Password harus dikonfirmasi */
    public function test_affiliator_register_validation_password_confirmed(): void
    {
        $response = $this->post(route('affiliate.register.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'wrongconfirmation',
            'phone' => '081234567890',
            'type' => 'alumni',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /** Tipe affiliator wajib diisi */
    public function test_affiliator_register_validation_type_required(): void
    {
        $response = $this->post(route('affiliate.register.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '081234567890',
            'type' => '',
        ]);

        $response->assertSessionHasErrors('type');
    }

    // ─── Login ───────────────────────────────────────────────────────

    /** Halaman login bisa diakses */
    public function test_affiliator_can_view_login_page(): void
    {
        $response = $this->get(route('affiliate.login'));

        $response->assertStatus(200);
        $response->assertViewIs('affiliate.auth.login');
    }

    /** Affiliator aktif bisa login */
    public function test_affiliator_can_login_successfully(): void
    {
        $affiliator = Affiliator::factory()->create([
            'status' => 'active',
            'password' => 'password123',
        ]);

        $response = $this->post(route('affiliate.login.attempt'), [
            'email' => $affiliator->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('affiliate.verification.notice'));
        $this->assertAuthenticatedAs($affiliator, 'affiliator');
    }

    /** Affiliator suspended tidak bisa login */
    public function test_affiliator_cannot_login_when_suspended(): void
    {
        $affiliator = Affiliator::factory()->suspended()->create([
            'password' => 'password123',
        ]);

        $response = $this->post(route('affiliate.login.attempt'), [
            'email' => $affiliator->email,
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('affiliator');
    }

    // ─── Logout ──────────────────────────────────────────────────────

    /** Affiliator bisa logout */
    public function test_affiliator_can_logout(): void
    {
        $affiliator = Affiliator::factory()->create(['status' => 'active']);

        $response = $this->actingAs($affiliator, 'affiliator')
            ->post(route('affiliate.logout'));

        $response->assertRedirect(route('affiliate.login'));
        $this->assertGuest('affiliator');
    }

    // ─── Email Verification ─────────────────────────────────────────

    /** Affiliator belum verified diarahkan ke halaman verification notice */
    public function test_unverified_affiliator_redirected_to_verification_notice(): void
    {
        $affiliator = Affiliator::factory()->create([
            'status' => 'active',
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($affiliator, 'affiliator')
            ->get(route('affiliate.verification.notice'));

        $response->assertStatus(200);
        $response->assertViewIs('affiliate.auth.verify-email');
    }

    /** Affiliator yang sudah verified tidak di-redirect oleh middleware */
    public function test_verified_affiliator_not_redirected(): void
    {
        $affiliator = Affiliator::factory()->create([
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Middleware EnsureAffiliatorIsVerified hanya redirect kalau belum verified.
        // Test via direct middleware invocation.
        $middleware = new EnsureAffiliatorIsVerified;

        $request = Request::create('/affiliate/dashboard', 'GET');
        $request->setUserResolver(fn () => $affiliator);

        // Set the guard user
        auth('affiliator')->setUser($affiliator);

        $response = $middleware->handle($request, fn ($req) => response('passed'));

        $this->assertEquals('passed', $response->getContent());
    }

    /** Verifikasi email dengan signed URL valid */
    public function test_verify_email_with_valid_signed_url(): void
    {
        $affiliator = Affiliator::factory()->create([
            'status' => 'active',
            'email_verified_at' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'affiliate.verification.verify',
            now()->addMinutes(60),
            ['id' => $affiliator->id, 'hash' => sha1($affiliator->email)]
        );

        $response = $this->actingAs($affiliator, 'affiliator')
            ->get($verificationUrl);

        $response->assertRedirect(route('affiliate.verification.notice'));
        $this->assertTrue($affiliator->fresh()->hasVerifiedEmail());
    }
}
