<?php
if ($_SERVER['SCRIPT_FILENAME'] == __FILE__) {
    exit(403);
}

$post = $_app->request()->post();

if (isset($post['name']) && isset($post['value']) && isset($post['pk'])) {
    if ($post['name'] == 'task') {
        $userID = $_app->__get('login')->info['userID'];
        if (\Recapo\Model\Task::updateTask($userID, $post['pk'], $post['value'])) {
            $_app->halt(200, 'Erfolgreich die Daten geändert.');
        } else {
            $_app->halt(403, 'Keine Berechtigung dieses Projekt zu ändern.');
        }
    } elseif ($post['name'] == 'itemID') {
        $userID = $_app->__get('login')->info['userID'];
        if (\Recapo\Model\Task::updateItemID($userID, $post['pk'], $post['value'])) {
            $_app->halt(200, 'Erfolgreich die Daten geändert.');
        } else {
            $_app->halt(403, 'Keine Berechtigung dieses Projekt zu ändern.');
        }
    } else {
        $_app->halt(500, 'Falsche Daten übergeben.');
    }
} else {
    $_app->halt(500, 'Fehler, Keine Daten übergeben.');
}
