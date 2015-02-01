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
class Experiment extends \Slim\Middleware
{
    /**
     * Call
     *
     * @return null
     */
    public function call()
    {
        $_app = $this->app;
        $_view = $_app->view();
        $_params = $_app->router()->getCurrentRoute()->getParams();
        if (isset($_params['PROJECTURL'])) {
            $project = \Recapo\Model\Project::getProjectByUrl($_params['PROJECTURL']);
            if ($project === false) {
                $_app->flash('warning', 'Projekt existiert leider nicht oder wurde noch nicht freigeschaltet.');
                $_app->redirect($_app->urlFor('/'));
                exit();
            } else {
                $_view->set('project', $project);
                $this->next->call();
            }
        } else {
            $_app->flash('warning', 'Es wurde keine Projekturl übergeben.');
            $_app->redirect($_app->urlFor('/'));
        }

        return;
    }
}
