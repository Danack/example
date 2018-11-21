<?php

declare(strict_types=1);

use Predis\Client as RedisClient;
use Predis\Collection\Iterator;

require_once __DIR__ . '/vendor/autoload.php';

if (true) {

// Parameters passed using a named array:
    $redis = new Predis\Client([
        'scheme' => 'tcp',
        'host' => '10.254.254.254',
        'port' => 6379,
        'password' => 'WfunSEPArNXnB4sh'
    ]);

}
else {
    $redis = new Redis();
    $redis->connect('10.254.254.254', 6379);
    $redis->auth('WfunSEPArNXnB4sh');
    $redis->ping();
}

function countPattern($redis, $pattern)
{
    $count = 0;
    if ($redis instanceof Redis) {
        while (($keys = $redis->scan($iterator, $pattern)) !== false) {
            $count += count($keys);
            foreach ($keys as $key) {
                echo "Found key " . $key . "\n";
            }
        }
    }
    else if ($redis instanceof RedisClient) {
        foreach (new Iterator\Keyspace($redis, $pattern) as $key) {
            $count += 1;
            echo "Found key " . $key . "\n";
        }
    }

    return $count;
}

$keyname = 'Some\String\With\Slashies';
$pattern = $keyname . '*';


echo "Key name is $keyname \n";
echo "Pattern is: $pattern \n";

// Set the user specific key for 10 minutes
$result = $redis->setex($keyname, 10 * 60, 'foobar');
echo "Stored value is ". var_export($redis->get($keyname), true) . "\n";


$count = countPattern($redis, $pattern);
echo "Count is " . $count . " for pattern [$pattern] \n";

$debugPattern = 'Some*';
$count = countPattern($redis, $debugPattern);
echo "Count is " . $count . " for pattern [$debugPattern] \n";

// Output is:
// Key name is Some\String\With\Slashies
// Pattern is: Some\String\With\Slashies*
// Stored value is 'foobar'
// Count is 0 for pattern [Some\String\With\Slashies*]
// Count is 1 for pattern [Some*]
