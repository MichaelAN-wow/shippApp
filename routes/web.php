<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\TeamManagementController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ShopifyController;
use App\Http\Controllers\ProductCalcController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\MakeSheetController;
use App\Http\Controllers\StickyNoteController;
use App\Http\Controllers\OverheadEntryController;
use App\Http\Controllers\MarketPrepController;
use App\Http\Controllers\UPSOAuthController;
use Carbon\Carbon;
use App\Http\Controllers\TossedItemController;
use App\Models\Product;
use App\Http\Controllers\CalendarController;
use Illuminate\Http\Request;
use App\Http\Controllers\ShippingManagerController;
use App\Http\Controllers\ShippingLabelController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\ShippedPackageController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\BoxController;
use App\Http\Controllers\ShippingConnectionController;
use App\Http\Controllers\UpsTestController;
use App\Http\Controllers\UPSController;
use App\Http\Controllers\UpsTrackingController;
use App\Http\Controllers\TrackingController;

use App\Http\Controllers\BoxInventoryController;
use App\Http\Controllers\ShippingConnectionsController;
use App\Http\Controllers\DashboardController;



use Shopify\Clients\Rest;

use Illuminate\Support\Facades\Mail;

Route::middleware(['auth'])->group(function () {
    // 📊 Market Tracker Routes
    Route::get('market-history', [MarketTrackerController::class, 'history'])->name('market.history');
    Route::get('market-tracker', [MarketTrackerController::class, 'index'])->name('market.tracker');
    Route::post('market-tracker/save', [MarketTrackerController::class, 'save'])->name('market.tracker.save');
    Route::get('market-tracker/load/{date}', [MarketTrackerController::class, 'load'])->name('market.tracker.load');

    // ✏️ Draft Auto-Save Routes
    Route::post('market-tracker/save-draft', [MarketTrackerController::class, 'saveDraft'])->name('market.saveDraft');
    Route::get('market-tracker/load-draft', [MarketTrackerController::class, 'loadDraft'])->name('market.loadDraft');
});

    // 🛍️ Market Prep Routes
    Route::prefix('market-prep')->group(function () {
        Route::get('/loadout', [MarketPrepController::class, 'loadout'])->name('market.loadout');
        Route::get('/restock', [MarketPrepController::class, 'restock'])->name('market.restock');
});

// Market Prep Routes
Route::prefix('market-prep')->group(function () {
    Route::get('/loadout', [MarketPrepController::class, 'loadout'])->name('market.loadout');
    Route::get('/restock', [MarketPrepController::class, 'restock'])->name('market.restock');
});

// ✅ Make Sheet Routes 

Route::get('/make-sheet', [MakeSheetController::class, 'index'])->name('make_sheet.index');
Route::post('/make-sheet/store', [MakeSheetController::class, 'store'])->name('make_sheet.store');
Route::post('/make-sheet/auto-save', [MakeSheetController::class, 'autoSave'])->name('make_sheet.auto_save');

// Tossed Items Report
Route::middleware(['auth'])->group(function () {
    Route::get('/tossed-items-report', [TossedItemController::class, 'index'])->name('tossed.items.report');
    Route::get('/tossed-items', [TossedItemController::class, 'index'])->name('tossed.items.index');
    Route::delete('/tossed-items/{id}', [TossedItemController::class, 'destroy'])->name('tossed_items.destroy');
    Route::post('/tossed-items', [TossedItemController::class, 'store'])->name('tossed_items.store');
});

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('activate-account/{token}', [UserController::class, 'activate'])->name('user.activate');


Route::get('/shopify/products', [ShopifyController::class, 'getProducts']);
Route::get('/shopify/orders', [ShopifyController::class, 'getAllOrders']);

