<?php

namespace Swoft\Rpc\Packer;

use Swoft\App;
use Swoft\Core\RequestContext;
use Swoft\Rpc\Exception\RpcException;
use Swoft\Rpc\Exception\RpcResponseException;
use Swoft\Rpc\Exception\RpcStatusException;
use Swoft\Rpc\Packer\Json\JsonPacker;

/**
 * RPC Service data packer
 */
class ServicePacker implements PackerInterface
{

    /**
     * @var string
     */
    private $defaultPacker = 'json';

    /**
     * Default packers configs
     *
     * @var array
     */
    protected $defaultPackers
        = [
            'json' => JsonPacker::class,
        ];

    /**
     * @var array
     */
    private $packers = [];

    /**
     * @param mixed $data
     * @param string $packer
     * @return mixed
     * @throws \Swoft\Rpc\Exception\RpcException
     */
    public function pack($data, string $packer = '')
    {
        return $this->getPacker($packer)->pack($data);
    }

    /**
     * @param mixed $data
     * @param string $packer
     * @return mixed
     * @throws \Swoft\Rpc\Exception\RpcException
     */
    public function unpack($data, string $packer = '')
    {
        return $this->getPacker($packer)->unpack($data);
    }

    /**
     * @param string $packer
     * @return PackerInterface
     * @throws \Swoft\Rpc\Exception\RpcException
     */
    public function getPacker(string $packer = ''): PackerInterface
    {
        $packer = $packer ? : $this->defaultPacker;
        $packers = $this->getPackers();
        if (! isset($packers[$packer]) || ! App::hasBean($packers[$packer])) {
            throw new RpcException(sprintf('Packer %s does not exist', $packer));
        }
        $packerInstance = App::getBean($packers[$packer]);
        if (! ($packerInstance instanceof PackerInterface)) {
            throw new RpcException(sprintf('Packer %s does not implement %s', $packer, PackerInterface::class));
        }
        return $packerInstance;
    }

    /**
     * Format the data for packer
     *
     * @param string $interface
     * @param string $version
     * @param string $method
     * @param array $params
     * @return array
     */
    public function formatData(string $interface, string $version, string $method, array $params): array
    {
        return [
            'interface' => $interface,
            'version' => $version,
            'method' => $method,
            'params' => $params,
            'logid' => RequestContext::getLogid(),
            'spanid' => RequestContext::getSpanid() + 1,
        ];
    }

    /**
     * @param array $data params
     * @return mixed
     * @throws \InvalidArgumentException
     * @throws \Swoft\Rpc\Exception\RpcException
     */
    public function checkData(array $data)
    {
        // Check response format
        if (! isset($data['status']) || ! isset($data['data']) || ! isset($data['msg'])) {
            throw (new RpcResponseException('Response of RPC is invalid'))->setResponse($data);
        }

        // Check response status
        if ($data['status'] !== 200) {
            throw (new RpcStatusException('Status of response is invalid'))->setResponse($data);
        }

        return $data['data'];
    }

    /**
     * Merge default and config packers
     *
     * @return array
     */
    public function getPackers(): array
    {
        return array_merge($this->packers, $this->defaultPackers);
    }

}
