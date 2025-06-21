<?php

namespace App\WebSockets;

use BeyondCode\LaravelWebSockets\Statistics\Logger\StatisticsLogger;
use BeyondCode\LaravelWebSockets\WebSockets\Channels\ChannelManager;
use React\Http\Browser;

class NullStatisticsLogger implements StatisticsLogger
{
    protected ChannelManager $channelManager;
    protected Browser $browser;

    public function __construct(ChannelManager $channelManager, Browser $browser)
    {
        $this->channelManager = $channelManager;
        $this->browser = $browser;
    }

    public function webSocketMessage($appId)
    {
        // Do nothing
    }

    public function apiMessage($appId)
    {
        // Do nothing
    }

    public function connection($appId)
    {
        // Do nothing
    }

    public function disconnection($appId)
    {
        // Do nothing
    }

    public function collectStatistics()
    {
        // Do nothing
    }

    public function save()
    {
        // Do nothing
    }
}
