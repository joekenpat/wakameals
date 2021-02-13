@markdown
#### Dear {{$user->name}}


Hurray!
Your Reservation Request:  **`{{$reservation->code}}`** has been Approved.


#### Reservation Details
- **Location:** {{$reservation->place->name}}.
- **Address:** {{$reservation->event_address}}.
- **No of Persons:** {{$reservation->no_of_persons}}
- **Proposed Time:** {{$reservation->reserved_at->toDayDateTimeString()}}

At your service,
[ **{{config('app.name')}} Team**]({{config('app.url')}}).
@endmarkdown
