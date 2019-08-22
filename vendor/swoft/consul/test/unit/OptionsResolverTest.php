<?php declare(strict_types=1);


namespace SwoftTest\Consul\Unit;


use PHPUnit\Framework\TestCase;
use Swoft\Consul\Helper\OptionsResolver;

/**
 * Class OptionsResolverTest
 *
 * @since 2.0
 */
class OptionsResolverTest extends TestCase
{
    public function testResolve()
    {
        $options = array(
            'foo'   => 'bar',
            'hello' => 'world',
            'baz'   => 'inga',
        );

        $availableOptions = array(
            'foo',
            'baz',
        );

        $result = OptionsResolver::resolve($options, $availableOptions);

        $expected = array(
            'foo' => 'bar',
            'baz' => 'inga',
        );

        $this->assertSame($expected, $result);
    }

    public function testResolveWithoutMatchingOptions()
    {
        $options = array(
            'hello' => 'world',
        );

        $availableOptions = array(
            'foo',
            'baz',
        );

        $result = OptionsResolver::resolve($options, $availableOptions);
        $this->assertSame(array(), $result);
    }
}