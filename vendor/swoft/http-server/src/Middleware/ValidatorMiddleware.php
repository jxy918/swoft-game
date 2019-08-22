<?php declare(strict_types=1);

namespace Swoft\Http\Server\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Http\Message\Request;
use Swoft\Http\Server\Contract\MiddlewareInterface;
use Swoft\Http\Server\Router\Route;
use Swoft\Http\Server\Router\Router;
use Swoft\Validator\Exception\ValidatorException;
use Swoft\Validator\ValidateRegister;
use Swoft\Validator\Validator;
use function explode;

/**
 * Class ValidatorMiddleware
 *
 * @Bean()
 *
 * @since 2.0
 */
class ValidatorMiddleware implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     * @throws ContainerException
     * @throws ReflectionException
     * @throws ValidatorException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /* @var Route $route */
        [$status, , $route] = $request->getAttribute(Request::ROUTER_ATTRIBUTE);
        if ($status !== Router::FOUND) {
            return $handler->handle($request);
        }

        // Controller and method
        $handlerId = $route->getHandler();
        [$className, $method] = explode('@', $handlerId);

        // Query validates
        $validates = ValidateRegister::getValidates($className, $method);
        if (empty($validates)) {
            return $handler->handle($request);
        }

        $data  = $request->getParsedBody();
        $query = $request->getQueryParams();

        // ParsedBody is empty string
        $parsedBody    = $data = empty($data) ? [] : $data;
        $notParsedBody = !is_array($data);
        if ($notParsedBody) {
            $parsedBody = [];
        }

        /* @var Validator $validator */
        $validator = BeanFactory::getBean('validator');

        /* @var Request $request */
        [$parsedBody, $query] = $validator->validateRequest($parsedBody, $validates, $query);

        if ($notParsedBody) {
            $parsedBody = $data;
        }

        $request = $request->withParsedBody($parsedBody)->withParsedQuery($query);

        return $handler->handle($request);
    }
}
