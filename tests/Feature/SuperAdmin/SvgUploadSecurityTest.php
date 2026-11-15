<?php

declare(strict_types=1);

namespace Tests\Feature\SuperAdmin;

use App\Enums\RoleEnum;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SvgUploadSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);

        $this->superAdmin = User::factory()->create([
            'role' => RoleEnum::SuperAdmin,
            'email_verified_at' => now(),
            'must_change_password' => false,
            'status_aktif' => true,
        ]);
    }

    public function test_superadmin_cannot_upload_clean_or_malicious_svg(): void
    {
        Storage::fake('public');

        $svgPayloads = [
            'normal.svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"><circle cx="50" cy="50" r="40"/></svg>',
            'script.svg' => '<svg xmlns="http://www.w3.org/2000/svg"><script>alert("XSS")</script></svg>',
            'onload.svg' => '<svg xmlns="http://www.w3.org/2000/svg" onload="alert(1)"><circle cx="10" cy="10" r="10"/></svg>',
            'foreign_object.svg' => '<svg xmlns="http://www.w3.org/2000/svg"><foreignObject width="100" height="100"><body xmlns="http://www.w3.org/1999/xhtml"><script>alert(1)</script></body></foreignObject></svg>',
            'onclick.svg' => '<svg xmlns="http://www.w3.org/2000/svg" onclick="fetch(\'/steal\')"><rect width="50" height="50"/></svg>',
        ];

        foreach ($svgPayloads as $filename => $content) {
            $file = UploadedFile::fake()->createWithContent($filename, $content);

            $response = $this->actingAs($this->superAdmin)
                ->postJson(route('superadmin.site-editor.media.upload'), [
                    'file' => $file,
                ]);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors('file');
        }
    }

    public function test_svg_disguised_with_png_extension_is_rejected(): void
    {
        Storage::fake('public');

        $disguisedSvg = UploadedFile::fake()->createWithContent(
            'avatar.png',
            '<svg xmlns="http://www.w3.org/2000/svg"><script>alert("Bypass")</script></svg>'
        );

        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.media.upload'), [
                'file' => $disguisedSvg,
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('file');
    }

    public function test_valid_raster_images_still_succeed(): void
    {
        Storage::fake('public');

        $validPng = UploadedFile::fake()->image('banner.png', 400, 300);

        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.media.upload'), [
                'file' => $validPng,
                'alt_text' => 'Banner Resmi',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertNotNull($response->json('item.id'));

        $savedPath = $response->json('item.path');
        Storage::disk('public')->assertExists($savedPath);
    }
}
