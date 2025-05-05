<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Booking;
use App\Models\Attendee;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    use CanLoadRelationships;

    public function __construct()
    {
        
    }

    private array $relations = ['user'];
    
    public function index()
    {
        $query = $this->loadRelationships(Booking::query());
        
        return BookingResource::collection(
            $query->latest()->paginate()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_id'                  => ['required','numeric','exists:events,id',
                function ($attribute, $value, $fail) {
                    $booking_limit = Booking::where('event_id',$value)->get()->count();
                    if ($booking_limit > 10) {
                        $fail($attribute.' Overbooking not allowed.Only 10 bookings allowed per event.');
                    }
                }
            ],
            'attendees'                 => 'required|array|min:1|max:5',
            'attendees.*.name'          => 'required|string|max:255',
            'attendees.*.email'         => ['required','email','distinct',
                function ($attribute, $value, $fail)use ($request) {
                    $attendee = Booking::join('attendees','bookings.id','=','attendees.booking_id')->where('event_id',$request->event_id)->where('email',$value)->first();
                    if ($attendee) {
                        $fail($attribute.' Duplicate booking');
                    }
                }
            ],
            'attendees.*.country'       => 'nullable|string',
        ]);

        $booking = Booking::create([
            
            'event_id'         => $request->event_id,
            'user_id'          => Auth::id()
        ]);

        foreach ($request->attendees as $key => $value) {
            Attendee::create([
                'name'          => $value['name'],
                'email'         => $value['email'],
                'country'       => $value['country'],
                'booking_id'    => $booking->id
            ]);
        }

        return response(['data'=>['booking_id'=>$booking->id],'message'=>'Booking created successfully'],200);
    }

    public function show(Event $event, Booking $booking)
    {
        return new BookingResource(
            $this->loadRelationships($booking)
        );
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'attendees'                 => 'required|array|min:1|max:5',
            'attendees.*.name'          => 'required|string|max:255',
            'attendees.*.email'         => 'required|email|distinct',
            'attendees.*.country'       => 'nullable|string',
        ]);

        Attendee::where('booking_id',$id)->delete();
        foreach ($request->attendees as $key => $value) {
            Attendee::create([
                'name'          => $value['name'],
                'email'         => $value['email'],
                'country'       => $value['country'],
                'booking_id'    => $id
            ]);
        }

        return response(['data'=>['booking_id'=>$id],'message'=>'Booking updated successfully'],200);
    }

    public function destroy(Booking $booking)
    {
        Attendee::where('booking_id',$booking->id)->delete();
        $booking->delete();
        return response(['message'=>'Booking deleted successfully'],status : 204);
    }
}
