<?php
/**
 * Recapo <http://recapo.de>
 *
 * @link      http://github.com/lherich/recapo.de
 * @copyright Copyright (c) 2014 Lucas Herich <info@recapo.de>
 * @license   MIT License <http://recapo.de/license>
 */

/**
 * Collection of functions
 */


/**
 * Upload
 *
 * @author      rommel at rommelsantor dot com
 * @link        http://php.net/filesize#106569
 *
 */
function human_filesize($bytes, $decimals = 2)
{
    $sz = 'BKMGTP';
    $factor = floor((strlen($bytes) - 1) / 3);

    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)).@$sz[$factor];
}

/**
 * recursive decode utf-8 in an array
 *
 * @author      chris at deliens dot be
 * @link        http://php.net/utf8_decode#108339
 *
 */
function utf8DecodeArray($array)
{
    $utf8DecodedArray = array();
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $utf8DecodedArray[$key] = utf8DecodeArray($value);
            continue;
        }
        $utf8DecodedArray[$key] = utf8_decode($value);
    }

    return $utf8DecodedArray;
}

/**
 * Import
 *
 * @author      Kelvin J
 * @link        http://php.net/in-array#89256
 *
 */
function in_arrayi($needle, $haystack)
{
    return in_array(strtolower($needle), array_map('strtolower', $haystack));
}

/**
 * Import
 *
 * @author      contato at williamsantana dot com dot br ¶
 * @link        http://php.net/array_map#112857
 *
 */
function array_map_recursive($callback, $array)
{
    foreach ($array as $key => $value) {
        if (is_array($array[$key])) {
            $array[$key] = array_map_recursive($callback, $array[$key]);
        } else {
            $array[$key] = call_user_func($callback, $array[$key]);
        }
    }

    return $array;
}

/**
 * array_pop_column
 * Similiar to array_column(), but the function only returns one column from an one-dimensional array.
 *
 * @author      lherich
 *
 */
function array_pop_column($pArray, $pColumn)
{
    if (!is_array($pArray)) {
        throw new Exception('First parameter should be an array.');
    }
    if (!isset($pArray[$pColumn])) {
        throw new Exception('Column does not exists in array.');
    }
    $pColumn = $pArray[$pColumn];

    return $pColumn;
}
