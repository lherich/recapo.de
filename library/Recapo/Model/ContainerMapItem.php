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
class ContainerMapItem extends \Recapo\Model\Model
{
    static protected $_sql = array(
        'addItem' => '
            INSERT INTO containermapitem (containerID, itemID)
            VALUES (:pContainerID, :pItemID)',
        'deleteItemsByContainerID' => '
            DELETE FROM containermapitem
            WHERE containermapitem.containerID = :pContainerID',
    );

    public static function addItem($pContainerID, $pItemID)
    {
        return self::__insertOne(__FUNCTION__, array('pContainerID' => $pContainerID, 'pItemID' => $pItemID));
    }
    public static function deleteItemsByContainerID($pContainerID)
    {
        return self::__delete(__FUNCTION__, array('pContainerID' => $pContainerID));
    }
}
