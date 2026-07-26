<?php

/**
 * Laika Bill Manager
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 */

##########################################################################
/*----------------------------- LBM LOADER -----------------------------*/
##########################################################################

declare(strict_types=1);

use Laika\Service\Resource;

// Require All Routes
Resource::register('routes', __DIR__ . '/Routes');