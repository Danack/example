<?php

declare(strict_types=1);

use ParamsExample\GetArticlesParams;
use VarMap\ArrayVarMap;
use Params\Exception\ValidationException;

require __DIR__ . "/../../vendor/autoload.php";

$varMap = new ArrayVarMap([]);

try {
    $varMap = new ArrayVarMap(['order' => 'not a valid value']);
    [$articleGetIndexParams, $errors] = GetArticlesParams::createFromVarMap($varMap);

    echo "shouldn't reach here.";
    exit(-1);
}
catch (ValidationException $ve) {
    echo "There were validation problems parsing the input:\n  ";
    echo implode("\n  ", $ve->getValidationProblems());

    echo "\nExample behaved as expected.\n";
    exit(0);
}
