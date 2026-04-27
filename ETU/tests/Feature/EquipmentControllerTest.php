<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Role;
use App\Models\User;
use App\Models\Equipment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class EquipmentControllerTest extends TestCase
{
    use RefreshDatabase;

    //https://www.csrhymes.com/2020/01/15/keeping-tests-simple.html
    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'user']);
        Role::create(['name' => 'admin']);
    }

    public function test_equipment_throttle_until_exceeded(): void
    {
        $admin = User::factory()->create(['role_id' => Role::where('name', 'admin')->first()->id]);
        $category = Category::factory()->create();
        Sanctum::actingAs($admin);

        $payload = Equipment::factory()->make(['category_id' => $category->id])->toArray();

        for ($i = 0; $i < 60; $i++) {
            $this->postJson('/api/equipment', $payload)->assertStatus(CREATED);
        }

        $this->postJson('/api/equipment', $payload)->assertStatus(429);
    }

    public function test_store_creates_equipment_successfully(): void
    {
        $admin = User::factory()->create(['role_id' => Role::where('name', 'admin')->first()->id]);
        $category = Category::factory()->create();
        Sanctum::actingAs($admin);

        $payload = Equipment::factory()->make([
            'name' => 'Raquette Pro',
            'category_id' => $category->id
        ])->toArray();

        $response = $this->postJson('/api/equipment', $payload);

        $response->assertStatus(CREATED)
                 ->assertJsonPath('data.name', 'Raquette Pro');

        $this->assertDatabaseHas('equipment', $payload);
    }

    public function test_store_fails_with_invalid_payload(): void
    {
        $admin = User::factory()->create(['role_id' => Role::where('name', 'admin')->first()->id]);
        Sanctum::actingAs($admin);

        $this->postJson('/api/equipment', [])
               ->assertStatus(INVALID_DATA)
             ->assertJsonValidationErrors(['name', 'daily_price', 'category_id']);
    }

    public function test_store_requires_authentication(): void
    {
        $this->postJson('/api/equipment', [])->assertStatus(UNAUTHORIZED);
    }

    public function test_update_modifies_equipment_successfully(): void
    {
        $admin = User::factory()->create(['role_id' => Role::where('name', 'admin')->first()->id]);
        Sanctum::actingAs($admin);

        $equipment = Equipment::factory()->create();
        $updateData = [
            'name' => 'Nom Modifié',
            'description' => 'Nouvelle description',
            'daily_price' => 99,
            'category_id' => $equipment->category_id,
        ];

        $this->putJson("/api/equipment/{$equipment->id}", $updateData)
               ->assertStatus(OK)
             ->assertJsonPath('data.name', 'Nom Modifié');

        $this->assertDatabaseHas('equipment', $updateData);
    }

    public function test_update_returns_404_for_nonexistent_equipment(): void
    {
        $admin = User::factory()->create(['role_id' => Role::where('name', 'admin')->first()->id]);
        Sanctum::actingAs($admin);

        $category = Category::factory()->create();
        $payload = Equipment::factory()->make(['category_id' => $category->id])->toArray();

        $this->putJson('/api/equipment/9999', $payload)->assertStatus(NOT_FOUND);
    }

    public function test_destroy_deletes_equipment_successfully(): void
    {
        $admin = User::factory()->create(['role_id' => Role::where('name', 'admin')->first()->id]);
        Sanctum::actingAs($admin);

        $equipment = Equipment::factory()->create();

        $this->deleteJson("/api/equipment/{$equipment->id}")->assertStatus(NO_CONTENT);
        $this->assertDatabaseMissing('equipment', ['id' => $equipment->id]);
    }

    public function test_destroy_requires_authentication(): void
    {
        $this->deleteJson('/api/equipment/1')->assertStatus(UNAUTHORIZED);
    }
}
