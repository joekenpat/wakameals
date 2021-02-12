@markdown
New Table Reservation Request Received,


A new table reservation request was received! you should call the user shortly to confirm the request.

> Request Code: **`{{$reservation->code}}`**


#### Reservation Details
- **Name:** {{$reservation->name}}.
- **Phone:** {{$reservation->user->phone}}.
- **Email:** {{$reservation->user->email}}.
- **Location:** {{$reservation->place->name}}.
- **Address:** {{$reservation->dispatcher->address}}.
- **No of Persons:** {{$reservation->no_of_persons}}
- **Proposed Time:** {{$reservation->reserved_at->toDayDateTimeString()}}

At your service,
[ **{{config('app.name')}} Team**]({{config('app.url')}}).
@endmarkdown
