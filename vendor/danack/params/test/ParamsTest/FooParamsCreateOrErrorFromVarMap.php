<?php

declare(strict_types=1);

namespace ParamsTest;

use Params\CreateOrErrorFromVarMap;

class FooParamsCreateOrErrorFromVarMap extends FooParams
{
    use CreateOrErrorFromVarMap;
}
