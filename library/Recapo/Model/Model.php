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
 * Abstract class for other models
 */
class Model
{
    protected static function __selectAll($pMethodName, $pWildCards = array())
    {
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(static::$_sql[$pMethodName]);
        $sth->execute($pWildCards);

        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    protected static function __selectOne($pMethodName, $pWildCards)
    {
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(static::$_sql[$pMethodName]);
        $sth->execute($pWildCards);
        $item = $sth->fetchAll(\PDO::FETCH_ASSOC);
        if (isset($item[0])) {
            return $item[0];
        } else {
            return;
        }
    }

    protected static function __insertOne($pMethodName, $pWildCards)
    {
        $_db = \Slim\Slim::getInstance()->container['db'];
        $sth = $_db->prepare(static::$_sql[$pMethodName]);
        $sth->execute($pWildCards);

        return $_db->lastInsertId();
    }

    protected static function __delete($pMethodName, $pWildCards)
    {
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(static::$_sql[$pMethodName]);

        return $sth->execute($pWildCards);
    }
    protected static function __update($pMethodName, $pWildCards)
    {
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(static::$_sql[$pMethodName]);

        return $sth->execute($pWildCards);
    }
}
