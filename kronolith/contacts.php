<?php
/**
 * Copyright 2002-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (GPL). If you
 * did not receive this file, see http://www.horde.org/licenses/gpl.
 */

require_once __DIR__ . '/lib/Application.php';
Horde_Registry::appInit('kronolith');

if (Kronolith::showAjaxView()) {
    Horde::url('', true)->redirect();
}

if (!$GLOBALS['registry']->getAuth()) {
    echo Horde::wrapInlineScript(array('window.close();'));
    exit;
}

/* Get the lists of address books through API */
$source_list = $registry->call('contacts/sources');

/* If we self-submitted, use that source. Otherwise, choose a good
 * source. */
$source = Horde_Util::getFormData('source');
if (empty($source) || !isset($source_list[$source])) {
    /* We don't just pass the second argument to getFormData() because
     * we want to trap for invalid sources, not just no source. */
    $source = key($source_list);
}

/* Get the search as submitted (defaults to '' which should list everyone). */
$search = Horde_Util::getFormData('search');

if ($search || $prefs->getValue('display_contact')) {
    $searchpref = Kronolith::getAddressbookSearchParams();
    $fields = isset($searchpref[$source])
        ? array($source => $searchpref[$source])
        : array();

    try {
        $results = $registry->call('contacts/search', array($search, array(
            'fields' => $fields,
            'sources' => array($source)
        )));
    } catch (Exception $e) {
        $results = array();
    }
} else {
    $results = array();
}

/* The results list returns an array for each source searched - at least
   that's how it looks to me. Make it all one array instead. */
$addresses = array();
foreach ($results as $r) {
    $addresses = array_merge($addresses, $r);
}

/* If self-submitted, preserve the currently selected users encoded by
   javascript to pass as value|text. */
$selected_addresses = array();
$sa = explode('|', Horde_Util::getFormData('sa'));
for ($i = 0; $i < count($sa) - 1; $i += 2) {
    $selected_addresses[$sa[$i]] = $sa[$i + 1];
}

/* Set the default list display (name or email). */
$display = Horde_Util::getFormData('display', 'name');

/* Display the form. */
$page_output->header(array(
    'title' => _("Address Book")
));
require KRONOLITH_TEMPLATES . '/javascript_defs.php';
require KRONOLITH_TEMPLATES . '/contacts/contacts.inc';
$page_output->footer();
