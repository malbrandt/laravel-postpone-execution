<?php

namespace Malbrandt\Laravel\PostponeInvoke\Tests;

use Malbrandt\Laravel\PostponeInvoke\Events\KernelTerminating;

class ServiceProviderTest extends TestCase
{
    /** @test */
    public function dispatches_kernel_terminating_event_after_response()
    {
        $this->expectsEvents(KernelTerminating::class);
        $this->doRandomRequest();
    }
}
