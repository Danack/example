<?php

declare(strict_types=1);

namespace Esprintf {

    function getEscapeCallable($search)
    {
        static $escaper = null;
        if ($escaper === null) {
            $escaper = new \Zend\Escaper\Escaper('utf-8');
        }

        static $rawStringFn = null;
        if ($rawStringFn === null) {
            $rawStringFn = function (string $string) {
                return $string;
            };
        }

        $callables = [
            ':attr_' => [$escaper, 'escapeHtmlAttr'],
            ':js_' => [$escaper, 'escapeJs'],
            ':css_' => [$escaper, 'escapeCss'],
            ':uri_' => [$escaper, 'escapeUrl'],
            ':raw_' => $rawStringFn
        ];

        foreach ($callables as $key => $callable) {
            if (strpos($search, $key) === 0) {
                return $callable;
            }
        }

        return [$escaper, 'escapeHtml'];
    }

}

namespace {

    use Esprintf\EsprintfException;

    /**
     * @param $string
     * @param $searchReplace
     * @return mixed
     * @throws EsprintfException
     */
    function esprintf($string, $searchReplace)
    {
        $escapedParams = [];

        // TODO - validate array is sane
        foreach ($searchReplace as $key => $value) {
            if (is_string($key) === false) {
                throw new EsprintfException("escape key [$key] is not a string");
            }
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
