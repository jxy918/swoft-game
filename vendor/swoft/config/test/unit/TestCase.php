<?php declare(strict_types=1);


namespace SwoftTest\Config\Unit;

use Swoft\Config\Config;

/**
 * Class TestCase
 *
 * @since 2.0
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @return array
     */
    public function getConfigData(): array
    {
        return [
            'key'  => [
                'key2' => 'value2Pro',
                'key3' => 'value2'
            ],
            'key2' => 'value2',
            'key3' => 'value3Pro',
            'data' => [
                'key'  => 'value2Pro',
                'key2' => 'value2ProKey2',
                'key3' => [
                    'key1' => [
                        'key' => 'dataChildKey1'
                    ]
                ]
            ]
        ];
    }
}

