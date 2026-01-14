<?php

/**
 * Laika Bill Manager
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 */

#########################################################################
/*--------------------------- LBM FUNCTIONS ---------------------------*/
#########################################################################

declare(strict_types=1);

// Require All Functions File
array_map(function($file)
{
    require_once $file;
}, glob(__DIR__ . '/Functions/*.func.php'));

// Require All Hooks File
array_map(function($file)
{
    require_once $file;
}, glob(__DIR__ . '/Hooks/*.hook.php'));

// Require All Routes
array_map(function($file)
{
    require_once $file;
}, glob(__DIR__ . '/Routes/*.route.php'));

echo 'dsdssd';die;