Route::get('/shopify-products', [ShopifyController::class, 'getShopifyProducts']);
Route::get('/local-products', [ShopifyController::class, 'getLocalProducts']);
Route::post('/save-product-match', [ShopifyController::class, 'saveProductMatch']);
Route::post('/sync-products', [ShopifyController::class, 'syncProducts'])->name('syncProducts');
Route::get('/shopify_sync_products', function () {
    $products = Product::where('company_id', session('company_id'))->whereNotNull('shopify_id')->orderBy('name')->get();
    $lastUpdated = Product::whereNotNull('shopify_id')
                          ->where('company_id', session('company_id'))
                          ->min('updated_at');
    $currentDate = Carbon::now();
    return view('Admin.shopify_sync_products', compact('products', 'lastUpdated', 'currentDate'));
})->middleware(['auth'])->name('shopify_sync_products');


Route::get('/link-products', function () {
    return view('Admin.shopify_link_products');
})->middleware(['auth'])->name('shopify_link_products');

Route::get('/', function (Request $request) {
    return auth()->check() ? app(MaterialController::class)->allMaterials($request) : view('welcome');
});

Route::get('/features', function () {
    return view('features');
});

Route::get('/price', function () {
    return view('price');
});

Route::get('/who_we_are', function () {
    return view('who_we_are');
});
Route::get('/home_blogs', [BlogController::class,'home']);
Route::get('/terms', function () {
    return view('terms');
});

Route::get('/privacy', function () {
    return view('privacy');
});

//materials
Route::post('/materials/add',[MaterialController::class,'store'])->middleware(['auth']);
Route::get('/materials/all',[MaterialController::class,'allMaterials'])->middleware(['auth'])->name('all.materials');
Route::delete('/materials/delete/{id}', [MaterialController::class, 'destroy'])->middleware(['auth'])->name('materials.destroy');
Route::get('/materials/get/{id}', [MaterialController::class, 'getMaterialById']);
Route::post('/materials/update/{material}', [MaterialController::class, 'updateMaterialById'])->middleware(['auth']);
Route::get('/materials/category/{id}', [MaterialController::class, 'showMaterialsByCategory'])->middleware(['auth'])->name('showMaterialsByCategory');
Route::post('/materials/upload', [MaterialController::class, 'upload_csv'])->middleware(['auth'])->name('materials/upload');
Route::get('/materials/pageScroll', [MaterialController::class, 'getPageScroll'])->middleware(['auth']);



//category
Route::get('/categories/all',[CategoryController::class,'getAll'])->middleware(['auth'])->name('all.categories');
Route::post('/categories/add',[CategoryController::class,'store'])->middleware(['auth']);
Route::post('/categories/update', [CategoryController::class, 'update'])->middleware(['auth'])->name('update.category');
Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->middleware(['auth']);


//products
Route::post('/products/add',[ProductController::class,'store'])->middleware(['auth']);
Route::get('/products/all',[ProductController::class,'getAllProducts'])->middleware(['auth'])->name('all.products');
Route::delete('/products/{product}', [ProductController::class, 'destroy'])->middleware(['auth'])->name('products.destroy');
Route::post('/products/upload', [ProductController::class, 'upload_csv'])->middleware(['auth'])->name('products/upload');
Route::get('/products/categories/{id}', [ProductController::class, 'showByCategory'])->name('products-category.show');
Route::get('/products/get/{id}', [ProductController::class, 'getProductById']);
Route::post('/products/update/{product}', [ProductController::class, 'updateProductById'])->middleware(['auth']);
Route::get('/products/pageScroll', [ProductController::class, 'getPageScroll'])->middleware(['auth']);
Route::post('/products/combine-variants',  [ProductController::class, 'combineVariants'])->middleware(['auth'])->name('products.combine.variants');
Route::get('/products/{id}', [ProductController::class, 'show'])->middleware(['auth'])->name('products.show');

//production
Route::get('/all-production',[ProductionController::class,'getAllProduction'])->middleware(['auth'])->name('all.production');
Route::get('/productions/{id}', [ProductionController::class, 'getProductionById'])->middleware(['auth']);
Route::post('/production/update/{id}', [ProductionController::class, 'updateProduction'])->middleware(['auth']);
Route::post('/create-production-order', [ProductionController::class, 'create_production_from_alert'])->middleware(['auth']);


