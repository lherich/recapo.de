<?php
/**
 * Recapo <http://recapo.de>
 *
 * @link      http://github.com/lherich/recapo.de
 * @copyright Copyright (c) 2014 Lucas Herich <info@recapo.de>
 * @license   MIT License <http://recapo.de/license>
 */

namespace Recapo\Middleware;

/**
 *
 */
class Authentication extends \Slim\Middleware
{
    /**
     * call the middleware
     *
     * @throws \Recapo\Exception\Unauthorized
     * @throws \Recapo\Exception\FailedAuthentication
     * @throws \Recapo\Exception\Forbidden
     */
    public function call()
    {
        $_app = $this->app;
        $routeName = $_app->router()->getCurrentRoute()->getName();
        $post = $_app->request()->post();
        $get = $_app->request()->get();

        $token = false;
        if (isset($_SESSION['token'])) {
            $token = $_SESSION['token'];
        }
        if (isset($get['token'])) {
            $token = $get['token'];
        }
        if (isset($post['token'])) {
            $token = $post['token'];
        }

        if ($token === false) {
            throw new \Recapo\Exception\Unauthorized();
        }

        // check token
        $login = \Recapo\Model\Login::authenticateToken($token);
        if ($login === false) {
            unset($_SESSION['token']);
            throw new \Recapo\Exception\FailedAuthenticationByToken();
        }
        if ($login->hasACL($_app->routes[$routeName]['id']) === false) {
            throw new \Recapo\Exception\Forbidden();
        }

        $_app->__set('login', $login);

        // call the next middleware
        $this->next->call();

        return;
    }
}
