<?php

declare(strict_types=1);

namespace Example;

use Example\Config\RedisConfig;
use Example\Config\TwigConfig;

class Config
{
    const EXAMPLE_DATABASE_INFO = ['example', 'database'];

    const EXAMPLE_REDIS_INFO = ['example', 'redis'];

    const EXAMPLE_TWILIO_INFO = ['example', 'twilio'];

    const EXAMPLE_STRIPE_INFO = ['example', 'stripe'];

    const EXAMPLE_SMS_NOTIFICATION_ENABLED = ['example', 'sms_notifications_enabled'];

    const EXAMPLE_MANDRILL_INFO = ['example', 'mandrill'];

    const EXAMPLE_EXCEPTION_LOGGING = ['example', 'exception_logging'];


    const TWIG_INFO_CACHE = ['twig', 'cache'];
    const TWIG_INFO_DEBUG = ['twig', 'debug'];

    public static function get($index)
    {
        return getConfig($index);
    }

    public static function testValuesArePresent()
    {
        $rc = new \ReflectionClass(self::class);
        $constants = $rc->getConstants();

        foreach ($constants as $constant) {
            $value = getConfig($constant);
        }
    }

    public static function getRedisConfig(): RedisConfig
    {
        $redisInfo = getConfig(Config::EXAMPLE_REDIS_INFO);

        return new RedisConfig(
            $redisInfo['host'],
            $redisInfo['port'],
            $redisInfo['password']
        );
    }

    public function getTwigConfig() : TwigConfig
    {
        return new TwigConfig(
            getConfig(self::TWIG_INFO_CACHE),
            getConfig(self::TWIG_INFO_DEBUG)
        );
    }
}