//orders
Route::get('/orders/all',[OrderController::class,'getAllOrders'])->middleware(['auth'])->name('all.orders');
Route::post('/orders/add',[OrderController::class,'store'])->middleware(['auth']);
Route::get('/orders/pageScroll', [OrderController::class, 'getPageScroll'])->middleware(['auth']);
Route::delete('/orders/{orders}', [OrderController::class, 'destroy'])->middleware(['auth']);
Route::get('/orders/{id}/details', [OrderController::class, 'getOrderDetails'])->middleware(['auth']);
Route::post('/orders/{id}/edit', [OrderController::class, 'update'])->middleware(['auth'])->name('orders.update');
Route::post('/create-purchase-order', [OrderController::class, 'create_purchase_from_alert'])->middleware(['auth']);

//sales
Route::post('/sales/add',[SaleController::class,'store'])->middleware(['auth']);
Route::get('/sales/all', [SaleController::class, 'getAllData'])->middleware(['auth'])->name('all.sales');
Route::get('/sales/retail', [SaleController::class, 'getRetailData'])->middleware(['auth'])->name('retail.sales');
Route::get('/sales/wholesale', [SaleController::class, 'getWholeSaleData'])->middleware(['auth'])->name('wholesale.sales');
Route::get('/sales/shopify', [SaleController::class, 'getShopifyData'])->middleware(['auth'])->name('shopify.sales');
Route::get('/sales/pageScroll', [SaleController::class, 'getPageScroll'])->middleware(['auth']);
Route::delete('/sales/{sales}', [SaleController::class, 'destroy'])->middleware(['auth']);
Route::get('/sales/{id}/edit', [SaleController::class, 'edit'])->middleware(['auth'])->name('sales.edit');
Route::put('/sales/{id}', [SaleController::class, 'update'])->middleware(['auth'])->name('sales.update');
Route::post('/sales/addYearlyTarget', [SaleController::class, 'updateYearlyTarget'])->middleware(['auth'])->name('sales.updateYearlyTarget');

Route::get('/sales/{id}', [SaleController::class, 'show'])->middleware(['auth'])->name('sales.show');
Route::get('/customers/{id}/sales', [CustomerController::class, 'showSales'])->middleware(['auth'])->name('customers.sales');

//reports
Route::get('/all-report',[ReportsController::class,'index'])->middleware(['auth'])->name('all.reports');
Route::get('/reports/sales-data', [ReportsController::class, 'getSalesData'])->middleware(['auth']);
Route::get('/reports/materials-data', [ReportsController::class, 'getMaterialsData'])->middleware(['auth']);
Route::get('/reports/products-data', [ReportsController::class, 'getProductsData'])->middleware(['auth']);
Route::get('/reports/inventory-breakdown', [ReportsController::class, 'getInventoryBreakdownData'])->middleware(['auth']);


//supplier

Route::get('/all-suppliers',[SupplierController::class,'index'])->middleware(['auth'])->name('all.suppliers');
Route::post('/add-supplier',[SupplierController::class,'store'])->middleware(['auth']);
Route::get('/suppliers/{id}/orders', [SupplierController::class, 'getSupplierOrders'])->middleware(['auth'])->name('suppliers.orders');
Route::get('/suppliers/{id}/edit', [SupplierController::class, 'edit'])->middleware(['auth'])->name('suppliers.edit');
Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy'])->middleware(['auth'])->name('suppliers.destroy');
Route::put('/suppliers/{id}', [SupplierController::class, 'update'])->middleware(['auth'])->name('suppliers.update');

