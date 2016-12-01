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
$_view->set('project', $_project);
$_view->set('projectContainers', \Recapo\Model\Container::getContainerByProjectID($_project['ID']));
$_view->set('projectItems', \Recapo\Model\Item::getItemsByProjectID($_project['ID']));
$_view->set('projectSections', \Recapo\Model\Section::getSectionsWithActiveFlagByProjectID($_project['ID']));
$_view->set('informationarchitecture', \Recapo\Model\Informationarchitecture::selectNestedSetByProjectID($_project['ID']));

$_app->render($_route['tpl'][$_this]);

/*
SELECT
  *,
IF(informationarchitecture.flag = 'container', 'child', item.flag) AS flag
 FROM informationarchitecture
LEFT JOIN container ON (container.ID = informationarchitecture.containerID)
LEFT JOIN item ON (item.ID = informationarchitecture.itemID)
WHERE informationarchitecture.projectID = 1
ORDER BY informationarchitecture.LFT*/;
