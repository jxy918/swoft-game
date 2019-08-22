<?php declare(strict_types=1);


namespace SwoftTest\Http\Server\Testing\Controller;

use Swoft\Http\Message\Request;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;

/**
 * Class HeaderController
 *
 * @since 2.0
 *
 * @Controller("testHeader")
 */
class HeaderController
{
    /**
     * @RequestMapping()
     *
     * @param Request $request
     *
     * @return array
     */
    public function headerLines(Request $request): array
    {
        return $request->getHeaderLines();
    }
}