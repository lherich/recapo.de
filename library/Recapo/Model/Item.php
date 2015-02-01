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
class Item extends \Recapo\Model\Model
{
    static protected $_sql = array(
        'selectRootItemByProjectID' => '
            SELECT *
            FROM item
            WHERE projectID = :pProjectID AND item.flag = "root" LIMIT 1',
        'insertRootItemByProjectID' => '
            INSERT INTO item (name, projectID, flag)
            VALUES (:pName, :pProjectID, :pFlag)',
        'insertDefaultRootItem' => '
            INSERT INTO item (name, projectID, flag)
            VALUES (:pName, :pProjectID, "root")',
        'selectItemsByProjectID' => '
            SELECT *
            FROM item
            WHERE item.projectID = :pProjectID AND item.flag = "item"
            ORDER BY item.flag, item.ID',
        'selectItemsNameByProjectID' => '
            SELECT item.name
            FROM item
            WHERE item.projectID = :pProjectID AND item.flag = "item"
            ORDER BY item.flag, item.ID',
        'deleteItemByID' => '
            DELETE FROM item
            WHERE item.ID = :pID',
        'deleteItemsByProjectID' => '
            DELETE FROM item
            WHERE item.projectID = :pProjectID',
        'selectItemByID' => '
            SELECT *
            FROM item
            WHERE item.ID = :pID',
        'selectItemByIDAndProjectID' => '
            SELECT *
            FROM item
            WHERE item.ID = :pID AND item.projectID = :pProjectID
            LIMIT 1',
        'selectItemsByProjectIDByName' => '
            SELECT item.name
            FROM item
            WHERE projectID = :pProjectID AND item.flag = "item"',
        'selectItemsByContainerID' => '
            SELECT item.*
            FROM item
            WHERE item.ID IN (
              SELECT containermapitem.itemID AS ID
              FROM containermapitem
              WHERE containermapitem.containerID = :pContainerID
            )',
        'selectItemByProjectIDAndFlag' => '
            SELECT *
            FROM item
            WHERE projectID = :pProjectID AND flag = :pFlag
            LIMIT 1',
        'insertItemDetail' => '
            INSERT INTO item (name, projectID, flag)
            VALUES (:pName, :pProjectID, :pFlag)',
        'insertItemByProjectID' => '
            INSERT INTO item (name, projectID, flag)
            VALUES (:pName, :pProjectID, :pFlag)',
        'insertItem' => '
            INSERT INTO item (name, projectID)
            VALUES (:pName, :pProjectID)',
        'issetItem' => '
            SELECT item.ID
            FROM item
            WHERE item.projectID = :pProjectID AND LOWER(item.name) = LOWER(:pName) AND item.flag != "root"
            LIMIT 1',
        'issetItemByID' => '
            SELECT item.ID
            FROM item
            WHERE item.projectID = :pProjectID AND item.ID = :pID',
    );

    public static function getItemsByProjectID($pProjectID)
    {
        return self::__selectAll('selectItemsByProjectID', array('pProjectID' => $pProjectID));
    }
    public static function selectItemsNameByProjectID($pProjectID)
    {
        return self::__selectAll(__FUNCTION__, array('pProjectID' => $pProjectID));
    }

    public static function selectItemsByProjectID($pProjectID)
    {
        return self::__selectAll(__FUNCTION__, array('pProjectID' => $pProjectID));
    }
    public static function selectItemsByContainerID($pContainerID)
    {
        return self::__selectAll(__FUNCTION__, array('pContainerID' => $pContainerID));
    }

    public static function deleteItemsByProjectID($pProjectID)
    {
        return self::__delete(__FUNCTION__, array('pProjectID' => $pProjectID));
    }

    public static function deleteItemByID($pID)
    {
        return self::__delete(__FUNCTION__, array('pID' => $pID));
    }

    public static function selectItemByID($pID)
    {
        return self::__selectOne(__FUNCTION__, array('pID' => $pID));
    }

