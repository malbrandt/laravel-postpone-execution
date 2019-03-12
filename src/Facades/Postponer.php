<?php

namespace Malbrandt\Laravel\PostponeInvoke\Facades;

use Illuminate\Support\Facades\Facade;
use Malbrandt\Laravel\PostponeInvoke\PostponesInvoke;

/**
 * @package Malbrandt\Laravel\PostponeInvoke\Facades
 * @method static PostponesInvoke postpone($callable, $args, $event, $name)
 * @author  Marek Malbrandt <marek.malbrandt@gmail.com>
 */
class Postponer extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'postpone';
    }
}
