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
use LBM\Middleware\Auth;
// use App\Middleware\Admin\InitMiddleware; // Use It In 1st Middleware Each Admin Routes Without Login Pages
use App\Middleware\Admin\ClientMiddleware;


// Admin Route Group
Http::group(ADMIN, function(){
    // Fallback
    Http::fallback(null);

    Http::get('/login', 'Admin\AuthController@login')->name('staff.login'); // Done
    Http::post('/login', 'Admin\AuthController@login');
    Http::get('/logout', 'Admin\AuthController@logout')->name('staff.logout'); // Done

    // Dashboard
    Http::get('/', 'Admin\DashboardController@index')->name('staff.dashboard'); // Almost Done

    // Clients
    Http::get('/clients', 'Admin\ClientController@clients')->name('staff.clients');
    // Single Client
    Http::get('/client/{client:[a-zA-Z0-9\-]+}', 'Admin\ClientController@single')->name('staff.client');
    // Http::post('/client/{client:[a-zA-Z0-9\-]+}', 'Admin\ClientController@single')->middleware([ClientMiddleware::class]);

    // Client Contacts
    Http::get('/client/{client:[a-zA-Z0-9\-]+}/contacts', 'Admin\ClientController@contacts')->name('staff.client.contacts');
    Http::get('/client/{client:[a-zA-Z0-9\-]+}/contact/{contact:[a-zA-Z0-9\-]+}', 'Admin\ClientController@addContact')->name('staff.client.contact');
    
    // Products
    Http::get('/products', function(){})->name('staff.products');
    Http::get('/product/{product:[a-zA-Z0-9\-]+}', function(){})->name('staff.product');
    
    // Orders
    Http::get('/orders', function(){})->name('staff.orders');
    Http::get('/order/{order:[a-zA-Z0-9\-]+}', function(){})->name('staff.order');

    // Tickets
    Http::get('/tickets', function(){})->name('staff.tickets');
    Http::get('/ticket/{ticket:[a-zA-Z0-9\-]+}', function(){})->name('staff.ticket');
    Http::get('/network-status', function(){})->name('staff.network');

    // Staffs
    Http::get('/staffs', function(){})->name('staff.staffs');
    Http::get('/staff/{staff:[a-zA-Z0-9\-]+}', function(){})->name('staff.staff');
    Http::get('/staff-activities', function(){})->name('staff.activities');

    // Addons
    Http::get('/addons', function(){})->name('staff.addons');
    
    // Noticeboard
    Http::get('/addons', function(){})->name('staff.noticeboard');    

    // Invoices
    Http::get('/invoices', function(){})->name('staff.invoices');
    Http::get('/invoice/{invoice:[a-zA-Z0-9\-]+}', function(){})->name('staff.invoice');

    // Reports & Reports Group
    Http::get('/reports', function(){})->name('staff.reports');
    Http::group('/reports', function () {
        Http::get('/invoice-report', function(){})->name('staff.invoice-report');
        Http::get('/order-report', function(){})->name('staff.order-report');
        Http::get('/ticket-feedbacks', function(){})->name('staff.ticket-feedbacks');
    });

    // Others
    Http::get('/my-account', function(){})->name('staff.account');
    Http::get('/settings', function(){})->name('settings');

    // Admin Add Route Group
    Http::group('/add', function(){
        // Add Client
        Http::get('/client', 'Admin\ClientController@create')->name('staff.add.client');
        Http::post('/client', 'Admin\ClientController@create')->middleware([ClientMiddleware::class]);

        // Add Staff
        Http::get('/staff', 'Admin\StaffController@create')->name('staff.add.staff');
        Http::post('/staff', 'Admin\StaffController@create');

        // Add Invoice
        Http::get('/invoice', 'Admin\InvoiceController@create')->name('staff.add.invoice');
        Http::post('/invoice', 'Admin\InvoiceController@create');

        // Add Product
        Http::get('/product', 'Admin\ProductController@create')->name('staff.add.product');
        Http::post('/product', 'Admin\ProductController@create');

        // Add Order
        Http::get('/order', 'Admin\OrderController@create')->name('staff.add.order');
        Http::post('/order', 'Admin\OrderController@create');

        // Add Ticket
        Http::get('/ticket', 'Admin\TicketController@create')->name('staff.add.ticket');
        Http::post('/ticket', 'Admin\TicketController@create');

        // Add Notice
        Http::get('/notice', 'Admin\NoticeController@create')->name('staff.add.notice');
        Http::post('/notice', 'Admin\NoticeController@create');

    });

    // Admin Edit Route Group
    Http::group('/edit', function(){
        // Edit Client
        Http::get('/client/{client:[a-zA-Z0-9\-]+}', 'Admin\ClientController@modify')->name('staff.edit.client');
        Http::post('/client/{client:[a-zA-Z0-9\-]+}', 'Admin\ClientController@modify')->middleware([ClientMiddleware::class]);

    });

})->middleware([Auth::class]);


// Panel Route Group
Http::group(PANEL, function(){
    // Fallback
    Http::fallback(null);

    Http::get('/login')->name('client.login');
});
