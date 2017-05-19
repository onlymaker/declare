<?php

@ini_set('opcache.enable', 0);

define('SYSTEM', dirname(__DIR__));

require_once SYSTEM . '/vendor/autoload.php';

call_user_func(function ($f3) {

    if (!$f3->log) {
        $f3->config(SYSTEM . '/src/cfg/system.ini;' . SYSTEM . '/src/cfg/local.ini');

        if ($f3->DEBUG) {
            ini_set('opcache.enable', 0); // opcache will cache f3 template
        }

        $f3->mset([
            'AUTOLOAD' => SYSTEM . '/src/',
            'LOGS' => SYSTEM . '/logs/'
        ]);

        $logger = new Log(date('Y-m-d') . '.log');

        $f3->log = function ($message, $context = []) use ($logger) {
            if (false !== strpos($message, '{') && !empty($context)) {
                $replacements = [];
                foreach ($context as $key => $val) {
                    if (is_null($val) || is_scalar($val) || (is_object($val) && method_exists($val, "__toString"))) {
                        $replacements['{' . $key . '}'] = $val;
                    } elseif (is_object($val)) {
                        $replacements['{' . $key . '}'] = '[object ' . get_class($val) . ']';
                    } else {
                        $replacements['{' . $key . '}'] = '[' . gettype($val) . ']';
                    }
                }
                $message = strtr($message, $replacements);
            }
            $logger->write($message, 'Y-m-d H:i:s');
        };

        if (PHP_SAPI != 'cli') {
            $f3->config(SYSTEM . '/src/cfg/route.ini');

            $f3->mset([
                'UI' => SYSTEM . '/src/tpl/',
                'UPLOADS' => SYSTEM . '/html/excel/',
                'ONERROR' => function ($f3) {
                    $error = $f3->get('ERROR');

                    if (!$f3->get('DEBUG')) {
                        unset($error['trace']);
                    }

                    if ($f3->get('AJAX')) {
                        echo json_encode(['error' => $error], JSON_UNESCAPED_UNICODE);
                    } else {
                        $f3->set('error', $error);
                        echo Template::instance()->render('error.html');
                    }
                }
            ]);

            $f3->run();
        }
    }
}, Base::instance());
