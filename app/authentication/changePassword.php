<?php
if ($_SERVER['SCRIPT_FILENAME'] == __FILE__) {
    exit(403);
}

$post = $_app->request()->post();

$v = new \Valitron\ValidatorDE($post, array('currentPassword', 'newPassword', 'newPassword2'));

$v->rule('required', array('currentPassword', 'newPassword', 'newPassword2'));
$v->rule('equals', 'newPassword', 'newPassword2');

if ($v->validate()) {
    $login = $_app->__get('login');
    $currentPasswordTest = \Recapo\Model\Login::authenticateLoginAndPassword($login->login, $post['currentPassword']);
    if ($currentPasswordTest == false) {
        $_app->flash('danger', 'Das derzeitige Passwort ist nicht korrekt.');
    } else {
        unset($currentPasswordTest);
        $login->setPassword($post['newPassword']);
        $_app->flash('success', 'Das Passwort wurde erfolgreich geändert.');
        $_app->redirect($_app->urlFor('/profile').'');
    }
} else {
    $errorMsg = array();
    foreach ($$v->errors() as $item) {
        if (is_array($item)) {
            foreach ($item as $subitem) {
                $errorMsg[] = $subitem;
            }
        } else {
            $errorMsg[] = $item;
        }
    }
    $_app->flash('danger', $errorMsg);
}

$_app->redirect($_app->urlFor('/profile').'#password');
