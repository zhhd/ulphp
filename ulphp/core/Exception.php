<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2017/6/1
 * Time: 10:39
 */

namespace ulphp\core;


class Exception
{
    public function run()
    {
        error_reporting(E_ALL);
        set_error_handler([$this, 'appError']);
        set_exception_handler([$this, 'appException']);
        register_shutdown_function([$this, 'appShutdown']);
    }

    /**
     * @param $e \Exception
     */
    public function appException($e)
    {
        $log = '<b>Exception error:</b> ' . $e->getMessage() . '<br>';
        $log .= '. file:' . $e->getFile() . '<br>';
        $log .= '. line:' . $e->getLine();
        write_log($log);
    }

    public function appError($errno, $errstr, $errfile, $errline)
    {
        $log = "<b>Custom error:</b> [$errno] $errstr<br>";
        $log .= " Error on line $errline in $errfile<br>";
        write_log($log);
    }

    public function appShutdown()
    {
        if (!is_null($error = error_get_last()) && self::isFatal($error['type'])) {
            $log = '<b>Shutdown error:</b> ' . $error['message'] . '<br>';
            $log .= '. file:' . $error['file'] . '<br>';
            $log .= '. line:' . $error['line'];
            write_log($log);
        }

        return TRUE;
    }

    protected function isFatal($type)
    {
        return in_array($type, array(E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE));
    }
}