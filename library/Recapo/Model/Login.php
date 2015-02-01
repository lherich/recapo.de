<?php
/**
 * Recapo <http://recapo.de>
 *
 * @link      http://github.com/lherich/recapo
 * @copyright Copyright (c) 2014 Lucas Herich <info@recapo.de>
 * @license   MIT License <http://recapo.de/license>
 */

namespace Recapo\Model;

/**
 *
 */
class Login
{
    public $ID = null;
    public $login = null;

    public $info = array('ACL' => 0);

    protected static $lifetime = LIFETIME;

    static protected $sql = array(
        'selectLogin' => '
            SELECT login.*, token.ID AS tokenID, token.token AS token
            FROM login LEFT JOIN token ON (token.loginID = login.ID)
            WHERE login.ID = :pID AND login.login = :pLogin
            LIMIT 1',
        'selectLoginUID' => '
            SELECT UID
            FROM login
            WHERE UID = :pUID
            LIMIT 1',
        'requestToken' => '
            INSERT INTO token (loginID, tokenRequestDatetime, token, tokenExpirationDatetime)
            VALUES (:pLoginID, NOW(), :pToken, DATE_ADD(NOW(), INTERVAL + :pLifetime MINUTE))',
        'revokeToken' => '
            UPDATE token
            SET status = :status
            WHERE ID = :pID',
        'authenticateLoginAndPassword' => '
            SELECT ID, login
            FROM login WHERE login = :pLogin AND password = :pPassword
            LIMIT 1',
        'authenticateToken' => '
            SELECT login.ID, login.login, token.ID AS tokenID
            FROM token LEFT JOIN login ON (login.ID = token.loginID)
            WHERE token.token = :pToken AND token.tokenExpirationDatetime > NOW() AND token.status = "granted"
            LIMIT 1',
        'updateTokenExpirationDatetime' => '
            UPDATE token
            SET tokenExpirationDatetime = DATE_ADD(NOW(), INTERVAL + :pLifetime MINUTE)
            WHERE ID = :pID',
        'issetLogin' => '
            SELECT true AS ISSET
            FROM login
            WHERE login.login = :pLogin',
        'setPassword' => '
            UPDATE login
            SET login.password = :pPassword
            WHERE login.ID = :pID',
    );

    public function __construct($pID, $pLogin)
    {
        $this->ID = $pID;
        $this->login = $pLogin;

        $sth = \Slim\Slim::getInstance()->container['db']->prepare(static::$sql['selectLogin']);
        $sth->execute(array('pID' => $pID, 'pLogin' => $pLogin));
        $this->info = $sth->fetch(\PDO::FETCH_ASSOC);

        if ($this->info === false) {
            throw new \Exception('login does not exist');
        }

        unset($this->info['password']);

        return;
    }

    public static function isLoginAvailable($pLogin)
    {
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(static::$sql['issetLogin']);
        $sth->execute(array('pLogin' => $pLogin));
        $item = $sth->fetch(\PDO::FETCH_ASSOC);
        if (isset($item['ISSET'])) {
            return false;
        } // is in use
        else {
            return $pLogin;
        }
    }

    public function requestToken()
    {
        $token = self::generateToken();
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(static::$sql['requestToken']);
        $sth->bindValue('pLoginID', $this->ID);
        $sth->bindValue('pToken', $token);
        $sth->bindParam(':pLifetime', self::$lifetime, \PDO::PARAM_INT);
        $sth->execute();

        return $token;
    }

    public function setPassword($pPassword)
    {
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(static::$sql['setPassword']);
        $sth->execute(array('pID' => $this->ID, 'pPassword' => self::saltPassword($pPassword)));

        return;
    }

    public function revokeToken()
    {
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(static::$sql['revokeToken']);
        $sth->execute(array('pID' => $this->info['tokenID'], 'status' => 'revoked'));

        return;
    }

