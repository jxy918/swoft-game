<?php declare(strict_types=1);

namespace SwoftTest\I18n\Unit;

use PHPUnit\Framework\TestCase;
use ReflectionException;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Exception\ContainerException;
use Swoft\I18n\I18n;

/**
 * Class I18nTest
 *
 * @since 2.0
 */
class I18nTest extends TestCase
{
    /**
     * Translate test
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function testCommon(): void
    {
        /** @var I18n $i18n */
        $i18n = BeanFactory::getBean('i18n');

        $this->assertNotEmpty($ls = $i18n->getLanguages());
        $this->assertContains('en', $ls);
        $this->assertContains('zh-CN', $ls);
    }

    /**
     * Translate test
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function testTranslate(): void
    {
        /** @var I18n $i18n */
        $i18n = BeanFactory::getBean('i18n');

        $title = $i18n->translate('title', ['name' => 'Swoft']);
        $this->assertEquals($title, 'Hello Swoft');

        $title = $i18n->translate('title', ['name' => 'Swoft'], 'zh-CN');
        $this->assertEquals($title, '你好 Swoft');

        $title = $i18n->translate('msg.body', ['name' => 'Swoft', 'base' => 'Swoole']);
        $this->assertEquals($title, 'Swoft framework，base on Swoole');

        $title = $i18n->translate('msg.body', ['name' => 'Swoft', 'base' => 'Swoole'], 'zh-CN');
        $this->assertEquals($title, 'Swoft框架协程框架，基于Swoole');
    }
}
