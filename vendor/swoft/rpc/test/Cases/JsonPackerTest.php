<?php

namespace SwoftTest\Rpc;

use Swoft\Rpc\Packer\Json\JsonPacker;


/**
 * @uses      JsonPackerTest
 * @version   2018年01月28日
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2018 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class JsonPackerTest extends AbstractTestCase
{

    /**
     * @test
     */
    public function pack()
    {
        $packer = new JsonPacker();
        $data = [
            '1',
            1,
            1.1234,
            bool,
            [
                'a',
                1,
                1.1234
            ],
        ];
        $packedData = $packer->pack($data);
        $this->assertInternalType('string', $packedData);
        $this->assertJson(\json_encode($data), $packedData);
    }

}