<?php


namespace App\Exception;


use Exception;

abstract class AbstractNormalizer implements NormalizerInterface
{
    private $exceptionTypes;

    public function __construct($exceptionTypes)
    {
        $this->exceptionTypes = $exceptionTypes;
    }

    public function supports(Exception $exception)
    {
        return in_array(get_class($exception), $this->exceptionTypes);
    }
}