<?php
/**
 * Name: Laika Shield
 * Provider: Laika IT
 * Email: strblackhawk@gmail.com
 */

declare(strict_types=1);

namespace LBM\Relay;

use Laika\Core\Relay\RelayProvider;
use LBM\Action\Activity;
use LBM\Action\AuthStaff;
use LBM\Action\AuthClient;
use LBM\Action\Client;
use LBM\Action\ClientContact;
use LBM\Action\Invoice;
use LBM\Action\Order;
use LBM\Action\Staff;
use LBM\Action\Support;
use LBM\Action\Country;
use LBM\Action\ClientNote;
use LBM\Support\Initiate;

class Provider extends RelayProvider
{
    public function register(): void
    {
        $this->registry->singleton('support.initiate', Initiate::class);
        $this->registry->singleton('action.activity', Activity::class);
        $this->registry->singleton('action.auth.staff', AuthStaff::class);
        $this->registry->singleton('action.auth.client', AuthClient::class);
        $this->registry->singleton('action.client', Client::class);
        $this->registry->singleton('action.client.contact', ClientContact::class);
        $this->registry->singleton('action.invoice', Invoice::class);
        $this->registry->singleton('action.staff', Staff::class);
        $this->registry->singleton('action.support', Support::class);
        $this->registry->singleton('action.country', Country::class);
        $this->registry->singleton('action.client.note', ClientNote::class);
        $this->registry->singleton('action.order', Order::class);
    } 
}
