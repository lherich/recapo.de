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

if (isset($_params['CONFIRMED'])) {
    $targetItem = \Recapo\Model\Informationarchitecture::selectItemByID($_params['ITEMID']);
    $targetItemDetails = \Recapo\Model\Item::selectItemByID($targetItem['itemID']);
    if ($targetItem['flag'] == 'container' || $targetItemDetails['flag'] == 'link') {
        $_app->flash('danger', 'Container oder Links können nicht verlinkt werden.');
        $_app->redirect($_app->urlFor('/project/informationarchitecture', array('ID' => $_params['ID'])));
    }

    $itemID = \Recapo\Model\Item::addItemToProjectID($_project['ID'], $_app->request()->get('linkname'), 'link');
    $itemInformationarchitectureID = \Recapo\Model\Informationarchitecture::insertItemByLFT($targetItem['LFT'], $_project['ID'], $itemID, $targetItem['sectionID'], $targetItem['flag']);
    $result = \Recapo\Model\Informationarchitecture::updateLinkByID($itemInformationarchitectureID, $targetItem['itemID'], $targetItem['ID']);
    if ($result) {
        $_app->flash('success', 'Der Link wurde erfolgreich angelegt.');
        $_app->redirect($_app->urlFor('/project/informationarchitecture', array('ID' => $_params['ID'])));
    } else {
        $_app->flash('danger', 'Der Link konnte nicht erstellt werden.');
        $_app->redirect($_app->urlFor('/project/informationarchitecture', array('ID' => $_params['ID'])));
    }
} else {
    $_view->set('project', $_project);
    $item = \Recapo\Model\Informationarchitecture::selectItemByID($_params['ITEMID']);
    $_view->set('item', $item);
    if ($item['flag'] == 'container') {
        $_app->flash('danger', 'Container können nicht verlinkt werden.');
        $_app->redirect($_app->urlFor('/project/informationarchitecture', array('ID' => $_params['ID'])));
    } else {
        $_view->set('itemInformation', \Recapo\Model\Item::selectItemByID($item['itemID']));
    }
    $_app->render($_route['tpl'][$_this]);
}
