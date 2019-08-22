<?php declare(strict_types=1);

namespace Swoft\Http\Server\Formatter;

use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Http\Message\ContentType;
use Swoft\Http\Message\Contract\ResponseFormatterInterface;
use Swoft\Http\Message\Response;
use Swoft\Stdlib\Helper\Arr;
use Swoft\Stdlib\Helper\JsonHelper;
use function is_object;
use function is_scalar;
use function method_exists;

/**
 * Class HtmlResponseFormatter
 *
 * @since 2.0
 *
 * @Bean()
 */
class HtmlResponseFormatter implements ResponseFormatterInterface
{
    /**
     * @param Response $response
     *
     * @return Response
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function format(Response $response): Response
    {
        $response = $response->withHeader(ContentType::KEY, ContentType::HTML);

        $data = $response->getData();
        if ($data === null) {
            return $response;
        }

        // It is scalar type: integer, float, string or boolean
        if (is_scalar($data)) {
            return $response->withContent((string)$data);
        }

        if (is_object($data)) {
            // Can convert to string, has method __toString()
            if (method_exists($data, '__toString')) {
                return $response->withContent((string)$data);
            }

            // Has toArray() method
            if (Arr::isArrayable($data)) {
                $data = $data->toArray();
            }
        }

        // Try convert to an JSON string
        $content = JsonHelper::encode($data);
        return $response->withContent($content);
    }
}
