<?php

declare(strict_types=1);

use ParamsExample\GetArticlesParams;
use VarMap\ArrayVarMap;

require __DIR__ . "/../../vendor/autoload.php";

// Handle errors
$varmap = new ArrayVarMap(['order' => 'error']);
[$articleGetIndexParams, $validationErrors] = GetArticlesParams::createOrErrorFromVarMap($varmap);

if ($validationErrors !== null && count($validationErrors->getValidationProblems()) !== 0) {
    $errors = $validationErrors->getValidationProblems();
    echo "There were errors creating ArticleGetIndexParams from input\n  " . implode('\n  ', $errors);
    echo "\nExample behaved as expected.\n";
    exit(0);
}

echo "shouldn't reach here.";
exit(-1);
