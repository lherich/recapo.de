<?php
################################################################################
# conf::START                                                                  #
################################################################################
$conf = array(
    'mode'          => 'development',
    'salt'          => '__CHANGE_ME__', // just a string with various characters
    'lifetime'      => 120, // in minutes

    'domain'        => 'http://recapo.de', // domain
    'url_path'      => '/', // everthing after the domain
    'absolute_path' => '/var/www/recapo.de/', // the absolute path within the directory structure

    'path'          => array(
        'library'       => '/var/www/recapo.de/library/',
    ),

    'db'            => array(
        'host'      => '__CHANGE_ME__', // mysql host
        'dbname'    => '__CHANGE_ME__', // mysql databasename
        'user' => '__CHANGE_ME__',
        'pass' => '__CHANGE_ME__'
    ),
    'view_vars'     => array()
  );
################################################################################
# conf::END                                                                    #
################################################################################

set_include_path(get_include_path().PATH_SEPARATOR.$conf['path']['library']);

require 'Recapo/Recapo.php';

$recapo = new \Recapo\Recapo($conf);
$recapo->parseXml($conf['absolute_path'].DIRECTORY_SEPARATOR.'recapo.xml');
$recapo->run();
