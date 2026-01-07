<?php
/**
 * Laika PHP MVC Framework
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 * License: MIT
 * This file is part of the Laika PHP MVC Framework.
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

use Laika\App\Helper\Nav;
use Laika\Core\Helper\Url;

// Add Admin Nav Header Filter
add_hook('admin.nav.header', function(){
    // Nav Object
    $nav = new Nav();
    $url = new Url();

    // Dashboard Child
    $nav->add(LANG::$dashboard, named(ADMIN, url:true));

    // Clients Nav
    $nav->child(LANG::$active, $url->build(named('staff.clients'), ['status' => 'active']));
    $nav->child(LANG::$inactive, $url->build(named('staff.clients'), ['status' => 'inactive']));
    $nav->child(LANG::$suspended, $url->build(named('staff.clients'), ['status' => 'suspended']));
    $nav->add(LANG::$clients, $url->build(named('staff.clients')));

    // Products/Services
    $nav->child(LANG::$active, do_hook('uri.make', [ADMIN,'products'], ['status'=>'active']));
    $nav->child(LANG::$inactive, do_hook('uri.make', [ADMIN,'products'], ['status'=>'inactive']));
    $nav->child(LANG::$suspended, do_hook('uri.make', [ADMIN,'products'], ['status'=>'suspended']));
    $nav->add(LANG::$products, do_hook('uri.make', [ADMIN,'products']), 'viewProducts');

    // Orders
    $nav->child(LANG::$active, do_hook('uri.make', [ADMIN,'orders'], ['status'=>'active']));
    $nav->child(LANG::$pending, do_hook('uri.make', [ADMIN,'orders'], ['status'=>'pending']));
    $nav->child(LANG::$canceled, do_hook('uri.make', [ADMIN,'orders'], ['status'=>'canceled']));
    $nav->child(LANG::$suspended, do_hook('uri.make', [ADMIN,'orders'], ['status'=>'suspended']));
    $nav->child(LANG::$fraud, do_hook('uri.make', [ADMIN,'orders'], ['status'=>'fraud']));
    $nav->add(LANG::$orders, do_hook('uri.make', [ADMIN,'orders']), 'viewOrders');

    // Invoices
    $nav->child(LANG::$paid, do_hook('uri.make', [ADMIN,'invoices'], ['status'=>'paid']));
    $nav->child(LANG::$unpaid, do_hook('uri.make', [ADMIN,'invoices'], ['status'=>'unpaid']));
    $nav->child(LANG::$canceled, do_hook('uri.make', [ADMIN,'invoices'], ['status'=>'canceled']));
    $nav->child(LANG::$overdue, do_hook('uri.make', [ADMIN,'invoices'], ['status'=>'overdue']));
    $nav->child(LANG::$refunded, do_hook('uri.make', [ADMIN,'invoices'], ['status'=>'refunded']));
    $nav->add(LANG::$invoices, do_hook('uri.make', [ADMIN,'invoices']), 'viewInvoices');

    // Support
    $nav->child(LANG::$networkStatus,do_hook('uri.make', [ADMIN, 'network-status']));
    $nav->add(LANG::$support, do_hook('uri.make', [ADMIN,'tickets']), 'viewTickets');

    // Reports
    $nav->child(LANG::$invoiceReport,do_hook('uri.make', [ADMIN, 'invoice-report']));
    $nav->child(LANG::$orderReport,do_hook('uri.make', [ADMIN, 'order-report']));
    $nav->child(LANG::$ticketFeedbacks,do_hook('uri.make', [ADMIN, 'ticket-feedbacks']));
    $nav->add(LANG::$reports, do_hook('uri.make', [ADMIN,'reports']), 'viewReports');

    // Staffs
    $nav->child(LANG::$active, do_hook('uri.make', [ADMIN, 'staffs'], ['status' => 'active']));
    $nav->child(LANG::$inactive, do_hook('uri.make', [ADMIN, 'staffs'], ['status' => 'inactive']));
    $nav->child(LANG::$suspended, do_hook('uri.make', [ADMIN, 'staffs'], ['status' => 'suspended']));
    $nav->add(LANG::$staffs, do_hook('uri.make', [ADMIN,'staffs']), 'viewStaffs');

    // Noticeboard
    $nav->add(LANG::$noticeboard, do_hook('uri.make', [ADMIN, 'notices']), 'viewStaffs');

    // Modules
    $nav->add(LANG::$modules, do_hook('uri.make', [ADMIN, 'modules']), 'viewModules');

    // Logs
    $nav->child(LANG::$activityLogs, do_hook('uri.make', [ADMIN, 'activity-logs']), access:'viewActivityLogs');
    $nav->child(LANG::$systemLogs, do_hook('uri.make', [ADMIN, 'system-logs']), access:'viewSystemLogs');
    $nav->child(LANG::$moduleLogs, do_hook('uri.make', [ADMIN, 'module-logs']), access:'viewModuleLogs');
    $nav->child(LANG::$loginLogs, do_hook('uri.make', [ADMIN, 'login-logs']), access:'viewLoginLogs');
    $nav->add(LANG::$logs, do_hook('uri.make', [ADMIN,'logs']), 'viewLogs');

    // Settings
    $nav->add(LANG::$settings, do_hook('uri.make', [ADMIN, 'settings']), 'modifySettings');

    // Return Redered Nav
    return $nav->render();
});