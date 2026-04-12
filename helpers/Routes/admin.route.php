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

// Admin Panel Fallback
Router::fallback(ADMIN, null, 'Auth\Checker|type='.ADMIN);

// Admin Login Routes
Router::group(ADMIN, function(){
    Router::get('/login', 'Admin\Login@index')->name('staff.login'); // Done
    Router::post('/login', 'Admin\Login@index');
}, ['Auth\Maker|type='.ADMIN]);


// Admin Route Group
Router::group(ADMIN, function(){
    // Dashboard, Login & Logout
    Router::get('/', 'Admin\Dashboard@index')->middleware('Admin\Dashboard')->name('staff.dashboard'); // Almost Done
    Router::get('/logout')->middleware('Admin\Logout')->name('staff.logout'); // Done

    // Clients
    Router::get('/clients', 'Admin\Client@clients')->middleware('Admin\Clients')->name('staff.clients');
    // Single Client
    Router::get('/client/{client:[a-zA-Z0-9\-]+}', 'Admin\Client@client')->middleware('Admin\Client')->name('staff.client');
    Router::post('/client/{client:[a-zA-Z0-9\-]+}', 'Admin\Client@client')->middleware('Admin\Client');

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

},['Auth\Checker|type='.ADMIN]);

// Admin Add Route Group
Router::group(ADMIN . '/add', function(){
    // Add Client
    Router::get('client', 'Admin\Client@create')->middleware('Admin\Client')->name('staff.add.client');
    Router::post('client', 'Admin\Client@create')->middleware('Admin\Client');

    // Add Invoice
    Router::get('invoice', 'Admin\Client@create')->middleware('Admin\Invoice')->name('staff.add.invoice');
    Router::post('invoice', 'Admin\Client@create')->middleware('Admin\Invoice');

},['Auth\Checker|type='.ADMIN]);
