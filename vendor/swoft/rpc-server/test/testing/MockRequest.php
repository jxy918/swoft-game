<?php declare(strict_types=1);


namespace SwoftTest\Rpc\Server\Testing;


use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Rpc\Server\Request;

/**
 * Class MockRequest
 *
 * @since 2.0
 *
 * @Bean(scope=Bean::PROTOTYPE)
 */
class MockRequest extends Request
{

}