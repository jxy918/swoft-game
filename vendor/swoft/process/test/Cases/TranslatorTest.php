<?php

namespace SwoftTest\I18n;

use Swoft\App;
use Swoft\I18n\Translator;


/**
 * @uses      TranslatorTest
 * @version   2018年02月07日
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2018 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class TranslatorTest extends AbstractTestCase
{

    /**
     * @test
     */
    public function translate()
    {
        $translator = new Translator();

        // Init property
        $this->assertEquals('@resources/languages/', $translator->languageDir);
        $reflectClass = new \ReflectionClass(Translator::class);
        $messagesProperty = $reflectClass->getProperty('messages');
        $messagesProperty->setAccessible(true);
        $messages = $messagesProperty->getValue($translator);
        $this->assertEquals([], $messages);

        // Load
        $realLanguagesDir = App::getAlias($translator->languageDir);
        if (! file_exists($realLanguagesDir)) {
            throw new \RuntimeException(sprintf('Testing config $languageDir(%s) is invalid', $realLanguagesDir));
        }
        $loadLanguagesMethod = $reflectClass->getMethod('loadLanguages');
        $loadLanguagesMethod->setAccessible(true);
        $loadLanguagesMethod->invoke($translator, $realLanguagesDir);
        $messagesAfterLoaded = $messagesProperty->getValue($translator);
        $expected = [
            'en'    => [
                'default' => [
                    'title' => 'English title'
                ],
                'msg'     => [
                    'body' => 'This is a message [%s] %d'
                ],
            ],
            'zh-cn' => [
                'default' => [
                    'title' => '中文标题'
                ],
                'msg'     => [
                    'body' => '这是一条消息 [%s] %d'
                ],
            ],
        ];
        $this->assertEquals($expected, $messagesAfterLoaded);

        // Translate
        $enTitle = $translator->translate('default.title', [], 'en');
        $this->assertEquals('English title', $enTitle);
        $enTitle = translate('default.title', [], 'en');
        $this->assertEquals('English title', $enTitle);
        $zhcnTitle = $translator->translate('default.title', [], 'zh-cn');
        $this->assertEquals('中文标题', $zhcnTitle);
        $zhcnTitle = $translator->translate('default.title', ['key' => 'value'], 'zh-cn');
        $this->assertEquals('中文标题', $zhcnTitle);
        $this->assertException(function () use ($translator) {
            $translator->translate('default.title', [], 'zh-hk');
        }, \InvalidArgumentException::class);
        $this->assertException(function () use ($translator) {
            $translator->translate('default', [], 'zh-cn');
        }, \InvalidArgumentException::class);

        $enTitle = $translator->translate('en.default.title', []);
        $this->assertEquals('English title', $enTitle);

        $enBody = $translator->translate('msg.body', ['hello world', 1], 'en');
        $this->assertEquals('This is a message [hello world] 1', $enBody);
        $enBody = $translator->translate('msg.body', ['key' => 'hello world', 'int' => 1], 'en');
        $this->assertEquals('This is a message [hello world] 1', $enBody);
        $enBody = $translator->translate('msg.body', [1, 'hello world'], 'en');
        $this->assertEquals('This is a message [1] 0', $enBody);
        $this->assertError(function () use ($translator) {
            $translator->translate('msg.body', ['hello world'], 'en');
        }, \PHPUnit_Framework_Error_Warning::class);
    }

}