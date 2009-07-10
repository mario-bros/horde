<?php
/**
 * Jeta base inclusion file.
 *
 * This file brings in all of the dependencies that every web ssh script
 * will need, and sets up objects that all scripts use.
 *
 * Copyright 2002-2009 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (GPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 */

// Check for a prior definition of HORDE_BASE (perhaps by an
// auto_prepend_file definition for site customization).
if (!defined('HORDE_BASE')) {
    define('HORDE_BASE', dirname(__FILE__) . '/../..');
}

// Find the base file path of Jeta.
if (!defined('JETA_BASE')) {
    define('JETA_BASE', dirname(__FILE__) . '/..');
}

// Load the Horde Framework core.
require_once HORDE_BASE . '/lib/core.php';

// Registry.
$registry = Horde_Registry::singleton();
try {
    $registry->pushApp('jeta', !defined('AUTH_HANDLER'));
} catch (Horde_Exception $e) {
    if ($e->getCode() == 'permission_denied') {
        Horde::authenticationFailureRedirect();
    }
    Horde::fatal($e, __FILE__, __LINE__, false);
}

$conf = &$GLOBALS['conf'];
define('JETA_TEMPLATES', $registry->get('templates'));

// Notification system.
$notification = &Horde_Notification::singleton();
$notification->attach('status');

// Includes.
require_once JETA_BASE . '/lib/Jeta.php';
