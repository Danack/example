<?php

declare(strict_types=1);

use ParamsExample\GetArticlesParams;
use VarMap\ArrayVarMap;

require __DIR__ . "/../../vendor/autoload.php";

// Handle errors
$varmap = new ArrayVarMap(['order' => 'error']);
[$articleGetIndexParams, $errors] = GetArticlesParams::createOrErrorFromVarMap($varmap);

if (count($errors) !== 0) {
    echo "There were errors creating ArticleGetIndexParams from input\n  " . implode('\n  ', $errors);
    echo "\nExample behaved as expected.\n";
    exit(0);
}

echo "shouldn't reach here.";
exit(-1);
