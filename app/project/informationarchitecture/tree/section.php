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

$sections = \Recapo\Model\Section::selectSectionsActiveByProjectIDForNestedSet($_project['ID']);
foreach ($sections as $item) {
    $item['children'] = array();
}

$_app->response->headers->set('Content-Type', 'application/json');
print json_encode(array_values($sections));
