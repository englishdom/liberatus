<?php
namespace Common\Middleware;

use Common\Exception;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\RouteResult;
use Throwable;
use Zend\Http;

final class ErrorResponseGenerator
{
    /**
     * @var array
     */
    private $responseCode;

    /**
     * ErrorResponseGenerator constructor.
     * @param array              $responseCodes
     */
    public function __construct(array $responseCodes)
    {
        $this->responseCode = $responseCodes;
    }


    public function __invoke($err, ServerRequestInterface $request, ResponseInterface $response)
    {
        if (!$err instanceof \Exception && !$err instanceof \Throwable) {
            $hasRoute = $request->getAttribute(RouteResult::class) !== null;
            if (!$hasRoute) {
                $err = new Exception\NotFoundException('Not found');
            } else {
                $err = new \Exception('Internal server error');
            }
        }

        return $this->prepareJson($request, $response, $err);
    }

    /**
     * @param ServerRequestInterface $request
     * @param Throwable $exception
     * @param int $httpCode
     * @return array
     */
    protected function fillTemplate(ServerRequestInterface $request, Throwable $exception, int $httpCode): array
    {
        $identifier = 'unknown';
        if ($exception instanceof Exception\ExceptionInterface) {
            $identifier = $exception->getIdentifier();
        }

        $result = [
            'errors' => [
                'id' => (string)$identifier,
                'status' => (string)$httpCode,
                'title' => (string)get_class($exception) . ': ' . $exception->getMessage(),
                'file' => (string)$exception->getFile() . ':' . $exception->getLine(),
                'code' => (string)$exception->getCode(),
                'source' => [
                    'pointer' => $request->getUri()->getPath(),
                    'parameter' => $request->getUri()->getQuery()
                ]
            ]
        ];
        if ($exception instanceof Exception\ExceptionDetailInterface) {
            $result['errors']['detail'] = $exception->getDetail();
        }

        return $result;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param Throwable $exception
     * @return MessageInterface
     */
    protected function prepareJson(ServerRequestInterface $request, ResponseInterface $response, Throwable $exception)
    {
        $exceptionName = get_class($exception);
        if (array_key_exists($exceptionName, $this->responseCode)) {
            $httpCode = $this->responseCode[$exceptionName];
            $result = null;
        } else {
            $result = $this->fillTemplate($request, $exception, Http\Response::STATUS_CODE_400);
            $httpCode = Http\Response::STATUS_CODE_400;
            $result = json_encode($result);
        }

        $newResponse = $response
            ->withHeader('Content-type', 'application/vnd.api+json')
            ->withStatus($httpCode);
        $newResponse->getBody()->write($result);

        return $newResponse;
    }
}
