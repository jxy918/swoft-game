<?php

namespace Swoft\Rpc\Client\Service;

use Swoft\App;
use Swoft\Core\AbstractResult;

/**
 * The data result of service
 */
class ServiceDataResult extends AbstractResult
{
    /**
     * @var mixed
     */
    private $fallbackData;

    /**
     * @param array ...$params
     *
     * @return mixed
     * @throws \Throwable
     */
    public function getResult(...$params)
    {
        try {
            $result = $this->connection->recv();
            App::debug('service result =' . json_encode($result));
            $packer = service_packer();
            $result = $packer->unpack($result);
            $data   = $packer->checkData($result);
        } catch (\Throwable $throwable) {
            if (empty($this->fallbackData)) {
                throw $throwable;
            }
            $data = $this->fallbackData;
        }

        $this->release();
        return $data;
    }

    /**
     * @param mixed $fallbackData
     */
    public function setFallbackData($fallbackData)
    {
        $this->fallbackData = $fallbackData;
    }
}