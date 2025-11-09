@extends('layouts.admin_master')
@section('content')
<link href="{{ asset('backend/css/reports.css') }}" rel="stylesheet" />

<div class="card-dashboard mb-4">
    <div class="card-dashboard-header">
        Product Calculator
    </div>
    <div class="card-dashboard-sub-header">
        <div class="card-dashboard-sub-header-controls">
            <div class="float-right">
                <button class="btn btn-sm btn-primary float-right" data-toggle="modal" data-target="#createProductCalcModal">
                <i class="fas fa-plus"></i>New Table</button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div id="productCalcsContainer" class= "container">
            @foreach($productCalcs as $productCalc)
                @php
                    $selectedUnit = $productCalc->unit;
                    $selectedGroup = $selectedUnit->type;
                @endphp
                <div class="card card-reports">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <span class="small mb-1 unit-label" for="inputLastName">Unit</span>
                                <select class="form-control unit-selector" name="unit">
                                    @foreach ($units as $type => $group)
                                        <optgroup label="{{ $type }}" @if($type != $selectedGroup) style="display: none;" @endif>
                                            @foreach ($group as $unit)
                                                <option value="{{ $unit->id }}" 
                                                        data-short-name="{{ $unit->short_name }}"
                                                        data-conversion-factor="{{ $unit->conversion_factor }}"
                                                        @if($unit->id == $selectedUnit->id) selected @endif>
                                                    {{ $unit->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="float-right">
                                    <button type="button" class="btn btn-sm btn-info edit-btn"
                                        data-toggle="modal"
                                        data-target="#createProductCalcModal"
                                        data-id="{{ $productCalc->id }}"
                                        style="margin-right: 20px;">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger delete-btn" data-toggle="modal" data-target="#importCSVModal"
                                        style="margin-right: 20px;" data-id="{{ $productCalc->id }}">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered" data-table-id="{{ $productCalc->id }}" data-conversion-factor="{{ $selectedUnit->conversion_factor }}">
                            <thead>
                                <tr>
                                    @foreach(json_decode($productCalc->headers) as $header)
                                        <th class="header-cell" data-original="{{ $header }}">{{ $header }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(json_decode($productCalc->data) as $rowIndex => $rowData)
                                    <tr>
                                        @foreach($rowData as $colIndex => $data)
                                            @if($colIndex == 0)
                                                <td class="header-cell" data-original="{{ $data }}" >{{ $data }}</td>
                                            @else
                                                <td>
                                                    @php
                                                        $formula = json_decode($productCalc->formulas)[$rowIndex][$colIndex] ?? '';
                                                    @endphp
                                                    <input type="text" class="form-control"
                                                        value="{{ $data }}"
                                                        data-row="{{ $rowIndex + 1 }}"
                                                        data-col="{{ $colIndex }}"
                                                        @if($formula)
                                                            formula="{{ $formula }}"
                                                            readonly
                                                        @else
                                                            oninput="calculateTable('{{ $productCalc->id }}', '{{ $selectedUnit->conversion_factor }}')"
                                                        @endif>
                                                </td>
                                            @endif
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="modal fade" id="createProductCalcModal" tabindex="-1" aria-labelledby="createProductCalcModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createProductCalcModalLabel">Create Table</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="formulaEditor">Edit Formula:</label>
                    <input type="text" id="formulaEditor" class="form-control" placeholder="Click on a cell to edit its formula" readonly>
                    <button id="applyFormula" class="btn btn-success mt-2">Apply Formula</button>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <button id="addRowBtn" class="btn btn-primary btn-sm">Add Row</button>
                        <button id="removeRowBtn" class="btn btn-danger btn-sm">Remove Row</button>
                    </div>
                    <div>
                        <button id="addColBtn" class="btn btn-primary btn-sm">Add Column</button>
                        <button id="removeColBtn" class="btn btn-danger btn-sm">Remove Column</button>
                    </div>
                </div>
                <form id="createProductCalcForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small mb-1" for="inputLastName">Unit Type</label>

                                <select class="form-control" name="unit">
                                    @foreach ($units as $type => $group)
                                    <optgroup label="{{ $type }}">
                                        @foreach ($group as $unit)
                                            <option value="{{ $unit->id }}" {{ $unit->id == 2 ? 'selected' : '' }}>{{ $unit->name }}</option>
                                        @endforeach
                                    </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <table class="table table-bordered" id="modalTable">
                        <thead>
                            <tr>
                                <th><input type="text" class="form-control" id="R0C0" value="" hidden></th>
                                <th><input type="text" class="form-control" id="R0C1" value="Total @unit"></th>
                                <th><input type="text" class="form-control" id="R0C2" value="Wax @unit"></th>
                                <th><input type="text" class="form-control" id="R0C3" value="Scent @unit"></th>
                                <th><input type="text" class="form-control" id="R0C4" value="# of scents"></th>
                                <th><input type="text" class="form-control" id="R0C5" value="@unit per scent"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" class="form-control" id="R1C0" value="WAX MELTS"></td>
                                <td><input type="text" class="form-control" id="R1C1" formula="R3C1 * R4C1" readonly></td>
                                <td><input type="text" class="form-control" id="R1C2" formula="R1C1 * (100 - R2C1) / 100" readonly></td>
                                <td><input type="text" class="form-control" id="R1C3" formula="R1C1 * R2C1 / 100" readonly></td>
                                <td><input type="text" class="form-control" id="R1C4"></td>
                                <td><input type="text" class="form-control" id="R1C5" formula="R1C3" readonly></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" id="R2C0" value="Scent %"></td>
                                <td><input type="text" class="form-control" id="R2C1" oninput=calculateTableOnModal() value="12"></td>
                                <td><input type="text" class="form-control" id="R2C2"></td>
                                <td><input type="text" class="form-control" id="R2C3"></td>
                                <td><input type="text" class="form-control" id="R2C4"></td>
                                <td><input type="text" class="form-control" id="R2C5" formula="R1C3 / 2" readonly></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" id="R3C0" value="@unit per container"></td>
                                <td><input type="text" class="form-control" id="R3C1" oninput=calculateTableOnModal() value="2.96"></td>
                                <td><input type="text" class="form-control" id="32C2"></td>
                                <td><input type="text" class="form-control" id="32C3"></td>
                                <td><input type="text" class="form-control" id="R3C4"></td>
                                <td><input type="text" class="form-control" id="R3C5" formula="R1C3 / 4" readonly></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" id="R4C0" value="# of containers"></td>
                                <td><input type="text" class="form-control" id="R4C1" oninput=calculateTableOnModal() value="30"></td>
                                <td><input type="text" class="form-control" id="R4C2"></td>
                                <td><input type="text" class="form-control" id="R4C3"></td>
                                <td><input type="text" class="form-control" id="R4C4"></td>
                                <td><input type="text" class="form-control" id="R4C5" formula="R1C3 / 8" readonly></td>
                            </tr>
                        </tbody>
                    </table>

                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
let selectedCellId = null;

$(document).ready(function() {
    let isEditMode = false;
    let currentTableId = null;

    $('#createProductCalcModal').on('shown.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        if (button.hasClass('edit-btn')) {
            isEditMode = true;
            var tableId = button.data('id');
            currentTableId = button.data('id');
            $.ajax({
                url: '/product-calcs/' + tableId, // Adjust the endpoint as needed
                method: 'GET',
                success: function(response) {
                    var headers = response.headers;
                    var data = response.data;
                    var formulas = response.formulas;
                    var unitId = response.unit_id;

                    // Populate modal with fetched data
                    modal.find('select[name="unit"]').val(unitId);
                    populateTable(headers, data, formulas);
                    calculateTableOnModal();
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                    alert('An error occurred while fetching the data.');
                }
            });

        } else {
            isEditMode = false;
            currentTableId = null;
            calculateTableOnModal();
        }
    });

    function populateTable(headers, data, formulas) {
        // Clear the current table
        $('#createProductCalcForm thead').empty(); // Clear table headers
        $('#createProductCalcForm tbody').empty(); // Clear table body rows

        // Populate table headers dynamically
        let headerRow = $('<tr></tr>');
        headers.forEach((header, index) => {
            const th = $('<th></th>');
            const input = $('<input>', {
                type: 'text',
                class: 'form-control',
                id: `R0C${index}`,
                value: header
            });
            th.append(input);
            headerRow.append(th);
        });
        $('#createProductCalcForm thead').append(headerRow);

        // Populate table body dynamically
        data.forEach((rowData, rowIndex) => {
            let row = $('<tr></tr>'); // Create a new row element for each row of data

            rowData.forEach((cellData, colIndex) => {
                const td = $('<td></td>');
                const input = $('<input>', {
                    type: 'text',
                    class: 'form-control',
                    id: `R${rowIndex + 1}C${colIndex}`,
                    value: cellData
                });

                // If there's a formula, set it as an attribute and make it readonly
                if (formulas[rowIndex] && formulas[rowIndex][colIndex]) {
                    input.attr('formula', formulas[rowIndex][colIndex]);
                    input.prop('readonly', true);
                } else {
                    input.removeAttr('formula');
                    input.prop('readonly', false);
                }

                td.append(input);
                row.append(td);
            });

            $('#createProductCalcForm tbody').append(row); // Append the populated row to tbody
        });
    }


    // Event listener for clicking on formula cells
    $('#createProductCalcModal').on('click', 'table input', function() {
        // Remove red border from previously selected cell
        if (selectedCellId) {
            $(`#${selectedCellId}`).css('border', '');
        }

        // Highlight the selected cell with a red border
        selectedCellId = $(this).attr('id');
        $(this).css('border', '2px solid red');

        // Show the formula in the external editor
        const formula = $(this).attr('formula');
        $('#formulaEditor').val(formula).prop('readonly', false);
    });

    // Event listener for applying the edited formula
    $('#applyFormula').click(function() {
        if (selectedCellId) {
            const newFormula = $('#formulaEditor').val().trim();

            // Update the formula in the selected cell
            $(`#${selectedCellId}`).attr('formula', newFormula);
            $(`#${selectedCellId}`).val('');

            // Recalculate the table based on the new formula
            calculateTableOnModal();
        }
    });

    $('.unit-selector').change(function() {
        var selectedUnitShortName = $(this).find('option:selected').data('short-name'); // Get the short name of the selected unit
        var conversionFactor = parseFloat($(this).find('option:selected').data('conversion-factor')); // Get the conversion factor
        var cardBody = $(this).closest('.card').find('.card-body');
        var table = cardBody.find('table');
        var tableId = table.data('table-id');

        // Replace @unit in the headers with the selected unit's short name using the original text
        table.find('.header-cell').each(function() {
            var originalText = $(this).data('original'); // Get the original text with @unit
            if (typeof originalText === 'string' && originalText !== '') {
                var newText = originalText.replace(/@unit/g, selectedUnitShortName);
                $(this).text(newText);
            }
        });

        // Recalculate the table with the new unit and apply the conversion factor
        calculateTable(tableId, conversionFactor);
    });

    // Initial calculation on page load
    $('.unit-selector').each(function() {
        $(this).trigger('change');
    });

    $('.delete-btn').on('click', function() {
            var productId = $(this).data('id');

            if (confirm('Are you sure you want to delete this item?')) {
                $.ajax({
                    url: '/product-calcs/' + productId,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}' // CSRF token for security
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.success);
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            toastr.error('Failed to delete!');
                        }
                    },
                    error: function(xhr, status, error) {
                        toastr.error('An error occurred: ' + error);
                    }
                });
            }
        });

        // Event listener for form submission
    $('#createProductCalcForm').on('submit', function(e) {
        e.preventDefault();

        // Collect form data
        var headers = [];
        $('#createProductCalcForm thead th').each(function() {
            var input = $(this).find('input');
            var headerText = input.length ? input.val().trim() : '';
            headers.push(headerText);
        });

        // Initialize an empty array to hold the table data
        var tableData = [];
        var formulas = [];
        
        var unitId = $('#createProductCalcForm select[name="unit"]').val();
        
        // Collect each row's data
        $('#createProductCalcForm tbody tr').each(function() {
            var rowData = [];
            var rowFormulas = [];

            // Collect data from each cell in the row
            $(this).find('input').each(function() {
                var value = $(this).val().trim();
                var formula = $(this).attr('formula');

                if (!formula) {
                    rowData.push(value);
                } else {
                    rowData.push(''); // Add an empty string or placeholder if needed
                }

                // If the cell has a formula, store it; otherwise, store an empty string
                rowFormulas.push(formula ? formula : '');
            });

            tableData.push(rowData);
            formulas.push(rowFormulas);
        });

        var formData = {
            headers: headers,
            data: tableData,
            formulas: formulas, // Include formulas in form data
            unit_id: unitId, // Include unit ID in form data
            _token: $('input[name="_token"]').val() // Add CSRF token
        };

        // Determine whether we're creating or updating
        var method = isEditMode ? 'PUT' : 'POST';
        var url = isEditMode ? '/product-calcs/' + currentTableId : '{{ route('product_calcs.store') }}';

        // Send the AJAX request
        $.ajax({
            type: method,
            url: url,
            data: JSON.stringify(formData),
            contentType: 'application/json; charset=utf-8',
            success: function(response) {
                // Reload or update the page
                location.reload();
            },
            error: function(error) {
                console.log(error);
            }
        });
    });

    $('#addRowBtn').click(function() {
        let lastRow = $('#modalTable tbody tr:last');
        let rowCount = $('#modalTable tbody tr').length + 1;
        let colCount = $('#modalTable thead th').length;

        // Create a new row
        let newRow = $('<tr></tr>');
        for (let i = 0; i < colCount; i++) {
            let newCell = $('<td></td>');
            let newInput = $('<input>', {
                type: 'text',
                class: 'form-control',
                id: 'R' + rowCount + 'C' + i
            });

            // Add formula attribute to new cells if it's a formula column
            if (lastRow.find('td:eq(' + i + ') input').attr('formula')) {
                newInput.attr('formula', ''); // New cells don't have formulas by default
                newInput.prop('readonly', true);
            }

            newCell.append(newInput);
            newRow.append(newCell);
        }

        // Append new row to the table
        $('#modalTable tbody').append(newRow);
    });

    // Remove Row button click
    $('#removeRowBtn').click(function() {
        let rowCount = $('#modalTable tbody tr').length;

        if (rowCount > 1) { // Ensure there's at least one row
            $('#modalTable tbody tr:last').remove();
        }
    });

    // Add Column button click
    $('#addColBtn').click(function() {
        // Get the current number of columns
        let colCount = $('#modalTable thead th').length;
        let newColIndex = colCount;

        // Add a new header column
        let newHeader = $('<th></th>');
        let newHeaderInput = $('<input>', {
            type: 'text',
            class: 'form-control',
            id: `R0C${newColIndex}`,
            value: 'New Column'
        });
        newHeader.append(newHeaderInput);
        $('#modalTable thead tr').append(newHeader);

        // Add a new input cell for each row in the table body
        $('#modalTable tbody tr').each(function(rowIndex) {
            let newCell = $('<td></td>');
            let newInput = $('<input>', {
                type: 'text',
                class: 'form-control',
                id: `R${rowIndex + 1}C${newColIndex}`, // Adjust column index
                value: '' // Empty by default
            });
            newCell.append(newInput);
            $(this).append(newCell);
        });
    });

    // Remove Column button click
    $('#removeColBtn').click(function() {
        let colCount = $('#modalTable thead th').length;

        if (colCount > 1) { // Ensure there's at least one column
            $('#modalTable thead th:last').remove(); // Remove last header column

            // Remove last cell in each row
            $('#modalTable tbody tr').each(function() {
                $(this).find('td:last').remove();
            });
        }
    });
});