    public static function selectItemByIDAndProjectID($pID, $pProjectID)
    {
        return self::__selectOne(__FUNCTION__, array('pID' => $pID, 'pProjectID' => $pProjectID));
    }

    public static function getItemsByProjectIDByName($pProjectID)
    {
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(self::$_sql['selectItemsByProjectIDByName']);
        $sth->execute(array('pProjectID' => $pProjectID));

        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }
    public static function issetItemByID($pProjectID, $pID)
    {
        $item = self::__selectOne(__FUNCTION__, array('pProjectID' => $pProjectID, 'pID' => $pID));
        print_r(__FUNCTION__);
        exit();
        if (isset($item['ID'])) {
            return $item['ID'];
        } else {
            return;
        }
    }

    public static function issetItem($pProjectID, $pName)
    {
        $item = self::__selectOne(__FUNCTION__, array('pProjectID' => $pProjectID, 'pName' => $pName));
        if (isset($item['ID'])) {
            return $item['ID'];
        } else {
            return;
        }
    }

    public static function addItemToProjectID($pProjectID, $pName, $pFlag = 'item')
    {
        return self::__insertOne('insertItemDetail', array('pProjectID' => $pProjectID, 'pName' => $pName, 'pFlag' => $pFlag));
    }

    public static function insertItemByProjectID($pProjectID, $pName, $pFlag = 'item')
    {
        return self::__insertOne(__FUNCTION__, array('pProjectID' => $pProjectID, 'pName' => $pName, 'pFlag' => $pFlag));
    }

    public static function insertItem($pProjectID, $pName)
    {
        $_db = \Slim\Slim::getInstance()->container['db'];
        $sth = $_db->prepare(self::$_sql[__FUNCTION__]);
        $sth->execute(array('pProjectID' => $pProjectID, 'pName' => $pName));

        return $_db->lastInsertId();
    }

    public static function getItemByProjectIDAndFlag($pProjectID, $pFlag)
    {
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(self::$_sql['selectItemByProjectIDAndFlag']);
        $sth->execute(array('pProjectID' => $pProjectID, 'pFlag' => $pFlag));
        $item = $sth->fetchAll(\PDO::FETCH_ASSOC);
        if (isset($item[0])) {
            return $item[0];
        } else {
            return $item;
        }
    }

    public static function importItemsByProjectID($pProjectID, $pData)
    {
        $_db = \Slim\Slim::getInstance()->container['db'];

        $storedItems = self::getItemsByProjectIDByName($pProjectID);
        $storedItems = array_map(
            function ($pArray) {
                if (isset($pArray['name'])) {
                    return $pArray['name'];
                }
            },
            $storedItems
        );
        $storedItems = array_map('strtolower', $storedItems);

        $sth = \Slim\Slim::getInstance()->container['db']->prepare(self::$_sql['insertItem']);
        $i = 0;
        foreach ($pData as $item) {
            $item = utf8_encode($item);
            if (!in_array(strtolower($item), $storedItems)) {
                $sth->execute(array('pProjectID' => $pProjectID, 'pName' => $item));
                $i++;
            }
        }

        return $i;
    }

    public static function selectRootItemByProjectID($pProjectID)
    {
        $rootItem = self::__selectOne(__FUNCTION__, array('pProjectID' => $pProjectID));
        if ($rootItem == null) {
            // Dummy Wurzel anlegen
            $rootItem = array('name' => 'Dummy', 'projectID' => $pProjectID, 'flag' => 'root');
            $rootItem['ID'] = self::__insertOne('insertRootItemByProjectID', array('pProjectID' => $pProjectID, 'pName' => $rootItem['name'], 'pFlag' => $rootItem['flag']));
        }

        return $rootItem;
    }
    public static function insertDefaultRootItem($pProjectID, $pName = 'Startseite')
    {
        return self::__insertOne(__FUNCTION__, array('pProjectID' => $pProjectID, 'pName' => $pName));
    }
}
