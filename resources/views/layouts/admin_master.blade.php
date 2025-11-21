<!DOCTYPE html>
<html lang="en">
    

@php
    try {
        $userType = Auth::user()->type;

        $company = Auth::user()->company;
       
        $companyName = $company ? $company->name : 'OHS';
        $companyLogo = ($company && !empty($company->logo_url)) ? asset('storage/' . $company->logo_url) : null;
    } catch (\Exception $e) {
        // Log the error
        Log::error('Error fetching user type: ' . $e->getMessage());

        // Redirect to login
        header('Location: ' . route('login'));
        exit();
    }

    function isActiveRoute($route, $output = 'active')
    {
        return Route::currentRouteName() == $route ? $output : '';
    }

    function areActiveRoutes(array $routes, $output = 'active')
    {
        foreach ($routes as $route) {
            if (Route::currentRouteName() == $route) {
                return $output;
            }
        }
        return '';
    }
@endphp

<head>

    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>OHS- On Hand Solution</title>
        
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

        
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        @stack('scripts')
        <!-- Standard Favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/favicon_io/favicon.ico') }}">

        <!-- PNG Favicons -->
        <link rel="shortcut icon" type="image/png" sizes="16x16"
            href="{{ asset('images/favicon_io/favicon-16x16.png') }}">
        <link rel="shortcut icon" type="image/png" sizes="32x32"
            href="{{ asset('images/favicon_io/favicon-32x32.png') }}">

        <!-- Apple Touch Icon -->
        <link rel="shortcut apple-touch-icon" sizes="180x180"
            href="{{ asset('images/favicon_io/apple-touch-icon.png') }}">

        <!-- Android Chrome Icons -->
        <link rel="shortcut icon" type="image/png" sizes="192x192"
            href="{{ asset('images/favicon_io/android-chrome-192x192.png') }}">
        <link rel="shortcut icon" type="image/png" sizes="512x512"
            href="{{ asset('images/favicon_io/android-chrome-512x512.png') }}">

        <!-- Manifest File for Web App -->
        <link rel="manifest" href="{{ asset('images/favicon_io/site.webmanifest') }}">


        <link href="{{ asset('backend/css/styles.css') }}" rel="stylesheet" />
        

        <link href="{{ asset('libs/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />

        <link href="{{ asset('libs/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />


        <script src="{{ asset('libs/font-awesome/5.15.1/js/all.min.js') }}" crossorigin="anonymous"></script>

        <link href="/frontend/plugins/toast/toastr.min.css" rel="stylesheet" />
        <link href="{{ asset('libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />

        <script src="{{ asset('libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js') }}"></script>
        <script src="https://js.stripe.com/v3/"></script>

        <link href="{{ asset('backend/css/custom.css') }}" rel="stylesheet" />
        <meta name="csrf-token" content="{{ csrf_token() }}">

       <style>
    .category-item {
        display: inline-block;
        padding: 0.25em 0.5em;
        border-radius: 0.25em;
    }

    .profile-image {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        object-fit: cover;
    }

    #insufficientMaterialsList,
    #insufficientProductsList {
        font-family: 'Arial Narrow', Arial, sans-serif;
        font-size: 16px;
    }
</style>

    </head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dashboard">
        <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#"><i
                class="fas fa-bars"></i></button>
        <a class="navbar-brand" style="color: white">
            <img src="{{ asset('images/dashboard-logo.png') }}" alt="Logo" height="auto;" width="4rem;">
        </a>

        <!-- Navbar Search-->
        <form class="d-none d-md-inline-block form-inline ml-auto mr-0 mr-md-3 my-2 my-md-0">
            <div class="input-group">
                {{-- <input class="form-control" type="text" placeholder="Search for..." aria-label="Search"
                    aria-describedby="basic-addon2" />
                <div class="input-group-append">
                    <button class="btn btn-primary" type="button"><i class="fas fa-search"></i></button>
                </div> --}}
            </div>
        </form>
        
        <!-- Navbar-->
        
<!-- Centered Market Tracker Icon -->
<div class="d-none d-md-block mx-auto text-center" style="flex-grow: 1;">
    <span onclick="document.getElementById('marketTrackerPopup').style.display='block'"
          style="cursor:pointer; font-size: 20px; transition: 0.2s;">
        <span style="font-size: 30px;" class="hover-glow">ðŸ’°</span>
    </span>
</div>

<style>
    .hover-glow:hover {
        color: #FFD700;
        text-shadow: 0 0 5px #FFD700;
    }
</style>
        
        <ul class="navbar-nav ml-auto ml-md-0">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="alertsDropdown" href="#" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
