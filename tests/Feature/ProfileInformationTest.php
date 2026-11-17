<?php

use App\Models\User;
use Laravel\Jetstream\Http\Livewire\UpdateProfileInformationForm;
use Livewire\Livewire;

test('current split name profile information is available', function () {
    $this->actingAs($user = User::factory()->create());

    $component = Livewire::test(UpdateProfileInformationForm::class);

    expect($component->state['first_name'])->toEqual($user->first_name);
    expect($component->state['middle_name'])->toEqual($user->middle_name);
    expect($component->state['last_name'])->toEqual($user->last_name);
    expect($component->state['email'])->toEqual($user->email);
});

test('profile information updates the real name columns', function () {
    $this->actingAs($user = User::factory()->create());

    Livewire::test(UpdateProfileInformationForm::class)
        ->set('state', [
            'first_name' => 'Test',
            'middle_name' => 'Middle',
            'last_name' => 'User',
            'email' => 'test@example.com',
        ])
        ->call('updateProfileInformation')
        ->assertHasNoErrors();

    expect($user->fresh())
        ->first_name->toEqual('Test')
        ->middle_name->toEqual('Middle')
        ->last_name->toEqual('User')
        ->full_name->toEqual('Test Middle User')
        ->email->toEqual('test@example.com');
});

test('a middle name may be left blank', function () {
    $this->actingAs($user = User::factory()->create());

    Livewire::test(UpdateProfileInformationForm::class)
        ->set('state', ['first_name' => 'Test', 'last_name' => 'User', 'email' => $user->email])
        ->call('updateProfileInformation')
        ->assertHasNoErrors();

    expect($user->fresh()->middle_name)->toBeNull();
});

test('changing the contact email clears its previous verification timestamp', function () {
    $this->actingAs($user = User::factory()->create(['email_verified_at' => now()]));

    Livewire::test(UpdateProfileInformationForm::class)
        ->set('state.email', 'changed@example.test')
        ->call('updateProfileInformation')
        ->assertHasNoErrors();

    expect($user->fresh()->email_verified_at)->toBeNull();
});

test('an existing email cannot be claimed by another profile', function () {
    $other = User::factory()->create(['email' => 'taken@example.test']);
    $this->actingAs($user = User::factory()->create());
    $originalEmail = $user->email;

    Livewire::test(UpdateProfileInformationForm::class)
        ->set('state.email', $other->email)
        ->call('updateProfileInformation')
        ->assertHasErrors(['email']);

    expect($user->fresh()->email)->toBe($originalEmail);
});

test('profile updates cannot change a users role or active status', function () {
    $this->actingAs($user = User::factory()->student()->create(['is_active' => true]));
    $originalRole = $user->role;

    Livewire::test(UpdateProfileInformationForm::class)
        ->set('state.first_name', 'Updated')
        ->set('state.role', 'admin')
        ->set('state.is_active', false)
        ->call('updateProfileInformation')
        ->assertHasNoErrors();

    expect($user->fresh()->role)->toBe($originalRole);
    expect($user->fresh()->is_active)->toBeTrue();
});
