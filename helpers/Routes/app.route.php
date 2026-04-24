<?php
/**
 * Laika Bill Manager
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 */

##########################################################################
/*----------------------------- LBM ROUTES -----------------------------*/
##########################################################################
use Laika\Core\App\Router;
use App\Middleware\Admin\Init; // Use It In 1st Middleware Each Admin Routes Without Login Pages
use App\Middleware\Admin\Login; // Only For Login Page
use App\Middleware\Admin\Client;

// Admin Panel Fallback
Router::fallback(ADMIN, null, Init::class);

// Admin Login Routes
Router::group(ADMIN, function(){
    Router::get('/login', 'Admin\AuthController@login')->name('staff.login'); // Done
    Router::post('/login', 'Admin\AuthController@login');
}, [Login::class]);


// Admin Route Group
Router::group(ADMIN, function(){
    // Dashboard, Login & Logout
    Router::get('/', 'Admin\DashboardController@index')->name('staff.dashboard'); // Almost Done
    Router::get('/logout', 'Admin\AuthController@logout')->name('staff.logout'); // Done

    // Clients
    Router::get('/clients', 'Admin\ClientController@clients')->name('staff.clients');
    // Single Client
    Router::get('/client/{client:[a-zA-Z0-9\-]+}', 'Admin\ClientController@single')->name('staff.client');
    Router::post('/client/{client:[a-zA-Z0-9\-]+}', 'Admin\ClientController@single')->middleware([Client::class]);

    // Ticket
    Router::get('/tickets', function(){})->name('staff.tickets');
    Router::get('/ticket/{uid:[a-zA-Z0-9\-]+}', function(){})->name('staff.ticket');
    
    // Order
    Router::get('/orders', function(){})->name('staff.orders');
    Router::get('/order/{uid:[a-zA-Z0-9\-]+}', function(){})->name('staff.order');

    // Staffs
    Router::get('/staffs', function(){})->name('staff.staffs');
    Router::get('/staff/{staff:[a-zA-Z0-9\-]+}', function(){})->name('staff.staff');
    Router::get('/staff-activities', function(){})->name('staff.activities');

    // Invoices
    Router::get('/invoices', function(){})->name('staff.invoices');
    Router::get('/invoice/{invoice:[a-zA-Z0-9\-]+}', function(){})->name('staff.invoice');

    // Others
    Router::get('/my-account', function(){})->name('staff.account');
    Router::get('/settings', function(){})->name('settings');

},[Init::class]);

// Admin Add Route Group
Router::group(ADMIN . '/add', function(){
    // Add Client
    Router::get('client', 'Admin\ClientController@create')->name('staff.add.client');
    Router::post('client', 'Admin\ClientController@create')->middleware([Client::class]);

    // Add Invoice
    Router::get('invoice', 'Admin\InvoiceController@create')->middleware('Admin\Invoice')->name('staff.add.invoice');
    Router::post('invoice', 'Admin\InvoiceController@create')->middleware('Admin\Invoice');

},[Init::class]);
