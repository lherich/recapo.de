<?php
/**
 * Recapo <http://recapo.de>
 *
 * @link      http://github.com/lherich/recapo
 * @copyright Copyright (c) 2014 Lucas Herich <info@recapo.de>
 * @license   MIT License <http://recapo.de/license>
 */

namespace Recapo\Model;

/**
 *
 */
class ResultData extends \Recapo\Model\Model
{
    static protected $_sql = array(
        'insertResultData' => '
            INSERT INTO resultdata (resultTaskID, itemID, informationarchitectureID, projectID, datetime) 
            VALUES (:pResultTaskID, :pItemID, :pInformationarchitectureID, :pProjectID, NOW(6))',
        'selectResultDataByProjectID' => '
            SELECT resultdata.*, item.name AS itemName 
            FROM resultdata 
            LEFT JOIN item ON(item.ID = resultdata.itemID) 
            WHERE resultdata.projectID = :pProjectID 
            ORDER BY resultdata.ID',
    );

    public static function insertResultData($pResultTaskID, $pItemID, $pInformationarchitectureID, $pProjectID)
    {
        return self::__insertOne(__FUNCTION__, array('pResultTaskID' => $pResultTaskID, 'pItemID' => $pItemID, 'pInformationarchitectureID' => $pInformationarchitectureID, 'pProjectID' => $pProjectID));
    }

    public static function selectResultDataByProjectID($pProjectID)
    {
        return self::__selectAll(__FUNCTION__, array('pProjectID' => $pProjectID));
    }
}
