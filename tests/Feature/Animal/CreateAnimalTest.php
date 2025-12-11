<?php

declare(strict_types=1);

use App\Helpers\Enums\UserRoles;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('admin can access to create animal screen ', function () {
    $user = User::factory()->verified()->create(['role' => UserRoles::Admin->value]);

    actingAs($user)
        ->get('animals/new')
        ->assertStatus(200)
        ->assertOk();
});

test('receptionist can access to create animal screen', function () {
    $user = User::factory()->verified()->create(['role' => UserRoles::Receptionist->value]);

    actingAs($user)
        ->get('animals/new')
        ->assertStatus(200)
        ->assertOk();
});

test('customer can access to create animal screen', function () {
    $user = User::factory()->verified()->create(['role' => UserRoles::Customer->value]);

    actingAs($user)
        ->get('animals/new')
        ->assertStatus(200)
        ->assertOk();
});

test('doctor cannot access to create animal screen', function () {
    $user = User::factory()->verified()->create(['role' => UserRoles::Doctor->value]);

    actingAs($user)
        ->get('animals/new')
        ->assertStatus(403)
        ->assertForbidden();
});

test('unverified user cannot access to create animal screen', function () {
    $user = User::factory()->unverified()->create(['role' => UserRoles::Customer->value]);

    actingAs($user)
        ->get('animals/new')
        ->assertStatus(302)
        ->assertRedirect('/email/verify');
});

test('guest cannot access to create animal screen', function () {
    get('animals/new')
        ->assertStatus(302)
        ->assertRedirect('/login');
});

test('admin can create new animal', function () {
    $user = User::factory()->verified()->create(['role' => UserRoles::Admin->value]);
    $customer = User::factory()->verified()->create(['role' => UserRoles::Customer->value]);

    actingAs($user)
        ->post('animals', [
            'name' => 'Animal Fake Test',
            'gender' => 'm',
            'birthday' => '2020-06-05',
            'species' => 'Specie One',
            'breed' => 'Breed Four',
            'owner' => $customer->username,
        ])
        ->assertStatus(302)
        ->assertSessionHas('success', 'Animal criado com sucesso.');

    $this->assertDatabaseHas('animals', [
        'name' => 'Animal Fake Test',
        'sex' => 'm',
        'birthday' => '2020-06-05',
        'species' => 'Specie One',
        'breed' => 'Breed Four',
    ]);
});

test('receptionist can create new animal', function () {
    $user = User::factory()->verified()->create(['role' => UserRoles::Receptionist->value]);
    $customer = User::factory()->verified()->create(['role' => UserRoles::Customer->value]);

    actingAs($user)
        ->post('animals', [
            'name' => 'Animal Fake Test',
            'gender' => 'm',
            'birthday' => '2020-06-05',
            'species' => 'Specie One',
            'breed' => 'Breed Four',
            'owner' => $customer->username,
        ])
        ->assertStatus(302)
        ->assertSessionHas('success', 'Animal criado com sucesso.');

    $this->assertDatabaseHas('animals', [
        'name' => 'Animal Fake Test',
        'sex' => 'm',
        'birthday' => '2020-06-05',
        'species' => 'Specie One',
        'breed' => 'Breed Four',
    ]);
});

test('customer can create new animal', function () {
    $user = User::factory()->verified()->create(['role' => UserRoles::Customer->value]);

    actingAs($user)
        ->post('animals', [
            'name' => 'Animal Fake Test',
            'gender' => 'm',
            'birthday' => '2020-06-05',
            'species' => 'Specie One',
            'breed' => 'Breed Four',
        ])
        ->assertStatus(302)
        ->assertSessionHas('success', 'Animal criado com sucesso.');

    $this->assertDatabaseHas('animals', [
        'name' => 'Animal Fake Test',
        'sex' => 'm',
        'birthday' => '2020-06-05',
        'species' => 'Specie One',
        'breed' => 'Breed Four',
    ]);
});

