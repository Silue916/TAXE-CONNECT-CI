<?php

namespace Epaphrodites\epaphrodites\define\config\traits;

trait currentResponses
{

    /**
     * @param mixed $result
     * @param array $msg
     * @return array
     */
    public static function Responses(
        mixed $result, 
        array $msg = []
    ):array{

        $response = match (true) {
            array_key_exists($result, $msg) => $msg[$result][0] ?? '',
            $result === true && isset($msg[true]) => $msg[true][0] ?? '',
            $result === false && isset($msg[false]) => $msg[false][0] ?? '',
            in_array($result, array_keys($msg)) => $msg[$result][0] ?? '',
            default => '',
        };
    
        $alert = match (true) {
            array_key_exists($result, $msg) => $msg[$result][1] ?? '',
            $result === true && isset($msg[true]) => $msg[true][1] ?? '',
            $result === false && isset($msg[false]) => $msg[false][1] ?? '',
            in_array($result, array_keys($msg)) => $msg[$result][1] ?? '',
            default => '',
        };
    
        $response = static::getMessageContent($response);

        return [$response, $alert];
    }

    /**
     * @param string $messageCode
     * @return string
     */
    private static function getMessageContent(
        string $messageCode
    ):string{

        $msg = static::initNamespace()['msg'];

        return $msg->answers($messageCode);
    }
}