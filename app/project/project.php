<?php
if ($_SERVER['SCRIPT_FILENAME'] == __FILE__) {
    exit(403);
}

$_app->flashKeep();
$_app->redirect($_app->urlFor('/project/dashboard', array('ID' => $_params['ID'])));
/*
$login = $_app->__get('login');
$_project = \Recapo\Model\Project::getProject($login->info['userID'], $_params['ID']);
if($_project == FALSE) {
  $_app->flash('warning', 'Dieses Projekt ist nicht vorhanden oder es besteht keine Berechtigung.');
  $_app->redirect($_app->urlFor('/projects'));
}
$_project = $_project[0];
$_view->set('project', $_project);

$_view->set('projectContainers', \Recapo\Model\Container::getContainerByProjectID($_project['ID']));
$_view->set('projectItems', \Recapo\Model\Item::getItemsByProjectID($_project['ID']));
$_view->set('projectSections', \Recapo\Model\Section::getSectionsWithActiveFlagByProjectID($_project['ID']));


$_app->render($_route['tpl'][$_this]);*/;
