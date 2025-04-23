<x-mail::message>

You’ve been tracking this item in Wayyti—and the price just dropped.</br>

Retailer Domain ( <a href="{{$store_url}}">{{$seller}}</a> )

Item: {{$product_name}}
</br>

New Price: <span style="color: green;">{{ $new_price }}</span><br>
</br>

Was: {{ $old_price }}
</br>

Price Dropped by: <span style="color: green;">{{ $percentage }}%</span>

<strong>Want to check it out?</strong>
Open Wayyti, go to your Alerts, and tap on this item to go straight to the store.
Just a heads-up—we’re not sure how long this price will last, but we’ll keep tracking it for you.

{{-- <x-mail::button :url="$store_url">
    Check here from retailer's store
</x-mail::button> --}}

</x-mail::message>
