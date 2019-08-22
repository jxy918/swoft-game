<?php declare(strict_types=1);


namespace SwoftTest\Annotation\Unit;


use PHPUnit\Framework\TestCase;
use Swoft\Annotation\AnnotationRegister;
use SwoftTest\Annotation\Testing\Annotation\Mapping\DemoClass;
use SwoftTest\Annotation\Testing\Annotation\Mapping\DemoMethod;
use SwoftTest\Annotation\Testing\Annotation\Mapping\DemoProperty;
use SwoftTest\Annotation\Testing\Annotation\Parser\DemoClassParser;
use SwoftTest\Annotation\Testing\AutoLoader;
use SwoftTest\Annotation\Testing\DemoAnnotation;

class AnnotationTest extends TestCase
{
    public function testAnnotationClass()
    {
        $annotations = AnnotationRegister::getAnnotations();

        $demoAnnotation = $annotations['SwoftTest\Annotation\Testing'][DemoAnnotation::class] ?? [];
        $this->assertTrue(!empty($demoAnnotation));

        $this->assertTrue(isset($demoAnnotation['reflection']));

        $annoClassName = [
            DemoClass::class
        ];

        foreach ($demoAnnotation['annotation'] as $anno) {
            $this->assertTrue(in_array(get_class($anno), $annoClassName));
            if ($anno instanceof DemoClass) {
                $this->assertEquals($anno->getName(), 'demoAnnotation');
            }
        }
    }

    public function testAnnotationProperty()
    {
        $annotations     = AnnotationRegister::getAnnotations();
        $propAnnotations = $annotations['SwoftTest\Annotation\Testing'][DemoAnnotation::class]['properties'] ?? [];

        $this->assertTrue(!empty($propAnnotations));

        foreach ($propAnnotations as $proName => $proAry) {
            $proAnnotation = $proAry['annotation'][0];
            if (!$proAnnotation instanceof DemoProperty) {
                $this->assertTrue(false);
                continue;
            }

            $this->assertEquals($proName, $proAnnotation->getName());
        }
    }

    public function testAnnotationMethod()
    {
        $annotations       = AnnotationRegister::getAnnotations();
        $methodAnnotations = $annotations['SwoftTest\Annotation\Testing'][DemoAnnotation::class]['methods'] ?? [];

        $this->assertTrue(!empty($methodAnnotations));

        foreach ($methodAnnotations as $methodName => $methodAry) {
            $methodAnnotation = $methodAry['annotation'][0];
            if (!$methodAnnotation instanceof DemoMethod) {
                $this->assertTrue(false);
                continue;
            }

            $this->assertEquals($methodName, $methodAnnotation->getName());
        }
    }

    public function testParser()
    {
        $parsers = AnnotationRegister::getParsers();

        $parserClassName = [
            DemoClassParser::class
        ];

        if (empty($parsers)) {
            $this->assertTrue(false);
        }

        foreach ($parsers as $parser) {
            $this->assertTrue(in_array($parser, $parserClassName));
        }
    }

    public function testAutoLoader()
    {
        $autoLoader = AnnotationRegister::getAutoLoader('SwoftTest\\Annotation\\Testing\\');
        $this->assertTrue($autoLoader instanceof AutoLoader);
    }
}