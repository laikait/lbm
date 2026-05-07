<?php
/**
 * Laika Bill Manager
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 */

##########################################################################
/*----------------------------- LBM ROUTES -----------------------------*/
##########################################################################
use Laika\Core\App\Http;
use App\Middleware\Admin\Init; // Use It In 1st Middleware Each Admin Routes Without Login Pages
use App\Middleware\Admin\Login; // Only For Login Page
use App\Middleware\Admin\ClientMiddleware;

// Admin Login Routes
Http::group(ADMIN, function(){

    Http::get('/login', 'Admin\AuthController@login')->name('staff.login'); // Done
    Http::post('/login', 'Admin\AuthController@login');

})->middleware([Login::class]);


// Admin Route Group
Http::group(ADMIN, function(){
    // Fallback
    Http::fallback(null);

    // Dashboard, Login & Logout
    Http::get('/', 'Admin\DashboardController@index')->name('staff.dashboard'); // Almost Done
    Http::get('/logout', 'Admin\AuthController@logout')->name('staff.logout'); // Done

    // Clients
    Http::get('/clients', 'Admin\ClientController@clients')->name('staff.clients');
    // Single Client
    Http::get('/client/{client:[a-zA-Z0-9\-]+}', 'Admin\ClientController@single')->name('staff.client');
    Http::post('/client/{client:[a-zA-Z0-9\-]+}', 'Admin\ClientController@single')->middleware([ClientMiddleware::class]);

    // Ticket
    Http::get('/tickets', function(){})->name('staff.tickets');
    Http::get('/ticket/{ticket:[a-zA-Z0-9\-]+}', function(){})->name('staff.ticket');
    
    // Order
    Http::get('/orders', function(){})->name('staff.orders');
    Http::get('/order/{order:[a-zA-Z0-9\-]+}', function(){})->name('staff.order');

    // Staffs
    Http::get('/staffs', function(){})->name('staff.staffs');
    Http::get('/staff/{staff:[a-zA-Z0-9\-]+}', function(){})->name('staff.staff');
    Http::get('/staff-activities', function(){})->name('staff.activities');

    // Invoices
    Http::get('/invoices', function(){})->name('staff.invoices');
    Http::get('/invoice/{invoice:[a-zA-Z0-9\-]+}', function(){})->name('staff.invoice');

    // Others
    Http::get('/my-account', function(){})->name('staff.account');
    Http::get('/settings', function(){})->name('settings');

    // Admin Add Route Group
    Http::group('/add', function(){
        // Add Client
        Http::get('client', 'Admin\ClientController@create')->name('staff.add.client');
        Http::post('client', 'Admin\ClientController@create')->middleware([ClientMiddleware::class]);

        // Add Invoice
        Http::get('invoice', 'Admin\InvoiceController@create')
            ->middleware('Admin\Invoice')
            ->name('staff.add.invoice');

        Http::post('invoice', 'Admin\InvoiceController@create')
            ->middleware('Admin\Invoice');

    });

})->middleware([Init::class]);