    public function hasACL($pID)
    {
        // anything is allowed
        return true;

        $bin = $this->getBinaryACL($this->info['ACL']);
        if (isset($bin[$pID]) && $bin[$pID] == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function setACL($pArray)
    {
        $bin = $this->getBinaryACL($this->info['ACL']);
        foreach ($pArray as $ID => $RIGHT) {
            $bin[$ID] = $RIGHT;
        }
        $this->info['ACL'] = $this->getDezimalACL($bin);
        // update mysql
        return true;
    }

    public function revokeACL($pArray)
    {
        $bin = $this->getBinaryACL($this->info['ACL']);
        foreach ($pArray as $ID) {
            $bin[$ID] = 0;
        }
        $this->info['ACL'] = $this->getDezimalACL($bin);
        // update mysql
        return true;
    }

    public function grantACL($pArray)
    {
        $bin = $this->getBinaryACL($this->info['ACL']);
        foreach ($pArray as $ID) {
            $bin[$ID] = 1;
        }
        $this->info['ACL'] = $this->getDezimalACL($bin);
        // update mysql
        return;
    }

    protected function getBinaryACL($pDezimal)
    {
        return base_convert($pDezimal, 10, 2);
    }

    protected function getDezimalACL($pBin)
    {
        return base_convert($pBin, 2, 10);
    }

    public static function create($pLogin, $pPassword, $pArray = array())
    {
            return \Slim\Slim::getInstance()->container['db']->createInsertAndExecute(
                'login',
                array_merge(
                    array(
                        'UID' => static::generateUID(),
                        'login' => $pLogin,
                        'password' => static::saltPassword($pPassword),
                        'registrationDatetime' =>  date('Y-m-d H:i:s')
                    ),
                    $pArray
                )
            );
    }

    public static function authenticateLoginAndPassword($pLogin, $pPassword)
    {
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(static::$sql['authenticateLoginAndPassword']);
        $sth->execute(array('pLogin' => $pLogin, 'pPassword' => static::saltPassword($pPassword)));
        $item = $sth->fetch(\PDO::FETCH_ASSOC);
        if ($item === false) {
            return false;
        } else {
            return new self($item['ID'], $item['login']);
        }
    }

    public static function authenticateToken($pToken)
    {
        $sth = \Slim\Slim::getInstance()->container['db']->prepare(static::$sql['authenticateToken']);
        $sth->execute(array('pToken' => $pToken));
        $item = $sth->fetch(\PDO::FETCH_ASSOC);
        if ($item === false) {
            return false;
        } else {
            $sth = \Slim\Slim::getInstance()->container['db']->prepare(static::$sql['updateTokenExpirationDatetime']);
            $sth->bindParam(':pLifetime', self::$lifetime, \PDO::PARAM_INT);
            $sth->bindValue(':pID', $item['tokenID']);
            $sth->execute();

            return new self($item['ID'], $item['login']);
        }
    }

    public static function saltPassword($pPassword, $pSalt = SALT, $pRuns = 50, $pAlgorithm = 'sha512')
    {
        // some nice big salt
        $salt = hash($pAlgorithm, $pSalt);

        // apply $algorithm $runs times for slowdown
        while ($pRuns--) {
            $pPassword = hash($pAlgorithm, $pPassword.$pSalt);
        }

        return $pPassword;
    }

    public static function generatePassword($pLenght = 8)
    {
        $pool = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.'abcdefghijklmnopqrstuvwxyz'.'1234567890';
        $password = '';

        for ($i = 0; $i < $pLenght; $i++) {
            $password .= $pool{rand(0, strlen($pool)-1)};
        }

        return $password;
    }

    public static function generateToken()
    {
        return self::saltPassword(self::generatePassword(20));
    }

    public static function generateUID($pPrefix = '', $pCount = 0)
    {
        $pUid = strtoupper(substr(uniqid(), 7, 6));

        $sth = \Slim\Slim::getInstance()->container['db']->prepare(static::$sql['selectLoginUID']);
        $sth->execute(array('pUID' => $pPrefix.$pUid));

        if ($sth->fetch() === false) {
            return $pPrefix.$pUid;
        } elseif ($pCount > 25) {
            trigger_error('Es konnte keine eindeutige ID generiert werden!');
        } else {
            Slim\Extras\Model\Person::generateUID($pPrefix, $pCount++);
        }
    }
}
