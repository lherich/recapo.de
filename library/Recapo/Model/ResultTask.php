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
class ResultTask extends \Recapo\Model\Model
{
    static protected $_sql = array(
        'insertresulttask' => '
            INSERT INTO resulttask (resultID, taskID, startDatetime, endDatetime, projectID, flag) 
            VALUES (:pResultID, :pTaskID, NOW(6), NOW(6), :pProjectID, "unknown")',
        'selectresulttasks' => '
            SELECT 
                item.name AS itemName, item.ID AS itemID, resulttask.*,
                TIMEDIFF(resulttask.endDatetime, resulttask.startDatetime) AS duration 
            FROM resulttask 
            LEFT JOIN task ON(task.ID = resulttask.taskID) 
            LEFT JOIN item ON(item.ID = task.itemID) 
            WHERE resulttask.projectID = :pProjectID',
        'selectTaskByID' => '
            SELECT task.* 
            FROM resulttask 
            LEFT JOIN task ON(task.ID = resulttask.taskID) 
            WHERE resulttask.ID = :pID AND resulttask.projectID = :pProjectID
            LIMIT 1',
        'updateresulttaskByID' => '
            UPDATE resulttask 
            SET resulttask.endDatetime = NOW(6), resulttask.flag = :pFlag 
            WHERE resulttask.ID = :pID',
        'issetresulttaskByID' => '
            SELECT resulttask.* 
            FROM result 
            JOIN resulttask ON(resulttask.resultID = result.ID) 
            WHERE result.projectID = :pProjectID AND resulttask.ID = :pID',
        'selectItemByID' => '
            SELECT resulttask.*, task.* 
            FROM result JOIN resulttask ON(resulttask.resultID = result.ID) 
            LEFT JOIN task ON(task.ID = resulttask.taskID) 
            WHERE result.projectID = :pProjectID AND resulttask.ID = :pID 
            LIMIT 1',
    );

    public static function selectItemByID($pProjectID, $pID)
    {
        $item = self::__selectOne(__FUNCTION__, array('pProjectID' => $pProjectID, 'pID' => $pID));
        if ($item == null) {
            return;
        }

        return $item;
    }
    public static function selectresulttasks($pProjectID)
    {
        return self::__selectAll(__FUNCTION__, array('pProjectID' => $pProjectID));
    }

    public static function insertresulttask($pResultID, $pTaskID, $pProjectID)
    {
        return self::__insertOne(__FUNCTION__, array('pResultID' => $pResultID, 'pTaskID' => $pTaskID, 'pProjectID' => $pProjectID));
    }
    public static function updateresulttaskByID($pID, $pFlag = 'unknown')
    {
        return self::__update(__FUNCTION__, array('pID' => $pID, 'pFlag' => $pFlag));
    }
    public static function selectTaskByID($pID, $pProjectID)
    {
        return self::__selectOne(__FUNCTION__, array('pID' => $pID, 'pProjectID' => $pProjectID));
    }
    public static function issetresulttaskByID($pProjectID, $pID)
    {
        $item = self::__selectOne(__FUNCTION__, array('pProjectID' => $pProjectID, 'pID' => $pID));
        if ($item == null) {
            return;
        }

        return $item['ID'];
    }
}
