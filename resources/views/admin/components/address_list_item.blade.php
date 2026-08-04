@php
    $i=0;
    $selectedAddress=session('selectedAddress')??[];
@endphp
@forelse ($addresses as $address)
    <a href="javascript:;" class="list-group-item list-group-item-action ">
        <div class="d-flex w-100 justify-content-between">
            <div class="form-check">
                <input class="form-check-input check-address" {{in_array($address->id,$selectedAddress)?'checked':''}} type="checkbox" id="addresscheck{{$i}}" name="addressId[]" value="{{$address->id}}">
           
            </div>
               <h5 class="mb-1">{{ $address->name }}</h5>
        </div>
        <p class="mb-1">
            {{ $address->shipping_address?$address->shipping_address.',':'' }}
            {{ $address->shipping_address_line_1?$address->shipping_address_line_1.',':'' }}
            {{ $address->shipping_address_line_2?$address->shipping_address_line_2.',':'' }}
            {{  $address->shipping_state?$address->shipping_state.',':'' }}
            {{ $address->shipping_city?$address->shipping_city.',':'' }}
            {{ $address->shipping_country?$address->shipping_country.',':'' }}
            {{ $address->shipping_zipcode?$address->shipping_zipcode.',':'' }}
        </p>
        <small>{{ $address->phone }}</small>
    </a>
    @php
    $i++;
    @endphp
@empty
<p class="mb-1 text-center">No address found </p>
@endforelse
