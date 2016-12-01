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
if (!isset($post['moveToLFT'], $post['sectionID'])) {
    exit('Missing Parameter');
}

// new LFT
// Node ID

$pMoveToLFT = $post['moveToLFT'];
$pID = $post['ID'];

$node = \Recapo\Model\Informationarchitecture::selectItemByProjectID($pID, $_project['ID']);
if ($node == null) {
    exit('Node does not exists or does not exists in this project.');
}

if (\Recapo\Model\Informationarchitecture::moveTreeToLFT($node, $pMoveToLFT)) {
    exit(\Recapo\Model\Informationarchitecture::updateItemByID($pID, $post['sectionID']));
} else {
    exit(0);
}

/*
$post = $_app->request()->post();
if(isset($post['ID'], $post['flag'], $post['section'], $post['LFT'])) {
  if($post['flag'] == 'item') {
    $item = \Recapo\Model\Item::selectItemByID($post['ID']);
    if($item == FALSE)
      exit('Karte nicht vorhanden');
  } else {
    $item = \Recapo\Model\Container::selectContainerByID($post['ID']);
    if($item == FALSE)
      exit('Container nicht vorhanden');
  }

  $ID = \Recapo\Model\Informationarchitecture::insertItemByLFT($post['LFT'], $_project['ID'], $item['ID'], $post['section'], $item['flag']);
  $item = \Recapo\Model\Informationarchitecture::selectItemByID($ID);

  $sections = \Recapo\Model\Section::selectSectionsActiveByProjectIDForNestedSet($_project['ID']);
  foreach($sections AS $item) {
    $item['children'] = array();
  }
  $sections = array_values($sections);
  // item in nested set eintragen
  $item['children'] = $sections;

  $_app->response->headers->set('Content-Type', 'application/json');
  print json_encode($item);
} else {
  exit('Nicht alle Parameter übergeben');
}*/;
