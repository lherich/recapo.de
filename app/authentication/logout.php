<?php
if ($_SERVER['SCRIPT_FILENAME'] == __FILE__) {
    exit(403);
}

unset($_SESSION['token']);
$_app->__get('login')->revokeToken();
$_app->flash('info', 'Erfolgreich abgemeldet.');
$_app->redirect($_app->urlFor('/'));
