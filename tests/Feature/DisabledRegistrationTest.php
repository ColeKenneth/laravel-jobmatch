<?php

use Tests\WithDisabledRegistration;

pest()->use(WithDisabledRegistration::class);

test('registration endpoints are unavailable when registration is disabled', function () {
    $this->get('/register')->assertNotFound();
    $this->post('/register', [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'disabled@example.com',
        'role' => 'student',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertNotFound();

    $this->assertGuest();
    $this->assertDatabaseMissing('users', ['email' => 'disabled@example.com']);
});
