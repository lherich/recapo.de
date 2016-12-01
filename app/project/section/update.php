<?php
if ($_SERVER['SCRIPT_FILENAME'] == __FILE__) {
    exit(403);
}

$post = $_app->request()->post();
$projectID = $_params['ID'];
$userID = $_app->__get('login')->info['userID'];

if (isset($post['section']) && is_array($post['section'])) {
    $storedSections = \Recapo\Model\Section::getSectionsID();
    $storedSections = array_map(
        function ($pArray) {
            if (isset($pArray['ID'])) {
                return $pArray['ID'];
            }
        },
        $storedSections
    );
    $sectionIDs = array();
    foreach ($post['section'] as $index => $value) {
        if (strtolower($value) == 'on' && in_array($index, $storedSections)) {
            $sectionIDs[] = $index;
        }
    }
    $i = \Recapo\Model\Section::insertSectionsByProjectID($projectID, $sectionIDs);
    $_app->flash('success', 'Es wurden '.$i.' Sektionen erfolgreich gespeichert.');
    $_app->redirect($_app->urlFor('/project/section', array('ID' => $projectID)));
} else {
    $_app->flash('danger', 'Konnte Sektionen nicht speichern.');
    $_app->redirect($_app->urlFor('/project/section', array('ID' => $projectID)));
}
