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

echo 'Loaded LBM<br>';
// Require All Functions File
foreach (glob(__DIR__ . '/Functions/*.func.php') as $func_file) require_once $func_file;

// // Require All Hooks File
foreach (glob(__DIR__ . '/Hooks/*.hook.php') as $hook_file) require_once $hook_file;

// Require All Routes
foreach (glob(__DIR__ . '/Routes/*.route.php') as $route_file) require_once $route_file;