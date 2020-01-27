<?php

namespace App;

class Config
{
    const ONE_MINUTE = 5;

    public static $login = 'myLogin';
    public static $password = 'myPassword';
    public static $lockResource = 'instagram-send-message';
    public static $cacheFolder = '/cache';
    public static $available_message_sending_periods = [
        ['09:15:00', '10:15:00'],
        ['15:15:00', '16:15:00'],
    ];
}

if (is_readable(__DIR__ . '/../custom_config.php')) {
    require_once __DIR__ . '/../custom_config.php';
}