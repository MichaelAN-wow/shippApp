@foreach ($materials as $row)
    @php
        // Convert to numerical values for comparison
        $currentStockLevel = is_numeric($row->current_stock_level) ? floatval($row->current_stock_level) : 0;
        $minStockLevel = is_numeric($row->min_stock_level) ? floatval($row->min_stock_level) : null;
        // Determine if row should be highlighted in red
        $rowClass = $minStockLevel !== null && $currentStockLevel < $minStockLevel ? 'text-danger' : '';
    @endphp
    <tr class="{{ $rowClass }}"  data-id="{{ $row->id }}">
        <td class="material-name">
            {{ $row->name }}
            @if ($row->photo_path)
                <img src="{{ asset('storage/' . $row->photo_path) }}" alt="Material Photo" width="28" height="28"
                    style="float: right; margin-left: 10px;">
            @endif
        </td>
        <td>{{ $currentStockLevel }} {{ $row->unit->name }}</td>
        <td>
            @if (is_null($row->min_stock_level))
                -
            @else
                {{ $minStockLevel }} {{ $row->unit->name }}
            @endif
        </td>
        <td>${{ number_format($row->price_per_unit, 2) }}/{{ $row->unit->name }}</td>
        <td>{{ $row->material_code }}</td>
        <td>{{ optional($row->supplier)->name ?? '' }}</td>
        <td>{{ optional($row->category)->name ?? '' }}</td>
        <td>
            @php
                $updatedDate = \Carbon\Carbon::parse($row->last_order_date);
                $formattedDate = $updatedDate->isToday() ? 'Today' : $updatedDate->format('F j, Y');
            @endphp
            {{ $formattedDate }}
        </td>
        <td>{{ $row->notes }}</td>
        <td>
            <a href="#" class="btn btn-sm btn-info edit-btn" data-toggle="modal" data-target="#editMaterialModal"
                data-id="{{ $row->id }}" title="Edit Material"><i class="fa fa-edit"></i></a>
            <a href="#" class="btn btn-sm btn-danger delete-btn" data-id="{{ $row->id }}"
                title="Delete Material"><i class="fa fa-trash"></i></a>
        </td>
    </tr>
@endforeach