//team managemt
Route::get('/team/time_tracking', [TeamManagementController::class, 'time_tracking_index'])->middleware(['auth'])->name('team.time_tracking');
Route::post('/team/time_tracking/add',[TeamManagementController::class,'add_time_track'])->middleware(['auth']);
Route::post('/team/time_tracking/addByUserId',[TeamManagementController::class,'add_time_track_by_userid'])->middleware(['auth']);
Route::post('/team/time_tracking/edit/{id}',[TeamManagementController::class,'edit_time_track'])->middleware(['auth']);
Route::delete('/team/time_tracking/{id}', [TeamManagementController::class, 'destory_time_track']);
Route::get('/team/management', [TeamManagementController::class, 'team_management_index'])->middleware(['auth'])->name('team.management');
Route::post('/team/update-hourly-rate', [TeamManagementController::class, 'update_hourly_rate'])->middleware(['auth']);
Route::post('/team/pay-user', [TeamManagementController::class, 'pay_user'])->middleware(['auth']);
Route::post('/team/invite', [TeamManagementController::class, 'sendInvite'])->middleware(['auth'])->name('team.sendInvite');
Route::get('/team/accept_invite/{token}', [TeamManagementController::class, 'acceptInvite'])->name('team.acceptInvite');
Route::post('/team/time_tracking/record_time',[TeamManagementController::class,'add_time_track_clock_in_and_out'])->middleware(['auth']);
Route::post('/team/time_tracking/get_status',[TeamManagementController::class,'getStatus'])->middleware(['auth']);
Route::post('/team/archive-employee', [TeamManagementController::class, 'archiveEmployee'])->middleware(['auth'])->name('team.archiveEmployee');
Route::post('/team/restore-employee', [TeamManagementController::class, 'restoreEmployee'])->middleware(['auth'])->name('team.restoreEmployee');
Route::get('/team/archived-employees', [TeamManagementController::class, 'getArchivedEmployees'])->middleware(['auth'])->name('team.archivedEmployees');


//product calculator
Route::get('/product-calcs', [ProductCalcController::class, 'index'])->middleware(['auth'])->name('product_calcs.index');
Route::post('/product-calcs', [ProductCalcController::class, 'store'])->middleware(['auth'])->name('product_calcs.store');
Route::delete('/product-calcs/{id}', [ProductCalcController::class, 'destroy'])->middleware(['auth'])->name('product_calcs.destroy');
Route::get('/product-calcs/{id}', [ProductCalcController::class, 'getProductCalc'])->middleware(['auth'])->name('product_calcs.getProductCalc');
Route::put('/product-calcs/{id}', [ProductCalcController::class, 'update'])->middleware(['auth'])->name('product_calcs.update');

Route::resource('users', UserController::class)->middleware(['auth']);
Route::put('/users/{user}', [UserController::class, 'updateAccount'])->middleware(['auth'])->name('users.updateAccount');

//invoice
Route::get('/add-invoice/{id}', [InvoiceController::class,'formData'])->middleware(['auth']);

Route::get('/new-invoice', [InvoiceController::class,'newformData'])->middleware(['auth'])->name('new.invoice');

Route::post('/insert-invoice',[InvoiceController::class,'store'])->middleware(['auth']);

Route::get('/invoice-details', function () {
    return view('Admin.invoice_details');
})->middleware(['auth'])->name('invoice.details');

Route::get('/all-invoice', [InvoiceController::class,'allInvoices'])->middleware(['auth'])->name('all.invoices');

Route::get('/sold-products',[InvoiceController::class,'soldProducts'])->middleware(['auth'])->name('sold.products');
// Route::get('/delete', [InvoiceController::class,'delete']);

//production
Route::post('/production/add',[ProductionController::class,'store'])->middleware(['auth']);
Route::post('/production/update',[ProductionController::class,'update'])->middleware(['auth']);
Route::post('/production/complete-run',[ProductionController::class,'completeRun'])->middleware(['auth']);
Route::delete('/production/delete/{id}', [ProductionController::class, 'destroy'])->middleware(['auth']);

//test


Route::get('/ups-test-token', [UPSTestController::class, 'testShipmentWithToken']);


