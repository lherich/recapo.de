<?php
if ($_SERVER['SCRIPT_FILENAME'] == __FILE__) {
    exit(403);
}

$login = $_app->__get('login');
$_view->set('projects', \Recapo\Model\Project::getProjects($login->info['userID']));
$_app->render($_route['tpl'][$_this]);
