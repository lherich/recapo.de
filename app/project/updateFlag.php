<?php
if ($_SERVER['SCRIPT_FILENAME'] == __FILE__) {
    exit(403);
}

$post = $_app->request()->post();
$projectID = $_params['ID'];
$pFlag = $_params['FLAG'];
$userID = $_app->__get('login')->info['userID'];

if (in_array($pFlag, array('public', 'protected', 'private'))) {
    \Recapo\Model\Project::updateField($userID, $projectID, 'flag', $pFlag);

    $_app->flash('success', 'Projektstatus wurde erfolgreich geändert.');
  //$_app->redirect($_app->urlFor('/project', array('ID'=>$projectID)));
} else {
    $_app->flash('danger', 'Projektstatus konnte nicht verändert werden. Ggf. wurde ein nicht unterstützter Status übergeben.');
  //$_app->redirect($_app->urlFor('/project', array('ID'=>$projectID)));
}
