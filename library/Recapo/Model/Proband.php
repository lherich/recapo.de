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
class Proband extends \Recapo\Model\Model
{
    static protected $_sql = array(
        'selectProbandsByProjectID' => '
            SELECT * 
            FROM proband 
            WHERE proband.projectID = :pProjectID',
        'selectProbandsExportByProjectID' => '
            SELECT proband.ID, proband.email, proband.forename, proband.surname, proband.datetime 
            FROM proband 
            WHERE proband.projectID = :pProjectID',
        'insertProbandByProjectID' => '
            INSERT INTO proband (email, forename, surname, projectID, datetime) 
            VALUES (:pEmail, :pForename, :pSurname, :pProjectID, NOW())',
        'getLastFiveProbandsByProjectID' => '
            SELECT * 
            FROM proband 
            WHERE proband.projectID = :pProjectID 
            ORDER BY proband.datetime DESC 
            LIMIT 5',
    );

    public static function selectProbandsExportByProjectID($pProjectID)
    {
        return self::__selectAll(__FUNCTION__, array('pProjectID' => $pProjectID));
    }
    public static function getProbandsByProjectID($pProjectID)
    {
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(self::$_sql['selectProbandsByProjectID']);
        $sth->execute(array('pProjectID' => $pProjectID));

        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getLastFiveProbandsByProjectID($pProjectID)
    {
        return self::__selectAll(__FUNCTION__, array('pProjectID' => $pProjectID));
    }

    public static function insertProbandByProjectID($pProjectID, $pEmail, $pForename, $pSurname)
    {
        return self::__insertOne(__FUNCTION__, array('pProjectID' => $pProjectID, 'pEmail' => $pEmail, 'pForename' => $pForename, 'pSurname' => $pSurname));
    }
}
