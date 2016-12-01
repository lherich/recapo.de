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

if (isset($_params['CONFIRMED'])) {
    if (\Recapo\Model\Project::deleteProjectByID($_params['ID'])) {
        $_app->flash('success', 'Der Datensatz wurde erfolgreich gelöscht.');
        $_app->redirect($_app->urlFor('/projects'));
    } else {
        $_app->flash('danger', 'Der Datensatz konnte nicht gelöscht werden. Ggf. ist er nicht mehr vorhanden.');
        $_app->redirect($_app->urlFor('/projects'));
    }
} else {
    $_view->set('project', $_project);
    $_app->render($_route['tpl'][$_this]);
}
