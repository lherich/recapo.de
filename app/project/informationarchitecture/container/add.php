<?php
if ($_SERVER['SCRIPT_FILENAME'] == __FILE__) {
    exit(403);
}

$login = $_app->__get('login');
$_project = \Recapo\Model\Project::getProject($login->info['userID'], $_params['ID']);
$post = $_app->request()->post();

if ($_project == false) {
    $_app->flash('warning', 'Dieses Projekt ist nicht vorhanden oder es besteht keine Berechtigung.');
    $_app->halt(500, 'Dieses Projekt ist nicht vorhanden oder es besteht keine Berechtigung.');
}

$_project = $_project[0];

if (!isset($post['containerName'], $post['item']) || !trim($post['containerName'])) {
    $_app->flash('danger', 'Es wurde kein Containername angegeben oder es wurde versucht einen leeren Container anzulegen.');
    $_app->halt(500, 'Es wurde kein Containername angegeben oder es wurde versucht einen leeren Container anzulegen.');
}

$containerID = \Recapo\Model\Container::insertContainer($_project['ID'], $post['containerName']);
foreach ($post['item'] as $itemID) {
    \Recapo\Model\ContainerMapItem::addItem($containerID, $itemID);
}
$_app->response->headers->set('Content-Type', 'application/json');
print json_encode(array('ID' => $containerID, 'name' => $post['containerName'], 'flag' => 'container'));
