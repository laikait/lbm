<?php

/**
 * Laika Bill Manager
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 */

#########################################################################
/*----------------------------- LBM HOOKS -----------------------------*/
#########################################################################

declare(strict_types=1);

// Require All Functions File
array_map(function($file)
{
    require_once $file;
}, glob(__DIR__ . '/hooks/*.hook.php'));