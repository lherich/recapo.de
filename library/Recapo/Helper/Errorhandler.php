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
 * Deals with Exceptions etc.
 * @todo: needs a big update
 */
class Errorhandler
{
    public static $_app = null;
    static public $exceptions = array(
        array('exception' => 'FatalException', 'status' => 500, 'message' => 'An error occured during the error handling routine.'),
        array('exception' => 'UncaughtException', 'status' => 500, 'message' => 'Caught an unknow error in the error handling routine.'),
        array('exception' => 'Exception', 'status' => 500, 'message' => 'Fatal error'),
    );

    // public static function softErrorhandler($errstr = '', $errno = NULL, $errfile = '', $errline = NULL) {
    public static function softErrorhandler($errno = 0, $errstr = '', $errfile = '', $errline = null)
    {
        if (!is_numeric($errno)) {
            $tmp = $errstr;
            $errstr = $errno;
            $errno = (int) $tmp;
        }
        throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
    }

    public static function fatalErrorhandler()
    {
        $error = error_get_last();

        if ($error !== null) {
            $errfile = 'unknown file';
            $errstr  = 'shutdown';
            $errno   = E_CORE_ERROR;
            $errline = 0;

            $errno   = $error["type"];
            $errfile = $error["file"];
            $errline = $error["line"];
            $errstr  = $error["message"];
            self::hardErrorhandler(new \ErrorException($errstr, $errno, 0, $errfile, $errline));
        }
    }

    public static function hardErrorhandler(\Exception $e)
    {
        header("HTTP/1.1 500 Internal Server Error");
        // log
        self::$_app->getLog()->error($e);

        $output = ob_end_clean();
        $ename = get_class($e);
        //print_r($e);
        //print_r(self::$exceptions);
        $key = self::recursive_array_search($ename, self::$exceptions);
        if (isset(self::$exceptions[$key])) {
            if (isset(self::$exceptions[$key]['redirect'])) {
                self::$_app->flash('danger', self::$exceptions[$key]['status'].': '.self::$exceptions[$key]['message']);
                self::$_app->environment['slim.flash']->save();
                self::$_app->redirect(self::$_app->urlFor(self::$exceptions[$key]['redirect']));
            } else {
                print 'Fehler<br/>';
                print '<br />'.htmlspecialchars($output).'<br/>';
                print '<br />'.$ename.'<br/>';
                print self::$exceptions[$key]['status'].'<br/>';
                print self::$exceptions[$key]['message'].'<br/>';
                print $e->getMessage().'<br/>';
                print $e->getFile().'<br/>';
                print $e->getLine().'<br/>';
                self::$_app->flashNow('error', $e->getMessage());
                self::$_app->flash('error', $e->getMessage());
                self::$_app->flashKeep();
          //self::$_app->environment['slim.flash']->save();
          //print_r(self::$_app->environment['slim.flash']->getMessages());
            }
        } else {
            // unknow exception
            //print_r($e);
            exit(self::$exceptions[0]['message']);
        }
        if (self::$_app->settings['mode'] == 'development') {
        } else {
            ob_end_clean();
            exit('An error occured in live mode.');
        }
        exit();
    }

    public static function addExceptions($pData = array())
    {
        self::$exceptions = array_merge(self::$exceptions, $pData);
    }

    public static function recursive_array_search($needle, $haystack)
    {
        // http://de3.php.net/manual/en/function.array-search.php
        foreach ($haystack as $key => $value) {
            $current_key = $key;
            if ($needle === $value or (is_array($value) && self::recursive_array_search($needle, $value) !== false)) {
                return $current_key;
            }
        }

        return false;
    }
}

/* TODO Slim mit Fehler testn und  mit einbeziehen */
