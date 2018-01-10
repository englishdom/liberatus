<?php

namespace Common\Middleware;

use Common\Action\ActionInterface;
use Common\Container\ConfigInterface;
use Common\Exception\RuntimeException;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;
use Zend\Http\Response as HttpResponse;
use Zend\Diactoros\Stream;

class PrepareResponseMiddleware implements MiddlewareInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * PrepareResponseMiddleware constructor.
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @param ServerRequestInterface $request
     * @param DelegateInterface      $delegate
     * @return ResponseInterface
     * @throws RuntimeException
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate): ResponseInterface
    {
        /* Get HTTP code */
        $httpCode = $request->getAttribute(ActionInterface::HTTP_CODE);
        if (!$httpCode) {
            $httpCode = HttpResponse::STATUS_CODE_200;
        }

        $response = (new Response())->withStatus($httpCode)->withHeader('Content-Type', 'application/vnd.api+json');

        if (in_array($httpCode, [HttpResponse::STATUS_CODE_204, HttpResponse::STATUS_CODE_400])) {
            return $response;
        }

        $fractal = $request->getAttribute(ActionInterface::RESPONSE);
        if (!$fractal instanceof Item && !$fractal instanceof Collection) {
            throw new RuntimeException('Unsupported type');
        }

        /* Set META info */
        $meta = $request->getAttribute(ActionInterface::META);
        if (!empty($meta) && is_array($meta)) {
            $fractal->setMeta($meta);
        }

        $fractalManager = new Manager();
        $jsonData = $fractalManager->createData($fractal)->toJson();

        $stream = new Stream('php://memory', 'w');
        $stream->write($jsonData);

        return $response->withBody($stream);
    }
}
