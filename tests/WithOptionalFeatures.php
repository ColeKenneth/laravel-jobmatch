<?php

namespace Tests;

use Laravel\Fortify\Features as FortifyFeatures;
use Laravel\Jetstream\Features as JetstreamFeatures;

trait WithOptionalFeatures
{
    use ConfiguresFeatureTests;

    protected function featureConfiguration(): array
    {
        return [
            'fortify.features' => array_values(array_unique([
                ...config('fortify.features', []),
                FortifyFeatures::emailVerification(),
            ])),
            'jetstream.features' => array_values(array_unique([
                ...config('jetstream.features', []),
                JetstreamFeatures::api(),
            ])),
        ];
    }
}
