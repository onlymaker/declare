<?php
namespace app;

use app\common\Url;
use data\Database;
use Ramsey\Uuid\Uuid;

class Login
{
    use Url;

    function get($f3)
    {
        $f3->set('title', '登录');
        echo \Template::instance()->render('login.html');
    }

    function post($f3)
    {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $f3->log('User login: ' . $username);

        //$query = Database::mysql()->exec("SELECT * FROM user WHERE username = '$username' AND PASSWORD = md5(concat('$password', salt))");
        $query = true;

        if ($query) {
            $f3->set('SESSION.AUTHENTICATION', $username);
            echo json_encode([
                'error' => ['code' => 0]
            ]);
        } else {
            echo json_encode([
                'error' => [
                    'code' => -1,
                    'text' => 'login error'
                ]
            ]);
        }
    }
}
