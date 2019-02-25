<?php

declare(strict_types=1);

namespace Example\CliController;

use PDO;

class Setup
{
    public function loadWords(PDO $pdo)
    {
        $filename = __DIR__ . '/../../../data/words.txt';

        $fileHandle = @fopen($filename, 'r+');

        if ($fileHandle === false) {
            echo "Failed to open $filename for reading.\n";
            exit(-1);
        }

        $count = null;

        try {
            $statement = $pdo->query('select count(*) as count from word_list');
            if ($statement === false) {
                throw new \Exception("Query failed for unknown reason.");
            }

            $result = $statement->fetch();
            $count = $result['count'];

            if ($count > 0) {
                echo "The table word_list already contains data. Please empty it before loading the word list.";
                exit(-1);
            }
        }
        catch (\Exception $e) {
            echo "Exception getting current count of words in table word_list\n";
            echo $e->getMessage();
            echo "\n";
            exit(-1);
        }


        $insertQuery = 'insert into word_list (word_forward, word_reversed) values (:word_forward, :word_reversed)';

        $insertStatement = $pdo->prepare($insertQuery);


        $wordsInserted = 0;

        do {
            $word = fgets($fileHandle);
            if ($word === false) {
                echo "Finished reading words";
                break;
            }

            $word = trim($word);
            if (strlen($word) === 0) {
                continue;
            }
            $insertStatement->execute([
                ':word_forward' => $word,
                ':word_reversed' => strrev($word)
            ]);

            $wordsInserted += 1;

            if (($wordsInserted % 500) === 0) {
                set_time_limit(30);
                echo "Inserted $wordsInserted\n";
            }
        } while (true);

        echo "Fin\n";
        exit(0);
    }
}