<i class="fas fa-bell fa-fw" style="color: #FFD700; font-size: 20px;"></i>
                    <span id="alertCount" class="badge badge-danger">0</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="alertsDropdown">
                    <h6 class="dropdown-header">Alerts</h6>
                    <div id="alertsList"></div>
                </div>
            </li>
            
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ $companyName }}
                    <img src="{{ $companyLogo ?? asset('images/default_logo.png') }}"  alt="{{ Auth::user()->name }}"
                        class="profile-image">
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                    <div class="dropdown-item">
                        {{ Auth::user()->name }}<br>
                        {{ Auth::user()->email }}
                    </div>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editAccountModal">Manage Account</a>
                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#upgradePlans">Upgrade Plans</a>
                    @if (Auth::user()->type == 'super_admin' || Auth::user()->type == 'admin')
                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#settingsModal">Settings</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a class="dropdown-item" href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">Log out</a>
                    </form>
                </div>
            </li>
        </ul>
    </nav>
    <!-- Edit Account Modal -->
    <div class="modal fade" id="editAccountModal" tabindex="-1" role="dialog"
        aria-labelledby="editAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAccountModalLabel">Edit Account</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editAccountForm" method="POST" enctype="multipart/form-data"
                        action="{{ route('users.update', Auth::user()->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="fullName">Full Name</label>
                            <input type="text" class="form-control" id="fullName" name="name"
                                value="{{ Auth::user()->name }}" required>
                            <input type="text" class="form-control" id="type" name="type"
                                value="{{ Auth::user()->type }}" hidden>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <strong>{{ Auth::user()->email }}</strong>
                        </div>
                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input type="password" class="form-control" id="password" name="password"
                                autocomplete="new-password">
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirmation"
                                name="password_confirmation" autocomplete="new-password">
                        </div>
                        @if (Auth::user()->type == 'super_admin' || Auth::user()->type == 'admin')
                        <div class="form-group">
                            <!-- Display the company name -->
                            <label for="company_name">Company Name</label>
                            <input type="text" class="form-control" id="company_name" name="company_name" 
                                value="{{ $companyName }}">
                        </div>

                        <div class="form-group">
                            <!-- Display the current company logo -->
                            <label for="current_company_logo">Current Company Logo</label>
                            <div>
                                @if ($companyLogo)
                                    <img src="{{ $companyLogo }}" alt="Company Logo" 
                                        style="width: 100px; height: 100px; object-fit: cover; border-radius: 10px;">
                                @else
                                    <p>No logo available</p>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="small mb-1" for="photo">Update Company Logo</label>
                            <input class="form-control-file" id="photo" name="photo" type="file" accept="image/*"
                                onchange="previewPhoto();" />
                        </div>

                        <!-- Preview for new company logo -->
                        <div id="photo-preview-container" style="display:none; margin-top: 10px; align-items: center;">
                            <img id="photoPreview" src="#" alt="Photo Preview" style="width: 100px; height: 100px; object-fit: cover; border-radius: 10px;" />
                            <div style="margin-left: 15px; display: flex; flex-direction: column;">
                                <span id="photo-name"></span>
                                <span id="photo-info"></span>
                            </div>
                            <div style="margin-left: auto; display: flex; flex-direction: column;">
                                <button id="edit-button" onclick="editPhoto();" style="background: none; border: none; cursor: pointer;">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button id="delete-button" onclick="deletePhoto();" style="background: none; border: none; cursor: pointer;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        @endif
                        <div id="passwordAlert" class="alert alert-danger" style="display: none;"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" onclick="validatePassword(event)">Save
                        changes</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="upgradePlans" tabindex="-1" role="dialog" aria-labelledby="upgradePlansModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAccountModalLabel">Upgrade Plans</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="payment-form">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <p class="lead"><strong>$39 USD/monthly</strong></p>
                            <p class="text-muted">Select your payment method below.</p>
                        </div>
                        <div class="form-group">
                            <label for="card-element">Credit or Debit Card</label>
                            <div id="card-element" class="form-control">
                                <!-- Stripe Card Element will be inserted here -->
                            </div>
                            <!-- Error message placeholder -->
                            <div id="card-errors" class="text-danger mt-2" role="alert"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary btn-block" id="submit-button" type="submit">Pay Now</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">

<!-- SHIPPING MANAGER LINK -->
<a class="nav-link {{ request()->is('shipping*') ? 'active' : '' }}" href="{{ route('shipping.dashboard') }}">
    <div class="sb-nav-link-icon">
        <i class="fas fa-truck" style="color: #FFD700; width: 20px;"></i>
    </div>
    Shipping Manager
</a>
<div class="collapse {{ request()->is('shipping*') ? 'show' : '' }}"
     id="collapseShipping" aria-labelledby="headingShipping"
     data-parent="#sidenavAccordion">
    <nav class="sb-sidenav-menu-nested nav">
        <!-- Dashboard -->
        <a class="nav-link {{ request()->routeIs('shipping.dashboard') ? 'active' : '' }}"
           href="{{ route('shipping.dashboard') }}">Dashboard</a>

        <!-- Contacts -->
        <a class="nav-link {{ request()->routeIs('shipping.contacts') ? 'active' : '' }}"
           href="{{ route('shipping.contacts') }}">Contacts</a>

        <!-- Box Inventory -->
        <a class="nav-link {{ request()->routeIs('shipping.box_inventory.index') ? 'active' : '' }}"
           href="{{ route('shipping.box_inventory.index') }}">Box Inventory</a>

        <!-- Shipping Connections -->
        <a class="nav-link {{ request()->routeIs('shipping.connections.index') ? 'active' : '' }}"
           href="{{ route('shipping.connections.index') }}">Shipping Connections</a>

        
    </nav>
</div>


                    <!-- Material Link -->
