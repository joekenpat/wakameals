@markdown
#### Dear {{$user->name}}


Too Bad!
Your Reservation Request:  **`{{$reservation->code}}`** has was Declined.


#### Reservation Details
- **Location:** {{$reservation->place->name}}.
- **Address:** {{$reservation->event_address}}.
- **No of Seats:** {{$reservation->no_of_persons}}
- **Proposed Time:** {{$reservation->reserved_at->toDayDateTimeString()}}

At your service,
[ **{{config('app.name')}} Team**]({{config('app.url')}}).
@endmarkdown
