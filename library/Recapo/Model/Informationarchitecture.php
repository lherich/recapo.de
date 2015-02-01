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
class Informationarchitecture extends \Recapo\Model\Model
{
    static protected $_sql = array(
        'deleteInformationarchitectureByProjectID' => '
            DELETE FROM informationarchitecture
            WHERE informationarchitecture.projectID = :pProjectID',
        'selectItemByID2' => '
            SELECT
                informationarchitecture.*,
                IF(informationarchitecture.flag = "container", container.name, item.name) AS title,
                IF(informationarchitecture.flag = "container", container.name, item.name) AS name
            FROM informationarchitecture
            LEFT JOIN container ON (container.ID = informationarchitecture.containerID)
            LEFT JOIN item ON (item.ID = informationarchitecture.itemID)
            WHERE informationarchitecture.ID = :pID
            LIMIT 1',
        'selectItemByID' => '
            SELECT
                informationarchitecture.*,
                IF(informationarchitecture.flag = "container", container.name, item.name) AS title,
                IF(informationarchitecture.flag = "container", container.name, item.name) AS name
            FROM informationarchitecture
            LEFT JOIN container ON (container.ID = informationarchitecture.containerID)
            LEFT JOIN item ON (item.ID = informationarchitecture.itemID)
            WHERE informationarchitecture.ID = :pID AND informationarchitecture.projectID = :pProjectID
            LIMIT 1',
        'selectItemByProjectID' => '
            SELECT *
            FROM informationarchitecture
            WHERE informationarchitecture.ID = :pID AND informationarchitecture.projectID = :pProjectID
            LIMIT 1',
        'deleteItemByID' => '
            DELETE FROM informationarchitecture
            WHERE informationarchitecture.LFT >= :pLFT AND informationarchitecture.RGT <= :pRGT',
        'deleteItemByIDUpdateLFT' => '
            UPDATE informationarchitecture
            SET informationarchitecture.LFT = informationarchitecture.LFT - (:pRGT-:pLFT+1)
            WHERE informationarchitecture.LFT > :pRGT',
        'deleteItemByIDUpdateRGT' => '
            UPDATE informationarchitecture
            SET informationarchitecture.RGT = informationarchitecture.RGT - (:pRGT-:pLFT+1)
            WHERE informationarchitecture.RGT > :pRGT',
        'updateItemByID' => '
            UPDATE informationarchitecture
            SET informationarchitecture.sectionID = :pSectionID
            WHERE informationarchitecture.ID = :pID',
        'updateLinkByID' => '
            UPDATE informationarchitecture
            SET informationarchitecture.linkToInformationarchitectureID = :pLinkToInformationarchitectureID, informationarchitecture.linkToItemID = :pLinkToItemID
            WHERE informationarchitecture.ID = :pID',
        'selectRootItemByProjectID' => '
            SELECT *
            FROM informationarchitecture
            WHERE informationarchitecture.projectID = :pProjectID AND informationarchitecture.flag = "root"
            ORDER BY informationarchitecture.LFT ASC
            LIMIT 1',
        'selectItemAndLevelByItemID' => '
            SELECT item.*, node.*, COUNT(*) - 1 AS LEVEL
            FROM informationarchitecture AS node
            LEFT JOIN item ON(item.ID = node.itemID),
            informationarchitecture AS parent
            WHERE (node.LFT BETWEEN parent.LFT AND parent.RGT) AND (node.ID = :pID) AND (node.projectID = parent.projectID)
            GROUP BY node.LFT ORDER BY node.LFT
            LIMIT 1',
        'selectSubTreeByLevel' => '
            SELECT item.*, item.ID AS ITEMID, node.*
            FROM (
                SELECT node.*, COUNT(*) - 1 AS LEVEL
                FROM informationarchitecture AS node, informationarchitecture AS parent
                WHERE
                    (:pLFT < node.LFT AND node.RGT < :pRGT)
                    AND (parent.LFT < node.LFT AND node.RGT < parent.RGT)
                    AND (node.projectID = :pProjectID)
                    AND (parent.projectID = :pProjectID)
                GROUP BY node.LFT
                ORDER BY node.LFT
            ) AS node
            LEFT JOIN item ON(item.ID = node.itemID)
            WHERE (node.LEVEL = :pLEVEL)',
        'selectParentByItemID' => '
            SELECT
                parent.*,
                IF(parent.flag = "container", container.name, item.name) AS title,
                IF(parent.flag = "container", container.name, item.name) AS name
            FROM informationarchitecture AS parent
            LEFT JOIN container ON (container.ID = parent.containerID)
            LEFT JOIN item ON (item.ID = parent.itemID),
            (SELECT * FROM informationarchitecture WHERE informationarchitecture.ID = :pID LIMIT 1) AS node
            WHERE
                (parent.LFT < node.LFT)
                AND (parent.projectID = node.projectID)
                AND (node.RGT < parent.RGT)
                AND (parent.flag != "root")',
        'insertItemByProjectID' => '
            INSERT INTO informationarchitecture (LFT, RGT, itemID, containerID, projectID, sectionID, flag)
            VALUES (:LFT, :RGT, :pItemID, :pContainerID, :pProjectID, :pSectionID, :pFlag)',
        'issetItemByID' => '
            SELECT informationarchitecture.ID
            FROM informationarchitecture
            WHERE informationarchitecture.projectID = :pProjectID AND informationarchitecture.ID = :pID
            LIMIT 1',

        // source http://www.ninthavenue.com.au/how-to-move-a-node-in-nested-sets-with-sql
        'move1' => '
            UPDATE informationarchitecture AS ia
            SET ia.LFT = ia.LFT + :width
            WHERE ia.LFT >= :newpos',
        'move2' => '
            UPDATE informationarchitecture AS ia
            SET ia.RGT = ia.RGT + :width
            WHERE ia.RGT >= :newpos',
        'move3' => '
           UPDATE informationarchitecture AS ia
           SET ia.LFT = ia.LFT + :distance, ia.RGT = ia.RGT + :distance
           WHERE ia.LFT >= :oldlpos AND ia.RGT < :oldlpos + :width',
        'move4' => '
           UPDATE informationarchitecture AS ia
           SET ia.LFT = ia.LFT - :width
           WHERE ia.LFT > :oldrpos',
        'move5' => '
           UPDATE informationarchitecture AS ia
           SET ia.RGT = ia.RGT - :width
           WHERE ia.RGT > :oldrpos',

        'prepareInsertItemByLFT' => '
            UPDATE informationarchitecture AS ia
            SET ia.LFT =
              CASE WHEN ia.LFT >= :pLFT
                THEN ia.LFT + 2
                ELSE ia.LFT
              END,
              ia.RGT = ia.RGT + 2
            WHERE ia.projectID = :pProjectID AND ia.RGT >= :pLFT',

        'insertItemByLFT' => '
            INSERT INTO informationarchitecture (LFT, RGT, itemID, containerID, projectID, sectionID, flag)
            VALUES (:pLFT, :pRGT, :pItemID, :pContainerID, :pProjectID, :pSectionID, :pFlag)',

        'prepareInsertItemByParentID' => '
            UPDATE informationarchitecture AS ia
            LEFT JOIN informationarchitecture AS iaParent ON (iaParent.projectID = ia.projectID)
            SET ia.LFT =
              CASE WHEN ia.LFT > iaParent.LFT
                THEN ia.LFT + 2
                ELSE ia.LFT
              END,
              ia.RGT = ia.RGT + 2
            WHERE
              iaParent.ID = :pParentID
              AND ia.projectID = :pProjectID
              AND ia.RGT >= iaParent.RGT',

        'insertItemByParentID' => '
            INSERT INTO informationarchitecture (LFT, RGT, itemID, containerID, projectID, sectionID, flag)
            (
                SELECT
                    RGT-2 AS LFT, RGT-1 AS RGT, :pItemID AS itemID, :pContainerID AS containerID,
                    :pProjectID AS projectID, :pSectionID AS sectionID, :pFlag  AS flag
                FROM informationarchitecture
                WHERE informationarchitecture.ID = :pParentID AND informationarchitecture.projectID = :pProjectID
            )',

        'prepareForInsertByParentContainerID' => '
            UPDATE informationarchitecture AS ia
            LEFT JOIN informationarchitecture AS iaParent ON (iaParent.projectID = ia.projectID)
            SET ia.LFT =
                CASE WHEN ia.LFT > iaParent.LFT
                    THEN ia.LFT + 2
                    ELSE ia.LFT
                END,
                ia.RGT = ia.RGT + 2
            WHERE iaParent.ID = :pParentID AND ia.projectID = :pProjectID AND ia.RGT >= iaParent.RGT',

        'insertItemByParentContainerID' => '
            INSERT INTO informationarchitecture (LFT, RGT, itemID, containerID, projectID, sectionID, flag)
            (
                SELECT RGT-2 AS LFT, RGT-1 AS RGT, :pItemID AS itemID, :pContainerID AS containerID, :pProjectID AS projectID, :pSectionID AS sectionID, :pFlag  AS flag
                FROM informationarchitecture
                WHERE informationarchitecture.itemID = :pParentID AND informationarchitecture.projectID = :pProjectID
            )',

    // set the icon of the link here...
        'selectNestedSetByProjectIDForData' => '
            SELECT
                informationarchitecture.ID,
                informationarchitecture.RGT,
                informationarchitecture.LFT,
                informationarchitecture.ID AS "key",
                IF(informationarchitecture.flag = "container", container.name, IF(item.flag = "link", CONCAT(item.name, " <i class=\"glyphicon glyphicon-link\"></i>"), item.name)) AS title,
                informationarchitecture.flag AS flag,
                informationarchitecture.sectionID AS sectionID,
                informationarchitecture.itemID AS itemID,
                informationarchitecture.containerID AS containerID
            FROM informationarchitecture
            LEFT JOIN container ON (container.ID = informationarchitecture.containerID)
            LEFT JOIN item ON (item.ID = informationarchitecture.itemID)
            WHERE
                informationarchitecture.projectID = :pProjectID AND informationarchitecture.flag != "root"
                AND (item.ID IS NOT NULL OR container.name IS NOT NULL)
            ORDER BY informationarchitecture.LFT, informationarchitecture.sectionID',

        'selectNestedSetByProjectID' => '
            SELECT
                informationarchitecture.ID,
                informationarchitecture.RGT,
                informationarchitecture.LFT,
                informationarchitecture.ID AS "key",
                IF(informationarchitecture.flag = "container", container.name, item.name) AS title,
                informationarchitecture.flag AS flag,
                informationarchitecture.sectionID AS sectionID,
                informationarchitecture.itemID AS itemID,
                informationarchitecture.containerID AS containerID,
              	informationarchitecture.linkToInformationarchitectureID AS linkToInformationarchitectureID,
              	informationarchitecture.linkToItemID AS linkToItemID
            FROM informationarchitecture
            LEFT JOIN container ON (container.ID = informationarchitecture.containerID)
            LEFT JOIN item ON (item.ID = informationarchitecture.itemID)
            WHERE informationarchitecture.projectID = :pProjectID AND informationarchitecture.flag != "root"
                AND (item.ID IS NOT NULL OR container.name IS NOT NULL)
            ORDER BY informationarchitecture.LFT, informationarchitecture.sectionID',

        'selectNestedSetByActiveSectionAndProjectID' => '
            SELECT
                informationarchitecture.ID,
                informationarchitecture.RGT,
                informationarchitecture.LFT,
                informationarchitecture.ID AS "key",
                IF(informationarchitecture.flag = "container", container.name, item.name) AS title,
                informationarchitecture.flag AS flag,
                informationarchitecture.sectionID AS sectionID,
                informationarchitecture.itemID AS itemID,
                informationarchitecture.containerID AS containerID,
              	informationarchitecture.linkToInformationarchitectureID AS linkToInformationarchitectureID,
              	informationarchitecture.linkToItemID AS linkToItemID
            FROM informationarchitecture
            LEFT JOIN container ON (container.ID = informationarchitecture.containerID)
            LEFT JOIN item ON (item.ID = informationarchitecture.itemID)
            WHERE informationarchitecture.projectID = :pProjectID
                AND (item.ID IS NOT NULL OR container.name IS NOT NULL)
                AND informationarchitecture.sectionID IN (SELECT projectmapsection.sectionID FROM projectmapsection WHERE projectmapsection.projectID = :pProjectID)
            ORDER BY informationarchitecture.LFT, informationarchitecture.sectionID',

        'selectNestedSetForExportByProjectID' => '
            SELECT
                informationarchitecture.RGT,
                informationarchitecture.LFT,

                section.shortcut,
                item.flag AS itemFlag,
                IF(informationarchitecture.flag = "container", container.name, item.name) AS name,
                informationarchitecture.ID AS informationarchitectureID,
                informationarchitecture.linkToInformationarchitectureID,

                IFNULL(duplicateItem.ID, 0) AS duplicated,

                informationarchitecture.flag AS flag,
                informationarchitecture.containerID AS containerID
            FROM informationarchitecture
            LEFT JOIN container ON (container.ID = informationarchitecture.containerID)
            LEFT JOIN item ON (item.ID = informationarchitecture.itemID)
            LEFT JOIN item AS duplicateItem ON (duplicateItem.projectID = :pProjectID AND LOWER(duplicateItem.name) = LOWER(item.name) AND duplicateItem.flag != "root" AND duplicateItem.ID != item.ID)
            LEFT JOIN section ON (section.ID = informationarchitecture.sectionID)
            WHERE informationarchitecture.projectID = :pProjectID AND informationarchitecture.flag != "root"
               AND (item.ID IS NOT NULL OR container.name IS NOT NULL)
            ORDER BY informationarchitecture.LFT, informationarchitecture.sectionID',

        'selectNestedSetForExtendedExportByProjectID' => '
            SELECT
                informationarchitecture.RGT,
                informationarchitecture.LFT,
            CONCAT(
              	IF(informationarchitecture.flag = "container", container.ID, item.ID),
                ":",
              	section.shortcut,
              	IF(informationarchitecture.flag = "container", "c(", ""),
              	IF(informationarchitecture.flag = "container", container.name, item.name),
              	IF(informationarchitecture.flag = "container", ")", "")
            ) AS
            title,
                informationarchitecture.flag AS flag,
                informationarchitecture.containerID AS containerID
            FROM informationarchitecture
            LEFT JOIN container ON (container.ID = informationarchitecture.containerID)
            LEFT JOIN item ON (item.ID = informationarchitecture.itemID)
            LEFT JOIN section ON (section.ID = informationarchitecture.sectionID)
            WHERE informationarchitecture.projectID = :pProjectID AND informationarchitecture.flag != "root"
                AND (item.ID IS NOT NULL OR container.name IS NOT NULL)
            ORDER BY informationarchitecture.LFT, informationarchitecture.sectionID',
    );

