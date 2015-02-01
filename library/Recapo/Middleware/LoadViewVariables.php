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
class LoadViewVariables extends \Slim\Middleware
{
    public function call()
    {
        $_app = $this->app;
        $_view = $_app->view();
        $_db = $_app->container['db'];
        $_data = $_db->executeStatement(
            'SELECT setting.index, setting.value 
            FROM setting 
            WHERE setting.flag = :flag',
            array(':flag' => 'view.variable')
        );
        foreach ($_data as $item) {
            $_view->set($item['index'], $item['value']);
        }
        $this->next->call();

        return;
    }
}
