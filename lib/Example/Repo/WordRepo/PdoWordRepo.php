<?php

declare(strict_types=1);

namespace Example\Repo\WordRepo;

use Example\Params\WordSearchParams;
use PDO;

class PdoWordRepo implements WordRepo
{
    /** @var \PDO */
    private $pdo;

    /**
     *
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function searchExact(WordSearchParams $wordSearchParams)
    {
        $query = 'select word_forward as word from word_list where word_forward like :search limit :limit';
        $escapedSearchString = escapeMySqlLikeString($wordSearchParams->getSearchString());
        $statement = $this->pdo->prepare($query);
        $params = [
            ':search' => $escapedSearchString . '%',
            ':limit' => $wordSearchParams->getLimit()
        ];

        $statement->execute($params);

        return $statement->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    private function searchBeginsWith(WordSearchParams $wordSearchParams)
    {
        $query = 'select word_forward as word from word_list where word_forward like :search limit :limit';
        $escapedSearchString = escapeMySqlLikeString($wordSearchParams->getSearchString());
        $statement = $this->pdo->prepare($query);
        $params = [
            ':search' => $escapedSearchString . '%',
            ':limit' => $wordSearchParams->getLimit()
        ];

        $statement->execute($params);

        return $statement->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    private function searchEndsWith(WordSearchParams $wordSearchParams)
    {
        $query = 'select word_forward as word from word_list where word_reversed like :search limit :limit';

        $escapedSearchString = escapeMySqlLikeString(strrev($wordSearchParams->getSearchString()));
        $statement = $this->pdo->prepare($query);
        $params = [
            ':search' => strrev($escapedSearchString) . '%',
            ':limit' => $wordSearchParams->getLimit()
        ];

        $statement->execute($params);

        return $statement->fetchAll(PDO::FETCH_COLUMN, 0);
    }


    public function searchForWords(WordSearchParams $wordSearchParams)
    {
        if ($wordSearchParams->getMatchType() == WordSearchParams::MATCH_TYPE_EXACT) {
            return $this->searchExact($wordSearchParams);
        }

        if ($wordSearchParams->getMatchType() == WordSearchParams::MATCH_TYPE_BEGINS_WITH) {
            return $this->searchBeginsWith($wordSearchParams);
        }

        if ($wordSearchParams->getMatchType() == WordSearchParams::MATCH_TYPE_ENDS_WITH) {
            return $this->searchEndsWith($wordSearchParams);
        }

        $message = sprintf(
            "Unknown search type [%s]",
            $wordSearchParams->getMatchType()
        );

        throw new \Exception($message);
    }
}
