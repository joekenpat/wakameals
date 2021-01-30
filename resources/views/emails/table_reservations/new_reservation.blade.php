@markdown
New Table Reservation Request Received,


A new table reservation request was received! you should call the user shortly to confirm the request.
> Request Code: **`{{$reservation->code}}`**


### Reservation Details
- **User Name:** {{$reservation->user->last_name." ".$reservation->user->first_name}}.
- **User Phone:** {{$reservation->user->phone}}.
- **User Email:** {{$reservation->user->email}}.
- **Location:** {{$reservation->place->name}}.
- **Address:** {{$reservation->dispatcher->address}}.
- **No of Seats:** {{$reservation->seat_quantity}}
- **Proposed Time:** {{$reservation->reserved_at->toDayDateTimeString()}}

@endmarkdown
