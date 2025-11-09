@if ($sales->isEmpty())
    <tr>
        <td colspan="6" class="text-center">No Data</td>
    </tr>
@else
    @foreach ($sales as $row)
        @php $isShopify = $row->sale_type == 'Shopify'; @endphp
        <tr>
            {{-- Sale ID --}}
            <td style="width: 110px;">
                <a href="javascript:onSaleIdClick({{ $row->id }})" class="sale-id" data-id="{{ $row->id }}">
                    {{ $isShopify ? $row->shopify_order_name : $row->id }}
                </a>
            </td>

            {{-- Total --}}
            <td style="width: 90px;">${{ $row->total }}</td>

            {{-- Status --}}
            <td style="width: 160px;">
                <span class="category-label {{ strtolower($row->status) }}" data-category="{{ $row->status }}">
                    {{ $row->status }}
                </span>
                <span class="category-label {{ strtolower($row->fulfillment_status) }}"
                      data-category="{{ $row->fulfillment_status }}">
                    {{ $row->fulfillment_status }}
                </span>
            </td>

            {{-- Date --}}
            <td style="width: 120px;">{{ $row->sale_date }}</td>

            {{-- Type --}}
            <td style="text-align: center; width: 30px;">
                @if ($isShopify)
                    <a href="https://{{ auth()->user()->company->shopify_domain }}/admin/orders/{{ $row->shopify_id }}"
                       target="_blank" rel="nofollow noreferrer">
                        <img src="{{ asset('images/shopify.ca02b3a3da85bb570f6786752a5f08fc.svg') }}"
                             alt="shopify" class="shopify-logo" width="30" height="30">
                    </a>
                @else
                    {{ $row->sale_type }}
                @endif
            </td>
        </tr>
    @endforeach
@endif