<a class="nav-link {{ request()->is('materials/all') || request()->is('materials/category/*') ? 'active' : '' }}"
   href="#" data-toggle="collapse" data-target="#collapseMaterials"
   aria-expanded="false" aria-controls="collapseMaterials">
    <div class="sb-nav-link-icon">
        <i class="fas fa-vial" style="color: #FFD700; width: 20px;"></i>
    </div>
    Materials
</a>    
                    
{{-- Materials Content --}}
<div class="collapse"
    id="collapseMaterials" aria-labelledby="headingMaterials"
    data-parent="#sidenavAccordion">
    <nav class="sb-sidenav-menu-nested nav">
        <a class="nav-link {{ request()->is('materials/all') ? 'active' : '' }}"
            href="{{ route('all.materials') }}">All Materials</a>
        @foreach (App\Models\Category::where('company_id', session('company_id'))->where('type', 1)->orderBy('name')->get() as $category)
            <a class="nav-link {{ request()->is('materials/category/' . $category->id) ? 'active' : '' }}"
                href="{{ route('showMaterialsByCategory', $category->id) }}">
                {{ $category->name }}
            </a>
        @endforeach
    </nav>
</div>

                        {{-- Products Link --}}
                        <a class="nav-link {{ request()->is('products/all') || request()->is('products/categories/*') ? 'active' : '' }}"
                            href="#" data-toggle="collapse" data-target="#collapseProducts"
                            aria-expanded="{{ request()->is('products/all') ? 'true' : 'false' }}"
                            aria-controls="collapseProducts">
                            <div class="sb-nav-link-icon">
                                <img src="{{ asset('images/svg/products.svg') }}" alt="Products Icon" width="16" height="16">
                            </div>
                            Products
                        </a>
                        {{-- Products Content --}}
                        <div class="collapse {{ request()->is('products/all') || request()->is('products/categories/*') ? 'show' : '' }}"
                            id="collapseProducts" aria-labelledby="headingProducts" data-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link {{ request()->is('products/all') ? 'active' : '' }}"
                                    href="{{ route('all.products') }}">All Products</a>
                                @foreach (App\Models\Category::where('company_id', session('company_id'))->where('type', 2)->orderBy('name')->get() as $category)
                                    <a class="nav-link {{ request()->is('products/categories/' . $category->id) ? 'active' : '' }}"
                                        href="{{ route('products-category.show', $category->id) }}">
                                        {{ $category->name }}
                                    </a>
                                @endforeach
                            </nav>
                        </div>
                        
                        <!-- Make Sheet Link -->
<a class="nav-link {{ request()->is('make-sheet') ? 'active' : '' }}" href="{{ url('/make-sheet') }}">
    <div class="sb-nav-link-icon">
        <i class="fas fa-list"></i>
    </div>
    Make Sheet
</a>

<!-- Market Prep -->
<a class="nav-link {{ request()->is('market-prep*') ? 'active' : '' }}"
   href="#" data-toggle="collapse" data-target="#collapseMarket"
   aria-expanded="{{ request()->is('market-prep*') ? 'true' : 'false' }}"
   aria-controls="collapseMarket">
    <div class="sb-nav-link-icon">
        <i class="fas fa-store" style="color: #FFD700;"></i>
    </div>
    Market Prep
    <div class="sb-sidenav-collapse-arrow"></div>
</a>
<div class="collapse {{ request()->is('market-prep*') ? 'show' : '' }}"
     id="collapseMarket" aria-labelledby="headingMarket" data-parent="#sidenavAccordion">
    <nav class="sb-sidenav-menu-nested nav">

        <a class="nav-link {{ request()->is('market/loadout') ? 'active' : '' }}"
           href="{{ route('market.loadout') }}">
            <i class="fas fa-dolly" style="color: #FFD700; width: 20px;"></i>
            Load Out
        </a>

        <a class="nav-link {{ request()->is('market-prep/restock') ? 'active' : '' }}"
           href="{{ route('market.restock') }}">
            <i class="fas fa-dolly-flatbed" style="color: #FFD700; width: 20px;"></i>
            Replenish
        </a>

    </nav>
</div>

<!-- Orders Link -->
<a class="nav-link {{ request()->is('orders/all', 'all-suppliers', 'materials/reorder-suggestions') ? 'active' : '' }}"
    href="#" data-toggle="collapse" data-target="#collapseOrders"
    aria-expanded="{{ request()->is('orders/all', 'all-suppliers', 'materials/reorder-suggestions') ? 'true' : 'false' }}"
    aria-controls="collapseOrders">
    <div class="sb-nav-link-icon">
        <img src="{{ asset('images/svg/purchase.svg') }}" alt="Purchase Icon" width="16" height="16">
    </div>
    Purchase
    <div class="sb-sidenav-collapse-arrow"></div>
</a>

<!-- Orders Content -->
<div class="collapse {{ request()->is('orders/all', 'all-suppliers', 'materials/reorder-suggestions') ? 'show' : '' }}"
    id="collapseOrders" aria-labelledby="headingOrders" data-parent="#sidenavAccordion">
    <nav class="sb-sidenav-menu-nested nav">
        <a class="nav-link {{ request()->is('orders/all') ? 'active' : '' }}"
            href="{{ route('all.orders') }}">
            <i class="fas fa-list-alt" style="color: #FFD700; width: 20px;"></i>
