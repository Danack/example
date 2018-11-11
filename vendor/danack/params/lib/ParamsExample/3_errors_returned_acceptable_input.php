<?php

declare(strict_types=1);

use ParamsExample\GetArticlesParams;
use VarMap\ArrayVarMap;

require __DIR__ . "/../../vendor/autoload.php";

$varmap = new ArrayVarMap(['limit' => 5]);

[$articleGetIndexParams, $errors] = GetArticlesParams::createOrErrorFromVarMap($varmap);

echo "After Id: " . $articleGetIndexParams->getAfterId() . PHP_EOL;
echo "Limit:    " . $articleGetIndexParams->getLimit() . PHP_EOL;
echo "Ordering: " . var_export($articleGetIndexParams->getOrdering()->toOrderArray(), true) . PHP_EOL;

echo "\nExample behaved as expected.\n";
exit(0);
