@extends('layouts.admin_master')

@section('content')
<div class="container-fluid m-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Shipping Dashboard</h1>
    </div>

    {{-- Stats Tabs --}}
    <ul class="nav nav-tabs mb-4" id="shippingTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="open-orders-tab" data-bs-toggle="tab" data-bs-target="#open-orders" type="button" role="tab" aria-controls="open-orders" aria-selected="true">
                Open Orders ({{ $stats['pending_orders'] ?? 0 }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="in-transit-tab" data-bs-toggle="tab" data-bs-target="#in-transit" type="button" role="tab" aria-controls="in-transit" aria-selected="false">
                In Transit ({{ $stats['in_transit'] ?? 0 }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="delivered-tab" data-bs-toggle="tab" data-bs-target="#delivered" type="button" role="tab" aria-controls="delivered" aria-selected="false">
                Delivered ({{ $stats['delivered'] ?? 0 }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="exceptions-tab" data-bs-toggle="tab" data-bs-target="#exceptions" type="button" role="tab" aria-controls="exceptions" aria-selected="false">
                Exceptions ({{ $stats['exceptions'] ?? 0 }})
            </button>
        </li>
        
        {{-- Right Buttons --}}
        <div class="d-flex align-self-right ms-auto mr-4">
            {{-- Tab-specific button (Purchase) --}}
            <div id="tabButtons" class="mr-2"></div>

            {{-- Always visible export button --}}
            <button class="btn btn-secondary btn-sm mb-1" data-bs-toggle="modal" data-bs-target="#exportModal">
                Export
            </button>
        </div>
    </ul>

    {{-- Tab Content --}}
    <div class="tab-content mr-4" id="shippingTabsContent">
        <div class="tab-pane fade show active" id="open-orders" role="tabpanel" aria-labelledby="open-orders-tab">
            @include('shipping.partials.open_orders_table')
        </div>
        <div class="tab-pane fade" id="in-transit" role="tabpanel" aria-labelledby="in-transit-tab">
            @include('shipping.partials.in_transit_table')
        </div>
        <div class="tab-pane fade" id="delivered" role="tabpanel" aria-labelledby="delivered-tab">
            @include('shipping.partials.delivered_table')
        </div>
        <div class="tab-pane fade" id="exceptions" role="tabpanel" aria-labelledby="exceptions-tab">
            @include('shipping.partials.exceptions_table')
        </div>
    </div>

</div>

<!-- Purchase Modal -->
@include('shipping.partials.purchase_label_modal')

<!-- Export Modal -->
@include('shipping.partials.export_modal')

<!-- View Shipment Modal -->
@include('shipping.partials.viewModal')

<script>
    window.openOrders = @json($openOrders ?? []);

    $(document).ready(function () {
        // Initialize DataTables
        $('#openOrdersTable, #inTransitTable, #deliveredTable, #exceptionsTable').DataTable({
            paging: true,
            lengthChange: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100]
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const tabButtonsContainer = document.getElementById('tabButtons');
        const modalBox = document.getElementById('modalBox');
        const modalBoxDimensions = document.getElementById('modalBoxDimensions');
        const modalBoxWeight = document.getElementById('modalBoxWeight');
        const modalProductWeightLb = document.getElementById('modalProductWeightLb');
        const modalProductWeightOz = document.getElementById('modalProductWeightOz');
        const modalTotalWeight = document.getElementById('modalTotalWeight');
        const modalOrderId = document.getElementById('modalOrderId');

        const openOrders = @json($openOrders ?? []);
        const inTransit = @json($inTransit ?? []);
        const delivered = @json($delivered ?? []);

        // Utility functions
        const formatLbOz = (lb, oz) => `${lb} lb ${oz.toFixed(1)} oz`;
        const addLbOz = (lb1, oz1, lb2, oz2) => {
            const totalOz = oz1 + oz2;
            const extraLb = Math.floor(totalOz / 16);
            const remainingOz = totalOz % 16;
            return { lb: lb1 + lb2 + extraLb, oz: remainingOz };
        };
        const updateTotalWeight = () => {
            const box = modalBox.selectedOptions[0];
            const boxLb = parseInt(box?.dataset.weightLb || 0);
            const boxOz = parseFloat(box?.dataset.weightOz || 0);
            const prodLb = parseInt(modalProductWeightLb.value || 0);
            const prodOz = parseFloat(modalProductWeightOz.value || 0);
            const total = addLbOz(boxLb, boxOz, prodLb, prodOz);
            modalTotalWeight.value = formatLbOz(total.lb, total.oz);
        };

        // Update tab buttons
        const updateTabButtons = (activeTabId) => {
            let html = '';
            if (activeTabId === 'open-orders-tab') {
                html = `<button class="btn btn-primary btn-sm">Purchase Labels</button>`;
            } else if (activeTabId === 'in-transit-tab') {
                html = `<button class="btn btn-success btn-sm" id="btnRefreshStatus">Refresh Status</button>`;
            }
            tabButtonsContainer.innerHTML = html;

            // Attach Refresh Status click
            const btnRefresh = document.getElementById('btnRefreshStatus');
            if (btnRefresh) {
                btnRefresh.addEventListener('click', () => {
                    localStorage.setItem('activeShippingTab', 'in-transit-tab');
                    location.reload();
                });
            }
        };

        // Initial tab button setup
        const activeTab = document.querySelector('#shippingTabs .nav-link.active');
        updateTabButtons(activeTab?.id || 'open-orders-tab');

        // Listen for tab change
        document.querySelectorAll('#shippingTabs button[data-bs-toggle="tab"]').forEach(tab => {
            tab.addEventListener('shown.bs.tab', e => updateTabButtons(e.target.id));
        });

        // Restore active tab after reload
        const savedTab = localStorage.getItem('activeShippingTab');
        if (savedTab) {
            const tabEl = document.getElementById(savedTab);
            if (tabEl) tabEl.click();
            localStorage.removeItem('activeShippingTab');
        }

        // Modal weight updates
        modalBox.addEventListener('change', () => {
            const selected = modalBox.selectedOptions[0];
            if (selected) {
                modalBoxDimensions.value = `${selected.dataset.length}×${selected.dataset.width}×${selected.dataset.height}`;
                modalBoxWeight.value = formatLbOz(parseInt(selected.dataset.weightLb || 0), parseFloat(selected.dataset.weightOz || 0));
            } else {
                modalBoxDimensions.value = '';
                modalBoxWeight.value = '0 lb 0 oz';
            }
            updateTotalWeight();
        });
        modalProductWeightLb.addEventListener('input', updateTotalWeight);
        modalProductWeightOz.addEventListener('input', updateTotalWeight);

        // Reset modal on create shipment
        document.querySelectorAll('.btn-create-shipment').forEach(btn => {
            btn.addEventListener('click', () => {
                modalOrderId.value = btn.dataset.orderId;
                modalBoxDimensions.value = '';
                modalBoxWeight.value = '';
                modalProductWeightLb.value = '';
                modalProductWeightOz.value = '';
                modalTotalWeight.value = '';
            });
        });

        // Export CSV
        const exportModalEl = document.getElementById('exportModal');
        const exportTabSelect = document.getElementById('exportTabSelect');
        document.getElementById('exportForm').addEventListener('submit', e => {
            e.preventDefault();
            const activeTab = document.querySelector('#shippingTabs .nav-link.active');
            if (!activeTab) return alert('No active tab found!');

            const tab = activeTab.id.replace('-tab', '');
            const tableIdMap = {
                'open-orders': 'openOrdersTable',
                'in-transit': 'inTransitTable',
                'delivered': 'deliveredTable',
                'exceptions': 'exceptionsTable'
            };
            const table = document.getElementById(tableIdMap[tab]);
            if (!table) return alert('Table not found for export!');

            const selectedRows = [...table.querySelectorAll('tbody tr')]
                .filter(row => row.querySelector('input[type="checkbox"]')?.checked);
            if (!selectedRows.length) return alert('No rows selected for export!');

            const essentialCols = ['Order #', 'Customer', 'Carrier', 'Tracking #', 'Status', 'Dates', 'Cost'];
            const tableHeaders = [...table.querySelectorAll('thead th')]
                .map(th => th.innerText.trim())
                .filter(Boolean)
                .slice(1, -1);

            const headers = [...new Set([...essentialCols, ...tableHeaders])];
            let csv = headers.map(h => `"${h}"`).join(',') + '\n';

            selectedRows.forEach(row => {
                const cells = [...row.children].slice(1, -1);
                const rowData = headers.map(col => {
                    switch (col) {
                        case 'Order #': return row.querySelector('td:nth-child(2)')?.innerText ?? '';
                        case 'Customer': return row.querySelector('td:nth-child(3)')?.innerText ?? '';
                        case 'Carrier': {
                            const select = row.querySelector('td:nth-child(8) select');
                            return select ? select.value : row.querySelector('td:nth-child(4)')?.innerText ?? '';
                        }
                        case 'Tracking #': return row.querySelector('td:nth-child(5)')?.innerText ?? '';
                        case 'Status': return row.querySelector('td:nth-child(8)')?.innerText ?? '';
                        case 'Dates': return row.querySelector('td:nth-child(6)')?.innerText ?? '';
                        case 'Cost': return row.querySelector('td:nth-child(9)')?.innerText ?? '';
                        default:
                            const idx = tableHeaders.indexOf(col);
                            return idx >= 0 && cells[idx] ? cells[idx].innerText : '';
                    }
                });
                csv += rowData.map(v => `"${String(v).replace(/"/g, '""')}"`).join(',') + '\n';
            });

            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = `shipments_${tab}_${new Date().toISOString().slice(0, 10)}.csv`;
            document.body.appendChild(link);
            link.click();
            link.remove();

            const modal = bootstrap.Modal.getInstance(exportModalEl);
            if (modal) modal.hide();
        });
    });
</script>


@if (session('label_gif'))
    <script>
        window.open("{{ session('label_gif') }}", "_blank");
    </script>
@endif
@endsection