Orders List</a>
        <a class="nav-link {{ request()->is('all-suppliers') ? 'active' : '' }}"
            href="{{ route('all.suppliers') }}">
            <i class="fas fa-handshake" style="color: #FFD700; width: 20px;"></i> Suppliers</a>
                            </nav>
                        </div>

 <a class="nav-link {{ request()->is('sales/all', 'sales/retail', 'sales/whole') ? 'active' : '' }}"
                            href="#" data-toggle="collapse" data-target="#collapseInvoice"
                            aria-expanded="{{ request()->is('all.sales', 'sales/retail', 'sales/whole') ? 'true' : 'false' }}"
                            aria-controls="collapseInvoice">
                            <div class="sb-nav-link-icon">
                                <img src="{{ asset('images/svg/sales.svg') }}" alt="Sales Icon" width="16" height="16">
                            </div>
                            Transactions
                            <div class="sb-sidenav-collapse-arrow"></div>
                        </a>
                        <!-- Sales Content -->
                        <div class="collapse {{ request()->is('sales/all', 'sales/retail', 'sales/wholesale', 'sales/shopify') ? 'show' : '' }}"
                            id="collapseInvoice" aria-labelledby="headingInvoice" data-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link {{ request()->routeIs('all.sales') ? 'active' : '' }}"
                                    href="{{ route('all.sales') }}">All Sales</a>
                                    <a class="nav-link {{ request()->routeIs('shopify.sales') ? 'active' : '' }}"
   href="{{ route('shopify.sales') }}">
    <i class="fab fa-shopify" style="color: #96BF48; width: 20px;"></i> Shopify
</a>
<a class="nav-link {{ request()->routeIs('retail.sales') ? 'active' : '' }}"
   href="{{ route('retail.sales') }}">
    <img src="{{ asset('images/logos/square-logo-24-balanced.png') }}" alt="Square Logo"
     width="16" height="16" style="margin-right: 6px;"> Square
</a>
<a class="nav-link {{ request()->routeIs('retail.sales') ? 'active' : '' }}"
   href="{{ route('retail.sales') }}">
    <i class="fab fa-etsy" style="color: #F16521; width: 20px;"></i> Etsy
</a>

                        </div>
                        
                        <a class="nav-link collapsed" href="{{ route('all.production') }}" onclick="event.preventDefault(); document.getElementById('productionLink').click();" 
                        data-toggle="collapse" data-target="#collapseProduction" aria-controls="collapseProduction">
                            <div class="sb-nav-link-icon">
                                <img src="{{ asset('images/svg/production.svg') }}" alt="Production Icon" width="16" height="16">
                            </div>

                        Production
                        <div class="sb-sidenav-collapse-arrow"></div>
                    </a>

                    <div class="collapse show" id="collapseProduction" aria-labelledby="headingCustomers" data-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a id="productionLink" href="{{ route('all.production') }}" style="display:none;"></a>
                            <a class="nav-link {{ request()->is('product-calcs') ? 'active' : '' }}" href="{{ route('product_calcs.index') }}">
                                <div class="sb-nav-link-icon">
                                    <img src="{{ asset('images/svg/calculator.svg') }}" alt="Calculator Icon" width="16" height="16">
                                </div>
                                Calculator
                            </a>
                        </nav>
                    </div>
                    
{{-- Sticky Notes --}}
<li class="{{ request()->is('sticky-notes') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('sticky.notes') }}">
        <div class="sb-nav-link-icon">
            <i class="fas fa-sticky-note"></i>
        </div>
        Sticky Notes
    </a>
</li>
                     <a class="nav-link collapsed" href="{{ route('all.reports') }}">
                            <div class="sb-nav-link-icon">
                                <img src="{{ asset('images/svg/reports.svg') }}" alt="Reports Icon" width="16" height="16">
                            </div>
                            Reports
                            <div class="sb-sidenav-collapse-arrow"></div>
                        </a>
                    
                        
@php
    $userType = Auth::user()->type;
    $companyId = Auth::user()->company_id;
@endphp

@if ($userType == 'super_admin' && $companyId == 1)
    <a class="nav-link collapsed" href="{{ url('/profit-loss') }}">
        <div class="sb-nav-link-icon">
            <i class="fas fa-chart-line"></i>
        </div>
        Profit & Loss
        <div class="sb-sidenav-collapse-arrow"></div>
    </a>
