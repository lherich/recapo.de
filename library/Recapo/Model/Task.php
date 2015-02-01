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
class Task extends \Recapo\Model\Model
{
    static protected $_sql = array(
        'selectTaskByProjectID' => '
            SELECT task.*, item.ID AS itemID, item.name AS itemName, item.flag AS itemFlag
            FROM task
            LEFT JOIN item ON(item.ID = task.itemID)
            WHERE task.projectID = :pProjectID',
        'selectTaskNameAndItemByProjectID' => '
            SELECT task.task AS taskName, item.name AS itemName
            FROM task
            LEFT JOIN item ON(item.ID = task.itemID)
            WHERE task.projectID = :pProjectID',
        'selectNextTaskByProjectID' => '
            SELECT task.*, item.ID AS itemID, item.name AS itemName, item.flag AS itemFlag
            FROM task LEFT JOIN item ON(item.ID = task.itemID)
            WHERE
                task.projectID = :pProjectID
                AND task.ID NOT IN (
                    SELECT resulttask.taskID
                    FROM resulttask
                    WHERE resulttask.projectID = :pProjectID AND resulttask.resultID = :pResultID
                )',
        'updateTask' => '
            UPDATE task
            SET task = :pTask
            WHERE task.ID = :pTaskID AND task.projectID IN (
                SELECT project.ID
                FROM project
                WHERE project.userID = :pUserID
            )',
        'updateItemID' => '
            UPDATE task
            SET itemID = :pItemID
            WHERE task.ID = :pTaskID AND task.projectID IN (
                SELECT project.ID
                FROM project
                WHERE project.userID = :pUserID
            )',
        'updateItemIDByID' => '
            UPDATE task
            SET task.itemID = :pItemID
            WHERE task.ID = :pTaskID AND task.projectID = :pProjectID',
        'deleteTaskByID' => '
            DELETE FROM task
            WHERE task.ID = :pID',
        'selectTaskByID' => '
            SELECT *
            FROM task
            WHERE task.ID = :pID
            LIMIT 1',
        'selectTaskByIDAndProjectID' => '
            SELECT *
            FROM task
            WHERE task.ID = :pID AND task.projectID = :pProjectID
            LIMIT 1',
        'insertTask' => '
            INSERT INTO task (task, itemID, projectID)
            VALUES (:pTask, :pItemID, :pProjectID)',
        'issetTask' => '
            SELECT task.ID
            FROM task
            WHERE task.projectID = :pProjectID AND LOWER(task.task) = LOWER(:pName)
            LIMIT 1',
    );

    public static function issetTask($pProjectID, $pName)
    {
        $item = self::__selectOne(__FUNCTION__, array('pProjectID' => $pProjectID, 'pName' => $pName));
        if (isset($item['ID'])) {
            return $item['ID'];
        } else {
            return;
        }
    }

    public static function selectTaskByProjectID($pProjectID)
    {
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(self::$_sql[__FUNCTION__]);
        $sth->execute(array('pProjectID' => $pProjectID));

        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }
    public static function selectTaskNameAndItemByProjectID($pProjectID)
    {
        return self::__selectAll(__FUNCTION__, array('pProjectID' => $pProjectID));
    }

    public static function updateTask($pUserID, $pTaskID, $pTask)
    {
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(self::$_sql[__FUNCTION__]);

        return $sth->execute(array('pUserID' => $pUserID, 'pTaskID' => $pTaskID, 'pTask' => $pTask));
    }

    public static function updateItemID($pUserID, $pTaskID, $pItemID)
    {
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(self::$_sql[__FUNCTION__]);

        return $sth->execute(array('pUserID' => $pUserID, 'pTaskID' => $pTaskID, 'pItemID' => $pItemID));
    }
    public static function updateItemIDByID($pProjectID, $pTaskID, $pItemID)
    {
        return self::__update(__FUNCTION__, array('pProjectID' => $pProjectID, 'pTaskID' => $pTaskID, 'pItemID' => $pItemID));
    }

    public static function deleteTaskByID($pID)
    {
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(self::$_sql[__FUNCTION__]);

        return $sth->execute(array('pID' => $pID));
    }

    public static function insertTask($pProjectID, $pTask, $pItemID)
    {
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(self::$_sql[__FUNCTION__]);

        return $sth->execute(array('pProjectID' => $pProjectID, 'pTask' => $pTask, 'pItemID' => $pItemID));
    }

    public static function selectNextTaskByProjectID($pResultID, $pProjectID)
    {
        return self::__selectOne(__FUNCTION__, array('pResultID' => $pResultID, 'pProjectID' => $pProjectID));
    }

    public static function selectTaskByIDAndProjectID($pID, $pProjectID)
    {
        return self::__selectOne(__FUNCTION__, array('pID' => $pID, 'pProjectID' => $pProjectID));
    }

    public static function selectTaskByID($pID)
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
