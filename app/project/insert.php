<?php
if ($_SERVER['SCRIPT_FILENAME'] == __FILE__) {
    exit(403);
}

$post = $_app->request()->post();

$v = new \Valitron\ValidatorDE($post, array('name', 'url', 'offerButton', 'section', 'schedulingFlagCheckbox', 'schedulingStartDatetime', 'schedulingEndType', 'schedulingEndDatetime', 'schedulingEndSpinbox'));

$v->rule('required', array('name', 'url'));
$v->rule('slug', 'url');
$v->rule('date', 'schedulingStartDatetime');
$v->rule('date', 'schedulingEndDatetime');
$v->rule('in', 'schedulingEndType', array('no', 'day', 'point'));

if ($v->validate()) {
    if (isset($post['offerButton']) && $post['offerButton'] == 'offerButton') {
        $offerButton = true;
    } else {
        $offerButton = false;
    }

    if (isset($post['section']) && is_array($post['section'])) {
        $storedSections = \Recapo\Model\Section::getSectionsID();
        $storedSections = array_map(function ($pArray) {
            if (isset($pArray['ID'])) {
                return $pArray['ID'];
            }
        }, $storedSections);
        $sectionIDs = array();
        foreach ($post['section'] as $index => $value) {
            if (strtolower($value) == 'on' && in_array($index, $storedSections)) {
                $sectionIDs[] = $index;
            }
        }
    } else {
        $sectionIDs = false;
    }

    $schedulingStartDatetime = (isset($post['schedulingStartDatetime'])) ? date('Y-m-d', strtotime($post['schedulingStartDatetime'])) : '0000-00-00';
    $schedulingEndDatetime = '0000-00-00';

    if (isset($post['schedulingEndType']) && $post['schedulingEndType'] == 'point' && isset($post['schedulingEndDatetime'])) {
        $schedulingEndDatetime = date('Y-m-d', strtotime($post['schedulingEndDatetime']));
    } elseif (isset($post['schedulingEndType']) && $post['schedulingEndType'] == 'day' && isset($post['schedulingEndSpinbox'])) {
        $schedulingEndDatetime = date('Y-m-d', $schedulingStartDatetime + (60*60*24)*strtotime($post['schedulingEndSpinbox']));
    }

    if (isset($post['schedulingFlagCheckbox']) && strtolower($post['schedulingFlagCheckbox'] == 'on')) {
        $schedulingStartDatetime = '0000-00-00';
        $schedulingEndDatetime = '0000-00-00';
    }

    $userID = $_app->__get('login')->info['userID'];
    $projectID = \Recapo\Model\Project::insertProject($userID, $post['name'], $post['url'], $schedulingStartDatetime, $schedulingEndDatetime, $offerButton);
    if ($sectionIDs !== false) {
        $i = \Recapo\Model\Section::insertSectionsByProjectID($projectID, $sectionIDs);
    } else {
        $i = 0;
    }

  // insert dummy root to items
  $rootItem = \Recapo\Model\Item::selectRootItemByProjectID($projectID);

  // insert dummy root into the ia
  $rootIA = \Recapo\Model\Informationarchitecture::insertRootItemByProjectID($projectID, $rootItem['ID'], $rootItem['flag']);

    $_app->flash('success', 'Projekt #'.$projectID.' - '.$post['name'].' mit '.$i.' Sektionen erfolgreich erstellt.');
    $_app->redirect($_app->urlFor('/projects'));
} else {
    $errorMsg = array();
    foreach ($$v->errors() as $item) {
        if (is_array($item)) {
            foreach ($item as $subitem) {
                $errorMsg[] = $subitem;
            }
        } else {
            $errorMsg[] = $item;
        }
    }
    $_app->flash('danger', $errorMsg);
    $_app->redirect($_app->urlFor('/newproject'));
}
