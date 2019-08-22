<?php declare(strict_types=1);


namespace Swoft\Rpc\Packet;


use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Rpc\Error;
use Swoft\Rpc\Protocol;
use Swoft\Rpc\Response;
use Swoft\Stdlib\Helper\JsonHelper;

/**
 * Class SwoftPacketV1
 *
 * @since 2.0
 *
 * @Bean(scope=Bean::PROTOTYPE)
 */
class SwoftPacketV1 extends AbstractPacket
{
    /**
     * @param Protocol $protocol
     *
     * @return string
     */
    public function encode(Protocol $protocol): string
    {
        $data = [
            'interface' => $protocol->getInterface(),
            'version'   => $protocol->getVersion(),
            'method'    => $protocol->getMethod(),
            'params'    => $protocol->getParams(),
            'logid'     => context()->get('logid', ''),
            'spanid'    => 0,
        ];

        $data = JsonHelper::encode($data, JSON_UNESCAPED_UNICODE);
        $data = $this->addPackageEof($data);
        return $data;
    }

    /**
     * @param string $string
     *
     * @return Protocol
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function decode(string $string): Protocol
    {
        $data = JsonHelper::decode($string);

        $interface = $data['interface'] ?? '';
        $version   = $data['version'] ?? '';
        $method    = $data['method'] ?? '';
        $params    = $data['params'] ?? [];
        $logid     = $data['logid'] ?? '';
        $spanid    = $data['spanid'] ?? 0;

        return Protocol::new($version, $interface, $method, $params, [$logid, $spanid]);
    }

    /**
     * @param mixed    $result
     * @param int|null $code
     * @param string   $message
     * @param null     $data
     *
     * @return string
     */
    public function encodeResponse($result, int $code = null, string $message = '', $data = null): string
    {
        return '';
    }

    /**
     * @param string $string
     *
     * @return Response
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function decodeResponse(string $string): Response
    {
        $data = JsonHelper::decode($string, true);
        // Check response status
        if ($data['status'] == 200 && isset($data['data'])) {
            return Response::new($data['data'], null);
        }

        $msg = $data['msg'] ?? '';

        $error = Error::new(1, $msg, '');
        return Response::new(null, $error);
    }
}