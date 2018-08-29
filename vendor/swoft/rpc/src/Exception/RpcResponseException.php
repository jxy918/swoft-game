<?php

namespace Swoft\Rpc\Exception;


class RpcResponseException extends RpcException
{

    /**
     * @var mixed
     */
    protected $response;

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param mixed $response
     * @return RpcResponseException
     */
    public function setResponse($response): self
    {
        $this->response = $response;
        return $this;
    }

}