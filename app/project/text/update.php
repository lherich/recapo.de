<?php
if ($_SERVER['SCRIPT_FILENAME'] == __FILE__) {
    exit(403);
}

$post = $_app->request()->post();
$projectID = $_params['ID'];
$userID = $_app->__get('login')->info['userID'];

if (isset($post['welcomeText']) && isset($post['instructionText']) && isset($post['tributeText'])) {
    \Recapo\Model\Project::updateText($userID, $projectID, $post['welcomeText'], $post['instructionText'], $post['tributeText']);

    $_app->flash('success', 'Die Texte wurden erfolgreich gespeichert.');
    $_app->redirect($_app->urlFor('/project/text', array('ID' => $projectID)));
} else {
    $_app->flash('danger', 'Ein oder mehrere Parameter fehlen, weshalb die Texte nicht gespeichert worden konnten.');
    $_app->redirect($_app->urlFor('/project/text', array('ID' => $projectID)));
}
