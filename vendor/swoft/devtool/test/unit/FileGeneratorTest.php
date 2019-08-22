<?php declare(strict_types=1);

namespace SwoftTest\Devtool\Unit;

use PHPUnit\Framework\TestCase;
use Swoft\Devtool\FileGenerator;

/**
 * Class FileGeneratorTest
 */
class FileGeneratorTest extends TestCase
{
    /**
     * @throws \Leuffen\TextTemplate\TemplateParsingException
     */
    public function testGen()
    {
        $data = [
            'prefix' => '/path',
            'className' => 'DemoController',
            'namespace' => 'App\Controller',
        ];

        $gen = new FileGenerator([
            'tplDir' => __DIR__ . '/res',
        ]);

        $code = $gen
            ->setTplFilename('some')
            ->render($data);

        $this->assertTrue(\strpos($code, $data['prefix']) > 0);
        $this->assertTrue(\strpos($code, $data['className']) > 0);
        $this->assertTrue(\strpos($code, $data['namespace']) > 0);
    }
}
