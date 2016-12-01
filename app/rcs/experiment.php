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
\Recapo\Model\Result::updateEndDatetimeByID($experiment['resultID']);

if (isset($experiment['resultTaskID'])) {
    $currentTask = \Recapo\Model\ResultTask::selectTaskByID($experiment['resultTaskID'], $project['ID']);
    \Recapo\Model\ResultTask::updateResultTaskByID($experiment['resultTaskID']);
    $showTask = false;
} else {
    //nächsten Task auslesen
    $currentTask = \Recapo\Model\Task::selectNextTaskByProjectID($experiment['resultID'], $project['ID']);
    if ($currentTask == null) {
        unset($_SESSION[$project['url']]);
        $_app->redirect($_app->urlFor('/end', array('PROJECTURL' => $project['url'])));
        exit();
    }
    $currentResultTaskID = \Recapo\Model\ResultTask::insertResultTask($experiment['resultID'], $currentTask['ID'], $project['ID']);
    $experiment['resultTaskID'] = $currentResultTaskID;

    $showTask = true;
    $_view->set('showTask', $showTask);
}
$_view->set('currentTask', $currentTask);

$tmp = \Recapo\Model\Section::selectSectionsActiveByProjectIDForNestedSet($project['ID']);
// prepare
$sections = array();
foreach ($tmp as $section) {
    $sections[$section['ID']] = array();
}

if (isset($_params['ID']) && \Recapo\Model\Informationarchitecture::issetItemByID($project['ID'], $_params['ID']) != null) {
    $pID = $_params['ID'];
} else {
    $pID = array_pop_column(\Recapo\Model\Informationarchitecture::selectRootItemByProjectID($project['ID']), 'ID');
}
if (isset($_params['ITEMID']) && \Recapo\Model\Informationarchitecture::issetItemByID($project['ID'], $_params['ID']) != null) {
    $pITEMID = $_params['ITEMID'];
} else {
    $pITEMID = array_pop_column(\Recapo\Model\Informationarchitecture::selectRootItemByProjectID($project['ID']), 'itemID');
}
if ($showTask == false) {
    // log visit
  \Recapo\Model\ResultData::insertResultData($experiment['resultTaskID'], $pITEMID, $pID, $project['ID']);
}

$currentNode = \Recapo\Model\Informationarchitecture::selectItemAndLevelByItemID($pID);
$item = \Recapo\Model\Item::selectItemByIDAndProjectID($pITEMID, $project['ID']);
$currentNode['name'] = $item['name'];
$currentNode['itemID'] = $item['ID'];
$currentNodeParents = \Recapo\Model\Informationarchitecture::selectParentByItemID($pID);

$_view->set('currentNode', $currentNode);
$_view->set('currentNodeParents', $currentNodeParents);
$currentNodeID = $currentNode['ID'];

$subtree = \Recapo\Model\Informationarchitecture::selectSubTreeByItemID($currentNodeID);
$containerCache = array();

foreach ($subtree as $node) {
    if ($node['containerID'] == 0) { // the node is an item
    if (isset($sections[$node['sectionID']])) {// is the sectionID an active section? else ignore it
      $sections[$node['sectionID']][] = $node;
    }
    } else { // the node is a container
    if (!isset($containerCache[$node['containerID']])) { // hit cache?
      $containerCache[$node['containerID']] = \Recapo\Model\Item::selectItemsByContainerID($node['containerID']);
    }
        foreach ($containerCache[$node['containerID']] as $item) {
            if (isset($sections[$node['sectionID']])) { // is the sectionID an active section? else ignore it
        $item['ITEMID'] = $item['ID'];
                $item['ID'] = $node['ID'];
                $sections[$node['sectionID']][] = $item; // add the item of the container to the section
            }
        }
    }
}
$loremipsumText = array(
  0 => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr,  sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr,  sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr,  sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.',
  1 => 'Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.',
  2 => 'Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.',
  3 => 'Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.',
  4 => 'Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis.',
  5 => 'At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr,  sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr,  At accusam aliquyam diam diam dolore dolores duo eirmod eos erat, et nonumy sed tempor et et invidunt justo labore Stet clita ea et gubergren, kasd magna no rebum. sanctus sea sed takimata ut vero voluptua.',
);
$loremipsum = '';
if (isset($sections[2])) {
    foreach ($sections[2] as $item) {
        $text = &$loremipsumText[rand(0, count($loremipsumText)-1)];
        $cut = rand(0, strlen($text));
        $buttonSizeType = array('btn-lg', 'btn-sm', 'btn-xs', '');
        $buttonSize = $buttonSizeType[rand(0, count($buttonSizeType)-1)];

        if ($item['linkToInformationarchitectureID'] == null) {
            $loremipsum .= substr($text, 0, $cut).' <a href="'.$_app->urlFor('/experiment', array('PROJECTURL' => $project['url'], 'ID' => $item['ID'], 'ITEMID' => $item['ITEMID'])).'" class="btn '.$buttonSize.' btn-default" type="button">'.htmlentities($item['name']).'</a> '.substr($text, $cut);
        } else {
            $loremipsum .= substr($text, 0, $cut).' <a href="'.$_app->urlFor('/experiment', array('PROJECTURL' => $project['url'], 'ID' => $item['linkToInformationarchitectureID'], 'ITEMID' => $item['linkToItemID'])).'" class="btn '.$buttonSize.' btn-default" type="button">'.htmlentities($item['name']).'</a> '.substr($text, $cut);
        }
    }
}

$_view->set('loremipsum', $loremipsum);
$_view->set('sections', $sections);
$_view->set('subtree', $subtree);
$_app->render($_route['tpl'][$_this]);
