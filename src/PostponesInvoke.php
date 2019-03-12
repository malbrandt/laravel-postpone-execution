<?php

namespace Malbrandt\Laravel\PostponeInvoke;


interface PostponesInvoke
{
    /**
     * Postpones invoke of an callable to a moment of event occur. By default,
     * it is `kernel.terminate` event, which is executed after sending response
     * to the client. This allows you not to extend the response time.
     *
     * @param callable       $callable
     * @param mixed|callable $args
     * @param string         $event
     * @param string|null    $name
     */
    public function postpone(
        $callable,
        $args = [],
        $event = 'kernel.terminate',
        $name = null
    );
}