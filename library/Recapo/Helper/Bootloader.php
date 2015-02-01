<?php
/**
 * Recapo <http://recapo.de>
 *
 * @link      http://github.com/lherich/recapo
 * @copyright Copyright (c) 2014 Lucas Herich <info@recapo.de>
 * @license   MIT License <http://recapo.de/license>
 */

namespace Recapo\Helper;

/**
 * Bootloader for general stuff
 */
class Bootloader
{
    public static $_app = null;
    public static $_ID = 0;

    public static function parseXml($pXmlFile)
    {
        $_app = self::$_app;
        $_db = $_app->db;
        $_view = $_app->view;

        // lazy xml loading
        $xml = simplexml_load_file($pXmlFile);
        $json = json_encode($xml);
        $data = json_decode($json, true);
        if (isset($data['errors'])) {
            self::parseXmlErrors($data['errors']['error']);
        }

        if (isset($data['conditions'])) {
            self::parseXmlConditions($data['conditions']);
        }
        if (isset($data['routes']['route'])) {
            if (!isset($data['routes']['route'][0]) || !is_array($data['routes']['route'][0]) && trim($data['routes']['route'][0])) {
                // if is string convert to 0-array
                $data['routes']['route'] = array(0 => $data['routes']['route']);
            }
            if (is_array($data['routes']['route'])) {
                self::parseXmlRoutes($data['routes']['route']);
            }
        }
        if (isset($data['routes']['file'])) {
            if (!is_array($data['routes']['file']) && trim($data['routes']['file'])) {
                // if is string convert to 0-array
                $data['routes']['file'] = array(0 => $data['routes']['file']);
            }
            if (is_array($data['routes']['file'])) {
                self::parseXmlFile($data['routes']['file']);
            }
        }
        unset($data);
    }

    public static function parseXmlErrors($pData)
    {
        \Recapo\Helper\Errorhandler::addExceptions($pData);
        unset($pData);
    }

    public static function parseXmlConditions($pData)
    {
        \Slim\Route::setDefaultConditions(\Slim\Route::getDefaultConditions() + $pData);
        unset($pData);
    }

    public static function parseXmlFile($pData)
    {
        global $_CONF;
        foreach ($pData as $file) {
            self::parseXml($_CONF['path']['app'].$file);
        }
    }

    public static function parseXmlRoutes($pData)
    {
        global $_CONF;
        $_app = &self::$_app;

        // init db connection (so other classes can use it)
        $_db = $_app->db;
        $_view = $_app->view;

        $pDataCount = count($pData);
        if ($pDataCount == 8) {
            var_dump($pData);
            exit();
        }

        for ($i = 0; $i < $pDataCount; $i++) {
            if (!isset($pData[$i]['name'], $pData[$i]['route'], $pData[$i]['method'], $pData[$i]['path'])) {
                throw new \InvalidArgumentException('A node for "name", "route", "method" or "path" in route '.print_r($pData, true).$pDataCount.'is missing.');
            }
            $tmp = explode('/', $pData[$i]['name']);
            $_CONF['routes'][$pData[$i]['name']] = array(
                    'id' => self::$_ID,
                    'name' => $pData[$i]['name'],
                    'route' => $pData[$i]['route'],
                    'method' => $pData[$i]['method'],
                    'path' => $pData[$i]['path'],
                    'this' => array_pop($tmp),
                    'sql' => array(),
                    'tpl' => array(),
                    'js' => array(),
                    'middlewares' => array(0 => new \Recapo\Middleware\DefaultMiddleware()),
                    'callback' => null,
                );

            // set pointer for easy usage
            $_route = &$_CONF['routes'][$pData[$i]['name']];

            // set Callback
            $_route['callback'] = self::getCallbackRoute($pData[$i], $_route);

            // add a route for every JS file
            if (isset($pData[$i]['js'])) {
                if (!is_array($pData[$i]['js']) && trim($pData[$i]['js'])) {
                    // if is string convert to 0-array
                    $pData[$i]['js'] = array(0 => $pData[$i]['js']);
                }
                if (is_array($pData[$i]['js'])) {
                    self::setJavascriptRoutes($pData[$i]['js'], $_route);
                }
            }
            // add Middlewares and add the route to Slim
            if (isset($pData[$i]['middleware'])) {
                if (!is_array($pData[$i]['middleware']) && trim($pData[$i]['middleware'])) {
                    // if is string convert to 0-array
                    $pData[$i]['middleware'] = array(0 => $pData[$i]['middleware']);
                }
                if (is_array($pData[$i]['middleware'])) {
                    foreach ($pData[$i]['middleware'] as $middleware) {
                        $newMiddleware = '\Recapo\Middleware\\'.$middleware;
                        $newMiddleware = new $newMiddleware();
                        $newMiddleware->setApplication($_app);
                        $newMiddleware->setNextMiddleware($_route['middlewares'][0]);
                        array_unshift($_route['middlewares'], $newMiddleware);
                    }
                    $_app->$_route['method']($_route['route'], array($_route['middlewares'][0], 'call'), $_route['callback'])->name($_route['name']);
                } else {
                    $_app->$_route['method']($_route['route'], $_route['callback'])->name($_route['name']);
                }
            } else {
                $_app->$_route['method']($_route['route'], $_route['callback'])->name($_route['name']);
            }

            // garbage collector
            unset($pData[$i]);
            self::$_ID++;
        }
        unset($pData);

        // set the routes configuration array for further use
        if (is_array($_app->__get('routes'))) {
            $_app->__set('routes', $_app->__get('routes') + $_CONF['routes']);
        } else {
            $_app->__set('routes', $_CONF['routes']);
        }

    //print_r($_CONF['routes']);

        // set not found route
        if (isset($_app->routes['/notfound']['callback'])) {
            $_app->notFound($_app->routes['/notfound']['callback']);
        }
    }

