<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Event;
use App\Models\Booking;
use App\Models\Attendee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    use CanLoadRelationships;

    private array $relations = ['user', 'attendees', 'attendees.user'];

    public function __construct()
    {

    }

    public function index()
    {        
        $query = $this->loadRelationships(Event::query());
        
        return EventResource::collection(
            $query->latest()->paginate()
        );
    }

    public function store(Request $request)
    {
        $event = Event::create([
            $request->validate([
                'name'          => 'required|string|max:255|unique:events',
                'description'   => 'nullable|string',
                'country'       => 'nullable|string',
                'start_time'    => 'required|date',
                'end_time'      => 'required|date|after:start_time'
            ]),
            'name'             => $request->name,
            'description'      => $request->description,
            'country'          => $request->country,
            'start_time'       => $request->start_time,
            'end_time'         => $request->end_time,
            "user_id"          => Auth::id()
        ]);

        return response(['data'=>['id'=>$event->id],'message'=>'Event Added successfully'],200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {

        $event->update($request->validate([
            'name'          => 'sometimes|string|max:255',
            'description'   => 'nullable|string',
            'country'       => 'nullable|string',
            'start_time'    => 'sometimes|date',
            'end_time'      => 'sometimes|date|after:start_time'
        ]));

        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        Attendee::join('bookings','bookings.id','=','attendees.booking_id')->where('event_id',$event->id)->delete();
        Booking::where('event_id',$event->id)->delete();
        $event->delete();

        return response(['message'=>'Event deleted successfully'],status: 204);
    }
}
