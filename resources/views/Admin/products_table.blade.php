@php
    // Group the paginated items by variant or product ID
    $groupedProducts = $paginatedItems->groupBy(function ($item) {
        return $item['variant'] ? $item['variant']->variants_name : $item['products']->first()->id;
    });

    // Fixed color map or dynamic hashed color (choose one version)
    $categoryColors = [
        'Astrology Collection' => '#4CAF50',
        'Fall Collection' => '#FF9800',
        'Signature Collection' => '#9C27B0',
        'Summer Collection' => '#FBC02D',
        'Spring Collection' => '#E91E63',
        'Winter Collection' => '#03A9F4',
        'Mystical Collection' => '#424242',
        'Vibes Collection' => '#9E9E9E',
        'Cereal Collection' => '#81D4FA',
        'Ice Cream Collection' => '#A5D6A7'
    ];

    function getColorForCategory($name, $colors) {
        return $colors[$name] ?? '#CCCCCC';
    }
@endphp

@if ($paginatedItems->isEmpty() && ($currentPage ?? 1) == 1)
    <tr>
        <td colspan="9" class="text-center">No Data</td>
    </tr>
@else
    @foreach ($paginatedItems as $item)
        @if ($item['variant'])
            @php
                $variant = $item['variant'];
                $products = $item['products'];
                $totalStockLevel = $products->sum('current_stock_level');
                $minCost = $products->min('price');
                $maxCost = $products->max('price');
            @endphp
            <tr class="collapsible" data-variant-id="{{ $variant->id }}">
                <td>
                    <label class="custom-checkbox">
                        <input type="checkbox" class="variant-checkbox" data-id="{{ $variant->id }}">
                        <span class="checkmark"></span>
                    </label>
                </td>
                <td><strong>{{ $variant->variants_name }}</strong></td>
                <td>{{ $totalStockLevel }} Pieces</td>
                <td></td>
                <td>${{ number_format($minCost, 2) }} - ${{ number_format($maxCost, 2) }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @foreach ($products as $row)
                @php
                    $rowClass = '';
                    foreach ($row->productMaterials as $productMaterial) {
                        $current = floatval($productMaterial->material->current_stock_level ?? 0);
                        $min = isset($productMaterial->material->min_stock_level) ? floatval($productMaterial->material->min_stock_level) : null;
                        if ($min !== null && $current < $min) {
                            $rowClass = 'text-danger';
                            break;
                        }
                    }
                @endphp
                <tr class="content {{ $rowClass }}" data-variant-id="{{ $variant->id }}" data-id="{{ $row->id }}">
                    <td>
                        <label class="custom-checkbox">
                            <input type="checkbox" class="product-checkbox" data-id="{{ $row->id }}">
                            <span class="checkmark"></span>
                        </label>
                    </td>
                    <td>
                        @if ($row->shopify_id)
                            <img src="{{ asset('images/shopify.ca02b3a3da85bb570f6786752a5f08fc.svg') }}" alt="shopify" width="28" height="28" style="float: left;">
                        @endif
                        <a href="javascript:void(0)" class="product-name" data-id="{{ $row->id }}">{{ $row->name }}</a>
                        @if ($row->photo_path)
                            <img src="{{ asset('storage/' . $row->photo_path) }}" alt="Product Photo" width="28" height="28" style="float: right;">
                        @endif
                    </td>
                    <td class="product-stock">{{ $row->current_stock_level }} {{ $row->unit->name }}</td>
                    <td class="product-min-stock">
                        {{ is_null($row->min_stock_level) ? '-' : $row->min_stock_level . ' ' . $row->unit->name }}
                    </td>
                    <td class="product-price">${{ number_format($row->price, 2) }}/{{ $row->unit->name }}</td>
                    <td class="product-code">{{ $row->product_code }}</td>
                    <td style="text-align: center;">
                        @if ($row->category)
                            @php
                                $bgColor = getColorForCategory($row->category->name, $categoryColors);
                            @endphp
                            <span class="category-label" style="background-color: {{ $bgColor }}; color: #fff; padding: 2px 6px; border-radius: 4px;" data-category="{{ $row->category->name }}">
                                {{ $row->category->name }}
                            </span>
                        @endif
                    </td>
                    <td class="product-notes">{{ $row->notes }}</td>
                    <td>
                        <a href="javascript:editProductClick({{ $row->id }})" class="btn btn-sm btn-info edit-btn" data-toggle="modal" data-target="#editProductModal" data-id="{{ $row->id }}" title="Edit Product">
                            <i class="fa fa-edit"></i>
                        </a>
                        <a href="javascript:deleteProduct({{ $row->id }})" class="btn btn-sm btn-danger delete-btn" data-id="{{ $row->id }}" title="Delete Product">
                            <i class="fa fa-trash"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        @else
            @foreach ($item['products'] as $row)
                <tr data-id="{{ $row->id }}">
                    <td>
                        <label class="custom-checkbox">
                            <input type="checkbox" class="product-checkbox" data-id="{{ $row->id }}">
                            <span class="checkmark"></span>
                        </label>
                    </td>
                    <td>
                        @if ($row->shopify_id)
                            <img src="{{ asset('images/shopify.ca02b3a3da85bb570f6786752a5f08fc.svg') }}" alt="shopify" width="28" height="28" style="float: left;">
                        @endif
                        <a href="javascript:void(0)" class="product-name" data-id="{{ $row->id }}">{{ $row->name }}</a>
                        @if ($row->photo_path)
                            <img src="{{ asset('storage/' . $row->photo_path) }}" alt="Product Photo" width="28" height="28" style="float: right;">
                        @endif
                    </td>
                    <td class="product-stock">{{ $row->current_stock_level }} {{ $row->unit->name }}</td>
                    <td class="product-min-stock">
                        {{ is_null($row->min_stock_level) ? '-' : $row->min_stock_level . ' ' . $row->unit->name }}
                    </td>
                    <td class="product-price">${{ number_format($row->price, 2) }}/{{ $row->unit->name }}</td>
                    <td class="product-code">{{ $row->product_code }}</td>
                    <td style="text-align: center;">
                        @if ($row->category)
                            @php
                                $bgColor = getColorForCategory($row->category->name, $categoryColors);
                            @endphp
                            <span class="category-label" style="background-color: {{ $bgColor }}; color: #fff; padding: 2px 6px; border-radius: 4px;" data-category="{{ $row->category->name }}">
                                {{ $row->category->name }}
                            </span>
                        @endif
                    </td>
                    <td class="product-notes">{{ $row->notes }}</td>
                    <td>
                        <a href="javascript:editProductClick({{ $row->id }})" class="btn btn-sm btn-info edit-btn" data-toggle="modal" data-target="#editProductModal" data-id="{{ $row->id }}" title="Edit Product">
                            <i class="fa fa-edit"></i>
                        </a>
                        <a href="javascript:deleteProduct({{ $row->id }})" class="btn btn-sm btn-danger delete-btn" data-id="{{ $row->id }}" title="Delete Product">
                            <i class="fa fa-trash"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        @endif
    @endforeach
@endif
