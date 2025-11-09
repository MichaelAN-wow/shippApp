@foreach ($orders as $index => $row)
    <tr>
        <td><a href="#" class="order-id" data-id="{{ $row->id }}">{{ $index + 1 }}</a></td>
        {{-- <td>
            <a href="#" class="supplier-name" data-id="{{ $row->id }}">{{ $row->supplier->name }}</a>
        </td> --}}
        <td>
            {{ $row->supplier->name }}
        </td>
        <td>
            @if ($row->status == '0')
                Draft
            @elseif ($row->status == '1')
                Placed
            @else
                Received
            @endif
        </td>
        <td>${{ $row->total }}</td>
        <td>{{ $row->items }} item</td>
        <td>{{ $row->received_at }}</td>

        <td>
            <a href="#" class="btn btn-sm btn-info edit-btn" data-toggle="modal" data-target="#editOrderModal"
                data-id="{{ $row->id }}" title="Edit Product"><i class="fa fa-edit"></i></a>
            <a href="#" class="btn btn-sm btn-danger delete-btn" data-id="{{ $row->id }}"
                title="Delete Product"><i class="fa fa-trash"></i></a>
        </td>
    </tr>
@endforeach
