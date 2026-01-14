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

use Laika\Core\Helper\NavBuilder;
use LBM\Factory\Addons;

/*=============================== NAVBAR ===============================*/
// Add Admin Nav Header Filter
add_hook('admin.nav.header', function(){
    $nav = new NavBuilder();
    // Dashboard
        $nav->add(LANG::$dashboard, named(ADMIN, url:true))
            // Clients
            ->add(LANG::$clients, named('staff.clients', url:true), function(NavBuilder $n){
                $n->add(LANG::$add, named('staff.new-client', url:true), null, admin_access('client.create'));
                $n->add('--'.LANG::$active, named('staff.clients?status=active', url:true));
                $n->add('--'.LANG::$inactive, named('staff.clients?status=inactive', url:true));
                $n->add('--'.LANG::$suspended, named('staff.clients?status=suspended', url:true));
            }, admin_access('client.read'))

            // Products/Services
            ->add(LANG::$products, named('staff.products', url:true), function(NavBuilder $n){
                $n->add(LANG::$add, named('staff.new-product', url:true), null, admin_access('product.create'));
                $n->add('--'.LANG::$active, named('staff.products?status=active', url:true));
                $n->add('--'.LANG::$inactive, named('staff.products?status=inactive', url:true));
                $n->add('--'.LANG::$suspended, named('staff.products?status=suspended', url:true));
            }, admin_access('product.read'))

            // Orders
            ->add(LANG::$orders, named('staff.orders', url:true), function(NavBuilder $n){
                $n->add(LANG::$add, named('staff.new-order', url:true), null, admin_access('order.read'));
                $n->add('--'.LANG::$active, named('staff.orders?status=active', url:true));
                $n->add('--'.LANG::$pending, named('staff.orders?status=pending', url:true));
                $n->add('--'.LANG::$canceled, named('staff.orders?status=canceled', url:true));
                $n->add('--'.LANG::$suspended, named('staff.orders?status=suspended', url:true));
                $n->add('--'.LANG::$fraud, named('staff.orders?status=fraud', url:true));
            }, admin_access('order.read'))

            // Invoices
            ->add(LANG::$invoices, named('staff.invoices', url:true), function(NavBuilder $n){
                $n->add(LANG::$add, named('staff.new-invoice', url:true), null, admin_access('invoice.create'));
                $n->add('--'.LANG::$paid, named('staff.invoices?status=paid', url:true));
                $n->add('--'.LANG::$unpaid, named('staff.invoices?status=unpaid', url:true) );
                $n->add('--'.LANG::$canceled, named('staff.invoices?status=canceled', url:true));
                $n->add('--'.LANG::$overdue, named('staff.invoices?status=overdue', url:true));
                $n->add('--'.LANG::$refunded, named('staff.invoices?status=refunded', url:true));
            }, admin_access('invoice.read'))

            // Support
            ->add(LANG::$support, named('staff.tickets', url:true), function(NavBuilder $n){
                $n->add(LANG::$add, named('staff.new-ticket', url:true), null, admin_access('ticket.create'));
                $n->add('--'.LANG::$open, named('staff.invoices?status=open', url:true));
                $n->add('--'.LANG::$ongoing, named('staff.tickets?status=ongoing', url:true));
                $n->add('--'.LANG::$closed, named('staff.tickets?status=closed', url:true));
                $n->add('--'.LANG::$solved, named('staff.tickets?status=solved', url:true));
                $n->add(LANG::$networkStatus, named('staff.network', url:true));
            }, admin_access('ticket.read'))

            // Reports
            ->add(LANG::$reports, named('staff.reports', url:true), function(NavBuilder $n){
                $n->add(LANG::$add, named('staff.new-report', url:true), null, admin_access('report.create'));
                $n->add(LANG::$invoiceReport, named('staff.invoice-report', url:true));
                $n->add(LANG::$orderReport, named('staff.order-report', url:true));
                $n->add(LANG::$ticketFeedbacks, named('staff.ticket-feedbacks', url:true));
            }, admin_access('ticket.read'))

            // Staffs
            ->add(LANG::$staffs, named('staff.staffs', url:true), function(NavBuilder $n){
                $n->add(LANG::$add, named('staff.new-staff', url:true), null, admin_access('staff.create'));
                $n->add('--'.LANG::$active, named('staff.staffs?status=active', url:true));
                $n->add('--'.LANG::$inactive, named('staff.staffs?status=inactive', url:true));
                $n->add('--'.LANG::$suspended, named('staff.staffs?status=suspended', url:true));
            }, admin_access('staff.read'))

            // Addons
            ->add(LANG::$addons, named('staff.addons', url:true), function(NavBuilder $n){
                $addons = Addons::get();
                foreach ($addons as $addon) {
                    $n->add($addon['title'], named('staff.addon', ['name' => $addon['url']], true));
                }
            }, admin_access('staff.read'))

            // Noticeboard
            ->add(LANG::$noticeboard, named('staff.noticeboard', url:true), function(NavBuilder $n){
                $n->add(LANG::$add, named('staff.new-notice', url:true), null, admin_access('notice.create'));
            }, admin_access('notice.read'));
    
    // Render & Return
    return $nav->render('admin-nav');
}, 1000);

// Admin Settings Link
add_hook('admin.settings.link', function(){
    if (admin_access('settings.read')) {
        return '<a href="' . named('settings', url:true) . '">' . LANG::$settings . '</a>';
    }
    return '';
});