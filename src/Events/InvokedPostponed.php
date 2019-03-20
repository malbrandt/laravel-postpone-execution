<?php

namespace Malbrandt\Laravel\PostponeInvoke\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event that occurs after postponed callable invoke.
 *
 * @author  Marek Malbrandt <marek.malbrandt@gmail.com>
 */
class InvokedPostponed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Callable that was invoked.
     *
     * @var callable
     */
    public $callable;
    /**
     * Resolved arguments (passed to callable during invoke).
     *
     * @var array
     */
    public $args;
    /**
     * Event name, that postpone was waiting for.
     *
     * @var string
     */
    public $event;
    /**
     * Results from callable invocation.
     *
     * @var mixed|null
     */
    public $result;
    /**
     * The name of postponed invoke (for further identification purposes).
     *
     * @var string|null
     */
    public $name;

    /**
     * Create a new event instance.
     *
     * @param callable     $callable
     * @param array        $args
     * @param string|mixed $event
     * @param mixed|null   $result
     * @param string|null  $name
     */
    public function __construct(
        $callable,
        $args,
        $event,
        $result = null,
        $name = null
    ) {
        $this->callable = $callable;
        $this->args = $args;
        $this->event = $event;
        $this->result = $result;
        $this->name = $name;
    }
}
