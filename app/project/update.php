<?php
if ($_SERVER['SCRIPT_FILENAME'] == __FILE__) {
    exit(403);
}

$post = $_app->request()->post();
if (in_array($_params['FIELD'], array('name', 'flag', 'datetime')) && isset($post['name']) && isset($post['value']) && isset($post['pk'])) {
    $userID = $_app->__get('login')->info['userID'];
    $projectID = $post['pk'];
    \Recapo\Model\Project::updateField($userID, $projectID, $_params['FIELD'], $post['value']);
    $_app->halt(200, 'Erfolgreich die Daten geändert.');
} else {
    $_app->halt(400, 'Fehler, Daten konnten nicht geändert werden.');
}
