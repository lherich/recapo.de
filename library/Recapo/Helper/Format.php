<?php
/**
 * Recapo <http://recapo.de>
 *
 * @link      http://github.com/lherich/recapo
 * @copyright Copyright (c) 2014 Lucas Herich <info@recapo.de>
 * @license   MIT License <http://recapo.de/license>
 */

namespace Recapo\Helper;

/**
 * Encapsulate some converting functions
 */
class Format
{
    public static function nestedsetToArray($pData)
    {
        $stack = array();
        $arraySet = array();

        foreach ($pData as $item) {
            $stackSize = count($stack); //how many opened tags?
            while ($stackSize > 0 && $stack[$stackSize-1]['RGT'] < $item['LFT']) {
                array_pop($stack); //close sibling and his childrens
                $stackSize--;
            }

            $link = & $arraySet;
            for ($i = 0; $i<$stackSize; $i++) {
                $link = & $link[$stack[$i]['index']]['children']; //navigate to the proper children array
            }
            $tmp = array_push($link, $item + array('children' => array()));
            array_push($stack, array('index' => $tmp-1, 'RGT' => $item['RGT']));
        }

        return $arraySet;
    }

    public static function intendtionChildren($pItems, &$pResultArray, &$pLevelV, &$pLevelH)
    {
        if (isset($pItems[0])) {
            $pLevelH++; // increase indention
            $pushArray = array(); // wildcard array for indention
            for ($i = 0; $i<$pLevelH; $i++) {
                array_push($pushArray, '');
            }

            $countItems = count($pItems);
            for ($j = 0; $j<$countItems; $j++) {
                if ($j == 0) {
                    // add first child in the same row
                    $pResultArray[$pLevelV][] = $pItems[$j]['title'];
                } else {
                    // create new row with indention
                    $pLevelV++;
                    $pResultArray[$pLevelV] = $pushArray;
                    $pResultArray[$pLevelV][] = $pItems[$j]['title'];
                }
                // recurisve
                self::intendtionChildren($pItems[$j]['children'], $pResultArray, $pLevelV, $pLevelH);
            }
        }
    }

    public static function nestedSetArrayToCsv($pData)
    {
        $levelV = 0;
        $levelH = 0;
        $resultArray = array();
        foreach ($pData as $item) {
            $resultArray[$levelV] = array($item['title']);
            self::intendtionChildren($item['children'], $resultArray, $levelV, $levelH);
            $levelV++;
        }

        return $resultArray;
  /*
  var_dump($pData);
    $return = array();
    $levelh = 0; // horizontal
    $levelv = 0; // Vertikal

    $countData = count($pData);
    for($i=0; $i<$countData; $i++) {
      $return[$levelh] = array($pData[$i]['title']);

      returnChildren($pData[$i]['children']);

      if(isset($pData[$i]['children'][0])) {
        $return[$levelh][] = $pData[$i]['children'][0]['title'];
        if(isset($pData[$i]['children'][0]['title']['children'][0]))

        if(isset($pData[$i]['children'][1])) {
          $levelv++;
          // $return[$levelh]
          $pushArray = array();
          for($l=0; $l<$levelv; $l++) {
            $pushArray[] = '';
          }
          $countChildren = count($pData[$i]['children']);
          for($j=1;$j<$countChildren; $j++) {
            $levelh++;
            $return[$levelh] = $pushArray;
            $return[$levelh][] = $pData[$i]['children'][$j]['title'];
          }
          $levelv--;
        }
      }
      $levelh++;
    }
    return $return;
    */
    }

    public static function replaceContainerWithCards($pArray, &$pContainer)
    {
        $result = array();
        $informationArray = array('linkToInformationarchitectureID' => null, 'linkToItemID' => null, 'flag' => 'container');
        foreach ($pArray as $item) {
            if (isset($item['children'])) {
                $item['children'] = self::replaceContainerWithCards($item['children'], $pContainer);
            } else {
                $item['children'] = array();
            }

            if ($item['flag'] == 'container') {
                // current item is a container

                if (isset($pContainer[$item['containerID']])) {
                    // container exists
                    foreach ($pContainer[$item['containerID']] as $containerItem) {
                        // add every card within the container and append the container children and the ID from the informationarchitecture
                        $result[] = $containerItem + $informationArray + array('ID' => $item['ID'], 'children' => $item['children']); // append children and ID
                    }
                }
            } else {
                // current item is an item
                $result[] = $item;
            }
        }

        return $result;
    }
}
