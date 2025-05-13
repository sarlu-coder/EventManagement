<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Attendee;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'user_id'       => $this->user_id,
            'event_id'      => $this->event_id,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
            'attendees'     => Attendee::where('booking_id',$this->id)->get()->toArray(),
        ];
    }
}
