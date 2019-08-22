<?php declare(strict_types=1);


namespace SwoftTest\Bean\Unit;


use PHPUnit\Framework\TestCase;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Container;
use SwoftTest\Bean\Testing\Definition\PrototypeClass;
use SwoftTest\Bean\Testing\Definition\RequestClass;
use SwoftTest\Bean\Testing\Definition\SessionClass;
use SwoftTest\Bean\Testing\Definition\SingletonClass;
use SwoftTest\Bean\Testing\InjectBean;

class DefinitionTest extends TestCase
{
    /**
     * @throws \ReflectionException
     * @throws \Swoft\Bean\Exception\ContainerException
     */
    public function testSingle()
    {
        /* @var SingletonClass $singletoClass */
        $singletoClass = BeanFactory::getBean('singleton');
        $this->assertTrue($singletoClass instanceof SingletonClass);

        $this->beanCase($singletoClass);

        /* @var SingletonClass $singletoClass */
        $singletoClass = BeanFactory::getSingleton('singleton');
        $this->assertTrue($singletoClass instanceof SingletonClass);

        $this->beanCase($singletoClass);
        $this->assertTrue(BeanFactory::hasBean('singleton'));

        /* @var SingletonClass $singletoClass */
        $singletoClass = BeanFactory::getBean('singleton-alias');
        $this->assertTrue($singletoClass instanceof SingletonClass);

        $this->beanCase($singletoClass);

        /* @var SingletonClass $singletoClass */
        $singletoClass = BeanFactory::getSingleton('singleton-alias');
        $this->assertTrue($singletoClass instanceof SingletonClass);

        $this->beanCase($singletoClass);
        $this->assertTrue(BeanFactory::hasBean('singleton-alias'));


        /* @var SingletonClass $singletoClass */
        $singletoClass = BeanFactory::getBean(SingletonClass::class);
        $this->assertTrue($singletoClass instanceof SingletonClass);

        $this->beanCase($singletoClass);

        /* @var SingletonClass $singletoClass */
        $singletoClass = BeanFactory::getSingleton(SingletonClass::class);
        $this->assertTrue($singletoClass instanceof SingletonClass);

        $this->assertTrue(BeanFactory::hasBean(SingletonClass::class));


        $this->beanCase($singletoClass);
    }

    /**
     * @throws \ReflectionException
     * @throws \Swoft\Bean\Exception\ContainerException
     */
    public function testPrototype()
    {
        /* @var SingletonClass $prototypeClass */
        $prototypeClass = BeanFactory::getBean('prototype');
        $this->assertTrue($prototypeClass instanceof PrototypeClass);

        $this->beanCase($prototypeClass);

        /* @var SingletonClass $singletoClass */
        $prototypeClass = BeanFactory::getBean('prototype-alias');
        $this->assertTrue($prototypeClass instanceof PrototypeClass);

        $this->beanCase($prototypeClass);


        /* @var SingletonClass $singletoClass */
        $prototypeClass = BeanFactory::getBean(PrototypeClass::class);
        $this->assertTrue($prototypeClass instanceof PrototypeClass);

        $this->beanCase($prototypeClass);
    }

    /**
     * @throws \ReflectionException
     * @throws \Swoft\Bean\Exception\ContainerException
     */
    public function testRequest()
    {
        $id = uniqid();

        /* @var RequestClass $requestClass */
        $requestClass = BeanFactory::getRequestBean('requestClass', $id);
        $this->beanCase($requestClass);

        /* @var RequestClass $requestClass */
        $requestClass = BeanFactory::getRequestBean('request-alias', $id);
        $this->beanCase($requestClass);

        /* @var RequestClass $requestClass */
        $requestClass = BeanFactory::getRequestBean(RequestClass::class, $id);
        $this->beanCase($requestClass);

        BeanFactory::destroyRequest($id);

        $this->assertTrue(empty(Container::getInstance()->getRequestPool()));
    }

    /**
     * @throws \ReflectionException
     * @throws \Swoft\Bean\Exception\ContainerException
     */
    public function testSession()
    {
        $id = uniqid();

        /* @var SessionClass $sessionClass */
        $sessionClass = BeanFactory::getSessionBean('sessionClass', $id);
        $this->beanCase($sessionClass);

        /* @var SessionClass $sessionClass */
        $sessionClass = BeanFactory::getSessionBean('session-alias', $id);
        $this->beanCase($sessionClass);

        /* @var SessionClass $sessionClass */
        $sessionClass = BeanFactory::getSessionBean(SessionClass::class, $id);
        $this->beanCase($sessionClass);

        BeanFactory::destroySession($id);

        $this->assertTrue(empty(Container::getInstance()->getSessionPool()));
    }

    /**
     * @param SingletonClass|PrototypeClass|RequestClass|SessionClass $singletoClass
     */
    private function beanCase($singletoClass)
    {
        $this->assertEquals($singletoClass->getClassPrivate(), 'classPrivate');
        $this->assertEquals($singletoClass->getClassPublic(), 12);
        $this->assertEquals($singletoClass->getPrivateProp(), 'privateProp');
        $this->assertEquals($singletoClass->getPublicProp(), 12);
        $this->assertEquals($singletoClass->getSetProp(), 'setProp-setter');

        $injectBean = $singletoClass->getInjectBean();
        $this->assertTrue($injectBean instanceof InjectBean);
        $this->assertEquals($injectBean->getData(), 'InjectBeanData-InjectChildBeanData');

        $injectBean = $singletoClass->getInjectBeanAlias();
        $this->assertTrue($injectBean instanceof InjectBean);
        $this->assertEquals($injectBean->getData(), 'InjectBeanData-InjectChildBeanData');

        $injectBean = $singletoClass->getInjectBeanName();
        $this->assertTrue($injectBean instanceof InjectBean);
        $this->assertEquals($injectBean->getData(), 'InjectBeanData-InjectChildBeanData');

        $injectBean = $singletoClass->getInjectBeanClass();
        $this->assertTrue($injectBean instanceof InjectBean);
        $this->assertEquals($injectBean->getData(), 'InjectBeanData-InjectChildBeanData');

        $injectBean = $singletoClass->getDefinitionBean();
        $this->assertTrue($injectBean instanceof InjectBean);
        $this->assertEquals($injectBean->getData(), 'InjectBeanData-InjectChildBeanData');

        $injectBean = $singletoClass->getDefinitionBeanAlias();
        $this->assertTrue($injectBean instanceof InjectBean);
        $this->assertEquals($injectBean->getData(), 'InjectBeanData-InjectChildBeanData');

        $injectBean = $singletoClass->getDefinitionBeanClass();
        $this->assertTrue($injectBean instanceof InjectBean);
        $this->assertEquals($injectBean->getData(), 'InjectBeanData-InjectChildBeanData');
    }
}