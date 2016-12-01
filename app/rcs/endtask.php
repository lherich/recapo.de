<?php
if ($_SERVER['SCRIPT_FILENAME'] == __FILE__) {
    exit(403);
}

$project = \Recapo\Model\Project::getProjectByUrl($_params['PROJECTURL']);

// no session started?
if (!isset($_SESSION[$project['url']])) {
    exit('ERROR1');
    $_app->redirect($_app->urlFor('/welcome', array('PROJECTURL' => $project['url'])));
    exit();
}
$experiment = &$_SESSION[$project['url']];
// check if data is within the session
if (!isset($experiment['resultID'], $experiment['resultTaskID'])) {
    exit('ERROR2');
    $_app->redirect($_app->urlFor('/experiment', array('PROJECTURL' => $project['url'])));
    exit();
}

// check if the ID of the result is valid
$result = \Recapo\Model\Result::selectItemByID($project['ID'], $experiment['resultID']);
if ($result == null) {
    exit('ERROR3');
    $_app->redirect($_app->urlFor('/experiment', array('PROJECTURL' => $project['url'])));
    exit();
}
// check if the ID of the node is within the IA
$currentNode = \Recapo\Model\Informationarchitecture::selectItemByID($project['ID'], $_params['ID']);
$currentNodeItem = \Recapo\Model\Item::selectItemByIDAndProjectID($_params['ITEMID'], $project['ID']);

if ($currentNode == null || $currentNodeItem == null) {
    exit('ERROR4');
    $_app->redirect($_app->urlFor('/experiment', array('PROJECTURL' => $project['url'])));
    exit();
}

// check if the ID of the resultTask valid
$resultTask = \Recapo\Model\ResultTask::selectItemByID($project['ID'], $experiment['resultTaskID']);
if ($resultTask == null) {
    exit('ERROR5');
    $_app->redirect($_app->urlFor('/experiment', array('PROJECTURL' => $project['url'])));
    exit();
}

/*
var_dump($result);
var_dump($currentNode);
var_dump($resultTask);
*/

if ($resultTask['itemID'] == $currentNodeItem['ID']) {
    // hit
  \Recapo\Model\ResultTask::updateResultTaskByID($experiment['resultTaskID'], 'hit');
} else {
    // miss
  \Recapo\Model\ResultTask::updateResultTaskByID($experiment['resultTaskID'], 'miss');
}

// unset current TaskID, so the next task will be shown
unset($_SESSION[$project['url']]['resultTaskID']);

// redirect
$_app->redirect($_app->urlFor('/experiment', array('PROJECTURL' => $project['url'])));
