<?php

namespace Tests\Feature;

use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function sekolah(string $npsn): Sekolah
    {
        return Sekolah::create([
            'npsn' => $npsn,
            'nama' => "Sekolah $npsn",
            'jenjang' => 'SD',
            'status' => 'Negeri',
            'alamat' => 'Jl. Uji',
            'kecamatan' => 'Uji',
            'latitude' => -3.3,
            'longitude' => 114.59,
        ]);
    }

    private function user(string $role, ?Sekolah $sekolah = null): User
    {
        return User::factory()->create([
            'role' => $role,
            'sekolah_id' => $sekolah?->id,
        ]);
    }

    public function test_tamu_dialihkan_ke_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_login_gagal_dan_berhasil(): void
    {
        $u = $this->user('dinas');

        $this->post('/login', [
            'email' => $u->email,
            'password' => 'salah',
        ])->assertSessionHasErrors('email');

        $this->post('/login', [
            'email' => $u->email,
            'password' => 'password',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($u);
    }

    public function test_admin_hanya_mengelola_pengguna(): void
    {
        $admin = $this->user('admin');

        $this->actingAs($admin)->get('/admin/users')->assertOk();
        $this->actingAs($admin)->get('/dinas/sekolah')->assertForbidden();
    }

    public function test_admin_menambah_pengguna_sekolah(): void
    {
        $sekolah = $this->sekolah('11111111');

        $this->actingAs($this->user('admin'))->post('/admin/users', [
            'name' => 'Operator',
            'email' => 'op@uji.test',
            'role' => 'sekolah',
            'sekolah_id' => $sekolah->id,
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertRedirect('/admin/users');

        $this->assertDatabaseHas('users', [
            'email' => 'op@uji.test',
            'role' => 'sekolah',
            'sekolah_id' => $sekolah->id,
        ]);
    }

    public function test_role_sekolah_wajib_memilih_sekolah(): void
    {
        $this->actingAs($this->user('admin'))->post('/admin/users', [
            'name' => 'Operator',
            'email' => 'op@uji.test',
            'role' => 'sekolah',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertSessionHasErrors('sekolah_id');
    }

    public function test_dinas_mengelola_semua_sekolah(): void
    {
        $a = $this->sekolah('11111111');
        $dinas = $this->user('dinas');

        $this->actingAs($dinas)->get('/dinas/sekolah')->assertOk();
        $this->actingAs($dinas)->get("/sekolah/{$a->id}/edit")->assertOk();
        $this->actingAs($dinas)->get("/sekolah/{$a->id}/profil")->assertOk();
        $this->actingAs($dinas)->get('/admin/users')->assertForbidden();
    }

    public function test_sekolah_hanya_mengakses_sekolah_sendiri(): void
    {
        $a = $this->sekolah('11111111');
        $b = $this->sekolah('22222222');
        $op = $this->user('sekolah', $a);

        $this->actingAs($op)->get("/sekolah/{$a->id}/edit")->assertOk();
        $this->actingAs($op)->get("/sekolah/{$b->id}/edit")->assertForbidden();
        $this->actingAs($op)->get("/sekolah/{$b->id}/profil")->assertForbidden();
        $this->actingAs($op)->get('/dinas/sekolah')->assertForbidden();
        $this->actingAs($op)->get('/admin/users')->assertForbidden();
    }

    public function test_sekolah_tidak_dapat_mengubah_npsn(): void
    {
        $a = $this->sekolah('11111111');
        $op = $this->user('sekolah', $a);

        $this->actingAs($op)->put("/sekolah/{$a->id}", [
            'npsn' => '99999999',
            'nama' => 'Nama Baru',
            'jenjang' => 'SD',
            'status' => 'Negeri',
            'alamat' => 'Jl. Baru',
            'kecamatan' => 'Uji',
            'latitude' => -3.31,
            'longitude' => 114.6,
        ])->assertRedirect();

        $a->refresh();

        $this->assertSame('11111111', $a->npsn);
        $this->assertSame('Nama Baru', $a->nama);
    }

    public function test_sekolah_menyimpan_profil_sendiri(): void
    {
        $a = $this->sekolah('11111111');
        $op = $this->user('sekolah', $a);

        $this->actingAs($op)->put("/sekolah/{$a->id}/profil", [
            'kepala_sekolah' => 'Bapak Uji',
            'akreditasi' => 'A',
        ])->assertRedirect();

        $this->assertDatabaseHas('profil_sekolahs', [
            'sekolah_id' => $a->id,
            'kepala_sekolah' => 'Bapak Uji',
        ]);
    }

    public function test_sekolah_tidak_dapat_menghapus_sekolah(): void
    {
        $a = $this->sekolah('11111111');
        $op = $this->user('sekolah', $a);

        $this->actingAs($op)->delete("/dinas/sekolah/{$a->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('sekolahs', [
            'id' => $a->id,
        ]);
    }
}
