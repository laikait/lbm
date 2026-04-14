<?php

/**
 * Laika Bill Manager
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 */

#########################################################################
/*---------------------------- ADMIN HOOKS ----------------------------*/
#########################################################################

declare(strict_types=1);

use Laika\Core\Relay\Relays\Nav;

/*=============================== NAVBAR ===============================*/
// Add Admin Nav Header Filter
add_hook('admin.nav.header', function(){
    // Dashboard
    Nav::add(LANG::$dashboard, named('staff.dashboard', url:true));

    // Clients
    Nav::add(LANG::$clients, named('staff.dashboard', url:true))
            ->child(LANG::$add, named('staff.add-client', url:true), staff_has_access('client.create'))->end()
            ->child('--'.LANG::$active, named('staff.clients?status=active', url:true))->end()
            ->child('--'.LANG::$inactive, named('staff.clients?status=inactive', url:true))->end()
            ->child('--'.LANG::$suspended, named('staff.clients?status=suspended', url:true))->end();

    // Products/Services
    Nav::add(LANG::$products, named('staff.products', url:true))
            ->child(LANG::$add, named('staff.add-product', url:true), staff_has_access('product.create'))->end()
            ->child('--'.LANG::$active, named('staff.products?status=active', url:true))->end()
            ->child('--'.LANG::$inactive, named('staff.products?status=inactive', url:true))->end()
            ->child('--'.LANG::$suspended, named('staff.products?status=suspended', url:true))->end();

    // Orders
    Nav::add(LANG::$orders, named('staff.orders', url:true))
            ->child(LANG::$add, named('staff.add-order', url:true), staff_has_access('order.create'))->end()
            ->child('--'.LANG::$active, named('staff.orders?status=active', url:true))->end()
            ->child('--'.LANG::$pending, named('staff.orders?status=pending', url:true))->end()
            ->child('--'.LANG::$canceled, named('staff.orders?status=canceled', url:true))->end()
            ->child('--'.LANG::$suspended, named('staff.orders?status=suspended', url:true))->end()
            ->child('--'.LANG::$fraud, named('staff.orders?status=fraud', url:true))->end();

    // Invoices
    Nav::add(LANG::$invoices, named('staff.invoices', url:true))
            ->child(LANG::$add, named('staff.add-invoice', url:true), staff_has_access('invoice.create'))->end()
            ->child('--'.LANG::$paid, named('staff.invoices?status=paid', url:true))->end()
            ->child('--'.LANG::$unpaid, named('staff.invoices?status=unpaid', url:true))->end()
            ->child('--'.LANG::$canceled, named('staff.invoices?status=canceled', url:true))->end()
            ->child('--'.LANG::$overdue, named('staff.invoices?status=overdue', url:true))->end()
            ->child('--'.LANG::$refunded, named('staff.invoices?status=refunded', url:true))->end();

    // Support
    Nav::add(LANG::$support, named('staff.tickets', url:true))
            ->child(LANG::$add, named('staff.add-ticket', url:true), staff_has_access('ticket.create'))->end()
            ->child('--'.LANG::$open, named('staff.tickets?status=open', url:true))->end()
            ->child('--'.LANG::$ongoing, named('staff.tickets?status=ongoing', url:true))->end()
            ->child('--'.LANG::$closed, named('staff.tickets?status=closed', url:true))->end()
            ->child('--'.LANG::$solved, named('staff.tickets?status=solved', url:true))->end()
            ->child(LANG::$networkStatus, named('staff.network', url:true))->end();

    // Support
    Nav::add(LANG::$reports, named('staff.reports', url:true))
            ->child(LANG::$invoiceReport, named('staff.invoice-report', url:true))->end()
            ->child(LANG::$orderReport, named('staff.order-report', url:true))->end()
            ->child(LANG::$ticketFeedbacks, named('staff.ticket-feedbacks', url:true))->end();

    // Staffs
    Nav::add(LANG::$staffs, named('staff.staffs', url:true))
            ->child(LANG::$add, named('staff.add-staff', url:true), staff_has_access('staff.create'))->end()
            ->child('--'.LANG::$active, named('staff.staffs?status=active', url:true))->end()
            ->child('--'.LANG::$inactive, named('staff.staffs?status=inactive', url:true))->end()
            ->child('--'.LANG::$suspended, named('staff.staffs?status=suspended', url:true))->end();

    // Addons
    Nav::add(LANG::$addons, named('staff.addons', url:true)); // Add Addons Sub Menu Later From Addons Class

    // Noticeboard
    Nav::add(LANG::$noticeboard, named('staff.noticeboard', url:true))
            ->child(LANG::$add, named('staff.add-notice', url:true), staff_has_access('notice.create'))->end();
    
    // Render & Return
    return Nav::render('admin-nav');
}, 1000);

// Admin Settings Link
add_hook('admin.settings.link', function(){
    if (staff_has_access('settings.read')) {
        return '<a href="' . named('settings', url:true) . '">' . LANG::$settings . '</a>';
    }
    return '';
});