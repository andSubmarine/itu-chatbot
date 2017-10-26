<?php

namespace App\Traits;

use GuzzleHttp\Client;

trait Responds
{
    public function sendMessage($message, $id)
    {
        $guzzle = new Client([
            'timeout'  => 5,
        ]);

        $guzzle->request('POST', 'https://graph.facebook.com/v2.6/me/messages?access_token='.env('MESSENGER_KEY'), [
            'json' => [
                'recipient' => [
                    'id' => $id,
                ],
                'message' => [
                    'text' => $message,
                ],
            ],
        ]);
    }
}
