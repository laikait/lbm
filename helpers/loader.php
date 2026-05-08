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

// Require All Routes
foreach (glob(__DIR__ . '/Routes/*.route.php') as $route_file) require_once $route_file;