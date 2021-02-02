@markdown
New Order Received,
A new order was received! you should call the user shortly to confirm the order.
> Order Code: **`{{$order->code}}`**

### Order Details

|image|Meal|Additions|Sub-total|
|:---|---:|---:|---:|
@foreach($order->ordered_meals as $ordered_meal)
|![{{$ordered_meal->meal->name}}]({{$ordered_meal->meal->image}}) | {{$ordered_meal->meal->name}}|@if(count($ordered_meal->ordered_meal_extra_items)) @foreach($ordered_meal->ordered_meal_extra_items as $ordered_extra_item)`{{$loop->index+1}}. {{$ordered_extra_item->meal_extra_item->name}} ₦{{$ordered_extra_item->cost}}` @endforeach @endif | ₦{{$ordered_meal->cost}} |
@endforeach

**More Details**

- **User Name:** {{$order->user->first_name." ".$order->user->last_name}}.
- **User Phone:** {{$order->user->phone}}.
- **Location:** {{$order->place->name}}.
- **Address:** {{$order->address}}.
- **Delivery Type:** @if($order->delivery_type == 'door_delivery') Door @else Pickup @endif.
- **Total:** ₦{{$order->total}}

@endmarkdown
