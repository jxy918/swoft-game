<?php declare(strict_types=1);


namespace SwoftTest\Http\Server\Testing\Controller;

use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Http\Message\ContentType;
use Swoft\Http\Message\Request;
use Swoft\Http\Message\Response;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;

/**
 * Class ContentTypeController
 *
 * @since 2.0
 *
 * @Controller(prefix="ct")
 */
class ContentTypeController
{
    /**
     * @RequestMapping()
     *
     * @param Response $response
     *
     * @return Response
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function userCt(Response $response): Response
    {
        return $response->withContentType('image/jpeg')->withContent('imag data content');
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
    public function userCt2(Response $response): Response
    {
        return $response->withContentType(ContentType::XML)->withContent('xml data content');
    }

    /**
     * @RequestMapping()
     *
     * @param Response $response
     *
     * @return array
     */
    public function userCt3(Response $response): array
    {
        return ['key' => 'data'];
    }

    /**
     * @RequestMapping()
     *
     * @param Request $request
     *
     * @return array
     */
    public function ctm(Request $request): array
    {
        $data = [
            $request->getParsedQuery(),
            $request->getParsedQuery()
        ];

        return $data;
    }
}
