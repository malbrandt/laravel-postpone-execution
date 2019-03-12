<?php

namespace Malbrandt\Laravel\PostponeInvoke\Tests;

class FacadeTest extends TestCase
{
    /** @test */
    public function can_postpone_using_facade()
    {
        $this->mock('postponer', function ($mock) {
            $mock->shouldReceive('postpone')->once();
        });

        Postponer::postpone(function () {
            return 'foo';
        });
    }
}
