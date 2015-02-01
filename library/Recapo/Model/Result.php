<?php
/**
 * Recapo <http://recapo.de>
 *
 * @link      http://github.com/lherich/recapo.de
 * @copyright Copyright (c) 2014 Lucas Herich <info@recapo.de>
 * @license   MIT License <http://recapo.de/license>
 */

namespace Recapo\Model;

/**
 *
 */
class Result extends \Recapo\Model\Model
{
    static protected $_sql = array(
        'selectResultsByProjectID' => '
            SELECT result.*, TIMEDIFF(result.endDatetime, result.startDatetime) AS duration 
            FROM result 
            WHERE result.projectID = :pProjectID',
        'selectResultExportByProjectID' => '
            SELECT 
                result.ID, result.startDatetime, result.endDatetime, 
                TIMEDIFF(result.endDatetime, result.startDatetime) AS duration, 
                result.comment FROM result 
            WHERE result.projectID = :pProjectID',
        'deleteResultByProjectID' => '
            DELETE FROM result 
            WHERE result.ID = :pID AND result.projectID = :pProjectID',
        'deleteResultByID' => '
            DELETE FROM result 
            WHERE result.ID = :pID',
        'selectResultByID' => '
            SELECT * FROM result
            WHERE result.ID = :pID LIMIT 1',
        'selectItemByID' => '
            SELECT * 
            FROM result 
            WHERE result.ID = :pID AND result.projectID = :pProjectID 
            LIMIT 1',
        'insertResult' => '
            INSERT INTO result (projectID, startDatetime, endDatetime) 
            VALUES (:pProjectID, NOW(6), NOW(6))',
        'updateEndDatetimeByID' => '
            UPDATE result 
            SET result.endDatetime = NOW(6) 
            WHERE result.ID = :pID',
        'issetResultByID' => '
            SELECT * 
            FROM result 
            WHERE result.ID = :pID AND result.projectID = :pProjectID 
            LIMIT 1',
        'getLastFiveResultsByProjectID' => '
            SELECT 
                result.*, 
                TIMEDIFF(result.endDatetime, result.startDatetime) AS duration 
            FROM result 
            WHERE result.projectID = :pProjectID 
            ORDER BY result.endDatetime DESC 
            LIMIT 5',
    );

    public static function selectItemByID($pProjectID, $pID)
    {
        $item = self::__selectOne(__FUNCTION__, array('pProjectID' => $pProjectID, 'pID' => $pID));
        if ($item == null) {
            return;
        }

        return $item;
    }

    public static function getLastFiveResultsByProjectID($pProjectID)
    {
        return self::__selectAll(__FUNCTION__, array('pProjectID' => $pProjectID));
    }

    public static function selectResultExportByProjectID($pProjectID)
    {
        return self::__selectAll(__FUNCTION__, array('pProjectID' => $pProjectID));
    }

    public static function getResultsByProjectID($pProjectID)
    {
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(self::$_sql['selectResultsByProjectID']);
        $sth->execute(array('pProjectID' => $pProjectID));

        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function issetResultByID($pProjectID, $pID)
    {
        $item = self::__selectOne(__FUNCTION__, array('pProjectID' => $pProjectID, 'pID' => $pID));
        if ($item == null) {
            return;
        }

        return $item['ID'];
    }

    public static function deleteResultByID($pID)
    {
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(self::$_sql[__FUNCTION__]);

        return $sth->execute(array('pID' => $pID));
    }

    public static function insertResult($pProjectID)
    {
        return self::__insertOne(__FUNCTION__, array('pProjectID' => $pProjectID));
    }
    public static function updateEndDatetimeByID($pID)
    {
        return self::__update(__FUNCTION__, array('pID' => $pID));
    }

    public static function selectResultByID($pID)
    {
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(self::$_sql[__FUNCTION__]);
        $sth->execute(array('pID' => $pID));
        $item = $sth->fetchAll(\PDO::FETCH_ASSOC);
        if (isset($item[0])) {
            return $item[0];
        } else {
            return;
        }
    }
}
