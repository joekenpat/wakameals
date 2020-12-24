@markdown
#### Dear {{$user->first_name}}

Thanks for your patronage,
your new order was received! One of our reps will call you shortly to confirm your order.
> Order Code: **`{{$order->code}}`**

### Order Details

|image|Meal|Additions|Sub-total|
|:---|---:|---:|---:|
@foreach($order->ordered_meals as $ordered_meal)
|![{{$ordered_meal->meal->name}}]({{$ordered_meal->meal->image}}) | {{$ordered_meal->meal->name}}|@if(count($ordered_meal->ordered_meal_extra_items)) @foreach($ordered_meal->ordered_meal_extra_items as $ordered_extra_item)`{{$loop->index+1}}. {{$ordered_extra_item->meal_extra_item->name}} ₦{{$ordered_extra_item->cost}}` @endforeach @endif | ₦{{$ordered_meal->cost}} |
@endforeach

**More Details**

- **Location:** {{$order->state->name}}, {{$order->lga->name}}, {{$order->town->name?:""}}.
- **Address:** {{$order->address}}.
- **Delivery Type:** @if($order->delivery_type == 'door_delivery') Door @else Pickup @endif.
- **Total:** ₦{{$order->total?:0}}

**N/B**:
Keep an eye on your mailbox as more notifications would be sent shortly. If you have questions or concerns please shoot
us an email or call ASAP! Don't forget to mention your order code: **`{{$order->code}}`**.

At your service,
[ **{{config('app.name')}} Team**]({{config('app.url')}}).
@endmarkdown
