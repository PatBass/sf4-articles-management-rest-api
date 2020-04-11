<?php


namespace App\Exception;


use Exception;

/**
 * Interface NormalizerInterface
 * @package App\Exception
 */
interface NormalizerInterface
{
    /**
     * @param Exception $exception
     * @return mixed
     */
    public function normalize(Exception $exception);

    /**
     * @param Exception $exception
     * @return mixed
     */
    public function supports(Exception $exception);
}