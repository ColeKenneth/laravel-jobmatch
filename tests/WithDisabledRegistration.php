<?php

namespace Tests;

use Laravel\Fortify\Features;

trait WithDisabledRegistration
{
    use ConfiguresFeatureTests;

    protected function featureConfiguration(): array
    {
        return [
            'fortify.features' => array_values(array_diff(
                config('fortify.features', []),
                [Features::registration()],
            )),
        ];
    }
}
