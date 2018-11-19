<?php

/**
 * This file holds functions that are required by all environments.
 */

/**
 * @param array $indexes
 * @return mixed
 * @throws Exception
 */
function getConfig(array $indexes)
{
    static $options = [];
    require __DIR__ . '/../config.php';

    $data = $options;

    foreach ($indexes as $index) {
        if (array_key_exists($index, $data) === false) {
            throw new \Exception("Config doesn't contain an element for $index, for indexes [" . implode('|', $indexes) . "]");
        }

        $data = $data[$index];
    }

    return $data;
}

function showException(\Exception $exception)
{
    echo "oops";
    do {
        echo get_class($exception) . ":" . $exception->getMessage() . "\n\n";
        echo nl2br($exception->getTraceAsString());

        echo "<br/><br/>";
    } while (($exception = $exception->getPrevious()) !== null);
}

/**
 * @param $errorNumber
 * @param $errorMessage
 * @param $errorFile
 * @param $errorLine
 * @return bool
 * @throws Exception
 */
function saneErrorHandler($errorNumber, $errorMessage, $errorFile, $errorLine)
{
    if (error_reporting() === 0) {
        // Error reporting has been silenced
        if ($errorNumber !== E_USER_DEPRECATED) {
        // Check it isn't this value, as this is used by twig, with error suppression. :-/
            return true;
        }
    }
    if ($errorNumber === E_DEPRECATED) {
        return false;
    }
    if ($errorNumber === E_CORE_ERROR || $errorNumber === E_ERROR) {
        // For these two types, PHP is shutting down anyway. Return false
        // to allow shutdown to continue
        return false;
    }
    $message = "Error: [$errorNumber] $errorMessage in file $errorFile on line $errorLine.";
    throw new \Exception($message);
}

/**
 * Decode JSON with actual error detection
 *
 * @param $json
 * @return mixed
 * @throws \Example\Exception\JsonException
 */
function json_decode_safe($json)
{
    if ($json === null) {
        throw new \Example\Exception\JsonException("Error decoding JSON: cannot decode null.");
    }

    $data = json_decode($json, true);

    if (json_last_error() === JSON_ERROR_NONE) {
        return $data;
    }

    $parser = new \Seld\JsonLint\JsonParser();
    $parsingException = $parser->lint($json);

    if ($parsingException !== null) {
        throw $parsingException;
    }

    if ($data === null) {
        throw new \Example\Exception\JsonException("Error decoding JSON: null returned.");
    }

    throw new \Example\Exception\JsonException("Error decoding JSON: " . json_last_error_msg());
}



function get_password_options()
{
    $options = [
        'cost' => 12,
    ];

    return $options;
}

/**
 * @param $password
 * @return bool|string
 */
function generate_password_hash($password)
{
    $options = get_password_options();
    return password_hash($password, PASSWORD_BCRYPT, $options);
}


function getIpAddress()
{
    $ip = "";

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //shared internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   // from load balancer
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }

    if (!empty($ip)) {
        $ip .= ", " . $_SERVER['REMOTE_ADDR'];
    }
    else {
        $ip .= $_SERVER['REMOTE_ADDR'];
    }

    return $ip;
}




/**
 * Recursive directory search
 * @param $folder
 * @param $pattern
 * @return array
 */
function recursiveSearch($folder, $pattern)
{
    $dir = new \RecursiveDirectoryIterator($folder);
    $ite = new \RecursiveIteratorIterator($dir);
    $files = new \RegexIterator($ite, $pattern, \RegexIterator::GET_MATCH);
    $fileList = array();
    foreach ($files as $file) {
        $fileList = array_merge($fileList, $file);
    }
    return $fileList;
}





