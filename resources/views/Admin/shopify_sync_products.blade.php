@extends('layouts.admin_master')

@section('head')

<style>
    #progress-bar {
        width: 0%;
        height: 30px;
        background-color: green;
        text-align: center;
        line-height: 30px;
        color: white;
    }
</style>
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="card-dashboard mb-4">
    <div class="card-dashboard-header">
        Product Synchronization
    </div>
    <div class="card-dashboard-sub-header">
        <div class="card-dashboard-sub-header-controls">
            <div class="float-right">
                <button id="start-sync" class="btn btn-primary float-right">Start Sync</button>
            </div>
        </div>
        <div class="row progress mt-3">
            <div id="progress-bar" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
        </div>

        <div class="mt-4">
            <strong>Last Updated: </strong>
            @if ($lastUpdated)
                @php
                    $lastUpdatedTime = \Carbon\Carbon::parse($lastUpdated);
                    $timeAgo = $lastUpdatedTime->diffForHumans($currentDate);
                @endphp
                {{ $lastUpdatedTime }} ({{ $timeAgo }})
            @else
                <span>No data updated yet.</span>
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <div class="scrollbar-wrapper">
                <div class="scrollbar-top"></div>
            </div>
            <div class="table-wrapper">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Before Sync Stock Level</th>
                            <th>After Sync Stock Level</th>
                            <th>Shopify ID</th>
                        </tr>
                    </thead>
                    <tbody id="product-table-body">
                        @foreach ($products as $product)
                        <tr data-product-id="{{ $product->id }}">
                            <td>{{ $product->name }}</td>
                            <td class="before-sync">{{ $product->current_stock_level }}</td>
                            <td class="after-sync"></td>
                            <td>{{ $product->shopify_id }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Set CSRF token for all AJAX requests
  
        $('#start-sync').click(function() {
            var totalProducts = {{ $products->count() }};
            var syncedProducts = 0;

            // Disable the button to prevent multiple syncs
            $('#start-sync').prop('disabled', true);

            function syncNextProduct() {
                if (syncedProducts < totalProducts) {
                    $.ajax({
                        url: '{{ route('syncProducts') }}',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        data: { offset: syncedProducts },
                        success: function(response) {
                            syncedProducts++;
                            var percentage = (syncedProducts / totalProducts) * 100;
                            $('#progress-bar').css('width', percentage + '%').text(percentage.toFixed(2) + '%');

                            var productRow = $('#product-table-body').find('tr').eq(syncedProducts - 1);
                            productRow.find('.after-sync').text(response.afterSyncStockLevel);

                            syncNextProduct(); // Sync the next product
                        },
                        error: function(xhr, status, error) {
                            
                            // Re-enable the button in case of error
                            setTimeout(function() {
                                syncNextProduct(); // Continue with the next product after 2-second delay
                            }, 10000);
                        }
                    });
                } else {
                    
                    // Re-enable the button after completion
                    $('#start-sync').prop('disabled', false);
                }
            }

            syncNextProduct(); // Start syncing the first product
        });
    });
</script>
@endsection
