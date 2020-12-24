@markdown
#### Dear {{$user->first_name}}

WOW!👏
Thanks for letting us serve you! Your order  **`{{$order->code}}`** has been delivered successfully.

## Order Details

|image|Meal|Additions|Sub Total|
|:---|---:|---:|---:|
@foreach($order->ordered_meals as $ordered_meal)
|![{{$ordered_meal->meal->name}}]({{$ordered_meal->meal->image}}) | {{$ordered_meal->meal->name}}|@if(count($ordered_meal->ordered_meal_extra_items)) @foreach($ordered_meal->ordered_meal_extra_items as $ordered_extra_item)`{{$loop->index+1}}. {{$ordered_extra_item->meal_extra_item->name}} ₦{{$ordered_extra_item->cost}}` @endforeach @endif | ₦{{$ordered_meal->cost}} |
@endforeach

**More Details**

- **Location:** {{$order->state->name}}, {{$order->lga->name}}, {{$order->town->name?:""}}.
- **Address:** {{$order->address}}.
- **Delivery Type:** @if($order->delivery_type == 'door_delivery') Door @else Pickup @endif.
- **Total:** ₦{{$order->total}}

**N/B**:
Would you let us know what you think about the delivery process?
If yes please [click here](https://survey.wakameals.com) to take a 30secs survey and you will be enrolled to contest for our monthly customer reward program.

If you have any questions or concerns please don't hesitate to contact us via phone, whatsapp, LiveChat or email.

At your service,
[ **{{config('app.name')}} Team**]({{config('app.url')}}).
@endmarkdown
