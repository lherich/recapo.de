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

$container = \Recapo\Model\Container::selectAllContainerWithAllItemsByProjectID($_project['ID']);

$informationarchitecture = \Recapo\Model\Informationarchitecture::selectNestedSetByActiveSectionAndProjectID($_project['ID']);
$informationarchitecture = \Recapo\Helper\Format::nestedsetToArray($informationarchitecture);
$informationarchitecture = \Recapo\Helper\Format::replaceContainerWithCards($informationarchitecture, $container);

//print_r($informationarchitecture);

$_view->set('informationarchitecture', $informationarchitecture);

$_app->response->headers->set('Content-Type', 'text/xml');
$_app->response->headers->set('Content-Disposition', 'attachment;filename='.date('Y-m-d').'_-_'.$_project['url'].'_-_informationarchitecture.xml');

$_app->render($_route['tpl'][$_this]);
