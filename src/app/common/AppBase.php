<?php

namespace app\common;

class AppBase
{
    use Url;

    protected $user;
    protected $error = ['code' => -1, 'text' => 'Undefined'];

    function beforeRoute($f3)
    {
        if (!$f3->get('SESSION.AUTHENTICATION')) {
            if ($f3->get('VERB') == 'GET') {
                setcookie('target', $f3->get('REALM'), 0, '/');
            } else {
                setcookie('target', $this->url(), 0, '/');
            }
            $f3->reroute($this->url('/login'));
        }
        $this->user = [
            'name' => $f3->get('SESSION.AUTHENTICATION'),
            'role' => $f3->get('SESSION.AUTHORIZATION')
        ];
        $f3->set('user', $this->user);
    }

    function jsonResponse($data = [])
    {
        if ($this->error['code'] === 0) {
            return json_encode(array_merge(['error' => $this->error], $data), JSON_UNESCAPED_UNICODE);
        } else {
            return json_encode(['error' => $this->error]);
        }
    }
}