function convertToValue($name, $value)
{
    if (is_scalar($value) === true) {
        return $value;
    }
    if ($value === null) {
        return null;
    }

    $callable = [$value, 'toArray'];
    if (is_object($value) === true && is_callable($callable)) {
        return $value->toArray();
    }
    if (is_object($value) === true && $value instanceof \DateTime) {
        return $value->format(DATE_ATOM);
    }

    if (is_array($value) === true) {
        $values = [];
        foreach ($value as $key => $entry) {
            $values[$key] = convertToValue($key, $entry);
        }

        return $values;
    }

    $message = "Unsupported type [" . gettype($value) . "] for toArray for property $name.";


    if (is_object($value) === true) {
        $message = "Unsupported type [" . gettype($value) . "] of class [" . get_class($value) . "] for toArray for property $name.";
    }

    throw new \Exception($message);
}


function fetchUri($uri, $method, $queryParams = [], $body = null)
{
    $query = http_build_query($queryParams);
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $uri . $query);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);

    if ($body !== null) {
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
    }

    $headers = [];
    $handleHeaderLine = function ($curl, $headerLine) use (&$headers) {
        $headers[] = $headerLine;
        return strlen($headerLine);
    };
    curl_setopt($curl, CURLOPT_HEADERFUNCTION, $handleHeaderLine);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $body = curl_exec($curl);
    $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    return [$statusCode, $body, $headers];
}

/** When you only care about 200 json data. */
function fetchData($uri)
{
    [$statusCode, $body, $headers] = fetchUri($uri, 'GET');

    if ($statusCode === 200) {
        return json_decode_safe($body);
    }

    throw new \Exception("Failed to fetch data from " . $uri);
}



function escapeMySqlLikeString(string $string)
{
    return str_replace(
        ['\\', '_', '%', ],
        ['\\\\', '\\_', '\\%'],
        $string
    );
}

// Docker IP addresses are apparently "172.XX.X.X",
// Which should be in an IPV4 PRIVATE ADDRESS SPACE
// https://www.arin.net/knowledge/address_filters.html
function isIpAddressDockerBoxHost($ipAddress)
{
    if (substr($ipAddress, 0, 4) !== '172.') {
        return false;
    }

    $ipParts = explode('.', $ipAddress);

    if (count($ipParts) !== 4) {
        return false;
    }

    $ipPart1 = (int)$ipParts[1];
    if ($ipPart1 >= 16 && $ipPart1 <= 31) {
        return true;
    }

    return false;
}
function isIpAddressSameCluster($ipAddress)
{
    if (strpos($ipAddress, '10.') === 0) {
        return true;
    }

    return false;
}




function showRawCharacters($result)
{
    $resultInHex = unpack('H*', $result);
    $resultInHex = $resultInHex[1];

    $bytes = str_split($resultInHex, 2);
    $resultSeparated = implode(', ', $bytes); //byte safe
    return $resultSeparated;
}


function buildInString($prefix, $entries)
{
    $strings = [];
    $params = [];
    $count = 0;

    foreach ($entries as $entry) {
        $currentString = ':' . $prefix . $count;
        $strings[] = $currentString;
        $params[$currentString] = $entry;
        $count += 1;
    }

    return [implode(', ', $strings), $params];
}




function compareArrays(array $expected, array $actual, array $currentKeyPath = [])
{
    $errors = [];

    ksort($expected);
    ksort($actual);
    foreach ($expected as $key => $value) {
        $keyPath = $currentKeyPath;
        $keyPath[] = $key;

        if (array_key_exists($key, $actual) === false) {
            $errors[implode('.', $keyPath)] = "Missing key should be value " . \json_encode($expected[$key]);
        }
        else if (is_array($expected[$key]) === true && is_array($actual[$key]) === true) {
            $deeperErrors = compareArrays($expected[$key], $actual[$key], $keyPath);
            $errors = array_merge($errors, $deeperErrors);
        }
        else {
            $expectedValue = \json_encode($expected[$key]);
            $actualValue = \json_encode($actual[$key]);
            if ($expectedValue !== $actualValue) {
                $errors[implode('.', $keyPath)] = "Values don't match.\nExpected " . $expectedValue . "\n vs actual " . $actualValue . "\n";
            }
        }

        unset($actual[$key]);
    }

    foreach ($actual as $key => $value) {
        $keyPath = $currentKeyPath;
        $keyPath[] = $key;
        $errors[implode('.', $keyPath)] = "Has extra value of " . \json_encode($value);
    }

    return $errors;
}
