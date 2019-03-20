<?php

namespace Malbrandt\Laravel\PostponeInvoke\Tests;

use Malbrandt\Laravel\PostponeInvoke\ServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return ServiceProvider::class;
    }

    /**
     * Registers endpoint that returns current time, makes request and returns
     * the results.
     */
    protected function doRandomRequest(): string
    {
        $uri = 'test/show-current-time';
        app('router')->get($uri, function () {
            return (new \DateTime())->format('Y-m-d H:i:s');
        });

        return $this->get($uri)->getContent();
    }
}
