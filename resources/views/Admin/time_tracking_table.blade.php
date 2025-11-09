@foreach ($tracks as $index => $row)
    @if ($row->paid == 0)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td style="display:none;">{{ $row->id }}</td>
            <td>{{ $row->date }}</td>

            <!-- Check if arrival_time and departure_time are not null or empty -->
            <td>
                @if ($row->arrival_time)
                    {{ \Carbon\Carbon::createFromFormat('H:i:s', $row->arrival_time)->format('g:i A') }}
                @else
                    N/A
                @endif
            </td>

            <td>
                @if ($row->departure_time)
                    {{ \Carbon\Carbon::createFromFormat('H:i:s', $row->departure_time)->format('g:i A') }}
                @else
                    N/A
                @endif
            </td>

            <td>{{ $row->hours }}</td>
            <td>{{ $row->notes }}</td>
            <td>
                <!-- <button type="button" class="btn btn-sm btn-info edit-btn" data-toggle="modal" data-target="#editModal"
                    data-id="{{ $row->id }}" title="Edit"><i class="fa fa-edit"></i></button>
                <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $row->id }}"
                    title="Delete"><i class="fa fa-trash"></i></button> -->
            </td>
        </tr>
    @endif
@endforeach