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
class LoadDynamicPage extends \Slim\Middleware
{
    /**
     * Call
     *
     * @return null
     */
    public function call()
    {
        $_app = $this->app;
        $_currentRouteName = $_app->router()->getCurrentRoute()->getName();
        $_view = $_app->view();
        $_db = $_app->container['db'];
        $_data = $_db->executeStatement('SELECT page.index, page.value FROM page WHERE page.route = :route', array(':route' => $_currentRouteName));
        foreach ($_data as $item) {
            $_view->set($item['index'], $item['value']);
        }
        $this->next->call();

        return;
    }
}
