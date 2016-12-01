<?php
if ($_SERVER['SCRIPT_FILENAME'] == __FILE__) {
    exit(403);
}

$pLogin = $_app->request()->get('login', false);
if ($pLogin === false) {
    throw new InvalidArgumentException();
}

if (\Recapo\Model\Login::isLoginAvailable($pLogin)) {
    $_app->halt(200, 'Der Login ist frei.');
} else {
    $_app->halt(406, 'Der Login ist in Benutzung.');
}
