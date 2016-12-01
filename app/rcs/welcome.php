<?php
if ($_SERVER['SCRIPT_FILENAME'] == __FILE__) {
    exit(403);
}

$project = \Recapo\Model\Project::getProjectByUrl($_params['PROJECTURL']);

if (isset($_SESSION[$project['url']])) {
    unset($_SESSION[$project['url']]);
}

$_SESSION[$project['url']] = array('resultID' => \Recapo\Model\Result::insertResult($project['ID']));

$_app->render($_route['tpl'][$_this]);
