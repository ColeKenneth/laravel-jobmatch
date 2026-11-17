<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;

trait ConfiguresFeatureTests
{
    abstract protected function featureConfiguration(): array;

    public function createApplication()
    {
        $app = require Application::inferBasePath().'/bootstrap/app.php';

        $this->traitsUsedByTest = array_flip(class_uses_recursive(static::class));

        // Apply test configuration before providers register feature-dependent routes.
        $app->afterBootstrapping(LoadConfiguration::class, function ($app) {
            foreach ($this->featureConfiguration() as $key => $value) {
                $app['config']->set($key, $value);
            }
        });

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
