<?php declare(strict_types=1);

namespace Swoft\Contract;

use Swoft\Annotation\Contract\LoaderInterface;

/**
 * Interface ComponentInterface
 *
 * @since 2.0
 */
interface ComponentInterface extends DefinitionInterface, LoaderInterface
{
    public const DEFAULT_META = [
        'name'        => '',
        'title'       => '',
        'version'     => '1.0.0',
        'homepage'    => '',
        'description' => '',
    ];

    /**
     * Disable or enable this component.
     *
     * @return bool
     */
    public function isEnable(): bool;

    /**
     * Metadata information for the component
     *
     * @return array
     * [
     *  'name'        => 'user/package', // same composer.json -> name
     *  'title'       => 'my component',
     *  'version'     => '1.0.0',
     *  'authors'     => [
     *      [
     *          'name' => 'tom',
     *          'homepage' => 'https://github.com/tom'
     *      ]
     *  ],
     *  'keywords'    => ['one', 'two'],
     *  'createAt'    => '2019.02.12',
     *  'updateAt'    => '2019.04.12',
     *  'description' => 'description for the component',
     *  'homepage'    => 'https://github.com/inhere/some-component',
     * ]
     */
    public function getMetadata(): array;
}
