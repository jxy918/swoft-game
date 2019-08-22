<?php

use Swoft\Bean\Annotation\Mapping\Bean;
use SwoftTest\Bean\Testing\Contract\PrimaryInterface;
use SwoftTest\Bean\Testing\Definition\InterfaceBeanDefinition;
use SwoftTest\Bean\Testing\Definition\ManyInstance;
use SwoftTest\Bean\Testing\Definition\PrototypeClass;
use SwoftTest\Bean\Testing\Definition\RequestClass;
use SwoftTest\Bean\Testing\Definition\SessionClass;
use SwoftTest\Bean\Testing\Definition\SingletonClass;
use SwoftTest\Bean\Testing\InjectBean;

return [
    'singleton'       => [
        'class'               => SingletonClass::class,
        'privateProp'         => 'privateProp',
        'publicProp'          => 12,
        'classPrivate'        => 'classPrivate',
        'classPublic'         => 12,
        'setProp'             => 'setProp',
        'definitionBean'      => \bean('injectBean'),
        'definitionBeanAlias' => \bean('injectBeanAlias'),
        'definitionBeanClass' => \bean(InjectBean::class),
        '__option'            => [
            'scope' => Bean::SINGLETON,
            'alias' => 'singleton-alias'
        ]
    ],
    'prototype'       => [
        'class'               => PrototypeClass::class,
        'privateProp'         => 'privateProp',
        'publicProp'          => 12,
        'classPrivate'        => 'classPrivate',
        'classPublic'         => 12,
        'setProp'             => 'setProp',
        'definitionBean'      => \bean('injectBean'),
        'definitionBeanAlias' => \bean('injectBeanAlias'),
        'definitionBeanClass' => \bean(InjectBean::class),
        '__option'            => [
            'alias' => 'prototype-alias'
        ]
    ],
    'requestClass'    => [
        'class'               => RequestClass::class,
        'privateProp'         => 'privateProp',
        'publicProp'          => 12,
        'classPrivate'        => 'classPrivate',
        'classPublic'         => 12,
        'setProp'             => 'setProp',
        'definitionBean'      => \bean('injectBean'),
        'definitionBeanAlias' => \bean('injectBeanAlias'),
        'definitionBeanClass' => \bean(InjectBean::class),
        '__option'            => [
            'alias' => 'request-alias'
        ]
    ],
    'sessionClass'    => [
        'class'               => SessionClass::class,
        'privateProp'         => 'privateProp',
        'publicProp'          => 12,
        'classPrivate'        => 'classPrivate',
        'classPublic'         => 12,
        'setProp'             => 'setProp',
        'definitionBean'      => \bean('injectBean'),
        'definitionBeanAlias' => \bean('injectBeanAlias'),
        'definitionBeanClass' => \bean(InjectBean::class),
        '__option'            => [
            'alias' => 'session-alias'
        ]
    ],
    'manyOneInstance' => [
        'class' => ManyInstance::class,
    ],
    'manyTwoInstance' => [
        'class' => ManyInstance::class,
    ],
    'two.many'        => [
        'class' => ManyInstance::class,
    ],
    'commaNameClass'  => [
        'manyInstance2' => \bean('two.many')
    ],
    'testTypeBean'    => [
        'stringVar'  => 1,
        'intVar'     => '1',
        'integerVar' => '2',
        'floatVar'   => '1.1',
        'doubleVar'  => '1.2',
    ],
    'interfaceBeanDefinition' => [
        'class' => InterfaceBeanDefinition::class,
        'pinterface' => \bean(PrimaryInterface::class)
    ]
];