function calculateTable(tableId, conversionFactor = 1) {
        var table = $(`table[data-table-id="${tableId}"]`);
        var conversionFactor = conversionFactor / table.data('conversion-factor') ; 

        var r3c1Cell = table.find('input[data-row="3"][data-col="1"]');
        if (!r3c1Cell.data('original-value')) {
            // Save the original value in a data attribute
            r3c1Cell.data('original-value', r3c1Cell.val());
        }
        var originalValue = parseFloat(r3c1Cell.data('original-value'));
        if (!isNaN(originalValue)) {
            var adjustedValue = originalValue * conversionFactor;
            r3c1Cell.val(parseToDecimal(adjustedValue));
        }

        table.find('input[formula]').each(function() {
            var formula = $(this).attr('formula');
            var cellId = $(this).attr('data-row') + '-' + $(this).attr('data-col');

            // Replace cell references (like R1C1) with their current values
            var evaluatedFormula = formula.replace(/R(\d+)C(\d+)/g, function(match, row, col) {
                var cellValue = table.find(`input[data-row="${row}"][data-col="${col}"]`).val();
                return cellValue ? parseFloat(cellValue) : 0;
            });

            try {
                // Evaluate the formula safely
                var result = eval(evaluatedFormula);
                if (!isNaN(result)) {
                    $(this).val(parseToDecimal(result)); // Update the cell with the calculated value
                }
            } catch (error) {
                console.error(`Error evaluating formula for ${cellId}:`, error);
                $(this).val(''); // Clear the cell if there's an error
            }
        });
    }
function calculateTableOnModal() {
    // Find all cells with a formula attribute
    $('input[formula]').each(function() {
        const formula = $(this).attr('formula');  // Get the formula from the attribute
        const cellId = $(this).attr('id');        // Get the cell ID (e.g., "R1C2")

        // Replace cell references (like R1C1) with their current values
        const evaluatedFormula = formula.replace(/R(\d+)C(\d+)/g, function(match, row, col) {
            const cellValue = $(`#R${row}C${col}`).val();  // Get the value of the referenced cell
            return cellValue ? parseFloat(cellValue) : 0;  // Replace with the value, or 0 if empty
        });

        try {
            // Evaluate the formula safely
            const result = eval(evaluatedFormula);
            if (!isNaN(result)) {
                $(this).val(parseToDecimal(result));  // Update the cell with the calculated value
            }
        } catch (error) {
            console.error(`Error evaluating formula for ${cellId}:`, error);
            $(this).val('');  // Clear the cell if there's an error
        }
    });
}

</script>

@endsection
