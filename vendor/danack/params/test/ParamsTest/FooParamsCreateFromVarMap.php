<?php

declare(strict_types=1);

namespace ParamsTest;

use Params\CreateFromVarMap;

class FooParamsCreateFromVarMap extends FooParams
{
    use CreateFromVarMap;
}
