<?php


namespace App\Exception;


use Exception;
use Symfony\Component\HttpFoundation\Response;

class NotFoundHttpExceptionNormalizer extends AbstractNormalizer
{

    /**
     * @inheritDoc
     */
    public function normalize(Exception $exception)
    {
        $result['code'] = Response::HTTP_NOT_FOUND;
        $result['body'] = [
                'code' => Response::HTTP_NOT_FOUND,
                'message' => $exception->getMessage()
        ];

        return $result;
    }
}