Route::post('/ups/generate-label', [UPSController::class, 'generateLabel']);


Route::middleware(['auth'])->prefix('shipping')->name('shipping.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    
    Route::get('/', [ShippingManagerController::class, 'index'])->name('index');
    Route::get('/shipped-packages', [ShippingManagerController::class, 'index'])->name('shipped');
    Route::post('/shipped-packages/update-status/{id}', [ShippingManagerController::class, 'updateStatus'])->name('shipped.updateStatus');

    
    Route::post('/store', [ShippingManagerController::class, 'store'])->name('store'); 
    Route::get('/create/{orderId?}', [ShippingManagerController::class, 'create'])->name('create'); 

    
    Route::get('/download/{shipmentId}', [ShippingManagerController::class, 'downloadLabel'])->name('download');

   
    Route::get('/contacts', [ContactController::class, 'index'])->name('contacts');
    Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');
    Route::post('/contacts/import', [ContactController::class, 'import'])->name('contacts.import');
    Route::delete('/contacts/bulk-delete', [ContactController::class, 'bulkDelete'])->name('contacts.bulkDelete');
    Route::post('/contacts/merge-duplicates', [ContactController::class, 'mergeDuplicates'])->name('contacts.mergeDuplicates');
    Route::delete('/contacts/{id}', [ContactController::class, 'destroy'])->name('contacts.destroy');
    
    Route::get('/labels', [ShippingLabelController::class, 'index'])->name('labels'); 
    Route::get('/labels/create', [ShippingLabelController::class, 'create'])->name('labels.create');
    Route::post('/labels', [ShippingLabelController::class, 'store'])->name('labels.store');
    Route::get('/labels/{label}/download', [ShippingLabelController::class, 'download'])->name('labels.download');

    
    Route::get('/box-inventory', [BoxInventoryController::class, 'index'])->name('box_inventory.index');
    Route::post('/box-inventory', [BoxInventoryController::class, 'store'])->name('box_inventory.store');
    Route::put('/box-inventory/{box}', [BoxInventoryController::class, 'update'])->name('box_inventory.update');
    Route::delete('/box-inventory/{box}', [BoxInventoryController::class, 'destroy'])->name('box_inventory.destroy');

    
    Route::get('/connections', [ShippingConnectionsController::class, 'index'])->name('connections.index');
    Route::post('/connections', [ShippingConnectionsController::class, 'store'])->name('connections.store');
    Route::put('/connections/{connection}', [ShippingConnectionsController::class, 'update'])->name('connections.update');
    Route::delete('/connections/{connection}', [ShippingConnectionsController::class, 'destroy'])->name('connections.destroy');
    Route::get('/connections/connect/{carrier}', [ShippingConnectionsController::class, 'connectCarrier'])->name('connections.connectCarrier');
    Route::get('/connections/disconnect/{connection}', [ShippingConnectionsController::class, 'disconnectCarrier'])->name('connections.disconnect');
    Route::get('/connections/refresh/{connection}', [ShippingConnectionsController::class, 'refreshSync'])->name('connections.refreshSync');
    Route::post('/connections/defaults/update', [ShippingConnectionsController::class, 'updateDefaults'])->name('defaults.update');
    
    Route::get('/shipments/{id}/auto-update', [TrackingController::class, 'autoUpdate'])->name('shipments.autoUpdate');
    Route::get('/shipments/auto-update-all', [TrackingController::class, 'autoUpdateAll'])->name('shipments.autoUpdateAll');
});
// STICKY NOTES ROUTES
Route::middleware(['auth'])->group(function () {

    // Sticky Notes Main Page
    Route::get('/sticky-notes', [StickyNoteController::class, 'index'])->name('sticky.notes');
    // JSON for modal
    Route::get('/sticky-notes/trashed-json', [StickyNoteController::class, 'getTrashed']);

    // Optional: Blade view if needed for full-page trash
    Route::get('/sticky-notes/trashed', [StickyNoteController::class, 'trashed']);

    // Create new note
    Route::post('/sticky-notes', [StickyNoteController::class, 'store']);

    // Update note
    Route::put('/sticky-notes/{id}', [StickyNoteController::class, 'update']);

    // Soft delete note
    Route::post('/sticky-notes/soft-delete/{id}', [StickyNoteController::class, 'softDelete']);

    // Restore trashed notes
    Route::post('/sticky-notes/restore', [StickyNoteController::class, 'restoreSelected']);

    // Permanently delete trashed notes
    Route::delete('/sticky-notes/empty-trash', [StickyNoteController::class, 'emptyTrash']);

    // Link notes (optional feature)
    Route::post('/sticky-notes/link/{id}', [StickyNoteController::class, 'linkNote']);

    // Save notes to a production batch (legacy)
    Route::post('/sticky-notes/save-batch-note', [StickyNoteController::class, 'saveNote']);
});

