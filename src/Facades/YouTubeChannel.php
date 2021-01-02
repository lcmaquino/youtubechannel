<?php

namespace Lcmaquino\YouTubeChannel\Facades;

use Lcmaquino\YouTubeChannel\YouTubeChannelManager;
use Illuminate\Support\Facades\Facade;

class YouTubeChannel extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { 
        return YouTubeChannelManager::class;
    }
}