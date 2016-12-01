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

$_view->set('results', \Recapo\Model\Result::selectResultExportByProjectID($_project['ID']));

$resultTasks = array();
$tmp = \Recapo\Model\ResultTask::selectResultTasks($_project['ID']);
foreach ($tmp as $task) {
    if (isset($resultTasks[$task['resultID']])) {
        $resultTasks[$task['resultID']][] = $task;
    } else {
        $resultTasks[$task['resultID']] = array($task);
    }
}

$resultData = array();
$tmp = \Recapo\Model\ResultData::selectResultDataByProjectID($_project['ID']);
foreach ($tmp as $data) {
    if (isset($resultData[$data['resultTaskID']])) {
        $resultData[$data['resultTaskID']][] = $data;
    } else {
        $resultData[$data['resultTaskID']] = array($data);
    }
}

//var_dump($resultData);exit();
$_view->set('resultTasks', $resultTasks);
$_view->set('resultData', $resultData);
$_app->response->headers->set('Content-Type', 'text/xml');
$_app->response->headers->set('Content-Disposition', 'attachment;filename='.date('Y-m-d').'_-_'.$_project['url'].'_-_report.xml');

$_app->render($_route['tpl'][$_this]);
