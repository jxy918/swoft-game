<?php declare(strict_types=1);

namespace Swoft\Tcp\Packer;

use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Stdlib\Helper\JsonHelper;
use Swoft\Tcp\Contract\PackerInterface;
use Swoft\Tcp\Package;
use Swoft\Tcp\Response;

/**
 * Class JsonPacker
 *
 * @since 2.0.3
 * @Bean()
 */
class JsonPacker implements PackerInterface
{
    public const TYPE = 'json';

    /**
     * @return string
     */
    public static function getType(): string
    {
        return self::TYPE;
    }

    /**
     * Encode [Package] to string for request server
     *
     * @param Package $package
     *
     * @return string
     */
    public function encode(Package $package): string
    {
        return JsonHelper::encode($package->toArray());
    }

    /**
     * Decode client request body data to [Package] object
     *
     * @param string $data package data
     *
     * @return Package
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function decode(string $data): Package
    {
        $cmd = '';
        $ext = [];
        $map = JsonHelper::decode($data, true);

        // Find message route
        if (isset($map['cmd'])) {
            $cmd = (string)$map['cmd'];
            unset($map['cmd']);
        }

        if (isset($map['data'])) {
            $body = $map['data'];
            $ext  = $map['ext'] ?? [];
        } else {
            $body = $map;
        }

        return Package::new($cmd, $body, $ext);
    }

    /**
     * Encode [Response] to string for response client
     *
     * @param Response $response
     *
     * @return string
     */
    public function encodeResponse(Response $response): string
    {
        // If Response.content is not empty
        if ($content = $response->getContent()) {
            return $content;
        }

        return JsonHelper::encode($response->toArray());
    }

    /**
     * Decode the server response data as an [Response]
     *
     * @param string $data package data
     *
     * @return Response
     */
    public function decodeResponse(string $data): Response
    {
        $resp = new Response();
        $map  = JsonHelper::decode($data, true);

        $resp->setContent($data);

        if (isset($map['code'])) {
            $resp->setCode((int)$map['code']);
        }

        if (isset($map['msg'])) {
            $resp->setMsg($map['msg']);
        }

        if (isset($map['data'])) {
            $resp->setData($map['data']);
        }

        if (isset($map['data'])) {
            $resp->setData($map['data']);
        }

        return $resp;
    }
}
