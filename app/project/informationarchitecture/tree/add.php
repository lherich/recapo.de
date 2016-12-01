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

$post = $_app->request()->post();
if (isset($post['ID'], $post['flag'], $post['section'], $post['LFT'])) {
    if ($post['flag'] == 'item') {
        $item = \Recapo\Model\Item::selectItemByID($post['ID']);
        if ($item == false) {
            exit('Karte nicht vorhanden');
        }
    } else {
        $item = \Recapo\Model\Container::selectContainerByID($post['ID']);
        if ($item == false) {
            exit('Container nicht vorhanden');
        }
    }

    $ID = \Recapo\Model\Informationarchitecture::insertItemByLFT($post['LFT'], $_project['ID'], $item['ID'], $post['section'], $item['flag']);
    $node = \Recapo\Model\Informationarchitecture::selectItemByID($ID);

    $sections = \Recapo\Model\Section::selectSectionsActiveByProjectIDForNestedSet($_project['ID']);
    foreach ($sections as $item) {
        $item['children'] = array();
    }
    $sections = array_values($sections);
  // item in nested set eintragen
  $node['children'] = $sections;

  //print_r($node);exit();
  $_app->response->headers->set('Content-Type', 'application/json');
    print json_encode($node);
} else {
    exit('Nicht alle Parameter übergeben');
}
