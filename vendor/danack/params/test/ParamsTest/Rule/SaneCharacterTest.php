<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\Rule\SaneCharacters;

function getRawCharacters($string) {
    $resultInHex = bin2hex($string);
    $resultSeparated = implode(', ', str_split($resultInHex, 2)); //byte safe

    return $resultSeparated;
}

/**
 * @coversNothing
 */
class SaneCharacterTest extends BaseTestCase
{
    public function provideSuccessCases()
    {
        return [
            ["John Smith"],
            ["Basic punctuation:'\".⁋′″‴‵‶‷"],
            ["ÁGUEDA"],
            ["ALÍCIA"],
            ["☺😎😋😂"], // emoticons \u{1F600}-\u{1F64F}
            ["✅✨❕"], // Dingbats ( 2702 - 27B0 )
            ["🚅🚲🚤"], // Transport and map symbols ( 1F680 - 1F6C0 )
            ["🆕🇯🇵🉑"],    //Enclosed characters ( 24C2 - 1F251 )
            ["⁉4⃣⌛"], // Uncategorized
            ["😀😶😕"],           // Additional emoticons ( 1F600 - 1F636 )
            ["🚍🚛🚛"],         // Additional transport and map symbols
            ["🕜🐇🕝"], // Other additional symbols
        ];
    }

    public function provideFailureCases()
    {
        return [
            ["a̧͈͖r͒͑"],
//            [" ͎a̧͈͖r̽̾̈́͒͑e"],
//            ["TO͇̹̺ͅƝ̴ȳ̳ TH̘Ë͖́̉ ͠P̯͍̭O̚​N̐Y̡ H̸̡̪̯ͨ͊̽̅̾̎Ȩ̬̩̾͛ͪ̈́̀́͘"],
//            ["C̷̙̲̝͖ͭ̏ͥͮ͟Oͮ͏̮̪̝͍M̲̖͊̒ͪͩͬ̚̚͜Ȇ̴̟̟͙̞ͩ͌͝S̨̥̫͎̭ͯ̿̔̀ͅ"],
        ];
    }

    /**
     * @dataProvider provideSuccessCases
     * @covers \Params\Rule\SaneCharacters
     */
    public function testValidationSuccess($testValue)
    {
        $validator = new SaneCharacters();
        $validationResult = $validator('foo', $testValue);
        $this->assertNull($validationResult->getProblemMessage());
    }

    /**
     * @dataProvider provideFailureCases
     * @covers \Params\Rule\SaneCharacters
     */
    public function testValidationErrors($testValue)
    {
        $validator = new SaneCharacters();
        $validationResult = $validator('foo', $testValue);

        $bytesString = "Bytes were[" . getRawCharacters($testValue) . "]";

        $this->assertNotNull($validationResult->getProblemMessage(), "Should have been error: " . json_encode($testValue));
    }


    /**
     * @group wip
     */
    public function testPositionIsCorrect()
    {
        $testValue = "danack_a̧͈͖r͒͑_more_a̧͈͖r͒͑";
        $validator = new SaneCharacters();
        $validationResult = $validator('foo', $testValue);
        $message = $validationResult->getProblemMessage();

        $this->assertEquals("Invalid combining characters found at position 8", $message);
    }
}
