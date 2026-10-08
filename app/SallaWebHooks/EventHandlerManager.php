<?php

namespace App\SallaWebHooks;

abstract class EventHandlerManager
{
    protected $event;

    public function __construct()
    {
        $this->event = request()->event;
    }

    abstract public function process(): mixed;

    public function handle()
    {
        return $this->process();
    }
}
