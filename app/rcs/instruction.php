<?php
if ($_SERVER['SCRIPT_FILENAME'] == __FILE__) {
    exit(403);
}

$project = \Recapo\Model\Project::getProjectByUrl($_params['PROJECTURL']);

if (!isset($_SESSION[$project['url']]['resultID']) || \Recapo\Model\Result::selectResultByID($_SESSION[$project['url']]['resultID']) == null) {
    $_app->redirect($_app->urlFor('/welcome', array('PROJECTURL' => $project['url'])));
    exit();
}
\Recapo\Model\Result::updateEndDatetimeByID($_SESSION[$project['url']]['resultID']);

$_app->render($_route['tpl'][$_this]);
