<?php
if ($_SERVER['SCRIPT_FILENAME'] == __FILE__) {
    exit(403);
}

$post = $_app->request()->post();
$login = $_app->__get('login');

$_project = \Recapo\Model\Project::getProject($login->info['userID'], $_params['ID']);
if ($_project == false) {
    $_app->flash('warning', 'Dieses Projekt ist nicht vorhanden oder es besteht keine Berechtigung.');
    $_app->redirect($_app->urlFor('/projects'));
}
$_project = $_project[0];

if (isset($post['task'], $post['items'])) {
    \Recapo\Model\Task::insertTask($_params['ID'], $post['task'], $post['items']);
    $_app->flash('success', 'Die Aufgabe wurde erfolgreich eingetragen.');
    $_app->redirect($_app->urlFor('/project/task', array('ID' => $_params['ID'])));
} else {
    $_app->flash('danger', 'Es wurden nicht alle Felder ausgefüllt.');
    $_app->redirect($_app->urlFor('/project/task', array('ID' => $_params['ID'])));
}
