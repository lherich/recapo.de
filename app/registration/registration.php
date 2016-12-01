<?php
if ($_SERVER['SCRIPT_FILENAME'] == __FILE__) {
    exit(403);
}

$post = $_app->request()->post();

$filter = array(
    'login'     => array('filter'  => FILTER_CALLBACK,
                         'options' => array('\Recapo\Model\Login', 'isLoginAvailable'), ),
    'password'  => array('filter'  => FILTER_CALLBACK,
                          'options' => function ($pPassword) {
                              $l = strlen($pPassword);
                              if ($l > 2 && $l < 255) {
                                  return $pPassword;
                              } else {
                                  return false;
                              }
                          },
                      ),
    'forename'  => array('filter'  => FILTER_SANITIZE_STRING,
                         'options' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH, ),
    'surname'  => array('filter'  => FILTER_SANITIZE_STRING,
                         'options' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH, ),
    'email'    => FILTER_SANITIZE_EMAIL,
    );

$msg = array(
  'login' => 'Der angegebene Benutzername wird scheinbar schon verwendet.',
  'password' => 'Das eingegebene Passwort sollte mindestens 3 Zeichen lang sein',
  'forename' => 'Der eingegebene Vorname enthält ungültige Zeichen',
  'surname' => 'Der eingegebene Nachname enthält ungültige Zeichen',
  'email' => 'Die eingegebene E-Mail Adresse enthält ungültige Zeichen.',
);
$filterArray = filter_var_array($post, $filter);

foreach ($filterArray as $index => $item) {
    if ($item === null) {
        $_app->flash('danger', 'Der Benutzername oder das Passwort wurde nicht übergeben.');
        $_app->redirect($_app->urlFor('/'));
    } elseif ($item === false) {
        $_app->flash('danger', $msg[$index]);
        $_app->redirect($_app->urlFor('/'));
    }
}

// insert user data
$_route['sql'][$_this]->execute(array('forename' => $filterArray['forename'], 'surname' => $filterArray['surname'], 'email' => $filterArray['email']));
$userID = $_db->lastInsertId();
\Recapo\Model\Login::create($filterArray['login'], $filterArray['password'], array('userID' => $userID, 'ACL' => 1073741823));
$loginID = $_db->lastInsertId();

$login = new \Recapo\Model\Login($loginID, $filterArray['login']);
$_SESSION['token'] = $login->requestToken();

$_app->flash('success', 'Erfolgreich registriert.');
$_app->redirect($_app->urlFor('/'));
