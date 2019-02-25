<?php

declare(strict_types=1);


namespace Example\Repo\WordRepo;

use Example\Params\WordSearchParams;

interface WordRepo
{
    /**
     * @param WordSearchParams $wordSearchParams
     * @return string[]
     */
    public function searchForWords(WordSearchParams $wordSearchParams);
}
