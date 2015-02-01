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
class Project
{
    static protected $_sql = array(
    'nameUpdate' => '
        UPDATE project 
        SET project.name = :pValue 
        WHERE project.ID = :pID AND project.userID = :pUserID',
    'flagUpdate' => '
        UPDATE project 
        SET project.flag = :pValue 
        WHERE project.ID = :pID AND project.userID = :pUserID',
    'startDatetimeUpdate' => '
        UPDATE project 
        SET project.startDatetime = :pValue 
        WHERE project.ID = :pID AND project.userID = :pUserID',
    'endDatetimeUpdate'   => '
        UPDATE project 
        SET project.endDatetime = :pValue 
        WHERE project.ID = :pID AND project.userID = :pUserID',
    'textUpdate'          => '
        UPDATE project 
        SET 
            project.welcomeText = :pWelcomeText, 
            project.instructionText = :pInstructionText, 
            project.tributeText = :pTributeText 
        WHERE project.ID = :pID AND project.userID = :pUserID',
    'deleteProjectByID'   => '
        UPDATE project 
        SET project.flag = "private" 
        WHERE project.ID = :pID',

    'insertProject' => '
        INSERT INTO project (userID, name, url, type, startDatetime, endDatetime, createDatetime, offerButton) 
        VALUES (:pUserID, :pName, :pUrl, "RCS", :pStartDatetime, :pEndDatetime, NOW(), :pOfferButton)',

    'selectCurrent' => '
        SELECT project.* 
        FROM project 
        WHERE 
            project.userID = :pUserID 
            AND (
                (NOW() BETWEEN project.startDatetime AND project.endDatetime)
                OR (project.startDatetime <= NOW() AND project.endDatetime = "0000-00-00 00:00:00")
            ) 
            AND (project.flag = "public") 
            ORDER BY project.endDatetime',
    'selectElapsed' => '
        SELECT project.* 
        FROM project 
        WHERE 
            project.userID = :pUserID 
            AND NOT (
                (
                    (NOW() BETWEEN project.startDatetime AND project.endDatetime) 
                    OR (project.startDatetime <= NOW() AND project.endDatetime = "0000-00-00 00:00:00")
                ) 
                AND (project.flag = "public")
            ) 
            AND project.flag != "private" 
            ORDER BY project.endDatetime ASC',
    'selectAll' => '
        SELECT project.* 
        FROM project 
        WHERE project.userID = :pUserID AND project.flag != "private" 
        ORDER BY project.endDatetime ASC',
    'selectOne' => '
        SELECT 
            project.*,
            IF(
                (NOW() BETWEEN project.startDatetime AND project.endDatetime) 
                OR (project.startDatetime <= NOW() AND project.endDatetime = "0000-00-00 00:00:00"), 
                "active", 
                "inactive"
            ) AS intime
        FROM project 
        WHERE project.userID = :pUserID AND project.ID = :pID 
        LIMIT 1',
    'selectOneByUrl' => '
        SELECT project.* 
        FROM project 
        WHERE 
            (project.url = :pUrl) 
            AND (
                (NOW() BETWEEN project.startDatetime AND project.endDatetime) 
                OR (project.startDatetime <= NOW() AND project.endDatetime = "0000-00-00 00:00:00")
            ) 
            AND (project.flag = "public") 
        LIMIT 1',
    );

    public static function updateField($pUserID, $pID, $pField, $pValue)
    {
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(static::$_sql[$pField.'Update']);

        return $sth->execute(array('pUserID' => $pUserID, 'pID' => $pID, 'pValue' => $pValue));
    }

    public static function updateText($pUserID, $pID, $pWelcomeText, $pInstructionText, $pTributeText)
    {
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(static::$_sql['textUpdate']);

        return $sth->execute(array('pUserID' => $pUserID, 'pID' => $pID, 'pWelcomeText' => $pWelcomeText, 'pInstructionText' => $pInstructionText, 'pTributeText' => $pTributeText));
    }

    public static function deleteProjectByID($pID)
    {
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(static::$_sql[__FUNCTION__]);

        return $sth->execute(array('pID' => $pID));
    }

    public static function insertProject($pUserID, $pName, $pUrl, $pStartDatetime, $pEndDatetime, $pOfferButton)
    {
        $_db = \Slim\Slim::getInstance()->container['db'];
        $sth = $_db->prepare(static::$_sql['insertProject']);
        $sth->execute(array('pUserID' => $pUserID, 'pName' => $pName, 'pUrl' => $pUrl, 'pStartDatetime' => $pStartDatetime, 'pEndDatetime' => $pEndDatetime, 'pOfferButton' => $pOfferButton));

        return $_db->lastInsertId();
    }

    public static function getCurrentProjects($pUserID)
    {
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(self::$_sql['selectCurrent']);
        $sth->execute(array('pUserID' => $pUserID));

        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getElapsedProjects($pUserID)
    {
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(self::$_sql['selectElapsed']);
        $sth->execute(array('pUserID' => $pUserID));

        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getProjects($pUserID)
    {
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(self::$_sql['selectAll']);
        $sth->execute(array('pUserID' => $pUserID));

        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getProject($pUserID, $pID)
    {
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(self::$_sql['selectOne']);
        $sth->execute(array('pUserID' => $pUserID, 'pID' => $pID));

        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getProjectByUrl($pUrl)
    {
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(self::$_sql['selectOneByUrl']);
        $sth->execute(array('pUrl' => $pUrl));
        $item = $sth->fetchAll(\PDO::FETCH_ASSOC);
        if (isset($item[0])) {
            return $item[0];
        } else {
            return false;
        }
    }
}