Route::get('/ups-oauth/label', [UPSOAuthController::class, 'generateLabel']);

//Blog
Route::get('/blogs/home', [BlogController::class,'home'])->name('blogs.home');
Route::get('/blog/{id}', [BlogController::class, 'show'])->name('blogs.show');
Route::resource('blogs', BlogController::class);


//alert 
Route::get('/alerts/insufficient',[AlertController::class,'getInsufficient'])->middleware(['auth'])->name('alerts.insufficient');

//calendar
Route::middleware(['auth'])->group(function () {
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/events', [CalendarController::class, 'getEvents'])->name('calendar.events');
    Route::post('/calendar/events', [CalendarController::class, 'storeEvent'])->name('calendar.events.store');
    Route::get('/calendar/events/{event}/details', [CalendarController::class, 'getEvent'])->name('calendar.events.details');
    Route::put('/calendar/events/{event}', [CalendarController::class, 'updateEvent'])->name('calendar.events.update');
    Route::delete('/calendar/events/{event}', [CalendarController::class, 'deleteEvent'])->name('calendar.events.delete');
    Route::get('/calendar/export', [CalendarController::class, 'exportCalendar'])->name('calendar.export');
    Route::post('/calendar/import-holidays', [CalendarController::class, 'importHolidays'])->name('calendar.import_holidays');
});

// Time-off requests
Route::post('/calendar/time-off-requests', [CalendarController::class, 'storeTimeOffRequest'])->middleware(['auth'])->name('calendar.time_off.store');
Route::get('/calendar/time-off-requests/pending', [CalendarController::class, 'getPendingTimeOffRequests'])->middleware(['auth'])->name('calendar.time_off.pending');
Route::post('/calendar/time-off-requests/{timeOffRequest}/review', [CalendarController::class, 'reviewTimeOffRequest'])->middleware(['auth'])->name('calendar.time_off.review');
Route::get('/calendar/my-time-off-requests', [CalendarController::class, 'getMyTimeOffRequests'])->middleware(['auth'])->name('calendar.my_time_off');
Route::delete('/calendar/time-off-requests/{timeOffRequest}', [CalendarController::class, 'deleteTimeOffRequest'])->middleware(['auth'])->name('calendar.time_off.delete');

//reports 


//customer
// Route::get('/add-customer', function () {
//     return view('Admin.add_customer');
// })->middleware(['auth'])->name('add.customer');

Route::post('/add-customer',[CustomerController::class,'store'])->middleware(['auth']);

Route::get('/all-customers',[CustomerController::class,'customersData'])->middleware(['auth'])->name('all.customers');

Route::get('/payment', [PaymentController::class, 'showPaymentForm'])->name('payment.form');
Route::post('/payment', [PaymentController::class, 'processPayment'])->name('payment.process');

Route::get('/payment-success', function() {
    return view('payment.success');
})->name('payment.success');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::post('/save-shopify-info', [ShopifyController::class, 'saveShopifyData'])->middleware(['auth'])->name('save.shopify.data');

require __DIR__.'/auth.php';