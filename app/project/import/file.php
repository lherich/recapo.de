<?php
if ($_SERVER['SCRIPT_FILENAME'] == __FILE__) {
    exit(403);
}

$login = $_app->__get('login');
$_project = \Recapo\Model\Project::getProject($login->info['userID'], $_params['ID']);
if ($_project == false) {
    $_app->halt(500, 'Dieses Projekt ist nicht vorhanden oder es besteht keine Berechtigung.');
    exit();
}
$_project = $_project[0];

$_callback = function ($pFile, $pFileName) use ($_app, $_params, $login) {
    $projectID = $_params['ID'];
    $name = $_params['NAME'];

    switch ($name) {

        case 'container':
            $data = $pFile;

            if (trim($data) == '') {
                throw new Exception('Leere Datei');
            }

            $data = utf8_encode($data);
            $data = explode("\n", $data);
            $data = array_map(
                function ($p) {
                    return str_getcsv($p, ';');
                },
                $data
            );

            if (!isset($data[0][0])) {
                throw new Exception('Leere Datei');
            }
            $i = 0;
            foreach ($data as $item) {
                if (isset($item[0]) && trim($item[0])) {
                    // does the container exists?
                    $containerID = \Recapo\Model\Container::issetContainer($projectID, $item[0]);
                    if ($containerID == null) {
                        // create new container
                        $containerID = \Recapo\Model\Container::insertContainer($projectID, $item[0]);
                    } else {
                        // remove all items from containerMapItem
                        \Recapo\Model\ContainerMapItem::deleteItemsByContainerID($containerID);
                    }
                    $i++;

                    $itemCount = count($item);
                    for ($j = 1; $j<$itemCount; $j++) {
                        // isnt empty?
                        if (trim($item[$j])) {
                            // does this item exist?
                            $itemID = \Recapo\Model\Item::issetItem($projectID, $item[$j]);
                            if ($itemID == null) {
                                // create new item if it does not exist
                                $itemID = \Recapo\Model\Item::insertItemByProjectID($projectID, $item[$j]);
                            }

                            // add item to ContainerMapItem
                            \Recapo\Model\ContainerMapItem::addItem($containerID, $itemID);
                        }
                    }
                }
            }
            print $i.' Container importiert';
      break;

      case 'task':
      $data = $pFile;

      if (trim($data) == '') {
          throw new Exception('Leere Datei');
      }

      $data = utf8_encode($data);
      $data = explode("\n", $data);
      $data = array_map(function ($p) {
          return str_getcsv($p, ';');
      }, $data);

      if (!isset($data[0][0])) {
          throw new Exception('Leere Datei');
      }
      $i = 0;
      foreach ($data as $item) {
          if (isset($item[0]) && trim($item[0])) {
              if (isset($item[1])) {
                  $itemID = \Recapo\Model\Item::issetItem($projectID, $item[1]);
                  if ($itemID == null) {
                      $itemID = Recapo\Model\Item::insertItemByProjectID($projectID, $item[1]);
                  }
              } else {
                  $itemID = null;
              }

              $taskID = \Recapo\Model\Task::issetTask($projectID, $item[0]);
              if ($taskID == null) {
                  // new task
            \Recapo\Model\Task::insertTask($projectID, $item[0], $itemID);
              } else {
                  // update task
            \Recapo\Model\Task::updateItemIDByID($projectID, $taskID, $itemID);
              }
              $i++;
          }
      }
      print $i.' Aufgaben importiert';
    break;

    case 'section':
      $data = str_getcsv($pFile, ';');
      $i = \Recapo\Model\Section::importSectionsByProjectID($projectID, $data);
      print $i.' Sektionen importiert';
    break;

    case 'item':
      $data = str_getcsv($pFile, ';');
      $i = \Recapo\Model\Item::importItemsByProjectID($projectID, $data);
      print $i.' Items importiert';
    break;

    case 'ia':

/*
 * start
 */

$data = $pFile;
$stats = array('link' => 0, 'id' => 0, 'i' => 0, 'newi' => 0, 'c' => 0, 'newc' => 0);

try {
    $_db = \Slim\Slim::getInstance()->container['db'];
    $_db->beginTransaction();

    if (trim($data) == '') {
        throw new Exception('Leere Datei');
    }

    $data = utf8_encode($data);
    $data = explode("\n", $data);
    $data = array_map(function ($p) {
        return str_getcsv($p, ';');
    }, $data);

    if (!isset($data[0][0])) {
        throw new Exception('Leere Datei');
    }

  // Anzahl der Zeilen und Spalten ermitteln
  $rows = count($data);
    $cols = count($data[0]);

  // get Sections
  $sections = \Recapo\Model\Section::selectSectionsByProjectID($projectID);
    if (count($sections) < 1) {
        throw new Exception('Keine aktiven Sektionen verfügbar');
    }
    $sectionsRegEx = '';
    $sectionsShortcuts = array();
    foreach ($sections as &$section) {
        $sectionsRegEx .= preg_quote($section['shortcut'], '/').'|';
        $sectionsShortcuts[$section['shortcut']] = &$section['ID'];
    }
    $sectionsRegEx = '('.substr($sectionsRegEx, 0, -1).'|)';

  // IA löschen
  \Recapo\Model\Informationarchitecture::deleteInformationarchitectureByProjectID($projectID);

    $rootItem = \Recapo\Model\Informationarchitecture::selectRootItemByProjectID($projectID);
    $rootInformationarchitectureID = $rootItem['ID'];
  //$rootInformationarchitectureID = \Recapo\Model\Informationarchitecture::insertRootItemByProjectID($projectID, $rootItem['ID'], 0, $rootItem['flag']);

  $_parentLevel = 0;
    $parentStack = array();
    array_push($parentStack, $rootInformationarchitectureID);

    $_stackIDs = array();
    $_stackLinks = array();

    for ($i = 0; $i<$rows; $i++) {
        for ($j = 0; $j<$cols; $j++) {
            if (isset($data[$i][$j]) && !empty($data[$i][$j])) {
                do {
                    $_parentID = array_pop($parentStack);
                    $_parentLevel--;
                } while ($_parentLevel >= $j);

        // reset last parent
        array_push($parentStack, $_parentID);
                $_parentLevel++;

                $matches = array();

                if (preg_match('/'.$sectionsRegEx.'(((link|id|i|newi|c|newc|)\((.+)\))|(.+))/i', $data[$i][$j], $matches) != 1) {
                    throw new Exception('Das Element "'.$data[$i][$j].' '.print_r($sectionsRegEx, true).'" ist nicht valid.');
                }

                $_flag = 'item';
                if ($matches[1] == '') {
                    $matches[1] = $sections[0]['shortcut'];
                } // if no section is given, take the first section

        if (!isset($sectionsShortcuts[$matches[1]])) {
            throw new Exception('Die Sektion mit der Abkürzung "'.$matches[1].'" existiert nicht.');
        }
                $_sectionID = $sectionsShortcuts[$matches[1]];

        // default function is i()
        if (empty($matches[4])) {
            $function = 'i';
            $_name = &$matches[6];
        } else {
            $function = &$matches[4];
            $_name = &$matches[5];
        }

                $_linkID = null;

                switch ($function) {
          // force new container

          case 'id':
            // filter ID and name
            $tmp = explode(':', $_name, 2);
            if (!isset($tmp[0], $tmp[1])) {
                throw new Exception('Der Parameter '.$_name.' der Funktion id() ist falsch formatiert.');
            }

            $_linkID = $tmp[0];
            $_name = $tmp[1];

            // check if item already exists
            $_ID = \Recapo\Model\Item::issetItem($projectID, $_name);
            if ($_ID == null) {
                // otherwise create a new item
              $_ID = \Recapo\Model\Item::insertItem($projectID, $_name);
            }
            $stats['i']++;
          break;

          case 'link':
            // filter ID and name
            $tmp = explode(':', $_name, 2);

            if (!isset($tmp[0], $tmp[1])) {
                throw new Exception('Der Parameter '.$_name.' der Funktion link() ist falsch formatiert.');
            }

            $_linkID = $tmp[0];
            $_name = $tmp[1];

            // create a new item
            $_ID = \Recapo\Model\Item::addItemToProjectID($projectID, $_name, 'link');
          break;

          case 'newc':
            $_flag = 'container';
            $_ID = \Recapo\Model\Container::insertContainer($projectID, $_name);
            $stats['newc']++;
          break;

          // container
          case 'c':
            // container
            $_flag = 'container';
            // check if container already exists
            $_ID = \Recapo\Model\Container::issetContainer($projectID, $_name);
            if ($_ID == null) {
                // otherwise create a new container
              $_ID = \Recapo\Model\Container::insertContainer($projectID, $_name);
            }
            $stats['c']++;
          break;

          // force new item
          case 'newi':
            // create new item
            $_ID = \Recapo\Model\Item::insertItem($projectID, $_name);
            $stats['newi']++;
          break;

          // item
          default:
            // check if item already exists
            $_ID = \Recapo\Model\Item::issetItem($projectID, $_name);
            if ($_ID == null) {
                // otherwise create a new item
              $_ID = \Recapo\Model\Item::insertItem($projectID, $_name);
            }
            $stats['i']++;
          break;
        }
                $itemInformationarchitectureID = \Recapo\Model\Informationarchitecture::insertItemByParentID($_parentID, $projectID, $_ID, $_sectionID, $_flag);
                if ($_linkID !== null) {
                    // store
          if ($function == 'id') {
              $_stackIDs[$_linkID] = array('linkID' => $_linkID, 'name' => $_name, 'itemID' => $_ID, 'informationarchitectureID' => $itemInformationarchitectureID);
          } else {
              $_stackLinks[] = array('linkID' => $_linkID, 'name' => $_name, 'itemID' => $_ID, 'informationarchitectureID' => $itemInformationarchitectureID);
          }
                }

        // Debug
        /*
        print $data[$i][$j]."\n";
        print 'VaterID: '.$_parentID."\n";
        print 'ParentLevel: '.$_parentLevel."\n";
        print "\n";
        */

        // has children?
        if (isset($data[$i][$j+1]) && !empty($data[$i][$j+1])) {
            array_push($parentStack, $itemInformationarchitectureID);
            $_parentLevel++;
        }
            }
        }
    }

  // set links
  foreach ($_stackLinks as $item) {
      if (!isset($_stackIDs[$item['linkID']])) {
          throw new Exception('Das Linkziel mit der ID '.$item['linkID'].' existiert nicht.');
      }

      \Recapo\Model\Informationarchitecture::updateLinkByID($item['informationarchitectureID'], $_stackIDs[$item['linkID']]['itemID'], $_stackIDs[$item['linkID']]['informationarchitectureID']);
  }

    $_db->commit();
} catch (Exception $e) {
    $_db->rollBack();
    $_app->halt(500, htmlentities($e->getMessage()));
}

print 'Informationsarchitektur erfolgreich importiert.';

/*
 * end
 */

    break;

  }
};

