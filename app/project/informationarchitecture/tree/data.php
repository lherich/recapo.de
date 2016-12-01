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

$data = \Recapo\Model\Informationarchitecture::selectNestedSetByProjectIDForData($_project['ID']);
$sections = array();
foreach (\Recapo\Model\Section::selectSectionsActiveByProjectIDForNestedSet($_project['ID']) as $item) {
    $item['children'] = array();
    $sections[$item['ID']] = $item;
}

$data = \Recapo\Helper\Format::nestedsetToArray($data);

\Recapo\Helper\Helper::$array = $sections;

function addSection($current)
{
    $children = $current['children'];
    $current['children'] = \Recapo\Helper\Helper::$array;
    foreach ($children as $child) {
        if (isset(\Recapo\Helper\Helper::$array[$child['sectionID']])) {
            $current['children'][$child['sectionID']]['children'][] = addSection($child);
        }
    }
    $current['children'] = array_values($current['children']);

    return $current;
}

$return = \Recapo\Helper\Helper::$array;
foreach ($data as $item) {
    if (isset($sections[$item['sectionID']])) {
        $return[$item['sectionID']]['children'][] = addSection($item);
    }
}
$return = array_values($return);

$_app->response->headers->set('Content-Type', 'application/json');
print json_encode($return);
//print_r($return);


/*
$sections = \Recapo\Model\Section::selectSectionsActiveByProjectIDForNestedSet($_project['ID']);
foreach($sections AS $section) {
  $section['children'] = array();
}
*
$data = \Recapo\Helper\Format::nestedsetToArray($data);

function addSection ($data) {
  global $sections;
  if(is_array($data)) {
    foreach($data AS $item) {
      //print $item['key'];
      $item = addSection($item);
    }
    return $data;
  } else {
    print_r($data);
    //$data['children'] = $sections;
    return $data;
  }



  $children = $item['children'];
  $item['children'] = $sections;

  foreach($children AS $child) {
    $item['children'][$child['sectionID']]['children'] = $child;
  }
}

$data = addSection($data);*/
/*

$countData = count($data);
$jsonData = array();

for($i=0; $i < $countData; $i++) {
  $self = &$data[$i];
  if($i == 0) {
    // first item
    print 'root';
    $jsonData[$i] = &$self;
    $parent = &$self;
  } elseif($i == $countData - 1) {
    // last item
    print 'last';
  } else {
    $next = &$data[$i+1];
    // every other item
    print 'default';
    if($self['LFT'] > $next['LFT'] && $self['RGT'] < $next['RGT']) {
      // parent

    } elseif($self['LFT'] > $next['LFT'] && $self['RGT'] > $next['RGT']) {
      // linker nachbar

    } elseif($self['LFT'] < $next['LFT'] && $self['RGT'] < $next['RGT']) {
      // rechter nachbar

    } elseif($self['LFT'] < $next['LFT'] && $self['RGT'] > $next['RGT']) {
      // children

    } else {
      exit('Korruptes Nested Set');
    }
  }
}



String 	title 	node text (may contain HTML tags)
String 	key 	unique key for this node (auto-generated if omitted)
String 	refKey 	(reserved)
Boolean 	expanded
Boolean 	active 	(initialization only, but will not be stored with the node).
Boolean 	focus 	(initialization only, but will not be stored with the node).
Boolean 	folder
Boolean 	hideCheckbox
Boolean 	lazy
Boolean 	selected
Boolean 	unselectable
NodeData[] 	children 	optional array of child nodes
String 	tooltip
String 	extraClasses 	class names added to the node markup (separate with space)
object 	data 	all properties from will be copied to `node.data`
any 	OTHER 	attributes other than listed above will be copied to `node.data`




if self.LFT > next.LFT AND self.RGT < next.RGT
  parent

if self.LFT > next.LFT AND self.RGT > next.RGT
  linker nachbar

if self.LFT < next.LFT AND self.RGT < next.RGT
  rechter nachbar

if self.LFT < next.LFT AND self.RGT > next.RGT
  children







*/;
