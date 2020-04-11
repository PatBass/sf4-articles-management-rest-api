<?php


namespace App\Exception;


use JMS\Serializer\Serializer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionListener implements EventSubscriberInterface
{
    private $normalizers;

    private $serializer;

    public function __construct(Serializer $serializer, $normalizers)
    {
        $this->normalizers = $normalizers;
    }

    public function processException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $result = null;
        foreach ($this->normalizers as $normalizer) {
            if ($normalizer->supports($exception)) {
                $result = $normalizer->normalize($exception);
                break;
            }
        }

        if (null === $result) {
            $result['code'] = Response::HTTP_BAD_REQUEST;
            $result['body'] = [
                'code' => Response::HTTP_BAD_REQUEST,
                'message' => $exception->getMessage()
            ];
        }

        $body = $this->serializer->serialize($result['body'], 'json');

        $event->setResponse(new Response($body), $result['code']);
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => [['processException', 255]]
        ];
    }


}