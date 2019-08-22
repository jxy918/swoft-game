<?php

namespace Swoft\Http\Message\Helper;

use function array_keys;
use function explode;
use InvalidArgumentException;
use function is_array;
use Psr\Http\Message\UploadedFileInterface;
use ReflectionException;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Http\Message\Upload\UploadedFile;
use Swoft\Http\Message\Uri\Uri;

/**
 * Class HttpHelper
 * @since 2.0
 */
class HttpHelper
{
    /**
     * Return an UploadedFile instance array.
     *
     * @param array $files A array which respect $_FILES structure
     *
     * @throws InvalidArgumentException for unrecognized values
     * @return array
     */
    public static function normalizeFiles(array $files): array
    {
        $normalized = [];

        foreach ($files as $key => $value) {
            if ($value instanceof UploadedFileInterface) {
                $normalized[$key] = $value;
            } elseif (is_array($value)) {
                if (isset($value['tmp_name'])) {
                    $normalized[$key] = self::createUploadedFileFromSpec($value);
                    continue;
                }

                $normalized[$key] = self::normalizeFiles($value);
            } else {
                throw new InvalidArgumentException('Invalid value in files specification');
            }
        }

        return $normalized;
    }


    /**
     * Create and return an UploadedFile instance from a $_FILES specification.
     * If the specification represents an array of values, this method will
     * delegate to normalizeNestedFileSpec() and return that return value.
     *
     * @param array $value $_FILES structure
     *
     * @return array|UploadedFileInterface
     */
    private static function createUploadedFileFromSpec(array $value)
    {
        if (is_array($value['tmp_name'])) {
            return self::normalizeNestedFileSpec($value);
        }

        return new UploadedFile(
            $value['tmp_name'],
            (int)$value['size'],
            (int)$value['error'],
            $value['name'],
            $value['type']
        );
    }

    /**
     * Normalize an array of file specifications.
     * Loops through all nested files and returns a normalized array of
     * UploadedFileInterface instances.
     *
     * @param array $files
     *
     * @return UploadedFileInterface[]
     */
    private static function normalizeNestedFileSpec(array $files = []): array
    {
        $normalizedFiles = [];

        foreach (array_keys($files['tmp_name']) as $key) {
            $spec = [
                'tmp_name' => $files['tmp_name'][$key],
                'size'     => $files['size'][$key],
                'error'    => $files['error'][$key],
                'name'     => $files['name'][$key],
                'type'     => $files['type'][$key],
            ];

            $normalizedFiles[$key] = self::createUploadedFileFromSpec($spec);
        }

        return $normalizedFiles;
    }


    /**
     * Get a Uri populated with values from $swooleRequest->server.
     *
     * @param string $path
     * @param string $query
     * @param string $headerHost
     * @param array  $server
     *
     * @return Uri
     * @throws ContainerException
     * @throws ReflectionException
     */
    public static function newUriByCoRequest(string $path, string $query,  string $headerHost, array &$server): Uri
    {
        /** @var Uri $uri */
        $uri = BeanFactory::getBean(Uri::class);
        $uri = $uri->withScheme(isset($server['https']) && $server['https'] !== 'off' ? 'https' : 'http');
        $uri = $uri->withPath($path)->withQuery($query ?: $server['query_string']);

        if ($host = $server['http_host']) {
            $parts = explode(':', $host);
            $uri   = $uri->withHost($parts[0]);

            if (isset($parts[1])) {
                return $uri->withPort($parts[1]);
            }
        } elseif ($host = $server['server_name'] ?: $server['server_addr']) {
            $uri = $uri->withHost($host);
        } elseif ($headerHost) {
            $parts  = explode(':', $headerHost, 2);
            $uri   = $uri->withHost($parts[0]);

            if (isset($parts[1])) {
                return $uri->withPort($parts[1]);
            }
        }

        if (isset($server['server_port'])) {
            $uri = $uri->withPort($server['server_port']);
        }

        return $uri;
    }
}