@endif

                        @if ($userType == 'admin' || $userType == 'super_admin')
                            <a class="nav-link collapsed" href="{{ route('team.management') }}">
                                <div class="sb-nav-link-icon">
                                    <img src="{{ asset('images/svg/team.svg') }}" alt="Team Icon" width="16" height="16">
                                </div>
                                Team Management
                                <div class="sb-sidenav-collapse-arrow"></div>
                            </a>
                        @elseif ($userType == 'employee')
                            <a class="nav-link collapsed" href="{{ route('team.time_tracking') }}">
                                <div class="sb-nav-link-icon">
                                    <img src="{{ asset('images/svg/user.svg') }}" alt="User Icon" width="16" height="16">
                                </div>
                                Time Tracking
                                <div class="sb-sidenav-collapse-arrow"></div>
                            </a>
                        @endif

                        @if ($userType == 'admin')
                            <a class="nav-link collapsed" href="{{ route('users.index') }}">
                                <div class="sb-nav-link-icon">
                                    <img src="{{ asset('images/svg/user.svg') }}" alt="User Icon" width="16" height="16">
                                </div>
                                User Management
                                <div class="sb-sidenav-collapse-arrow"></div>
                            </a>
                        @endif

                        @if ($userType == 'super_admin')
                            <a class="nav-link collapsed" href="{{ route('users.index') }}">
                                <div class="sb-nav-link-icon">
                                    <img src="{{ asset('images/svg/user.svg') }}" alt="User Icon" width="16" height="16">
                                </div>
                                Control Panel
                                <div class="sb-sidenav-collapse-arrow"></div>
                            </a>
                            <a class="nav-link collapsed" href="{{ route('blogs.index') }}">
                                <div class="sb-nav-link-icon">
                                    <i class="fas fa-blog"></i>
                                </div>
                                Blog Management
                                <div class="sb-sidenav-collapse-arrow"></div>
                            </a>
                        @endif
<!-- Static "More" Section (Horizontal Divider Style) -->
 <span style="margin-left: 10px;"></span>
<div class="nav-link text-muted"
     style="padding: 5px 0; margin: 4px auto 0 auto; border-top: 2px solid #FFD700; font-weight: bold; width: 80%;">
    
</div>
<!-- Always-visible links under "More" -->
<nav class="sb-sidenav-menu-nested nav ml-4">
    <a class="nav-link" href="{{ route('all.categories') }}">
        <div class="sb-nav-link-icon">
            <img src="{{ asset('images/svg/category.svg') }}" alt="Category Icon" width="16" height="16">
        </div>
        Categories
    </a>

    <a class="nav-link" href="{{ route('shopify_link_products') }}">
        <div class="sb-nav-link-icon">
            <img src="{{ asset('images/svg/link.svg') }}" alt="Link Icon" width="16" height="16">
        </div>
        Linking Product
    </a>

    <a class="nav-link" href="{{ route('shopify_sync_products') }}">
        <div class="sb-nav-link-icon">
            <img src="{{ asset('images/svg/sync.svg') }}" alt="Sync Icon" width="16" height="16">
        </div>
        Sync Products
    </a>
</nav>


                    </div>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            @yield('content')

        </div>
    </div>
    <!-- Insufficient Materials Modal -->
    <div class="modal fade" id="insufficientMaterialsModal" tabindex="-1" role="dialog"
        aria-labelledby="insufficientMaterialsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="insufficientMaterialsModalLabel">Insufficient Materials</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <button id="printMaterialsTable" class="btn btn-primary mb-3">Print Table</button>
                    <div id="materialsTableWrapper">
                        <table class="table table-bordered" id="materialsTable">
                            <thead>
                                <tr>
                                    <th>Select</th>
                                    <th>Name</th>
                                    <th>Stock Level</th>
                                    <th>Min Level</th>
                                    <th>Unit Cost</th>
                                    <th>SKU</th>
                                    <th>Supplier</th>
                                    <th>Category</th>
                                    <th>Last Ordered</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody id="insufficientMaterialsList">
                                <!-- Materials list will be dynamically inserted here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="makeOrderBtn">New Order</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Insufficient Products Modal -->
    <div class="modal fade" id="insufficientProductsModal" tabindex="-1" role="dialog"
        aria-labelledby="insufficientProductsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="insufficientProductsModalLabel">Insufficient Products</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <button id="printProductsTable" class="btn btn-primary mb-3">Print Table</button>
                    <div id="productsTableWrapper">
                        <table class="table table-bordered" id="productsTable">
                            <thead>
                                <tr>
                                    <th>Select</th>
                                    <th>Product Name</th>
                                    <th>Location</th>
                                    <th>Current Stock Level</th>
                                    <th>Min Level</th>
                                    <th>Quantity Needed</th>
                                    <th>Unit Cost</th>
                                    <th>Category</th>
                                </tr>
                            </thead>
                            <tbody id="insufficientProductsList">
                                <!-- Products list will be dynamically inserted here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="makeProductionBtn">Make Production</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="settingsModal" tabindex="-1" role="dialog" aria-labelledby="integrationsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="integrationsModalLabel">Integrations</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Integration List -->
        <div class="container mt-4">
          <div class="list-group">
            <!-- Shopify Integration -->
            <div class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                
                <img src="{{ asset('images/svg/shopify.svg') }}" alt="Shopify" width="30">
                <span>Shopify</span>
              </div>
              <button class="btn btn-primary" data-toggle="modal" data-target="#shopifyModal">Connect App</button>
            </div>
            <!-- Etsy Integration -->
            <div class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <img src="{{ asset('images/svg/etsy.svg') }}" alt="Etsy" width="30">
                <span>Etsy</span>
              </div>
              <button class="btn btn-secondary" disabled>Connect App</button>
            </div>
            <!-- Add other integrations as needed -->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="shopifyModal" tabindex="-1" role="dialog" aria-labelledby="shopifyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="shopifyModalLabel">Connect Shopify</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="shopifyForm">
          <div class="form-group">
            <label for="shopifyUrl">Shopify Store URL</label>
            <div class="input-group">
              <input type="text" class="form-control" id="shopifyUrl" placeholder="Enter store name" aria-describedby="shopifyUrlAddon">
              <div class="input-group-append">
                <span class="input-group-text" id="shopifyUrlAddon">.myshopify.com</span>
              </div>
            </div>
            <small class="form-text text-muted">Example: your-store-name.myshopify.com</small>
          </div>

          <!-- Button to Go to Shopify App Settings for Access Token -->
          <button type="button" class="btn btn-info mb-3" id="getAccessTokenBtn">Get Access Token</button>
          
          <div class="form-group">
            <label for="accessToken">Access Token</label>
            <input type="text" class="form-control" id="accessToken" placeholder="Enter your access token">
          </div>
          
          <button type="submit" class="btn btn-primary">Save</button>
        </form>
      </div>
    </div>
  </div>
