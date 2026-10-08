<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebHookController extends Controller
{
    public function sallaHandle(Request $request)
    {
        $event = request()->event;
        $eventClass = 'App\\SallaWebHooks\\' . str_replace('.', '', ucwords($event, '.')) . 'Event';
        if (class_exists($eventClass))
            return (new $eventClass)->handle();
    }
}
