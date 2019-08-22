<?php declare(strict_types=1);

namespace Swoft\Smarty;

use Swoft\Helper\ComposerJSON;
use Swoft\SwoftComponent;

/**
 * Class AutoLoader
 *
 * @since 2.0
 */
class AutoLoader extends SwoftComponent
{
    /**
     * Get namespace and dir
     *
     * @return array
     * [
     *     namespace => dir path
     * ]
     */
    public function getPrefixDirs(): array
    {
        return [__NAMESPACE__ => __DIR__];
    }

    /**
     * Metadata information for the component.
     *
     * @return array
     * @see ComponentInterface::getMetadata()
     */
    public function metadata(): array
    {
        $jsonFile = \dirname(__DIR__) . '/composer.json';

        return ComposerJSON::open($jsonFile)->getMetadata();
    }

    /**
     * @return array
     */
    public function beans(): array
    {
        return [
            'smarty' => [
                'debugging'=>true,
                'caching'=>true,
                'cacheLifetime'=>120,
                'leftDelimiter' => '<!--{',
                'rightDelimiter' => '}-->',
                'templateDir' => '@base/resource/template',
                'compileDir' => '@base/runtime/template_c',
                'cacheDir' => '@base/runtime/cache'
            ],
        ];
    }
}