    public static function insertItemByLFT($pLFT, $pProjectID, $pID, $pSectionID, $pFlag)
    {
        if ($pFlag == 'container') {
            $pItemID = 0;
            $pContainerID = $pID;
        } else {
            $pItemID = $pID;
            $pContainerID = 0;
        }
        self::__insertOne('prepareInsertItemByLFT', array('pLFT' => $pLFT, 'pProjectID' => $pProjectID));

        return self::__insertOne(__FUNCTION__, array('pLFT' => $pLFT, 'pRGT' => ($pLFT+1), 'pProjectID' => $pProjectID, 'pItemID' => $pItemID, 'pContainerID' => $pContainerID, 'pSectionID' => $pSectionID, 'pFlag' => $pFlag));
    }

    public static function insertItemByParentID($pParentID, $pProjectID, $pID, $pSectionID, $pFlag)
    {
        if ($pFlag == 'container') {
            $pItemID = 0;
            $pContainerID = $pID;
        } else {
            $pItemID = $pID;
            $pContainerID = 0;
        }
        self::__insertOne('prepareInsertItemByParentID', array('pParentID' => $pParentID, 'pProjectID' => $pProjectID));

        return self::__insertOne(__FUNCTION__, array('pParentID' => $pParentID, 'pProjectID' => $pProjectID, 'pItemID' => $pItemID, 'pContainerID' => $pContainerID, 'pSectionID' => $pSectionID, 'pFlag' => $pFlag));
    }

