<?php

namespace Malbrandt\Laravel\PostponeInvoke\Tests;

use Illuminate\Support\Facades\Event;
use Malbrandt\Laravel\PostponeInvoke\Events\InvokedPostponed;

/**
 * Handles execution postponing, invokes postponed methods and raises proper
 * events.
 *
 * @package Malbrandt\Lori\Utils
 * @author  Marek Malbrandt <marek.malbrandt@gmail.com>
 * @see     \postponer()
 * @see     \postpone()
 */
class InvokePostponerTest extends TestCase
{
    public static $GLOBAL_VARIABLE = 'foo';

    /** @test */
    public function invokes_callable_after_sending_the_response()
    {
        if (($router = app('router')) === null) {
            $this->markTestIncomplete('Cannot resolve router instance.');
        }
        $router->get($uri = 'test/show-global-variable', function () {
            return InvokePostponerTest::$GLOBAL_VARIABLE;
        });
        // Defer the moment of changing global variable's value.
        // The value should be changed only after sending response to the client.
        postpone(function () {
            InvokePostponerTest::$GLOBAL_VARIABLE = 'bar';
        });

        $this->assertNotEquals(
            'bar',
            InvokePostponerTest::$GLOBAL_VARIABLE,
            'Value was changed immediately.'
        );

        // Do some request. After handling the requsts, deferred callables
        // should be executed, so global variable value should change.
        $this->get($uri)->getContent();

        $this->assertEquals(
            'bar',
            InvokePostponerTest::$GLOBAL_VARIABLE,
            'Value remains unchanged.'
        );
    }

    /** @test */
    public function invoke_methods_immediately_when_postponing_is_turned_off()
    {
        postponer()->isEnabled(false);
        $this->assertEquals('foo', InvokePostponerTest::$GLOBAL_VARIABLE);

        postpone(function () {
            InvokePostponerTest::$GLOBAL_VARIABLE = 'bar';
        });

        $this->assertEquals(
            'bar',
            InvokePostponerTest::$GLOBAL_VARIABLE,
            'Value was not changed immediately.'
        );
    }

    /** @test */
    public function can_pass_arguments_as_scalar()
    {
        postpone(function ($args) {
            return $args;
        }, 'foo');
        Event::listen(InvokedPostponed::class, function ($event) {
            $this->assertEquals('foo', $event->result);
        });
        $this->doRandomRequest();
    }

    /** @test */
    public function can_pass_arguments_as_array()
    {
        postpone(function ($args) {
            return $args;
        }, ['bar']);
        Event::listen(InvokedPostponed::class, function ($event) {
            $this->assertEquals(['bar'], $event->result);
        });
        $this->doRandomRequest();
    }

    /** @test */
    public function can_pass_arguments_as_a_closure_that_returns_scalar()
    {
        postpone(function ($args) {
            return $args;
        }, function () {
            return 'biz';
        });
        Event::listen(InvokedPostponed::class, function ($event) {
            $this->assertEquals('biz', $event->result);
        });
        $this->doRandomRequest();
    }

    /** @test */
    public function can_pass_arguments_as_a_closure_that_returns_an_array()
    {
        postpone(function ($args) {
            return $args;
        }, function () {
            return ['baz'];
        });
        Event::listen(InvokedPostponed::class, function ($event) {
            $this->assertEquals(['baz'], $event->result);
        });
        $this->doRandomRequest();
    }

    protected function setUp(): void
    {
        parent::setUp();
        InvokePostponerTest::$GLOBAL_VARIABLE = 'foo';
    }
}
