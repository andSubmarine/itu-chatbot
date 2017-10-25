<?php
namespace App\Traits;

use Illuminate\Http\Request;

trait Listens
{
    public function messengerVerify(Request $request)
    {
        if ($request->get('hub_verify_token') != env('MESSENGER_VERIFY_KEY')) {
            return response()->json('Not understood', 401);
        }

        if ($request->get('hub_mode') == 'subscribe') { //A facebook user is writing to us for the first time
            return response()->json((int)$request->get('hub_challenge'));
        }

        return response()->json('Not understood', 422);
    }

    public function parse($input)
    {
        $messengerMessage = json_decode(json_encode($input['entry'][0]['messaging'][0]));


        $input['senderId'] = $messengerMessage->sender->id;
        $input['messageId'] = $messengerMessage->message->mid ?? null;
        $input['messageText'] = $messengerMessage->message->text ?? null;

        return (object)$input;
    }
}