    public static function deleteInformationarchitectureByProjectID($pProjectID)
    {
        return self::__delete(__FUNCTION__, array('pProjectID' => $pProjectID));
    }

    public static function insertItemByProjectID($pProjectID, $pLFT, $pRGT, $pID, $pSectionID, $pFlag)
    {
        if ($pFlag == 'container') {
            $pItemID = 0;
            $pContainerID = $pID;
        } else {
            $pItemID = $pID;
            $pContainerID = 0;
        }

        return self::__insertOne(__FUNCTION__, array('LFT' => $pLFT, 'RGT' => $pRGT, 'pProjectID' => $pProjectID, 'pItemID' => $pItemID, 'pContainerID' => $pContainerID, 'pSectionID' => $pSectionID, 'pFlag' => $pFlag));
    }

    public static function insertRootItemByProjectID($pProjectID, $pID, $pFlag)
    {
        return self::insertItemByProjectID($pProjectID, 1, 2, $pID, 0, $pFlag);
    }

    public static function deleteItemByID($pID)
    {
        $item = self::selectItemByID($pID);
        $return = self::__delete(__FUNCTION__, array('pLFT' => $item['LFT'], 'pRGT' => $item['RGT']));
        self::__update('deleteItemByIDUpdateLFT', array('pLFT' => $item['LFT'], 'pRGT' => $item['RGT']));
        self::__update('deleteItemByIDUpdateRGT', array('pLFT' => $item['LFT'], 'pRGT' => $item['RGT']));

        return $return;
    }

