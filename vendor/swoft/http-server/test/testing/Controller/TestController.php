<?php declare(strict_types=1);

namespace SwoftTest\Http\Server\Testing\Controller;

use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Http\Message\ContentType;
use Swoft\Http\Message\Response;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;

/**
 * Class TestController
 *
 * @Controller("/fixture/test")
 */
class TestController
{
    /**
     * @RequestMapping()
     * @return string
     */
    public function hello(): string
    {
        return 'hello';
    }

    /**
     * @RequestMapping()
     *
     * @param Response $response
     *
     * @return Response
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function cookie(Response $response): Response
    {
        return $response->setCookie('ck', 'val')->withContent('hello');
    }

    /**
     * @RequestMapping()
     *
     * @param Response $response
     *
     * @return Response
     */
    public function htmlData(Response $response): Response
    {
        return $response->withData('<h1>hello</h1>')->withContentType(ContentType::HTML);
    }

    /**
     * @RequestMapping()
     *
     * @param Response $response
     *
     * @return Response
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function htmlContent(Response $response): Response
    {
        return $response->withContent('<h1>hello</h1>')->withContentType(ContentType::HTML);
    }
}
