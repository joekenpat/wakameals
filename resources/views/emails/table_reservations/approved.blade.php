@markdown
#### Dear {{$user->first_name}}


Hurray!
Your Reservation Request:  **`{{$reservation->code}}`** has been Approved.


#### Reservation Details
- **Location:** {{$reservation->place->name}}.
- **Address:** {{$reservation->dispatcher->address}}.
- **No of Seats:** {{$reservation->seat_quantity}}
- **Proposed Time:** {{$reservation->reserved_at->toDayDateTimeString()}}

At your service,
[ **{{config('app.name')}} Team**]({{config('app.url')}}).
@endmarkdown
