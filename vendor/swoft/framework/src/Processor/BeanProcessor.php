<?php declare(strict_types=1);

namespace Swoft\Processor;

use InvalidArgumentException;
use ReflectionException;
use Swoft\Annotation\AnnotationRegister;
use Swoft\Annotation\Exception\AnnotationException;
use Swoft\Bean\BeanFactory;
use Swoft\BeanHandler;
use Swoft\Config\Config;
use Swoft\Contract\ComponentInterface;
use Swoft\Contract\DefinitionInterface;
use Swoft\Helper\SwoftHelper;
use Swoft\Log\Helper\CLog;
use Swoft\Stdlib\Helper\ArrayHelper;
use function alias;
use function file_exists;
use function get_class;
use function sprintf;

/**
 * Bean processor
 * @since 2.0
 */
class BeanProcessor extends Processor
{
    /**
     * Handle bean
     *
     * @return bool
     * @throws ReflectionException
     * @throws AnnotationException
     */
    public function handle(): bool
    {
        if (!$this->application->beforeBean()) {
            return false;
        }

        $handler     = new BeanHandler();
        $definitions = $this->getDefinitions();
        $parsers     = AnnotationRegister::getParsers();
        $annotations = AnnotationRegister::getAnnotations();

        BeanFactory::addDefinitions($definitions);
        BeanFactory::addAnnotations($annotations);
        BeanFactory::addParsers($parsers);
        BeanFactory::setHandler($handler);
        BeanFactory::init();

        /* @var Config $config*/
        $config = BeanFactory::getBean('config');

        CLog::info('config path=%s', $config->getPath());
        CLog::info('config env=%s', $config->getEnv());

        $stats = BeanFactory::getStats();

        CLog::info('Bean is initialized(%s)', SwoftHelper::formatStats($stats));

        return $this->application->afterBean();
    }

    /**
     * Get bean definitions
     *
     * @return array
     */
    private function getDefinitions(): array
    {
        // Core beans
        $definitions = [];
        $autoLoaders = AnnotationRegister::getAutoLoaders();

        // get disabled loaders by application
        $disabledLoaders = $this->application->getDisabledAutoLoaders();

        foreach ($autoLoaders as $autoLoader) {
            if (!$autoLoader instanceof DefinitionInterface) {
                continue;
            }

            $loaderClass = get_class($autoLoader);

            // If the component is disabled by user.
            if (isset($disabledLoaders[$loaderClass])) {
                CLog::info('Auto loader(%s) is <cyan>disabled</cyan>, skip handle it', $loaderClass);
                continue;
            }

            // If the component is not enabled.
            if ($autoLoader instanceof ComponentInterface && !$autoLoader->isEnable()) {
                continue;
            }

            $definitions = ArrayHelper::merge($definitions, $autoLoader->beans());
        }

        // Bean definitions
        $beanFile = $this->application->getBeanFile();
        $beanFile = alias($beanFile);

        if (!file_exists($beanFile)) {
            throw new InvalidArgumentException(
                sprintf('The bean config file of %s is not exist!', $beanFile)
            );
        }

        $beanDefinitions = require $beanFile;
        $definitions     = ArrayHelper::merge($definitions, $beanDefinitions);

        return $definitions;
    }
}