if (isset($_FILES['file']) && trim($_FILES['file']['name'])) {
    try {
        require 'Upload/Autoloader.php';
        \Upload\Autoloader::register();

        $storage = new \Upload\Storage\Callback($_callback);
        $file = new \Upload\File('file', $storage);

    // Validate file upload
    // MimeType List => http://www.webmaster-toolkit.com/mime-types.shtml
    $file->addValidations(array(
        new \Upload\Validation\Size('5M'),
        //new \Upload\Validation\Mimetype(array('application/excel', 'text/plain', 'text/csv', 'application/vnd.ms-excel')),
        new \Upload\Validation\Extension(array('csv')),
    ));
        $file->upload();
    } catch (RuntimeException $e) {
        $_app->halt(500, htmlentities($e->getMessage()));
    }
} else {
    $_app->halt('500', 'Keine Datei hochgeladen');
}

/*
$login = $_app->__get('login');
$_project = \Recapo\Model\Project::getProject($login->info['userID'], $_params['ID']);
if($_project == FALSE) {
  $_app->flash('warning', 'Dieses Projekt ist nicht vorhanden oder es besteht keine Berechtigung.');
  $_app->redirect($_app->urlFor('/projects'));
}
$_project = $_project[0];
$_view->set('project', $_project);

$_view->set('projectContainers', \Recapo\Model\Container::getContainerByProjectID($_project['ID']));
$_view->set('projectItems', \Recapo\Model\Item::getItemsByProjectID($_project['ID']));


$_app->render($_route['tpl'][$_this]);*/;
