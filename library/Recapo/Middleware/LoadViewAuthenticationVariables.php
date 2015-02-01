<?php
/**
 * Recapo <http://recapo.de>
 *
 * @link      http://github.com/lherich/recapo
 * @copyright Copyright (c) 2014 Lucas Herich <info@recapo.de>
 * @license   MIT License <http://recapo.de/license>
 */

namespace Recapo\Middleware;

/**
 *
 */
class LoadViewAuthenticationVariables extends \Slim\Middleware
{
    protected static $_sql = array(
        'select' => '
            SELECT login.*, user.*, token.*, login.ID AS ID, CONCAT(user.forename, \' \', user.surname) AS name,
                COUNT(token.ID) AS currrentTokens,
                lastToken.tokenRequestDatetime AS lastToken
            FROM login
            LEFT JOIN user ON (user.ID = login.userID)
            LEFT JOIN token ON (token.loginID = login.ID AND NOW() BETWEEN token.tokenRequestDatetime AND token.tokenExpirationDatetime AND token.status = \'granted\')
            LEFT JOIN (SELECT token.tokenRequestDatetime FROM token WHERE token.loginID = :pID AND NOW() > token.tokenRequestDatetime ORDER BY token.tokenRequestDatetime DESC LIMIT 1) AS lastToken ON (TRUE)
            WHERE login.ID = :pID LIMIT 1',
        );

    /**
     * Call
     *
     * @return null
     */
    public function call()
    {
        $this->next->call();
        $_app = &$this->app;

        // login is already stored in Slim
        if ($_app->__isset('login')) {
            $_login = $_app->__get('login');
        // login isnt stored in Slim, check if the user is logged in at all
        } elseif (isset($_SESSION['token'])) {
            $_login = \Recapo\Model\Login::authenticateToken($_SESSION['token']);
            // authentication failed
            if ($_login === false) {
                unset($_SESSION['token']);
                $_login = null;
            // authentication was successful
            } else {
                $_app->__set('login', $_login);
            }
        // user isnt logged in
        } else {
            $_login = null;
        }
        $_view = $_app->view();
        if ($_login !== null) {
            $_db = $_app->container['db'];
            $sth = $_db->prepare(self::$_sql['select']);
            $user = $_db->executeByID($sth, array('ID' => $_login->ID));
            unset($user['password']);
        } else {
            $user = null;
        }
        $_view->set('user', $user);

        return;
    }
}
