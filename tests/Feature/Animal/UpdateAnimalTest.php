<?php


use App\Helpers\Enums\UserRoles;
use App\Models\Animal;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('admin can access to update animal screen ', function () {
    $animal = Animal::factory()->create();
    $user = User::factory()->verified()->create(['role' => UserRoles::Admin->value]);

    actingAs($user)
        ->get("animals/{$animal}/edit")
        ->assertStatus(200)
        ->assertOk();
});


test('receptionist can access to update animal screen', function () {
    $animal = Animal::factory()->create();
    $user = User::factory()->verified()->create(['role' => UserRoles::Receptionist->value]);

    actingAs($user)
        ->get("animals/{$animal}/edit")
        ->assertStatus(200)
        ->assertOk();
});

test('customer can access to update animal screen', function () {
    $animal = Animal::factory()->create();
    $user = User::factory()->verified()->create(['role' => UserRoles::Customer->value]);

    actingAs($user)
        ->get("animals/{$animal}/edit")
        ->assertStatus(200)
        ->assertOk();
});

test('doctor can access to update animal screen', function () {
    $animal = Animal::factory()->create();
    $user = User::factory()->verified()->create(['role' => UserRoles::Doctor->value]);

    actingAs($user)
        ->get("animals/{$animal}/edit")
        ->assertStatus(200)
        ->assertOk();
});

test('unverified user cannot access to update animal screen', function () {
    $animal = Animal::factory()->create();
    $user = User::factory()->unverified()->create(['role' => UserRoles::Doctor->value]);

    actingAs($user)
        ->get("animals/{$animal}/edit")
        ->assertStatus(302)
        ->assertRedirect('/email/verify');
});

test('guest cannot access to update animal screen', function () {
    $animal = Animal::factory()->create();

    get("animals/{$animal}/edit")
        ->assertStatus(302)
        ->assertRedirect('/login');
});

test("admin can update animal", function () {
    $animal = Animal::factory()->create();
    $user = User::factory()->verified()->create(['role' => UserRoles::Admin->value]);
    $customer = User::factory()->verified()->create(['role' => UserRoles::Customer->value]);

    actingAs($user)
        ->put("animals/{$animal->slug}", [
            'name' => 'Animal Fake Test',
            'gender' => 'm',
            'birthday' => '2020-06-05',
            'species' => 'Specie One',
            'breed' => 'Breed Four',
            'owner' => $customer->username
        ])
        ->assertStatus(302)
        ->assertRedirect("animals/{$animal->slug}");

    $this->assertDatabaseHas('animals', [
        'name' => 'Animal Fake Test',
        'sex' => 'm',
        'birthday' => '2020-06-05',
        'species' => 'Specie One',
        'breed' => 'Breed Four'
    ]);
});
