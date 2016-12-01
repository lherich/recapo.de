<?php
if ($_SERVER['SCRIPT_FILENAME'] == __FILE__) {
    exit(403);
}

$post = $_app->request()->post();
$projectID = $_params['ID'];
$userID = $_app->__get('login')->info['userID'];

$v = new \Valitron\ValidatorDE($post);

$v->rule('date', 'schedulingStartDatetime');
$v->rule('date', 'schedulingEndDatetime');
$v->rule('in', 'schedulingEndType', array('no', 'day', 'point'));

if ($v->validate()) {
    $schedulingStartDatetime = (isset($post['schedulingStartDatetime'])) ? date('Y-m-d', strtotime($post['schedulingStartDatetime'])) : '0000-00-00';
    $schedulingEndDatetime = '0000-00-00';

    if (isset($post['schedulingEndType']) && $post['schedulingEndType'] == 'point' && isset($post['schedulingEndDatetime'])) {
        $schedulingEndDatetime = date('Y-m-d', strtotime($post['schedulingEndDatetime']));
    } elseif (isset($post['schedulingEndType']) && $post['schedulingEndType'] == 'day' && isset($post['schedulingEndSpinbox'])) {
        $schedulingEndDatetime = date('Y-m-d', strtotime($post['schedulingStartDatetime']) + (60*60*24)*$post['schedulingEndSpinbox']);
    }

    if (isset($post['schedulingFlagCheckbox']) && strtolower($post['schedulingFlagCheckbox'] == 'on')) {
        $schedulingStartDatetime = '0000-00-00';
        $schedulingEndDatetime = '0000-00-00';
    }

    \Recapo\Model\Project::updateField($userID, $projectID, 'startDatetime', $schedulingStartDatetime);
    \Recapo\Model\Project::updateField($userID, $projectID, 'endDatetime', $schedulingEndDatetime);

    $_app->flash('success', 'Der Zeitplan wurde erfolgreich gespeichert.');
    $_app->redirect($_app->urlFor('/project/timetable', array('ID' => $projectID)));
} else {
    $errorMsg = array();
    foreach ($v->errors() as $item) {
        if (is_array($item)) {
            foreach ($item as $subitem) {
                $errorMsg[] = $subitem;
            }
        } else {
            $errorMsg[] = $item;
        }
    }
    $_app->flash('danger', $errorMsg);
    $_app->redirect($_app->urlFor('/project/timetable', array('ID' => $projectID)));
}
