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
class User
{
    static protected $sql = array(
        'forenameUpdate' => '
            UPDATE user
            SET user.forename = :pValue
            WHERE user.ID = :pID',
        'surnameUpdate' => '
            UPDATE user
            SET user.surname = :pValue
            WHERE user.ID = :pID',
        'emailUpdate' => '
            UPDATE user
            SET user.email = :pValue
            WHERE user.ID = :pID',
    );

    public static function updateField($pID, $pField, $pValue)
    {
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(static::$sql[$pField.'Update']);
        $sth->execute(array('pID' => $pID, 'pValue' => $pValue));
        return true;
    }
}
