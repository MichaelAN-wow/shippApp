@extends('layouts.admin_master')

@section('content')
<div class="container py-5">
    <h1 class="mb-5 fw-bold text-center">Shipping Connections</h1>
    @if(session('success'))
        <div class="alert alert-success py-2 mx-auto" style="max-width:600px">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger py-2 mx-auto" style="max-width:600px">
            <ul class="mb-0">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4 justify-content-center mb-5">
        @foreach(['UPS', 'USPS', 'FedEx'] as $carrier)
            @php
                $conn = $connections->firstWhere('carrier', $carrier);
            @endphp
            <div class="col-12 col-md-6 col-lg-4 d-flex align-items-stretch">
                <div class="card shadow-lg border-0 w-100 p-3 d-flex flex-column justify-content-between" style="min-height:440px;">
                    <div class="text-center mb-3">
                        <div style="display: flex; flex-direction: column; align-items: center">
                            <img src="/images/carriers/{{ strtolower($carrier) }}.png" alt="{{ $carrier }} logo" style="width:82px; height:82px; object-fit:contain; margin-bottom:1rem;">
                            @if($conn)
                                @if($conn->error)
                                    <span class="badge bg-danger fs-6 px-3 py-2 mt-2">Error</span>
                                @elseif($conn->api_key && $conn->account_number)
                                    <span class="badge bg-success fs-6 px-3 py-2 mt-2">Connected</span>
                                @else
                                    <span class="badge bg-secondary fs-6 px-3 py-2 mt-2">Disconnected</span>
                                @endif
                            @else
                                <span class="badge bg-secondary fs-6 px-3 py-2 mt-2">Disconnected</span>
                            @endif
                        </div>
                        <h4 class="fw-bold mt-3 mb-0">{{ $carrier }}</h4>
                    </div>
                    <div class="flex-grow-1 px-2 ">
                        @if($conn && $conn->api_key && $conn->account_number)
                            <div class="my-2 text-center">
                                <span class="text-muted">Nickname:</span> <strong>{{ $conn->nickname ?? $conn->account_number }}</strong><br>
                                <span class="text-muted">Last sync:</span> <strong>{{ $conn->last_sync_at ? $conn->last_sync_at->format('Y-m-d H:i') : 'Never' }}</strong>
                            </div>
                            <div class="mt-4">
                                @if(in_array($carrier, ['UPS', 'FedEx']))
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span>Negotiated Rates</span>
                                        <input class="form-check-input ms-2" type="checkbox" {{ $conn->negotiated_rates ? 'checked' : '' }} disabled>
                                    </div>
                                @endif
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span>Default Service</span>
                                    <span class="ms-2 small text-muted">{{ $conn->default_service ?? '-' }}</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span>Default Packaging</span>
                                    <span class="ms-2 small text-muted">{{ $conn->default_packaging ?? '-' }}</span>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info text-center py-2 px-2 my-3 ">
                                <span>Connect your <b>{{ $carrier }}</b> account to purchase shipping labels.</span>
                            </div>
                        @endif
                    </div>
                    <div class="text-center mt-2">
                        @if($conn && $conn->api_key && $conn->account_number)
                            <form class="d-inline" action="{{ route('shipping.connections.destroy', $conn->id) }}" method="POST" onsubmit="return confirm('Disconnect {{ $carrier }}?');">
                                @csrf 
                                @method('DELETE')
                                <button class="btn btn-outline-danger btn-lg me-1">Disconnect</button>
                            </form>

                            <a href="#" class="btn btn-outline-secondary btn-lg me-1" data-bs-toggle="modal" data-bs-target="#manageModal_{{ $carrier }}">
                                Manage
                            </a>

                            <a href="#" class="btn btn-outline-success btn-lg">Test Label</a>

                        @else
                            <a href="#" class="btn btn-primary btn-lg w-75" data-bs-toggle="modal" data-bs-target="#connectModal_{{ $carrier }}">
                                Connect {{ $carrier }}
                            </a>
                        @endif
                    </div>

                </div>
                <!-- Connect Modal -->
                <div class="modal fade" id="connectModal_{{ $carrier }}" tabindex="-1">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                      <div class="modal-header"><h5 class="modal-title">Connect {{ $carrier }}</h5></div>
                      <div class="modal-body">
                        <div class="mb-3">
                            <span class="badge bg-info mb-2">No API Key Required!</span>
                            <div>Click below to connect with OAuth and grant access.</div>
                        </div>
                        <form action="{{ route('shipping.connections.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="carrier" value="{{ $carrier }}">
                            <input type="hidden" name="mode" value="oauth">
                            <button class="btn btn-primary w-100">Authorize with {{ $carrier }}</button>
                        </form>
                        <div class="mt-3">
                            <a href="#" data-bs-toggle="collapse" data-bs-target="#manualDrawer_{{ $carrier }}" class="small text-secondary">Advanced: Manual Entry</a>
                            <div class="collapse mt-2" id="manualDrawer_{{ $carrier }}">
                                <form action="{{ route('shipping.connections.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="carrier" value="{{ $carrier }}">
                                    <input type="hidden" name="mode" value="manual">
                                    <div class="mb-2"><input name="account_number" class="form-control" placeholder="Account #" required></div>
                                    <div class="mb-2"><input name="api_key" class="form-control" placeholder="API Key"></div>
                                    <div class="mb-2"><input name="api_secret" class="form-control" placeholder="API Secret"></div>
                                    <div class="mb-2">
                                        <select name="sandbox" class="form-select">
                                            <option value="1">Sandbox</option>
                                            <option value="0">Production</option>
                                        </select>
                                    </div>
                                    <button class="btn btn-outline-primary w-100">Submit</button>
                                </form>
                            </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- End Card col -->
            </div>
        @endforeach
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow border-0 mb-5 px-0 px-md-4">
                <div class="card-header bg-white text-center pb-0">
                    <h3 class="fw-bold py-3 mb-0">Global Shipping Defaults</h3>
                </div>
                <div class="card-body px-1 px-md-5 py-4">
                    <form>
                        <div class="row gy-4 gx-3 align-items-center justify-content-center">
                            <div class="col-12 col-md-6 col-lg-4">
                                <label class="form-label">Ship-From Address</label>
                                <input class="form-control" value="{{ $shipFromAddress ?? '' }}" readonly>
                                <a href="#" class="btn btn-outline-secondary btn-sm mt-2">Edit</a>
                            </div>
                            <div class="col-12 col-md-6 col-lg-4">
                                <label class="form-label">Default Weight Unit</label>
                                <select class="form-select" disabled>
                                    <option value="oz">Ounces (oz)</option>
                                    <option value="g">Grams (g)</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6 col-lg-4">
                                <label class="form-label">Label Format</label>
                                <select class="form-select" disabled>
                                    <option>PDF</option>
                                    <option>ZPL</option>
                                </select>
                            </div>
                        </div>
                        <div class="row gy-4 gx-3 mt-1 align-items-center justify-content-center">
                            <div class="col-12 col-md-6 col-lg-4">
                                <label class="form-label">Insurance Options</label>
                                <input class="form-control" value="{{ $insuranceOptions ?? '' }}" readonly>
                            </div>
                            <div class="col-12 col-md-6 col-lg-4">
                                <label class="form-label">Signature Required</label>
                                <input class="form-control" value="{{ $signatureRequired ?? '' }}" readonly>
                            </div>
                            <div class="col-12 col-md-6 col-lg-4">
                                <label class="form-label">Auto-Download</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="autoDownloadToggle" {{ isset($autoDownload) && $autoDownload ? 'checked' : '' }} disabled>
                                    <label class="form-check-label" for="autoDownloadToggle">After purchase</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="text-end small text-muted mt-4">API creds encrypted & tokens refreshed automatically.</div>
</div>
@endsection
