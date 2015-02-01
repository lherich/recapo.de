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
class Container extends \Recapo\Model\Model
{
    static protected $_sql = array(
        'getContainerByProjectID' => '
            SELECT *
            FROM container
            WHERE container.projectID = :pProjectID',
        'selectContainerAndItemsByProjectID' => '
            SELECT item.*, container.ID AS containerID, container.name AS containerName
            FROM container
            LEFT JOIN containermapitem ON (containermapitem.containerID = container.ID)
            LEFT JOIN item ON (item.ID = containermapitem.itemID)
            WHERE container.projectID = :pProjectID',
        'selectAllContainerWithAllItemsByProjectID' => '
            SELECT
                item.ID AS itemID,
                item.name AS title,
                item.flag AS itemFlag,
                container.ID AS containerID,
                container.name AS containerName,
                container.projectID AS projectID
            FROM containermapitem
            JOIN item ON (item.ID = containermapitem.itemID)
            # make sure the container is within the current project
            JOIN container ON (container.ID = containermapitem.containerID AND container.projectID = :pProjectID)',
        'issetContainer' => '
            SELECT container.ID
            FROM container
            WHERE container.projectID = :pProjectID AND LOWER(container.name) = LOWER(:pName)',
        'insertContainer' => '
            INSERT INTO container(name, projectID)
            VALUES(:pName, :pProjectID)',
        'selectContainerByID' => '
            SELECT *
            FROM container
            WHERE container.ID = :pID',
        'selectItemByID' => '
            SELECT *
            FROM container
            WHERE container.ID = :pID',
        'deleteContainerByID' => '
            DELETE FROM container
            WHERE container.ID = :pID',
        'deleteContainerByProjectID' => '
            DELETE FROM container
            WHERE container.projectID = :pProjectID',
    );

    public static function selectContainerNameByProjectID($pProjectID)
    {
        return self::__selectAll(__FUNCTION__, array('pProjectID' => $pProjectID));
    }

    public static function selectContainerAndItemsByProjectID($pProjectID)
    {
        return self::__selectAll(__FUNCTION__, array('pProjectID' => $pProjectID));
    }

    public static function getContainerByProjectID($pProjectID)
    {
        return self::__selectAll(__FUNCTION__, array('pProjectID' => $pProjectID));
    }

    public static function selectAllContainerWithAllItemsByProjectID($pProjectID)
    {
        $containerItems = self::__selectAll(__FUNCTION__, array('pProjectID' => $pProjectID));

        $container = array();
        foreach ($containerItems as $containerItem) {
            if (isset($container[$containerItem['containerID']])) {
                $container[$containerItem['containerID']][] = $containerItem;
            } else {
                $container[$containerItem['containerID']] = array($containerItem);
            }
        }

        return $container;
    }

    public static function deleteContainerByID($pID)
    {
        return self::__delete(__FUNCTION__, array('pID' => $pID));
    }
    public static function deleteContainerByProjectID($pProjectID)
    {
        return self::__delete(__FUNCTION__, array('pProjectID' => $pProjectID));
    }

    public static function issetContainer($pProjectID, $pName)
    {
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(self::$_sql[__FUNCTION__]);
        $sth->execute(array('pProjectID' => $pProjectID, 'pName' => $pName));
        $item = $sth->fetchAll(\PDO::FETCH_ASSOC);
        if (isset($item[0]['ID'])) {
            return $item[0]['ID'];
        } else {
            return;
        }
    }

    public static function insertContainer($pProjectID, $pName)
    {
        return self::__insertOne(__FUNCTION__, array('pProjectID' => $pProjectID, 'pName' => $pName));
    }

    public static function selectItemByID($pID)
    {
        return self::__selectOne(__FUNCTION__, array('pID' => $pID));
    }

    public static function selectContainerByID($pID)
    {
        return self::__selectOne(__FUNCTION__, array('pID' => $pID));
    }
}
