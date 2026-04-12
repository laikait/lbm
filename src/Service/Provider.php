<?php
/**
 * Name: Laika Shield
 * Provider: Laika IT
 * Email: strblackhawk@gmail.com
 */

declare(strict_types=1);

namespace LBM\Service;

use Laika\Core\Relay\RelayProvider;
use LBM\Action\Activity;
use LBM\Action\StaffAuth;
use LBM\Action\Client;
use LBM\Support\Initiate;

class Provider extends RelayProvider
{
    public function register(): void
    {
        $this->registry->singleton('support.initiate', Initiate::class);
        $this->registry->singleton('action.activity', Activity::class);
        $this->registry->singleton('action.auth.staff', StaffAuth::class);
        $this->registry->singleton('action.client', Client::class);
    } 
}
