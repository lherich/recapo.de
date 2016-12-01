<?php
if ($_SERVER['SCRIPT_FILENAME'] == __FILE__) {
    exit(403);
}

$post = $_app->request()->post();
$project = \Recapo\Model\Project::getProjectByUrl($_params['PROJECTURL']);

$pEmail = (isset($post['email']) && trim($post['email']) ? $post['email'] : false);
$pForename = (isset($post['forename']) && trim($post['forename']) ? $post['forename'] : false);
$pSurname = (isset($post['surname']) && trim($post['surname']) ? $post['surname'] : false);
if ($pEmail !== false || $pForename !== false || $pSurname !== false) {
    \Recapo\Model\Proband::insertProbandByProjectID($project['ID'], $pEmail, $pForename, $pSurname);
}
$_app->redirect($_app->urlFor('/experiment', array('PROJECTURL' => $_params['PROJECTURL'])));
