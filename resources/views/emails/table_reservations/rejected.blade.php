@markdown
#### Dear {{$user->first_name}}


Too Bad!
Your Reservation Request:  **`{{$reservation->code}}`** has was Declined.


### Reservation Details
- **Location:** {{$reservation->place->name}}.
- **Address:** {{$reservation->dispatcher->address}}.
- **No of Seats:** {{$reservation->seat_quantity}}
- **Proposed Time:** {{$reservation->reserved_at->toDayDateTimeString()}}

@endmarkdown
