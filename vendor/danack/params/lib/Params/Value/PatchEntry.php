<?php

declare(strict_types=1);

namespace Params\Value;

interface PatchEntry
{
    const TEST      = "test";
    const REMOVE    = "remove";
    const ADD       = "add";
    const REPLACE   = "replace";
    const MOVE      = "move";
    const COPY      = "copy";

    const ALL_OPS = [
        self::TEST,
        self::REMOVE,
        self::ADD,
        self::REPLACE,
        self::MOVE,
        self::COPY
    ];

    public function getOp();

    public function getPath();

    public function getFrom();

    /**
     * @return mixed
     */
    public function getValue();
}
