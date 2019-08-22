<?php declare(strict_types=1);

namespace Swoft\Devtool\Model\Logic;

use Leuffen\TextTemplate\TemplateParsingException;
use function strpos;
use Swoft;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Container;
use Swoft\Bean\Definition\ObjectDefinition;
use Swoft\Devtool\FileGenerator;

/**
 * Class PhpstormLogic
 *
 * @since 2.0
 *
 * @Bean(name="metaLogic")
 */
class MetaLogic
{
    /**
     * @var string
     */
    private $tplDir = '';

    /**
     * @var string
     */
    private $overrideTplName = 'override';

    /**
     * @var string
     */
    private $phpstormTplName = 'phpstorm';

    /**
     * @var string
     */
    private $metaFile = '@base/.phpstorm.meta.php';

    /**
     * Init
     */
    public function init(): void
    {
        $this->tplDir = __DIR__ . '/../../../resource/template';
    }

    /**
     * @throws TemplateParsingException
     */
    public function generate(): void
    {
        $overrideStub = $this->generateBean();
        $file         = Swoft::getAlias($this->metaFile);

        $config = [
            'tplFilename' => $this->phpstormTplName,
            'tplDir'      => $this->tplDir,
        ];

        $data = [
            'overrides' => $overrideStub
        ];

        $gen = new FileGenerator($config);
        $gen->renderAs($file, $data);
    }

    /**
     * @return string
     * @throws TemplateParsingException
     */
    protected function generateBean(): string
    {
        $indentSpace    = '            ';
        $overrideStub   = $itemStub = '';
        $objDefinitions = Container::getInstance()->getObjectDefinitions();

        foreach ($objDefinitions as $beanName => $definition) {
            if (!$definition instanceof ObjectDefinition) {
                continue;
            }

            $alias     = $definition->getAlias();
            $className = $definition->getClassName();

            // It's should ignore class
            if ($this->isShouldIgnore($className)) {
                continue;
            }

            $itemStub .= sprintf("%s'%s' => \%s::class,%s", $indentSpace, $beanName, $className, PHP_EOL);

            if ($beanName !== $className) {
                $itemStub .= sprintf("%s'%s' => \%s::class,%s", $indentSpace, $className, $className, PHP_EOL);
            }

            if (!empty($alias)) {
                $itemStub .= sprintf("%s'%s' => \%s::class,%s", $indentSpace, $alias, $className, PHP_EOL);
            }
        }

        $functions = [
            '\bean(0)',
            '\Swoft::getBean()',
            '\Swoft\Bean\BeanFactory::getBean(0)',
            '\Swoft\Bean\Container::getInstance()->get(0)',
        ];

        foreach ($functions as $func) {
            $config = [
                'tplFilename' => $this->overrideTplName,
                'tplDir'      => $this->tplDir,
            ];

            $data = [
                'name' => $func,
                'item' => $itemStub
            ];

            $generator    = new FileGenerator($config);
            $overrideStub .= $generator->render($data);
        }

        return $overrideStub;
    }

    /**
     * Ignore listener, middleware, http controller, console command class
     *
     * @param string $className
     *
     * @return bool
     */
    private function isShouldIgnore(string $className): bool
    {
        if (strpos($className, '\\Listener\\') > 0) {
            return true;
        }

        if (strpos($className, '\\Middleware\\') > 0) {
            return true;
        }

        if (strpos($className, '\\Controller\\') > 0) {
            return true;
        }

        if (strpos($className, '\\Command\\') > 0) {
            return true;
        }

        if (strpos($className, '\\Validator\\') > 0) {
            return true;
        }

        return false;
    }
}