    public static function updateItemByID($pID, $pSectionID)
    {
        return self::__update(__FUNCTION__, array('pID' => $pID, 'pSectionID' => $pSectionID));
    }

    public static function updateLinkByID($pID, $pLinkToItemID, $pLinkToInformationarchitectureID)
    {
        return self::__update(__FUNCTION__, array('pID' => $pID, 'pLinkToInformationarchitectureID' => $pLinkToInformationarchitectureID, 'pLinkToItemID' => $pLinkToItemID));
    }

    public static function selectItemByID2($pID)
    {
        return self::__selectOne(__FUNCTION__, array('pID' => $pID));
    }
    public static function selectItemByID($pProjectID, $pID = null)
    {
        if ($pID == null) {
            return self::selectItemByID2($pProjectID);
        }
        $item = self::__selectOne(__FUNCTION__, array('pProjectID' => $pProjectID, 'pID' => $pID));
        if ($item == null) {
            return;
        }

        return $item;
    }

    public static function selectItemByProjectID($pID, $pProjectID)
    {
        return self::__selectOne(__FUNCTION__, array('pID' => $pID, 'pProjectID' => $pProjectID));
    }

    public static function selectRootItemByProjectID($pProjectID)
    {
        $rootInformationarchitecture = self::__selectOne(__FUNCTION__, array('pProjectID' => $pProjectID));
        if ($rootInformationarchitecture == null) {
            $rootItem = \Recapo\Model\Item::selectRootItemByProjectID($pProjectID);
            // insert into tree
            $rootInformationarchitectureID = self::insertRootItemByProjectID($pProjectID, $rootItem['ID'], $rootItem['flag']);
            $rootInformationarchitecture = $rootItem;
            $rootInformationarchitecture['ID'] = $rootInformationarchitectureID;
        }

        return $rootInformationarchitecture;
    }

