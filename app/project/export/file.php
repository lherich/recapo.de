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

$out = fopen('php://output', 'w');
switch ($_params['NAME']) {
    case 'item':
        $items = \Recapo\Model\Item::selectItemsNameByProjectID($_project['ID']);
        $csv = array();
        foreach ($items as $item) {
            $csv[] = $item['name'];
        }
        fputcsv($out, utf8DecodeArray($csv), ';');
    break;

    case 'container':
        $items = \Recapo\Model\Container::selectContainerAndItemsByProjectID($_project['ID']);
        //var_dump($items);exit();
        $container = array();
        foreach ($items as $item) {
            if (!isset($container[$item['containerID']][0])) {
                $container[$item['containerID']] = array($item['containerName']);
            }
            $container[$item['containerID']][] = $item['name'];
        }
        foreach ($container as $containerItems) {
            fputcsv($out, utf8DecodeArray($containerItems), ';');
        }
    break;

    case 'section':
        $items = \Recapo\Model\Section::selectSectionNameByProjectID($_project['ID']);
        $csv = array();
        foreach ($items as $item) {
            $csv[] = $item['name'];
        }
        fputcsv($out, utf8DecodeArray($csv), ';');
      break;

      case 'result':
        $items = \Recapo\Model\Result::selectResultExportByProjectID($_project['ID']);
        foreach ($items as $item) {
            fputcsv($out, utf8DecodeArray($item), ';');
        }
    break;

    case 'task':
        $items = \Recapo\Model\Task::selectTaskNameAndItemByProjectID($_project['ID']);
        foreach ($items as $item) {
            fputcsv($out, utf8DecodeArray($item), ';');
        }
    break;

    case 'proband':
        $items = \Recapo\Model\Proband::selectProbandsExportByProjectID($_project['ID']);
        foreach ($items as $item) {
            fputcsv($out, utf8DecodeArray($item), ';');
        }
      break;

      case 'ia':
        $data = \Recapo\Model\Informationarchitecture::selectNestedSetForExportByProjectID($_project['ID']);

        $_stackLinks = array();
        foreach ($data as &$item) {
            if ($item['itemFlag'] == 'link') {
                $stackLinks[$item['linkToInformationarchitectureID']] = true;
                $item['title'] = $item['shortcut'].'link('.$item['linkToInformationarchitectureID'].':'.$item['name'].')';
            } elseif ($item['flag'] == 'container') {
                $item['title'] = $item['shortcut'].'c('.$item['name'].')';
            } else {
                if ($item['duplicated'] == 0) {
                    $item['title'] = $item['shortcut'].$item['name'];
                } else {
                    $item['title'] = $item['shortcut'].'newi('.$item['name'].')';
                }
            }
        }

        foreach ($data as &$item) {
            if (isset($stackLinks[$item['informationarchitectureID']])) {
                $item['title'] = $item['shortcut'].'id('.$item['informationarchitectureID'].':'.$item['name'].')';
            }
        }

        $data = \Recapo\Helper\Format::nestedsetToArray($data);
        $data = \Recapo\Helper\Format::nestedSetArrayToCsv($data);
        foreach ($data as $row) {
            fputcsv($out, utf8DecodeArray($row), ';');
        }
    break;

    case 'ia_extended':
        $data = \Recapo\Model\Informationarchitecture::selectNestedSetForExtendedExportByProjectID($_project['ID']);

        $data = \Recapo\Helper\Format::nestedsetToArray($data);
        $data = \Recapo\Helper\Format::nestedSetArrayToCsv($data);
        foreach ($data as $row) {
            fputcsv($out, utf8DecodeArray($row), ';');
        }
    break;

    default:
        $_app->flash('warning', 'Dieser Exporttyp ist nicht bekannt.');
        $_app->redirect($_app->urlFor('/project', array('ID' => $_project['ID'])));
        exit();
    break;
}

fclose($out);
$_app->response->headers->set('Content-Type', 'text/csv');
$_app->response->headers->set('Content-Disposition', 'attachment;filename='.date('Y-m-d').'_-_'.$_project['url'].'_-_'.$_params['NAME'].'.csv');
