<?php
if ($_SERVER['SCRIPT_FILENAME'] == __FILE__) {
    exit(403);
}

$login = $_app->__get('login');
$_project = \Recapo\Model\Project::getProject($login->info['userID'], $_params['ID']);

if ($_project == false) {
    $_app->flash('warning', 'Dieses Projekt ist nicht vorhanden oder es besteht keine Berechtigung.');
    $_app->redirect($_app->urlFor('/projects'));
}
$_project = $_project[0];
$result = \Recapo\Model\Item::insertItem($_project['ID'], $_app->request()->post('text'));
if ($result > 0) {
    $_app->halt(200, $result);
} else {
    $_app->halt(500, 'Fehler beim Hinzufügen.');
}
