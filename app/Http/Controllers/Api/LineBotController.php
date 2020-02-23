<?php

namespace App\Http\Controllers\Api;

use App\Facades\LineBot as FacadesLineBot;
use App\Services\Line\Event\RecieveLocationService;
use App\Services\Line\Event\RecieveTextService;
use App\Services\Line\Event\FollowService;
use Illuminate\Http\Request;
use LINE\LINEBot;

class LineBotController
{
    public function verify() {
        return 'verify ok';
    }
    /**
     * callback from LINE Message API(webhook)
     * @param Request $request
     * @throws LINEBot\Exception\InvalidSignatureException
     */
    public function callback(Request $request)
    {

        /** @var LINEBot $bot */
        $bot = app('line-bot');

        $signature = $_SERVER['HTTP_'.LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];
        if (!LINEBot\SignatureValidator::validateSignature($request->getContent(), env('LINE_CHANNEL_SECRET'), $signature)) {
            logger()->info('recieved from difference line-server');
            abort(400, 'Unauthorized action.');
        }

        $events = $bot->parseEventRequest($request->getContent(), $signature);

        /** @var LINEBot\Event\BaseEvent $event */
        foreach ($events as $event) {
            $reply_token = $event->getReplyToken();
            $reply_message = 'その操作はサポートしてません。.[' . get_class($event) . '][' . $event->getType() . ']';

            switch (true){
                case $event instanceof LINEBot\Event\FollowEvent:
                    $service = new FollowService($bot);
                    $reply_message = $service->execute($event)
                        ? 'เป็นเพื่อนกันแล้ว !!'
                        : 'ฉันลงทะเบียนเป็นเพื่อน แต่ฉันไม่ได้ทำอะไรเพราะขั้นตอนการลงทะเบียนล้มเหลว';

                    break;
                case $event instanceof LINEBot\Event\MessageEvent\TextMessage:
                    $service = new RecieveTextService($bot);
                    $reply_message = $service->execute($event);
                    break;


                case $event instanceof LINEBot\Event\MessageEvent\LocationMessage:
                    $service = new RecieveLocationService($bot);
                    $reply_message = $service->execute($event);
                    break;


                case $event instanceof LINEBot\Event\PostbackEvent:
                    break;

                case $event instanceof LINEBot\Event\UnfollowEvent:
                    break;
                default:
                    $body = $event->getEventBody();
                    logger()->warning('Unknown event. ['. get_class($event) . ']', compact('body'));
            }

            $bot->replyText($reply_token, $reply_message);
        }
    }
}