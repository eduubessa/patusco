<?php

declare(strict_types=1);

use App\Models\User;

test('login screen can be rendered', function () {
    $response = $this->get('/login');
    $response->assertStatus(200);
});

test('users can authenticate using login screen', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertStatus(302);
    $response->assertRedirect(route('dashboard'));

    $this->assertAuthenticated();
});

test('users can\'t authenticate using login screen', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $response = $this->post('/login', [
        'username' => $user->username,
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertStatus(302);
    $response->assertRedirect(route('dashboard'));
});