    public static function selectParentByItemID($pID)
    {
        return self::__selectAll(__FUNCTION__, array('pID' => $pID));
    }

    public static function selectNestedSetByActiveSectionAndProjectID($pProjectID)
    {
        return self::__selectAll(__FUNCTION__, array('pProjectID' => $pProjectID));
    }
    public static function selectNestedSetByProjectID($pProjectID)
    {
        return self::__selectAll(__FUNCTION__, array('pProjectID' => $pProjectID));
    }

    public static function selectNestedSetByProjectIDForData($pProjectID)
    {
        return self::__selectAll(__FUNCTION__, array('pProjectID' => $pProjectID));
    }

    public static function selectNestedSetForExportByProjectID($pProjectID)
    {
        return self::__selectAll(__FUNCTION__, array('pProjectID' => $pProjectID));
    }

    public static function selectNestedSetForExtendedExportByProjectID($pProjectID)
    {
        return self::__selectAll(__FUNCTION__, array('pProjectID' => $pProjectID));
    }

    public static function selectItemAndLevelByItemID($pID)
    {
        return self::__selectOne(__FUNCTION__, array('pID' => $pID));
    }

    public static function issetItemByID($pProjectID, $pID)
    {
        $item = self::__selectOne(__FUNCTION__, array('pProjectID' => $pProjectID, 'pID' => $pID));
        if ($item == null) {
            return;
        }

        return $item['ID'];
    }

    public static function selectSubTreeByItemID($pID)
    {
        $item = self::__selectOne('selectItemAndLevelByItemID', array('pID' => $pID));
        if ($item == null) {
            return;
        }

        return self::__selectAll('selectSubTreeByLevel', array('pLFT' => $item['LFT'], 'pRGT' => $item['RGT'], 'pLEVEL' => $item['LEVEL'], 'pProjectID' => $item['projectID']));
    }

    public static function moveTreeToLFT($pNode, $pMoveToLFT)
    {
        if (!isset($pNode['LFT'], $pNode['RGT'])) {
            return false;
        }

        // the moving node should not be moved below itself
        if ($pMoveToLFT > $pNode['LFT'] && $pMoveToLFT < $pNode['RGT']) {
            return false;
        } // Cannot move node into itself


        // calculate position adjustment variables
        $newpos = $pMoveToLFT;
        $width = $pNode['RGT'] - $pNode['LFT'] + 1;
        $distance = $newpos - $pNode['LFT'];
        $oldlpos = $pNode['LFT'];
        $oldrpos = $pNode['RGT'];

        // backwards movement must account for new space
        if ($distance < 0) {
            $distance -= $width;
            $oldlpos += $width;
        }

        // create new space for subtree
        self::__update('move1', array('width' => $width, 'newpos' => $newpos));
        self::__update('move2', array('width' => $width, 'newpos' => $newpos));

        // move subtree into new space
        self::__update('move3', array('distance' => $distance, 'oldlpos' => $oldlpos, 'width' => $width));

        // remove old space vacated by subtree
        self::__update('move4', array('width' => $width, 'oldrpos' => $oldrpos));
        self::__update('move5', array('width' => $width, 'oldrpos' => $oldrpos));

        return true;
    }
}
