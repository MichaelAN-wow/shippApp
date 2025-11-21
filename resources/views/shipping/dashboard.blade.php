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
    $(document).ready(function () {
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

        // These JSON Blade injections were causing errors if Blade returned HTML.
        // Ensure backend sends pure JSON only.
        const openOrders = @json($openOrders ?? []);
        const inTransit = @json($inTransit ?? []);
        const delivered = @json($delivered ?? []);
        const exceptions = @json($exceptionShipments ?? []);
        console.log({ openOrders, inTransit, delivered });
        // -------------------------------------------------------------
        // Utility Functions
        // -------------------------------------------------------------
        const formatLbOz = (lb, oz) => `${lb} lb ${oz.toFixed(1)} oz`;

        const addLbOz = (lb1, oz1, lb2, oz2) => {
            const totalOz = oz1 + oz2;
            const extraLb = Math.floor(totalOz / 16);
            return {
                lb: lb1 + lb2 + extraLb,
                oz: totalOz % 16
            };
        };

        const updateTotalWeight = () => {
            const sel = modalBox.selectedOptions[0];
            const boxLb = parseInt(sel?.dataset.weightLb || 0);
            const boxOz = parseFloat(sel?.dataset.weightOz || 0);
            const prodLb = parseInt(modalProductWeightLb.value || 0);
            const prodOz = parseFloat(modalProductWeightOz.value || 0);
            const total = addLbOz(boxLb, boxOz, prodLb, prodOz);
            modalTotalWeight.value = formatLbOz(total.lb, total.oz);
        };

        // -------------------------------------------------------------
        // Tab Buttons
        // -------------------------------------------------------------
        const updateTabButtons = (activeTabId) => {
            let html = '';
            if (activeTabId === 'open-orders-tab') {
                html = `<button class="btn btn-primary btn-sm">Purchase Labels</button>`;
            } else if (activeTabId === 'in-transit-tab') {
                html = `<button class="btn btn-success btn-sm" id="btnRefreshStatus">Refresh Status</button>`;
            }
            tabButtonsContainer.innerHTML = html;

            // Bind refresh
            const btnRefresh = document.getElementById('btnRefreshStatus');
            if (btnRefresh) {
                btnRefresh.addEventListener('click', () => {
                    localStorage.setItem('activeShippingTab', 'in-transit-tab');
                    location.reload();
                });
            }
        };

        const activeTab = document.querySelector('#shippingTabs .nav-link.active');
        updateTabButtons(activeTab?.id || 'open-orders-tab');

        document.querySelectorAll('#shippingTabs button[data-bs-toggle="tab"]').forEach(tab => {
            tab.addEventListener('shown.bs.tab', e => updateTabButtons(e.target.id));
        });

        const savedTab = localStorage.getItem('activeShippingTab');
        if (savedTab) {
            const el = document.getElementById(savedTab);
            if (el) el.click();
            localStorage.removeItem('activeShippingTab');
        }

        // -------------------------------------------------------------
        // Modal Weight
        // -------------------------------------------------------------
        modalBox.addEventListener('change', () => {
            const sel = modalBox.selectedOptions[0];
            if (sel) {
                modalBoxDimensions.value = `${sel.dataset.length}×${sel.dataset.width}×${sel.dataset.height}`;
                modalBoxWeight.value = formatLbOz(
                    parseInt(sel.dataset.weightLb || 0),
                    parseFloat(sel.dataset.weightOz || 0)
                );
            } else {
                modalBoxDimensions.value = '';
                modalBoxWeight.value = '0 lb 0 oz';
            }
            updateTotalWeight();
        });

        modalProductWeightLb.addEventListener('input', updateTotalWeight);
        modalProductWeightOz.addEventListener('input', updateTotalWeight);

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

        // -------------------------------------------------------------
        // CSV Export
        // -------------------------------------------------------------
        const exportModalEl = document.getElementById('exportModal');
        document.getElementById('exportForm').addEventListener('submit', e => {
            e.preventDefault();
            
            try {
                // Get the selected tab from the dropdown
                const tabSelect = document.getElementById('exportTabSelect');
                const tab = tabSelect.value;

                // Map tab to dataset
                const dataMap = {
                    'open-orders': openOrders,
                    'in-transit': inTransit,
                    'delivered': delivered,
                    'exceptions': exceptions
                };

                const dataToExport = dataMap[tab] ?? [];
                if (!dataToExport.length) throw new Error('No data found for export!');

                // Fixed fields to export
                const fields = [
                    { key: 'order_id', label: 'Order #' },
                    { key: 'order_reference', label: 'Order Reference' },
                    { key: 'customer_name', label: 'Customer' },
                    { key: 'carrier', label: 'Carrier' },
                    { key: 'tracking_number', label: 'Tracking #' },
                    { key: 'status', label: 'Status' },
                    { key: 'status_description', label: 'Status Description' },
                    { key: 'service', label: 'Service' },
                    { key: 'created_at', label: 'Created At' },
                    { key: 'estimated_delivery', label: 'Estimated Delivery' },
                    { key: 'delivered_at', label: 'Delivered At' },
                    { key: 'last_location', label: 'Last Location' },
                    { key: 'product_weight', label: 'Product Weight' },
                    { key: 'total_weight', label: 'Total Weight' },
                    { key: 'cost', label: 'Cost' }
                ];

                // Build CSV header
                let csv = fields.map(f => `"${f.label}"`).join(',') + '\n';

                dataToExport.forEach(item => {
                    const rowData = fields.map(f => {
                        let value = item[f.key] ?? '';
                        return `"${String(value).replace(/"/g, '""')}"`;
                    });
                    csv += rowData.join(',') + '\n';
                });

                // Download CSV
                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = `shipments_${tab}_${new Date().toISOString().slice(0, 10)}.csv`;
                document.body.appendChild(link);
                link.click();
                link.remove();

                // Show success toastr
                toastr.success('CSV exported successfully.');

            } catch (err) {
                console.error(err);
                toastr.error(err.message || 'Failed to export CSV.');
            }
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        const shipmentModalEl = document.getElementById('shipmentModal');
        const shipmentModal = new bootstrap.Modal(shipmentModalEl);

        shipmentModalEl.addEventListener('hidden.bs.modal', () => {
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.getElementById('m_boxes').innerHTML = '';
        });

        // JSON Blade imports
        const inTransitData = @json($inTransit ?? []);
        const deliveredData = @json($delivered ?? []);

        const getStatusBadgeClass = (status) => {
            switch ((status ?? '').toLowerCase()) {
                case 'delivered': return 'bg-success';
                case 'in transit': return 'bg-primary';
                case 'pending': return 'bg-warning';
                case 'failed': return 'bg-danger';
                default: return 'bg-secondary';
            }
        };

        const populateShipmentModal = (s) => {
            document.getElementById('m_ship_id').innerText = s.id ?? '-';
            document.getElementById('m_order_id').innerText = s.order_id ?? '-';
            document.getElementById('m_customer').innerText = s.customer_name ?? '-';
            document.getElementById('m_carrier').innerText = s.carrier ?? '-';
            document.getElementById('m_tracking').innerText = s.tracking_number ?? '-';
            document.getElementById('m_last_scan').innerText = s.last_tracked_at ?? '-';
            document.getElementById('m_eta').innerText = s.estimated_delivery ?? '-';

            const statusEl = document.getElementById('m_status');
            statusEl.innerText = s.status ?? '-';
            statusEl.className = 'badge ' + getStatusBadgeClass(s.status);

            document.getElementById('m_delivered_at').innerText = s.delivered_at ?? '-';
            document.getElementById('m_service').innerText = s.service ?? '-';

            const boxList = document.getElementById('m_boxes');
            boxList.innerHTML = '';

            if (s.boxes?.length) {
                s.boxes.forEach(box => {
                    const li = document.createElement('li');
                    li.className = 'list-group-item d-flex justify-content-between';
                    li.innerHTML = `<span>Box #${box.id}</span><span class="fw-bold">${box.weight} lbs</span>`;
                    boxList.appendChild(li);
                });
            } else {
                const li = document.createElement('li');
                li.className = 'list-group-item text-muted';
                li.innerText = 'No boxes recorded';
                boxList.appendChild(li);
            }
        };

        const showShipmentById = (id, dataset) => {
            const s = dataset.find(x => String(x.id) === String(id));
            if (!s) return toastr.error('Shipment not found.');
            populateShipmentModal(s);
            shipmentModal.show();
        };

        // Delegated table bindings
        const inTransitTable = document.getElementById('inTransitTable');
        if (inTransitTable) {
            inTransitTable.addEventListener('click', (e) => {
                const viewBtn = e.target.closest('.btn-in-transit-view');
                if (viewBtn) {
                    e.preventDefault();
                    showShipmentById(viewBtn.dataset.shipmentId, inTransitData);
                    return;
                }

                const markDeliveredBtn = e.target.closest('.btn-mark-delivered');
                if (markDeliveredBtn) {
                    e.preventDefault();
                    const id = markDeliveredBtn.dataset.shipmentId;
                    const url = markDeliveredBtn.dataset.updateUrl;
                    if (!confirm(`Mark shipment #${id} as Delivered?`)) return;

                    const csrf = document.querySelector('meta[name="csrf-token"]').content;

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        body: JSON.stringify({ status: 'delivered' })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            const row = markDeliveredBtn.closest('tr');
                            if (row) row.querySelector('td:nth-child(8)').textContent = 'Delivered';
                            toastr.success('Shipment marked as Delivered!');
                        } else {
                            toastr.error(data.message || 'Failed to update shipment.');
                        }
                    })
                    .catch((e) => console.log(e));
                    return;
                }

                const reportLostBtn = e.target.closest('.btn-report-lost');
                if (reportLostBtn) {
                    e.preventDefault();
                    const id = reportLostBtn.dataset.shipmentId;
                    const url = reportLostBtn.dataset.updateUrl;

                    if (!confirm(`Report shipment #${id} as LOST?`)) return;

                    const csrf = document.querySelector('meta[name="csrf-token"]').content;

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        body: JSON.stringify({ status: 'cancelled' })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            const row = reportLostBtn.closest('tr');
                            if (row) row.querySelector('td:nth-child(8)').textContent = 'Lost';
                            toastr.warning('Shipment reported as LOST.');
                        } else {
                            toastr.error(data.message || 'Failed to update shipment.');
                        }
                    })
                    .catch((e) => console.log(e));

                    return;
                }
            });
        }

        const deliveredTable = document.getElementById('deliveredTable');
        if (deliveredTable) {
            deliveredTable.addEventListener('click', (e) => {
                const btn = e.target.closest('.btn-delivered-view');
                if (!btn) return;
                e.preventDefault();
                showShipmentById(btn.dataset.shipmentId, deliveredData);
            });
        }
    });

</script>

@if (session('label_gif'))
    <script>
        window.open("{{ session('label_gif') }}", "_blank");
    </script>
@endif
@endsection
