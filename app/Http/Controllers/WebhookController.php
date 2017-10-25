<?php
namespace App\Http\Controllers;

use App\Traits\Listens;
use App\Traits\Responds;
use App\Traits\Understands;
use Cache;
use Carbon\Carbon;
use ICal\ICal;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    use Listens, Responds, Understands;

    public function respond(Request $request)
    {
        $message = $this->parse($request->all());
        $intent = null;
        if ($message->messageText) {
            $meaning = $this->understand($message->messageText);
            $intent = $meaning->intent[0]->value;
        }

        $response = null;
        switch ($intent) {
            case 'lookup_room':
                $response = $this->respondToRoomLookup($meaning);
                break;
            default:
                $response = 'I did not understand that. If you want to help extend me, visit https://github.com/niclashedam/itu-chatbot';
                break;
        }

        $this->sendMessage($response, $message->senderId);
    }

    public function respondToRoomLookup($meaning)
    {
        $room = $meaning->room[0]->value ?? null;
        
        if(is_null($root)){
           return 'I did not understand that. If you want to help extend me, visit https://github.com/niclashedam/itu-chatbot';   
        }
        
        $events = $this->getEvents()->filter(function ($event) use ($room) {
            return str_contains($event->location, $room);
        })->sortBy('dtstart');


        $now = !isset($meaning->datetime);
        $time = $now ? Carbon::now() : new Carbon($meaning->datetime[0]->value);

        $nextEvent = $events->first(function ($event) use ($time) {
            return (new Carbon($event->dtend))->gt($time);
        }) ?? null;

        if (is_null($nextEvent)) {
            return sprintf('Either does \'%s\' not exist or it is avaiable for use this whole semester.', $room);
        }

        $start = new Carbon($nextEvent->dtstart);
        $end = new Carbon($nextEvent->dtend);

        $start->setTimezone('Europe/Copenhagen');
        $end->setTimezone('Europe/Copenhagen');

        if (!$time->between($start, $end)) {
            if (!$now) {
                return sprintf('Room \'%s\' is free at ' . $time->format('d/m H:i'), $room);
            }
            return sprintf('Room \'%s\' is free until ' . $start->diffForHumans(), $room);
        } else {
            if (!$now) {
                return sprintf('Room \'%s\' is booked at ' . $time->format('d/m H:i'), $room);
            }
            return sprintf('Room \'%s\' is booked until ' . $end->diffForHumans(), $room);
        }
    }


    private function getEvents()
    {
        return Cache::remember('ics', 60, function () {
            $studyActivities = collect((new ICal)->initUrl('https://dk.timeedit.net/web/itu/db1/public/ri6Q7Z6QQw0Z5gQ9f50on7Xx5YY00ZQ1ZYQycZw.ics')->events());
            $activities = collect((new ICal)->initUrl('https://dk.timeedit.net/web/itu/db1/public/ri6g7058yYQZXxQ5oQgZZ0vZ56Y1Q0f5c0nZQwYQ.ics')->events());
            return $studyActivities->merge($activities);
        });
    }
}