</div>

    <script src="{{ asset('libs/jquery/js/jquery-3.5.1.min.js') }}" crossorigin="anonymous"></script>
    <script src="{{ asset('libs/bootstrap/bootstrap.bundle.min.js') }}" crossorigin="anonymous"></script>

    <script src="{{ asset('backend') }}/js/scripts.js"></script>

    <script src="{{ asset('libs/datatables/jquery.dataTables.min.js') }}"></script>

    <script src="{{ asset('libs/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('libs/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('libs/datatables/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('libs/datatables/dataTables.keyTable.min.js') }}"></script>
    <script src="{{ asset('libs/datatables/dataTables.select.min.js') }}"></script>

    <script src="{{ asset('libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('libs/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('libs/pdfmake/vfs_fonts.js') }}"></script>

    <script src="/frontend/plugins/toast/toastr.min.js"></script>
    <script src="{{ asset('libs/select2/select2.min.js') }}"></script>

    <script src="{{ asset('libs/chart/chart.js') }}"></script>


    <script type="text/javascript" src="{{ asset('libs/jquery-ui/1.12.1/jquery-ui.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('libs/jquery-ui/1.12.1/popper.min.js') }}"></script>


    <script>

// Get CSRF token
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Fixed color map for category names
function getColorForCategory(categoryName) {
    const colorMap = {
        'Astrology Collection': '#4CAF50',
        'Fall Collection': '#FF9800',
        'Signature Collection': '#9C27B0',
        'Summer Collection': '#FBC02D',
        'Spring Collection': '#E91E63',
        'Winter Collection': '#03A9F4',
        'Mystical Collection': '#424242',
        'Vibes Collection': '#9E9E9E',
        'Cereal Collection': '#81D4FA',
        'Ice Cream Collection': '#A5D6A7'
    };

    return colorMap[categoryName] || '#CCCCCC'; // fallback color
}

        function parseToDecimal(value) {
            // Convert the input to a number
            const number = parseFloat(value);

            // Check if the value is a valid number
            if (isNaN(number)) {
                return 0;
            }

            // Return the number with trailing zeros stripped
            return Number(number.toFixed(3));
        }

        $(document).ready(function() {
            var insufficientMaterials = [];
            var insufficientProducts = [];

            function fetchAlerts() {
                $.ajax({
                    url: '{{ route('alerts.insufficient') }}',
                    method: 'GET',
                    success: function(data) {
                        var alertCount = data.totalAlerts;
                        
                        insufficientMaterials = data.insufficientMaterials;
                        insufficientProducts = data.insufficientProducts;
                        var totalProductsAlert = 0

                        insufficientProducts.forEach(function(product, index) {
                            totalProductsAlert += product.min_stock_level - product.current_stock_level;
                        });


                        $('#alertCount').text(insufficientMaterials.length + totalProductsAlert);
                        

                        var alertsList = $('#alertsList');
                        alertsList.empty();

                        if (alertCount > 0) {
                            if (insufficientMaterials.length > 0) {
                                var materialsAlertItem = `
                            <a class="dropdown-item" href="#" onclick="showInsufficientMaterialsModal()">
                                Insufficient Materials
                                <span class="badge badge-danger">${insufficientMaterials.length}</span>
                            </a>
                        `;
                                alertsList.append(materialsAlertItem);
                            }

                            if (insufficientProducts.length > 0) {
                                var productsAlertItem = `
                            <a class="dropdown-item" href="#" onclick="showInsufficientProductsModal()">
                                Insufficient Products
                                <span class="badge badge-danger">${totalProductsAlert}</span>
                            </a>
                        `;
                                alertsList.append(productsAlertItem);
                            }
                        } else {
                            alertsList.append('<span class="dropdown-item">No alerts</span>');
                        }
                    },
                    error: function(error) {
                        console.error('Error fetching alerts:', error);
                    }
                });
            }

            function showInsufficientMaterialsModal() {
                var materialsList = $('#insufficientMaterialsList');
                materialsList.empty();


                insufficientMaterials.forEach(function(material, index) {
                    var unitName = material.unit ? material.unit.name : 'units';
                    var supplierName = material.supplier ? material.supplier.name : 'N/A';
                    var categoryName = material.category ? material.category.name : 'N/A';
                    var lastOrderedDate = material.last_order_date ? new Date(material.last_order_date)
                        .toLocaleDateString() : 'N/A';
                    var color = getColorForCategory(categoryName);
                    var materialItem = `<tr>
                    <td class="text-center">
                        <input type="checkbox" id="materialSelect_${index}" class="material-select" data-material-id="${material.id}">
                    </td>
                    <td>${material.name || 'N/A'}</td>
                    <td>${material.current_stock_level} ${unitName}</td>
                    <td>${material.min_stock_level} ${unitName}</td>
                    <td>${material.price_per_unit || 'N/A'}</td>
                    <td>${material.material_code}</td>
                    <td>${supplierName}</td>
                    <td><span class="category-item" style="background-color: ${color};">${categoryName}<span></td>
                    <td >${lastOrderedDate}</td>
                    <td>${material.notes || ''}</td>
                </tr>
            `;
                    materialsList.append(materialItem);
                });

                $('#insufficientMaterialsModal').modal('show');
            }

            document.getElementById('makeOrderBtn').addEventListener('click', function () {
                // Gather all selected products
                var selectedMaterials = [];
                $('#insufficientMaterialsList .material-select:checked').each(function () {
                    var materialId = $(this).data('material-id');
                    // Find the product details by ID
                    var material = insufficientMaterials.find(p => p.id === materialId);
                    if (material) {
                        selectedMaterials.push(material);
                    }
                });

                if (selectedMaterials.length === 0) {
                    toastr.error('Please select at least one material.');
                    return;
                }

                // Now you can send the selectedProducts to the backend for production
                fetch('/create-purchase-order', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ materials: selectedMaterials })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success('Purchase order created successfully!');
                        $('#insufficientMaterialsList').modal('hide');
                        setTimeout(function() {
                            window.location.reload();
                        }, 2000); 
                    } else {
                        toastr.error('Failed to create purchase order.');
                    }
                })
                .catch(error => {
                    console.error('Error creating purchase order:', error);
                    toastr.error('Error creating purchase order.');
                });
            });

                document.getElementById('makeProductionBtn').addEventListener('click', function () {
                // Gather all selected products
                var selectedProducts = [];
                $('#insufficientProductsList .product-select:checked').each(function () {
                    var productId = $(this).data('product-id');
                    // Find the product details by ID
                    var product = insufficientProducts.find(p => p.product.id === productId);
                    if (product) {
                        selectedProducts.push(product);
                    }
                });

                if (selectedProducts.length === 0) {
                    toastr.error('Please select at least one product.');
                    return;
                }

                // Now you can send the selectedProducts to the backend for production
                fetch('/create-production-order', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ products: selectedProducts })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success('Production order created successfully!');
                        $('#insufficientProductsModal').modal('hide');
                        setTimeout(function() {
                            window.location.reload();
                        }, 2000); 
                    } else {
                        toastr.error('Failed to create production order.');
                    }
                })
                .catch(error => {
                    console.error('Error creating production order:', error);
                    toastr.error('Error creating production order.');
                });
            });

            function showInsufficientProductsModal() {
                var productsList = $('#insufficientProductsList');
                productsList.empty();

                insufficientProducts.forEach(function(product, index) {
                    var unitName = product.product.unit ? product.product.unit.name : 'units';
                    var categoryName = product.product.category ? product.product.category.name : '';
                    var location = product.location ? product.location.name : '';
                    var quantityNeeded = product.min_stock_level - product.current_stock_level;
                    var color = getColorForCategory(categoryName);

                    var productItem = `
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" id="productSelect_${index}" class="product-select" data-product-id="${product.product.id}">
                                </td>
                                <td>${product.product.name || ''}</td>
                                <td>${ location }</td>
                                <td>${product.current_stock_level + unitName}</td>
                                <td>${product.min_stock_level !== null ? product.min_stock_level + unitName : '-'}</td>
                                <td>${quantityNeeded + unitName}</td>
                                <td>$${product.product.price || '' + '/' + unitName}</td>
                                <td><span class="category-item" style="background-color: ${color};">${categoryName}<span></td>
                            </tr>
                        `;
                    productsList.append(productItem);
                });

                $('#insufficientProductsModal').modal('show');
            }

            $('#printMaterialsTable').on('click', function() {
                var element = document.getElementById('materialsTableWrapper');
                html2pdf().from(element).set({
                    margin: 1,
                    filename: 'Insufficient_Materials.pdf',
                    html2canvas: {
                        scale: 2
                    },
                    jsPDF: {
                        orientation: 'portrait'
                    }
                }).save();
            });

            $('#printProductsTable').on('click', function() {
                var element = document.getElementById('productsTableWrapper');
                html2pdf().from(element).set({
                    margin: 1,
                    filename: 'Insufficient_Products.pdf',
                    html2canvas: {
                        scale: 2
                    },
                    jsPDF: {
                        orientation: 'portrait'
                    }
                }).save();
            });

            window.showInsufficientMaterialsModal = showInsufficientMaterialsModal;
            window.showInsufficientProductsModal = showInsufficientProductsModal;

            fetchAlerts();
            setInterval(fetchAlerts, 900000); // Fetch every 15 minutes


            var stripe = Stripe('{{ env('STRIPE_KEY') }}');
            var elements = stripe.elements();

            // Create an instance of the card Element
            var cardElement = elements.create('card');

            // Mount the card Element into the `#card-element` div
            cardElement.mount('#card-element');

            // Handle real-time validation errors from the card Element
            cardElement.on('change', function(event) {
                var displayError = document.getElementById('card-errors');
                if (event.error) {
                    displayError.textContent = event.error.message;
                } else {
                    displayError.textContent = '';
                }
            });

            // Handle form submission
            var form = document.getElementById('payment-form');
            form.addEventListener('submit', function(event) {

                event.preventDefault();

                // Disable the submit button to prevent multiple submissions
                document.getElementById('submit-button').disabled = true;

                // Create a PaymentMethod with the card Element
                stripe.createPaymentMethod({
                    type: 'card',
                    card: cardElement,
                }).then(function(result) {
                    if (result.error) {
                        // Display error in #card-errors
                        var errorElement = document.getElementById('card-errors');
                        errorElement.textContent = result.error.message;

                        // Re-enable the submit button
                        document.getElementById('submit-button').disabled = false;
                    } else {
                        // Otherwise, send payment method ID to server
                        processPayment(result.paymentMethod.id);
                    }
                });
            });

            // Function to process the payment
            function processPayment(paymentMethodId) {
                fetch('{{ route('payment.process') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        payment_method_id: paymentMethodId,
                    })
                })
                .then(function(response) {
                    return response.json();
                })
                .then(function(responseJson) {
                    if (responseJson.success) {
                        toastr.success('Payment successful!');
                    } else {
                        toastr.success('Payment failed: ' + responseJson.error);
                    }

                    // Re-enable the submit button after processing
                    document.getElementById('submit-button').disabled = false;
                })
                .catch(function(error) {
                    console.error('Error:', error);
                    toastr.success('Payment error!');

                    // Re-enable the submit button after error
                    document.getElementById('submit-button').disabled = false;
                });
            }
            
        });

        function validatePassword(event) {
            var password = document.getElementById('password').value;
            var confirmPassword = document.getElementById('password_confirmation').value;
            var passwordAlert = document.getElementById('passwordAlert');

            if (password.length > 0) {
                if (password.length < 5) {
                    passwordAlert.innerText = 'Password must be at least 5 characters long.';
                    passwordAlert.style.display = 'block';
                    event.preventDefault();
                } else if (password !== confirmPassword) {
                    passwordAlert.innerText = 'Passwords do not match.';
                    passwordAlert.style.display = 'block';
                    event.preventDefault();
                } else {
                    passwordAlert.style.display = 'none';
                    document.getElementById('editAccountForm').submit();
                }
            } else {
                document.getElementById('editAccountForm').submit();
            }
        }

        function editPhoto() {
            const photoInput = document.getElementById('photo');
            photoInput.click();
        }

        function deletePhoto() {
            const photoInput = document.getElementById('photo');
            const photoPreviewContainer = document.getElementById('photo-preview-container');
            const photoPreview = document.getElementById('photoPreview');
            
            photoPreviewContainer.style.display = 'none'; // Hide the preview
            photoInput.style.display = 'block'; // Allow user to upload a new photo

            photoInput.value = ''; // Clear the input
            photoPreview.src = '#'; // Reset the image preview
            document.getElementById('photo-name').textContent = ''; // Clear file name
            document.getElementById('photo-info').textContent = ''; // Clear file info
        }

        function previewPhoto() {
            const photoInput = document.getElementById('photo');
            const photoPreview = document.getElementById('photoPreview');
            const photoPreviewContainer = document.getElementById('photo-preview-container');

            const file = photoInput.files[0];
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    photoPreview.src = e.target.result; // Set the preview image source
                    photoPreviewContainer.style.display = 'flex'; // Show the preview container
                    document.getElementById('photo-name').textContent = file.name; // Show file name
                    document.getElementById('photo-info').textContent = `${Math.round(file.size / 1024)} KB`; // Show file size
                }

                reader.readAsDataURL(file); // Read the image file
            }
        }

        document.getElementById('getAccessTokenBtn').addEventListener('click', function() {
            const storeName = document.getElementById('shopifyUrl').value;
            
            if (!storeName) {
                toastr.error('Please enter your store name.');
                return;
            }
            
            // Open the Shopify admin app settings in a new tab to get the access token
            const shopifyAdminUrl = `https://${storeName}.myshopify.com/admin/settings/apps`;
            window.open(shopifyAdminUrl, '_blank');
        });

        // When the form is submitted (Save the access token)
        document.getElementById('shopifyForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const storeName = document.getElementById('shopifyUrl').value;
            const accessToken = document.getElementById('accessToken').value;
            
            if (!storeName || !accessToken) {
                toastr.error('Please enter both the store name and access token.');
                return;
            }

            // Send AJAX request to save the store name and access token to the database
            fetch('/save-shopify-info', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token for Laravel
            },
            body: JSON.stringify({
                shopify_domain: `${storeName}.myshopify.com`,
                access_token: accessToken
            })
            })
            .then(response => response.json())
            .then(data => {
            if (data.success) {
                toastr.success('Shopify store and access token saved successfully.');
                location.reload();
            } else {
                toastr.error('Failed to save the Shopify store and access token.');
            }
            })
            .catch(error => console.error('Error:', error));
        });

        
    </script>
    @yield('script')

@include('live_market_tracker')

</body>

</html>
