<?php


if (!function_exists('postpone')) {
    /**
     * Postpones invoke of a callable after sending the response, so it will not
     * affect the response time. You need to specify valid PHP's callable.
     *
     * @param callable            $function Function to be deferred. Too get know what values you
     *                                      can pass, check PHP's manual:
     *                                      http://php.net/manual/en/language.types.callable.php.
     * @param array|\Closure|null $args     (Optional) Arguments passed as an array or callable
     *                                      that returns array with arguments. Callable would be executed
     *                                      right before execution deferred function.
     *
     * @return \Malbrandt\Laravel\PostponeInvoke\InvokePostponer
     *
     * @see     \postponer()
     */
    function postpone(
        $callable,
        $args = [],
        $event = 'Malbrandt\Laravel\PostponeInvoke\Events\KernelTerminating',
        $name = null
    ) {
        return postponer()->postpone($callable, $args, $event, $name);
    }
}
if (!function_exists('postponer')) {
    /**
     * Returns (singleton) instance of PostponedInvoke impl. Helper alias function.
     *
     * @return \Malbrandt\Laravel\PostponeInvoke\InvokePostponer
     *
     * @see   \postpone()
     */
    function postponer()
    {
        return app('postponer');
    }
}
