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
class Section extends \Recapo\Model\Model
{
    static protected $_sql = array(
        'selectSections' => '
            SELECT * 
            FROM section',
        'selectSectionsID' => '
            SELECT section.ID 
            FROM section',
        'selectSectionNameByProjectID' => '
            SELECT section.section AS name 
            FROM projectmapsection 
            LEFT JOIN section ON(section.ID = projectmapsection.sectionID) 
            WHERE projectmapsection.projectID = :pProjectID 
            ORDER BY section.ID',
        'deleteSectionsByProjectID' => '
            DELETE FROM projectmapsection 
            WHERE projectmapsection.projectID = :pProjectID',
        'insertSectionsByProjectID' => '
            INSERT INTO projectmapsection (projectID, sectionID) 
            VALUES (:pProjectID, :pSectionID)',
        'selectSectionsWithActiveFlagByProjectID' => '
            SELECT section.*, IF(projectmapsection.projectID IS NULL, 0, 1) AS active 
            FROM section
            LEFT JOIN projectmapsection ON(projectmapsection.sectionID = section.ID AND projectmapsection.projectID = :pProjectID)',
        'selectSectionsActiveByProjectIDForNestedSet' => '
            SELECT 
                CONCAT("s", section.ID) AS "key", 
                section.section AS title, "true" AS folder, 
                "section" AS flag, section.* 
            FROM projectmapsection 
            LEFT JOIN section ON (section.ID = projectmapsection.sectionID) 
            WHERE projectmapsection.projectID = :pProjectID',

        'updateSectionsByProjectID' => '
            INSERT INTO projectmapsection (projectID, sectionID)
            SELECT :pProjectID AS projectID, section.ID AS sectionID 
            FROM section
            WHERE (
                    LOWER(section.section) = LOWER(:pSection)
                ) 
                AND section.ID NOT IN (
                    SELECT projectmapsection.sectionID 
                    FROM projectmapsection
                    WHERE projectmapsection.projectID = :pProjectID
                )',

        'selectSectionsByProjectID' => '
            SELECT section.* 
            FROM section
            LEFT JOIN projectmapsection ON(projectmapsection.sectionID = section.ID AND projectmapsection.projectID = :pProjectID)',
    );

    public static function insertSectionsByProjectID($pProjectID, $pSectionIDs)
    {
        // delete every section
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(self::$_sql['deleteSectionsByProjectID']);
        $sth->execute(array('pProjectID' => $pProjectID));

        // insert new sections
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(self::$_sql['insertSectionsByProjectID']);
        $i = 0;
        foreach ($pSectionIDs as $pSectionID) {
            if ($sth->execute(array('pProjectID' => $pProjectID, 'pSectionID' => $pSectionID))) {
                $i++;
            }
        }

        return $i;
    }

    public static function importSectionsByProjectID($pProjectID, $pSections)
    {
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(self::$_sql['updateSectionsByProjectID']);
        $i = 0;
        foreach ($pSections as $section) {
            if ($sth->execute(array('pProjectID' => $pProjectID, 'pSection' => $section))) {
                $i++;
            }
        }

        return $i;
    }
    public static function selectSections()
    {
        return self::__selectAll(__FUNCTION__);
    }

    public static function getSections()
    {
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(self::$_sql['selectSections']);
        $sth->execute();

        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getSectionsID()
    {
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(self::$_sql['selectSectionsID']);
        $sth->execute();

        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function selectSectionsByProjectID($pProjectID)
    {
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(self::$_sql[__FUNCTION__]);
        $sth->execute(array('pProjectID' => $pProjectID));

        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getSectionsWithActiveFlagByProjectID($pProjectID)
    {
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(self::$_sql['selectSectionsWithActiveFlagByProjectID']);
        @$sth->execute(array('pProjectID' => $pProjectID));

        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function selectSectionNameByProjectID($pProjectID)
    {
        return self::__selectAll(__FUNCTION__, array('pProjectID' => $pProjectID));
    }

    public static function selectSectionsActiveByProjectIDForNestedSet($pProjectID)
    {
        return self::__selectAll(__FUNCTION__, array('pProjectID' => $pProjectID));
    }
}
