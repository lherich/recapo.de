<?php
if ($_SERVER['SCRIPT_FILENAME'] == __FILE__) {
    exit(403);
}

$post = $_app->request()->post();
if (in_array($_params['FIELD'], array('surname', 'forename', 'email')) && isset($post['value'])) {
    $userID = $_app->__get('login')->info['userID'];
    \Recapo\Model\User::updateField($userID, $_params['FIELD'], $post['value']);
    $_app->halt(200, 'Erfolgreich die Daten geändert.');
} else {
    $_app->halt(400, 'Fehler, Daten konnten nicht geändert werden.');
}
