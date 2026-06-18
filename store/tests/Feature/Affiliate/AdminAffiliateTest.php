<?php

namespace Tests\Feature\Affiliate;

use App\Models\Admin;
use App\Models\AffiliateEvent;
use App\Models\Affiliator;
use App\Models\Commission;
use App\Models\CommissionSetting;
use App\Models\Material;
use App\Models\Withdrawal;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Test admin affiliate panel — semua controller Batch 5.
 * Cakupan: auth guard, CRUD, status transitions, file upload.
 */
class AdminAffiliateTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminSeeder::class);
        $this->admin = Admin::first();
    }

    // ─── Auth Guard ────────────────────────────────────────────────────

    public function test_guest_tidak_bisa_akses_affiliator_index(): void
    {
        $this->get(route('admin.affiliators.index'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_guest_tidak_bisa_akses_commissions_index(): void
    {
        $this->get(route('admin.commissions.index'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_guest_tidak_bisa_akses_withdrawals_index(): void
    {
        $this->get(route('admin.withdrawals.index'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_guest_tidak_bisa_akses_materials_index(): void
    {
        $this->get(route('admin.materials.index'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_guest_tidak_bisa_akses_affiliate_events_index(): void
    {
        $this->get(route('admin.affiliate-events.index'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_guest_tidak_bisa_akses_commission_settings_index(): void
    {
        $this->get(route('admin.commission-settings.index'))
            ->assertRedirect(route('admin.login'));
    }

    // ─── Affiliator Controller ─────────────────────────────────────────

    public function test_admin_bisa_lihat_daftar_affiliator(): void
    {
        Affiliator::factory()->count(3)->create();

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.affiliators.index'))
            ->assertOk()
            ->assertSee('Affiliator');
    }

    public function test_admin_bisa_filter_affiliator_berdasarkan_status(): void
    {
        Affiliator::factory()->create(['status' => 'active', 'name' => 'Budi Aktif']);
        Affiliator::factory()->create(['status' => 'pending', 'name' => 'Siti Pending']);

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.affiliators.index', ['status' => 'active']))
            ->assertOk()
            ->assertSee('Budi Aktif')
            ->assertDontSee('Siti Pending');
    }

    public function test_admin_bisa_lihat_detail_affiliator(): void
    {
        $affiliator = Affiliator::factory()->create();

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.affiliators.show', $affiliator))
            ->assertOk()
            ->assertSee($affiliator->name);
    }

    public function test_admin_bisa_edit_affiliator(): void
    {
        $affiliator = Affiliator::factory()->create(['status' => 'pending', 'type' => 'peserta']);

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.affiliators.edit', $affiliator))
            ->assertOk();

        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.affiliators.update', $affiliator), [
                'status' => 'active',
                'type' => 'alumni',
            ])
            ->assertRedirect(route('admin.affiliators.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('affiliators', [
            'id' => $affiliator->id,
            'status' => 'active',
            'type' => 'alumni',
        ]);
    }

    public function test_admin_bisa_hapus_affiliator_soft_delete(): void
    {
        $affiliator = Affiliator::factory()->create();

        $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.affiliators.destroy', $affiliator))
            ->assertRedirect(route('admin.affiliators.index'));

        $this->assertSoftDeleted('affiliators', ['id' => $affiliator->id]);
    }

    // ─── Commission Controller ─────────────────────────────────────────

    public function test_admin_bisa_lihat_daftar_komisi(): void
    {
        Commission::factory()->count(2)->create();

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.commissions.index'))
            ->assertOk()
            ->assertSee('Komisi');
    }

    public function test_admin_bisa_approve_komisi_pending(): void
    {
        $commission = Commission::factory()->create(['status' => 'pending']);

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.commissions.approve', $commission))
            ->assertRedirect(route('admin.commissions.index'))
            ->assertSessionHas('status');

        $commission->refresh();
        $this->assertEquals('approved', $commission->status);
        $this->assertNotNull($commission->approved_at);
    }

    public function test_admin_bisa_reject_komisi_pending(): void
    {
        $commission = Commission::factory()->create(['status' => 'pending']);

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.commissions.reject', $commission))
            ->assertRedirect(route('admin.commissions.index'));

        $commission->refresh();
        $this->assertEquals('rejected', $commission->status);
    }

    public function test_admin_tidak_bisa_approve_komisi_non_pending(): void
    {
        $commission = Commission::factory()->create(['status' => 'approved']);

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.commissions.approve', $commission))
            ->assertStatus(422);
    }

    // ─── Withdrawal Controller ─────────────────────────────────────────

    public function test_admin_bisa_lihat_daftar_penarikan(): void
    {
        Withdrawal::factory()->count(2)->create();

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.withdrawals.index'))
            ->assertOk()
            ->assertSee('Penarikan');
    }

    public function test_admin_bisa_approve_penarikan_requested(): void
    {
        $withdrawal = Withdrawal::factory()->create(['status' => 'requested']);

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.withdrawals.approve', $withdrawal))
            ->assertRedirect(route('admin.withdrawals.index'));

        $this->assertDatabaseHas('withdrawals', [
            'id' => $withdrawal->id,
            'status' => 'approved',
        ]);
    }

    public function test_admin_bisa_mark_paid_penarikan_approved(): void
    {
        $withdrawal = Withdrawal::factory()->create(['status' => 'approved']);

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.withdrawals.mark-paid', $withdrawal))
            ->assertRedirect(route('admin.withdrawals.index'));

        $withdrawal->refresh();
        $this->assertEquals('paid', $withdrawal->status);
        $this->assertNotNull($withdrawal->processed_at);
    }

    public function test_admin_bisa_reject_penarikan_requested(): void
    {
        $withdrawal = Withdrawal::factory()->create(['status' => 'requested']);

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.withdrawals.reject', $withdrawal))
            ->assertRedirect(route('admin.withdrawals.index'));

        $this->assertDatabaseHas('withdrawals', [
            'id' => $withdrawal->id,
            'status' => 'rejected',
        ]);
    }

    public function test_admin_tidak_bisa_approve_penarikan_non_requested(): void
    {
        $withdrawal = Withdrawal::factory()->create(['status' => 'paid']);

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.withdrawals.approve', $withdrawal))
            ->assertStatus(422);
    }

    // ─── Material Controller ───────────────────────────────────────────

    public function test_admin_bisa_lihat_daftar_materi(): void
    {
        Material::factory()->count(2)->create();

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.materials.index'))
            ->assertOk()
            ->assertSee('Materi Affiliate');
    }

    public function test_admin_bisa_buat_materi_dengan_file_upload(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.materials.store'), [
                'title' => 'Banner Promo AMC',
                'description' => 'Banner untuk promosi kelas AMC.',
                'type' => 'banner',
                'file' => UploadedFile::fake()->image('banner.png', 800, 600),
            ])
            ->assertRedirect(route('admin.materials.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('materials', ['title' => 'Banner Promo AMC', 'type' => 'banner']);

        $material = Material::where('title', 'Banner Promo AMC')->first();
        $storedPath = str_replace('storage/', '', $material->file_path);
        Storage::disk('public')->assertExists($storedPath);
    }

    public function test_admin_bisa_update_materi(): void
    {
        $material = Material::factory()->create(['title' => 'Judul Lama']);

        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.materials.update', $material), [
                'title' => 'Judul Baru',
                'type' => 'brosur',
            ])
            ->assertRedirect(route('admin.materials.index'));

        $this->assertDatabaseHas('materials', ['id' => $material->id, 'title' => 'Judul Baru']);
    }

    public function test_admin_bisa_hapus_materi(): void
    {
        $material = Material::factory()->create();

        $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.materials.destroy', $material))
            ->assertRedirect(route('admin.materials.index'));

        $this->assertDatabaseMissing('materials', ['id' => $material->id]);
    }

    public function test_validasi_materi_store_gagal_tanpa_file(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.materials.store'), [
                'title' => 'Test',
                'type' => 'banner',
                // file kosong
            ])
            ->assertSessionHasErrors('file');
    }

    // ─── AffiliateEvent Controller ─────────────────────────────────────

    public function test_admin_bisa_lihat_daftar_event(): void
    {
        AffiliateEvent::factory()->count(2)->create();

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.affiliate-events.index'))
            ->assertOk()
            ->assertSee('Event Affiliate');
    }

    public function test_admin_bisa_buat_event(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.affiliate-events.store'), [
                'title' => 'Lomba Referral Juni',
                'description' => 'Event lomba referral bulan Juni.',
                'starts_at' => '2026-06-01 00:00:00',
                'ends_at' => '2026-06-30 23:59:59',
                'reward_note' => 'Hadiah iPhone 15',
                'status' => 'draft',
            ])
            ->assertRedirect(route('admin.affiliate-events.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('affiliate_events', ['title' => 'Lomba Referral Juni']);
    }

    public function test_admin_bisa_update_event(): void
    {
        $event = AffiliateEvent::factory()->create(['title' => 'Event Lama']);

        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.affiliate-events.update', $event), [
                'title' => 'Event Baru',
                'starts_at' => '2026-07-01 00:00:00',
                'ends_at' => '2026-07-31 23:59:59',
                'status' => 'active',
            ])
            ->assertRedirect(route('admin.affiliate-events.index'));

        $this->assertDatabaseHas('affiliate_events', ['id' => $event->id, 'title' => 'Event Baru']);
    }

    public function test_admin_bisa_hapus_event(): void
    {
        $event = AffiliateEvent::factory()->create();

        $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.affiliate-events.destroy', $event))
            ->assertRedirect(route('admin.affiliate-events.index'));

        $this->assertDatabaseMissing('affiliate_events', ['id' => $event->id]);
    }

    public function test_validasi_event_ends_at_harus_setelah_starts_at(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.affiliate-events.store'), [
                'title' => 'Event Invalid',
                'starts_at' => '2026-06-30 00:00:00',
                'ends_at' => '2026-06-01 00:00:00', // sebelum starts_at
                'status' => 'draft',
            ])
            ->assertSessionHasErrors('ends_at');
    }

    // ─── CommissionSetting Controller ──────────────────────────────────

    public function test_admin_bisa_lihat_pengaturan_komisi(): void
    {
        CommissionSetting::factory()->create();

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.commission-settings.index'))
            ->assertOk()
            ->assertSee('Pengaturan Komisi');
    }

    public function test_admin_bisa_update_pengaturan_komisi(): void
    {
        $setting = CommissionSetting::factory()->create([
            'rate_percent' => 10,
            'min_payout' => 50000,
        ]);

        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.commission-settings.update', $setting), [
                'rate_percent' => 15,
                'min_payout' => 100000,
            ])
            ->assertRedirect(route('admin.commission-settings.index'));

        $this->assertDatabaseHas('commission_settings', [
            'id' => $setting->id,
            'rate_percent' => 15,
            'min_payout' => 100000,
        ]);
    }

    public function test_validasi_rate_percent_maks_100(): void
    {
        $setting = CommissionSetting::factory()->create();

        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.commission-settings.update', $setting), [
                'rate_percent' => 150, // melebihi 100
                'min_payout' => 50000,
            ])
            ->assertSessionHasErrors('rate_percent');
    }
}
