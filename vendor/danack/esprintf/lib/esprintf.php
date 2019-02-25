<?php

declare(strict_types=1);

namespace Esprintf {

    function rawString(string $string)
    {
        return $string;
    }

    /**
     * @param string $search The string that will be searched for.
     * @return callable The callable that will do the escaping for the replacement.
     * @throws EsprintfException
     */
    function getEscapeCallable($search)
    {
        static $escaper = null;
        if ($escaper === null) {
            $escaper = new \Zend\Escaper\Escaper('utf-8');
        }

        $callables = [
            ':attr_' => [$escaper, 'escapeHtmlAttr'],
            ':js_' => [$escaper, 'escapeJs'],
            ':css_' => [$escaper, 'escapeCss'],
            ':uri_' => [$escaper, 'escapeUrl'],
            ':raw_' => 'Esprintf\rawString',
            ':html_' => [$escaper, 'escapeHtml']
        ];

        foreach ($callables as $key => $callable) {
            if (strpos($search, $key) === 0) {
                return $callable;
            }
        }

        throw EsprintfException::fromUnknownSearchString($search);
    }
}

namespace {

    use Esprintf\EsprintfException;

    /**
     * @param string $string
     * @param array $searchReplace
     * @return string
     * @throws EsprintfException
     */
    function esprintf($string, $searchReplace) : string
    {
        $escapedParams = [];

        $count = 0;
        foreach ($searchReplace as $key => $value) {
            if (is_string($key) === false) {
                throw EsprintfException::fromKeyIsNotString($count, $key);
            }
            $count += 1;
        }

        foreach ($searchReplace as $search => $replace) {
            $escapeFn = Esprintf\getEscapeCallable($search);
            $escapedParams[$search] = $escapeFn($replace);
        }

        return str_replace(
            array_keys($escapedParams),
            $escapedParams,
            $string
        );
    }
}
