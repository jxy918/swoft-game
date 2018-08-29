<?php

namespace Swoft\HttpClient\Adapter;

use Swoft\App;
use Swoft\Http\Message\Base\Response as BaseResponse;
use Swoft\Http\Message\Testing\Base\Response as TestingBaseResponse;

trait ResponseTrait
{

    /**
     * @return BaseResponse|TestingBaseResponse
     * @throws \RuntimeException
     */
    protected function createResponse()
    {
        return App::$isInTest ? new TestingBaseResponse() : new BaseResponse();
    }
}
