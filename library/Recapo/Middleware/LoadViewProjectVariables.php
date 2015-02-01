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
class LoadViewProjectVariables extends \Slim\Middleware
{
    /**
     * Call
     *
     * @return null
     */
    public function call()
    {
        $this->next->call();
        $_app = &$this->app;
        if ($_app->__isset('login')) {
            $_view = $_app->view();
            $_db = $_app->container['db'];
            $login = $_app->__get('login');

            $_view->set('selectCurrentProjects', \Recapo\Model\Project::getCurrentProjects($login->info['userID']));
            $_view->set('selectElapsedProjects', \Recapo\Model\Project::getElapsedProjects($login->info['userID']));
        }

        return;
    }
}
