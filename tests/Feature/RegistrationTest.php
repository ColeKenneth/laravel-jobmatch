<?php

use Laravel\Jetstream\Jetstream;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'first_name' => 'Test',
        'middle_name' => 'Example',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'role' => 'student',
        'password' => 'password',
        'password_confirmation' => 'password',
        'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature(),
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertDatabaseHas('users', [
        'first_name' => 'Test',
        'middle_name' => 'Example',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'role' => 'student',
        'is_active' => true,
    ]);
});

test('new users cannot self-register with a staff role', function () {
    $response = $this->post('/register', [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'role' => 'admin',
        'password' => 'password',
        'password_confirmation' => 'password',
        'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature(),
    ]);

    $response->assertSessionHasErrors('role');
    $this->assertGuest();
    $this->assertDatabaseMissing('users', [
        'email' => 'test@example.com',
    ]);
});
