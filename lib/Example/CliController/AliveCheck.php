<?php

declare(strict_types=1);

namespace Example\CliController;

class AliveCheck
{
    /**
     * This is a placeholder background task
     */
    public function run()
    {
        $callable = function () {
            $this->runInternal();
        };

        continuallyExecuteCallable(
            $callable,
            $secondsBetweenRuns = 5,
            $sleepTime = 1,
            $maxRunTime = 600
        );
    }

    public function runInternal()
    {
        echo "Alive check is alive at " . date('Y_m_d_H_i_s') . "\n";
    }
}
