<?php
if ($_SERVER['SCRIPT_FILENAME'] == __FILE__) {
    exit(403);
}

// try to login
if ($_app->request()->post('login') && $_app->request()->post('password')) {
    $login = \Recapo\Model\Login::authenticateLoginAndPassword($_app->request()->post('login'), $_app->request()->post('password'));
    try {
        if ($login === false) {
            throw new \Recapo\Exception\FailedAuthentication();
        } else {
            $_SESSION['token'] = $login->requestToken();
            $_app->flash('success', 'Erfolgreich angemeldet.');
        }
        if ($_app->request()->post('url', false) === false) {
            throw new \RuntimeException('No URL given.');
        }
        $route = $_app->urlFor($_app->request()->post('url'));
    } catch (\Exception $e) {
        //print $_app->request()->post('url', FALSE);
        $_app->redirect($_app->urlFor('/'));
        exit();
    }
    $_app->redirect($route);
} else {
    throw new \UnexpectedValueException();
}
