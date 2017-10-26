<?php

namespace App\Traits;

use GuzzleHttp\Client;

trait Understands
{
    public function understand($message)
    {
        $guzzle = new Client([
                'timeout'  => 5,
            ]);

        return json_decode((string) $guzzle->request('GET', 'https://api.wit.ai/message?q='.$message, [
                'headers'         => ['Authorization' => 'Bearer '.env('WIT_AI_KEY')],
            ])->getBody())->entities;
    }
}
