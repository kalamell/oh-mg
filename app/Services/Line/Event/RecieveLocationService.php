<?php

namespace App\Services\Line\Event;

use LINE\LINEBot;
use LINE\LINEBot\Event\MessageEvent\LocationMessage;

class RecieveLocationService
{
    /**
     * @var LineBot
     */
    private $bot;

    /**
     * Follow constructor.
     * @param LineBot $bot
     */
    public function __construct(LineBot $bot)
    {
        $this->bot = $bot;
    }

    /**
     * 登録
     * @param LocationMessage $event
     * @return string
     */
    public function execute(LocationMessage $event)
    {
        return "สถานที่ของท่านคือ：\n".$event->getAddress();
    }

}