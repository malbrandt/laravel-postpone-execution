<?php

namespace Malbrandt\Laravel\PostponeInvoke;

use Illuminate\Support\Facades\Event;
use Malbrandt\Laravel\PostponeInvoke\Events\InvokedPostponed;

/**
 * Takes care of postponing callable invokes. It also invokes postponed
 * callables and raises proper events. Callables are invoked after dispatch of
 * given event. By default, it's a termination of kernel (the
 * `kernel.terminate` event), so invocation of callable would be performed
 * after sending response to the client).
 *
 * TODO: Some contracts for overloading?
 * TODO: More events/hooks?
 * TODO: DTOs/class wrappers around postponed callables?
 * TODO: Any suggestions? What to we need?
 *
 * @package Malbrandt\Laravel\PostponeInvoke
 * @author  Marek Malbrandt <marek.malbrandt@gmail.com>
 */
class InvokePostponer implements PostponesInvoke
{
    /**
     * Whether postponing is enabled. If turned off, all methods that should be
     * postponed would be called immediately.
     *
     * @var bool
     */
    private $enabled;
    /**
     * Whether to raise events.
     *
     * @var bool
     */
    private $raiseEvent;

    public function __construct()
    {
        $this->postponed = [];
        $this->enabled = env('POSTPONE_INVOKE_ENABLED', true);
        $this->raiseEvent = env('POSTPONE_INVOKE_EVENTS', true);
    }

    /**
     * Postpones invoke of a callable after sending the response (or other
     * event if specified), so it will not affect the response time. It
     * require valid PHP's callable.
     *
     * @param callable       $callable      Callable to postpone invoke. To get
     *                                      know what values you can pass,
     *                                      check PHP's manual:
     *                                      http://php.net/manual/en/language.types.callable.php.
     * @param array|\Closure $args          (Optional) Arguments passed as an
     *                                      array or callable that returns
     *                                      array with arguments. Closure would
     *                                      be called right before execution
     *                                      deferred function.
     *
     * @param string         $event         The name of event, after which
     *                                      callable should be invoked.
     * @param string|null    $name          Unique identifier of postpone (i.e.
     *                                      to identify it later in event, to
     *                                      get know how to deal with results).
     *
     * @see \postpone() Helper function (alias) for this method
     * @return InvokePostponer instance for chaining (fluent)
     */
    public function postpone(
        $callable,
        $args = [],
        $event = 'Malbrandt\Laravel\PostponeInvoke\Events\KernelTerminating',
        $name = null
    ) {
        $argsArray = $this->makeArgsArray($args);

        $invoker = function () use ($callable, &$argsArray, &$event, &$name) {
            $this->invoke($callable, $argsArray, $event, $name);
        };

        if ($this->enabled) {
            // Postpone invoke.
            Event::listen($event, $invoker);
        } else {
            // Invoke immediately.
            call_user_func($invoker);
        }

        return $this;
    }

    /**
     * Prepares arguments before postponed callable invoke. In practice, it
     * resolves arguments when they were passed as a Closure.
     *
     * @param \Closure|array|null $args
     *
     * @return array Array of resolved arguments.
     */
    protected function makeArgsArray($args)
    {
        switch (true) {
            case null === $args:
                return [];

            case is_callable($args):
                return [$args()];

            default:
                return [$args];
        }
    }

    /**
     * Invoke postponed callable.
     *
     * @param callable    $callable
     * @param array       $args
     * @param string|null $event
     * @param string|null $name
     */
    protected function invoke($callable, $args, $event = null, $name = null)
    {
        // Invoked postponed method
        $results = call_user_func_array($callable, $args);

        if ($this->raiseEvent) {
            event(
                new InvokedPostponed($callable, $args, $event, $results, $name)
            );
        }
    }

    /**
     * Turns on events raising.
     *
     * @param bool $enable
     *
     * @return InvokePostponer
     */
    public function eventsEnabled(bool $enable = true)
    {
        $this->raiseEvent = $enable;

        return $this;
    }

    /**
     * Getter/setter for enabling postponing callables. When disabled,
     * postpones invokes would be invoked immediately.
     *
     * @param bool|null $enabled
     *
     * @return $this|bool|mixed
     */
    public function isEnabled($enabled = null)
    {
        if (func_num_args() === 0) {
            return $this->enabled;
        } else {
            if (!is_bool($enabled)) {
                throw new \InvalidArgumentException('Invalid value passed. Tip: value must be of type boolean.');
            }
        }

        $this->enabled = $enabled;

        return $this;
    }
}
