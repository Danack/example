<?php

declare(strict_types=1);

namespace Example\ApiController;

use Example\Repo\WordRepo\WordRepo;
use SlimAuryn\Response\JsonResponse;
use VarMap\VarMap;
use Example\Params\WordSearchParams;
use Params\ValidationErrors;

class Words
{
    public function searchForWords(VarMap $varMap, WordRepo $wordRepo)
    {
        [$wordSearchParams, $error] = WordSearchParams::createOrErrorFromVarMap($varMap);

        if ($error !== null) {
            /** @var ValidationErrors $error */
            $data = [
                'validation_errors' => $error->getValidationProblems()
            ];

            return new JsonResponse(
                $data,
                [],
                403
            );
        }

        $result = $wordRepo->searchForWords($wordSearchParams);

        return new JsonResponse($result);
    }
}