    public static function setJavascriptRoutes(&$_node, &$_route)
    {
        $_app = self::$_app;
        foreach ($_node as $jsFile) {
            $_jsroute = '/js/'.$_route['path'].((substr($_route['path'], -1) == '/') ? '' : '/').$jsFile.'.js';
            $_jsname  = '/js/'.$_route['path'].((substr($_route['path'], -1) == '/') ? '' : '/').$jsFile;
            $_jspath  = $_app->settings['path']['app'].$_route['path'].$jsFile.'.js';

            $_app->get($_jsroute, function () use ($_app, $_jspath) {
                $_app->response->headers->set('Content-Type', 'text/javascript');
                print file_get_contents($_jspath);
            })->name($_jsname);
        }
    }

    public static function getCallbackRoute(&$_node, &$_route)
    {
        $_app = &self::$_app;

        return function () use (&$_app, &$_node, &$_route) {
            $_params = func_get_args();
            $_params = array_shift($_params); // make sure to put "array(0=>$this->getParams())" in Slim/Route.php at line 462
            $_this   = &$_route['this'];
            $_db     = &$_app->db;
            $_view   = &$_app->view;

            // for what?
            if ($_route['name'] == '/notfound') {
                $_route['middlewares'][0]->call();
            }

            if (isset($_node['php'])) {
                // PHP file

                // load SQL files and prepare SQL queries
                if (isset($_node['sql'])) {
                    // if is string convert to 0-array
                    if (!is_array($_node['sql'])) {
                        $_node['sql'] = array(0 => $_node['sql']);
                    }

                    foreach ($_node['sql'] as $sqlFile) {
                        $_route['sql'][$sqlFile] = $_db->prepare(file_get_contents($_app->settings['path']['app'].$_route['path'].$sqlFile.'.sql'));
                    }
                }

                // load TPL file pathes
                if (isset($_node['tpl'])) {
                    // if is string convert to 0-array
                    if (!is_array($_node['tpl'])) {
                        $_node['tpl'] = array(0 => $_node['tpl']);
                    }

                    foreach ($_node['tpl'] as $tplFile) {
                        $_route['tpl'][$tplFile] = $_route['path'].$tplFile.$_app->settings['tplExtension'];
                    }
                }

                // execute PHP file
                require_once $_app->settings['path']['app'].$_route['path'].$_node['php'].'.php';
            } else {
                // display TPL
                if (is_array($_node['tpl'])) {
                    $_node['tpl'] = array_pop($_node['tpl']);
                }
                if (isset($_node['tpl'])) {
                    $_view->display($_route['path'].$_node['tpl'].$_app->settings['tplExtension']);
                }
            }
        };
    }

    public static function run(\Slim\Slim $_app)
    {
        self::$_app = $_app;
        self::parseXml($_app->settings['path']['xml']);
        self::$_app->run();
    }
}
