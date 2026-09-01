<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\BellyPhoto;
use App\Models\Pregnancy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PhotosAndChecklistTest extends TestCase
{
    use RefreshDatabase;

    private function actingWithPregnancy(): Pregnancy
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        return Pregnancy::factory()->for($user)->create([
            'method' => 'lmp',
            'input_date' => now()->subDays(168)->toDateString(),
        ]);
    }

    #[Test]
    public function a_photo_is_stored_off_the_public_disk(): void
    {
        Storage::fake(BellyPhoto::DISK);
        $this->actingWithPregnancy();

        $this->postJson('/api/v1/belly-photos', [
            'photo' => UploadedFile::fake()->image('karin.jpg', 800, 1000),
            'week' => 24,
        ])->assertCreated();

        $photo = BellyPhoto::sole();

        Storage::disk(BellyPhoto::DISK)->assertExists($photo->path);
        // Sağlık verisi: herkese açık diskte durmaz.
        $this->assertStringStartsWith('belly/', $photo->path);
    }

    #[Test]
    public function only_the_owner_can_fetch_the_file(): void
    {
        Storage::fake(BellyPhoto::DISK);
        $this->actingWithPregnancy();

        $this->postJson('/api/v1/belly-photos', [
            'photo' => UploadedFile::fake()->image('karin.jpg'),
            'week' => 24,
        ])->assertCreated();

        $photo = BellyPhoto::sole();

        $this->get("/api/v1/belly-photos/{$photo->id}/file")->assertOk();

        // Başka bir kullanıcı dosyaya ulaşamaz.
        $this->actingAs(User::factory()->create(), 'sanctum');
        $this->get("/api/v1/belly-photos/{$photo->id}/file")->assertStatus(404);
    }

    #[Test]
    public function deleting_a_photo_removes_the_file(): void
    {
        Storage::fake(BellyPhoto::DISK);
        $this->actingWithPregnancy();

        $this->postJson('/api/v1/belly-photos', [
            'photo' => UploadedFile::fake()->image('karin.jpg'),
            'week' => 24,
        ])->assertCreated();

        $photo = BellyPhoto::sole();
        $path = $photo->path;

        $this->deleteJson("/api/v1/belly-photos/{$photo->id}")->assertNoContent();

        // Yetim dosya birikmesin.
        Storage::disk(BellyPhoto::DISK)->assertMissing($path);
    }

    #[Test]
    public function non_images_are_rejected(): void
    {
        Storage::fake(BellyPhoto::DISK);
        $this->actingWithPregnancy();

        $this->postJson('/api/v1/belly-photos', [
            'photo' => UploadedFile::fake()->create('rapor.pdf', 100, 'application/pdf'),
            'week' => 24,
        ])->assertStatus(422);

        $this->assertDatabaseCount('belly_photos', 0);
    }

    #[Test]
    public function the_hospital_bag_list_arrives_ready(): void
    {
        $this->actingWithPregnancy();

        $response = $this->getJson('/api/v1/checklist')->assertOk();

        $this->assertGreaterThan(10, count($response->json('data')));
        $this->assertContains('bebek', array_column($response->json('data'), 'group'));
        $this->assertContains('Bebek oto koltuğu (çıkış için zorunlu)', array_column($response->json('data'), 'title'));
    }

    #[Test]
    public function the_template_is_only_seeded_once(): void
    {
        $this->actingWithPregnancy();

        $first = count($this->getJson('/api/v1/checklist')->json('data'));
        $second = count($this->getJson('/api/v1/checklist')->json('data'));

        $this->assertSame($first, $second);
    }

    #[Test]
    public function an_item_can_be_ticked_and_added(): void
    {
        $this->actingWithPregnancy();
        $items = $this->getJson('/api/v1/checklist')->json('data');

        $this->patchJson("/api/v1/checklist/{$items[0]['id']}", ['is_done' => true])
            ->assertOk()
            ->assertJsonPath('data.is_done', true);

        $this->postJson('/api/v1/checklist', ['title' => 'Fotoğraf makinesi', 'group' => 'anne'])
            ->assertCreated();

        $this->assertCount(count($items) + 1, $this->getJson('/api/v1/checklist')->json('data'));
    }

    #[Test]
    public function a_user_cannot_touch_another_users_checklist(): void
    {
        $this->actingWithPregnancy();
        $item = $this->getJson('/api/v1/checklist')->json('data.0');

        $this->actingAs(User::factory()->create(), 'sanctum');

        $this->patchJson("/api/v1/checklist/{$item['id']}", ['is_done' => true])->assertStatus(404);
        $this->deleteJson("/api/v1/checklist/{$item['id']}")->assertStatus(404);
    }
}
