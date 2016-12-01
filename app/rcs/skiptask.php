<?php
if ($_SERVER['SCRIPT_FILENAME'] == __FILE__) {
    exit(403);
}

$project = \Recapo\Model\Project::getProjectByUrl($_params['PROJECTURL']);

if (!isset($_SESSION[$project['url']]['resultID']) || \Recapo\Model\Result::selectResultByID($_SESSION[$project['url']]['resultID']) == null) {
    $_app->redirect($_app->urlFor('/welcome', array('PROJECTURL' => $project['url'])));
    exit();
}
$experiment = &$_SESSION[$project['url']];

if (!isset($experiment['resultTaskID'])) {
    $_app->redirect($_app->urlFor('/experiment', array('PROJECTURL' => $project['url'])));
    exit();
}
\Recapo\Model\ResultTask::updateResultTaskByID($experiment['resultTaskID'], 'skipped');
unset($_SESSION[$project['url']]['resultTaskID']);

$_app->redirect($_app->urlFor('/experiment', array('PROJECTURL' => $project['url'])));