test('admin cannot assign invalid customer as animal owner', function () {
    $user = User::factory()->verified()->create(['role' => UserRoles::Admin->value]);

    actingAs($user)
        ->post('animals', [
            'name' => 'Animal Fake Test',
            'gender' => 'm',
            'birthday' => '2020-06-05',
            'species' => 'Specie One',
            'breed' => 'Breed Four',
            'owner' => 'non-existent-customer',
        ])
        ->assertStatus(302)
        ->assertRedirectBack()
        ->assertSessionHasErrors([
            'owner' => 'The selected owner is invalid.',
        ]);

    $this->assertDatabaseMissing('animals', [
        'name' => 'Animal Fake Test',
        'sex' => 'm',
        'birthday' => '2020-06-05',
        'species' => 'Specie One',
        'breed' => 'Breed Four',
    ]);
});

test('receptionist cannot assign invalid customer as animal owner', function () {
    $user = User::factory()->verified()->create(['role' => UserRoles::Receptionist->value]);

    actingAs($user)
        ->post('animals', [
            'name' => 'Animal Fake Test',
            'gender' => 'm',
            'birthday' => '2020-06-05',
            'species' => 'Specie One',
            'breed' => 'Breed Four',
            'owner' => 'non-existent-customer',
        ])
        ->assertStatus(302)
        ->assertRedirectBack()
        ->assertSessionHasErrors([
            'owner' => 'The selected owner is invalid.',
        ]);

    $this->assertDatabaseMissing('animals', [
        'name' => 'Animal Fake Test',
        'sex' => 'm',
        'birthday' => '2020-06-05',
        'species' => 'Specie One',
        'breed' => 'Breed Four',
    ]);
});

test('admin cannot create animal with invalid birthday', function () {
    $user = User::factory()->verified()->create(['role' => UserRoles::Admin->value]);
    $customer = User::factory()->verified()->create(['role' => UserRoles::Customer->value]);

    actingAs($user)
        ->post('animals', [
            'name' => 'Animal Fake Test',
            'gender' => 'm',
            'birthday' => 'invalid-birthday',
            'species' => 'Specie One',
            'breed' => 'Breed Four',
            'owner' => $customer->username,
        ])
        ->assertStatus(302)
        ->assertRedirectBack()
        ->assertSessionHasErrors(['birthday' => 'The birthday field must be a valid date.']);

    $this->assertDatabaseMissing('animals', [
        'name' => 'Animal Fake Test',
    ]);
});

test('receptionist cannot create animal with invalid birthday', function () {
    $user = User::factory()->verified()->create(['role' => UserRoles::Receptionist->value]);
    $customer = User::factory()->verified()->create(['role' => UserRoles::Customer->value]);

    actingAs($user)
        ->post('animals', [
            'name' => 'Animal Fake Test',
            'gender' => 'm',
            'birthday' => 'invalid-birthday',
            'species' => 'Specie One',
            'breed' => 'Breed Four',
            'owner' => $customer->username,
        ])
        ->assertStatus(302)
        ->assertRedirectBack()
        ->assertSessionHasErrors(['birthday' => 'The birthday field must be a valid date.']);

    $this->assertDatabaseMissing('animals', [
        'name' => 'Animal Fake Test',
    ]);
});

test('customer cannot create animal with invalid birthday', function () {
    $user = User::factory()->verified()->create(['role' => UserRoles::Receptionist->value]);

    actingAs($user)
        ->post('animals', [
            'name' => 'Animal Fake Test',
            'gender' => 'm',
            'birthday' => 'invalid-birthday',
            'species' => 'Specie One',
            'breed' => 'Breed Four',
        ])
        ->assertStatus(302)
        ->assertRedirectBack()
        ->assertSessionHasErrors(['birthday' => 'The birthday field must be a valid date.']);

    $this->assertDatabaseMissing('animals', [
        'name' => 'Animal Fake Test',
    ]);
});
