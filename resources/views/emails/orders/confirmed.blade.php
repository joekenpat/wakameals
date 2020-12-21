@markdown
#### Dear {{$user->first_name}}

Hurray!
Your order:  **`{{$order->code}}`** has been confirmed and is presently being prepared. To monitor the status of your order kindly login to your account here and check the order status.

## Order Details

|image|Meal|Additions|Sub Total|
|:---|---:|---:|---:|
@foreach($order->ordered_meals as $ordered_meal)
|![{{$ordered_meal->meal->name}}]({{$ordered_meal->meal->image}}) | {{$ordered_meal->meal->name}}|@if(count($ordered_meal->ordered_meal_extra_items)) @foreach($ordered_meal->ordered_meal_extra_items as $ordered_extra_item)`{{$loop->index+1}}. {{$ordered_extra_item->meal_extra_item->name}} ₦{{$ordered_extra_item->meal_extra_item->price * $ordered_extra_item->quantity}}` @endforeach @endif | ₦{{$ordered_meal->meal->price}} |
@endforeach

**More Details**

- **Location:** {{$order->state->name}}, {{$order->lga->name}}, {{$order->town->name?:""}}.
- **Address:** {{$order->address}}.
- **Delivery Type:** @if($order->delivery_type == 'door_delivery') Door @else Pickup @endif.
- **Total:** ₦{{$order->total?:0}}

**N/B**:
Keep an eye on your mailbox as more notifications would be sent shortly. If you have questions or concerns please shoot
us an email or call ASAP! Don't forget to mention your order code: **`{{$order->code}}`**.

Sincerely at your service,
[ **{{config('app.name')}} Team**]({{config('app.url')}}).
@endmarkdown
