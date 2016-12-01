<?php
if ($_SERVER['SCRIPT_FILENAME'] == __FILE__) {
    exit(403);
}

//$_app->flash('danger', 'Login required');
//$_app->flash('default', 'Login required');
$_app->render('index/index.twig', array(
    'ID' => '1',
    'name' => 'DLRG',
    'flag' => 'public',
    'type' => 'RCS',
    'enddate' => '2014-08-10'
  )
);
