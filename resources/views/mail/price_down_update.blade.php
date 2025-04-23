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

{{-- <x-mail::button :url="$store_url">
    Check here from retailer's store
</x-mail::button> --}}

</x-mail::message>
