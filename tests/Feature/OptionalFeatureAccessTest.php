<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features as FortifyFeatures;
use Laravel\Jetstream\Features as JetstreamFeatures;

test('API token management is unavailable in the default application', function () {
    expect(JetstreamFeatures::hasApiFeatures())->toBeFalse();
    expect(Route::has('api-tokens.index'))->toBeFalse();

    $this->actingAs(User::factory()->create())->get('/user/api-tokens')->assertNotFound();
});

test('email verification endpoints are unavailable in the default application', function () {
    expect(FortifyFeatures::enabled(FortifyFeatures::emailVerification()))->toBeFalse();
    expect(Route::has('verification.notice'))->toBeFalse();
    expect(Route::has('verification.verify'))->toBeFalse();
    expect(Route::has('verification.send'))->toBeFalse();

    $this->actingAs(User::factory()->create())->get('/email/verify')->assertNotFound();
    $this->post('/email/verification-notification')->assertNotFound();
});
