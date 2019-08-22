<?php declare(strict_types=1);

namespace Swoft\Http\Server\Formatter;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Http\Message\ContentType;
use Swoft\Http\Message\Contract\ResponseFormatterInterface;
use Swoft\Http\Message\Response;
use function context;
use function current;
use function strpos;

/**
 * Class AcceptResponseFormatter
 *
 * @Bean("acceptFormatter")
 *
 * @since 2.0
 */
class AcceptResponseFormatter implements ResponseFormatterInterface
{
    /**
     * Formats
     *
     * @var array
     *
     * @example
     * [
     *   'application/json' => Response::FORMAT_JSON,
     *   'application/xml' => Response::FORMAT_JSON,
     * ]
     */
    protected $formats = [];

    /**
     * @param Response $response
     *
     * @return Response
     * @throws \Swoft\Exception\SwoftException
     */
    public function format(Response $response): Response
    {
        $request = context()->getRequest();
        $accepts = $request->getHeader('accept');

        $responseContentType = $response->getHeaderLine(ContentType::KEY);

        // Format by user response content type
        if ($responseContentType) {
            $format = $this->getResponseFormat($responseContentType);

            $response->setFormat($format);
            return $response;
        }

        // Fix empty bug
        if (!$accepts) {
            return $response;
        }

        // Accept type to format, default is json
        $acceptType = current($accepts);

        $format = $this->getResponseFormat($acceptType);
        if ($format) {
            $response->setFormat($format);
        }

        return $response;
    }

    /**
     * Match format from all formats
     *
     * @param string $responseContentType
     *
     * @return string
     */
    private function getResponseFormat(string $responseContentType): string
    {
        $format = '';
        foreach ($this->formats as $contentType => $formatType) {
            if (strpos($responseContentType, $contentType) === 0) {
                $format = $formatType;
                break;
            }
        }

        return $format;
    }
}
