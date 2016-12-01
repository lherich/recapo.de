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
    if (\Recapo\Model\Item::deleteItemsByProjectID($_params['ID'])) {
        $_app->flash('success', 'Alle Datensätze wurden erfolgreich gelöscht.');
        $_app->redirect($_app->urlFor('/project/informationarchitecture', array('ID' => $_params['ID'])));
    } else {
        $_app->flash('danger', 'Die Datensätze konnten nicht gelöscht werden. Ggf. sind keine vorhanden.');
        $_app->redirect($_app->urlFor('/project/informationarchitecture', array('ID' => $_params['ID'])));
    }
} else {
    $_view->set('project', $_project);
    $_app->render($_route['tpl'][$_this]);
}
