<?php declare(strict_types=1);

namespace Swoft\I18n;

use InvalidArgumentException;
use ReflectionException;
use SplFileInfo;
use Swoft;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Exception\ContainerException;
use Swoft\I18n\Exception\I18nException;
use Swoft\Log\Debug;
use Swoft\Stdlib\Helper\ArrayHelper;
use Swoft\Stdlib\Helper\DirectoryHelper;
use function array_keys;
use function implode;
use function is_readable;
use function is_string;
use function pathinfo;
use function sprintf;
use function str_replace;
use function strpos;
use const PATHINFO_EXTENSION;

/**
 * Class I18n
 *
 * @since 2.0
 *
 * @Bean(name="i18n")
 */
class I18n
{
    /**
     * Default lang
     */
    public const DEFAULT_LANG = 'en';

    /**
     * Source languages
     *
     * @var string
     */
    protected $resourcePath = '@resource/language/';

    /**
     * All loaded languages
     *
     * @var []string
     */
    protected $languages = [];

    /**
     * Translation messages
     *
     * @var array
     */
    protected $messages = [];

    /**
     * @var string
     */
    protected $defaultCategory = 'default';

    /**
     * @var string
     */
    protected $defaultLanguage = self::DEFAULT_LANG;

    /**
     * @throws I18nException
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function init(): void
    {
        $sourcePath = Swoft::getAlias($this->resourcePath);
        if (!$sourcePath || !file_exists($sourcePath)) {
            Debug::log('Resource i18n(%s) is not exist!', $sourcePath);
            return;
        }

        if (!is_readable($sourcePath)) {
            throw new I18nException(sprintf(' I18n resource path(%s) is not readable', $sourcePath));
        }

        $this->loadLanguages($sourcePath);
    }

    /**
     * @param string $sourcePath
     *
     * @return void
     */
    protected function loadLanguages(string $sourcePath): void
    {
        $languages = [];
        $files     = DirectoryHelper::recursiveIterator($sourcePath);

        /* @var SplFileInfo $splFileInfo */
        foreach ($files as $splFileInfo) {
            $file = $splFileInfo->__toString();
            if (pathinfo($file, PATHINFO_EXTENSION) !== 'php') {
                continue;
            }

            $messages = str_replace([$sourcePath, '.php'], '', $file);

            [$language, $category] = explode('/', $messages, 2);
            $languages[$language] = 1;

            // load data
            $this->messages[$language][$category] = require $file;
        }

        $this->languages = array_keys($languages);
    }

    /**
     * Translate
     *
     * @param string $key "category.key" or "locale.category.key"
     * @param array  $params
     * @param string $locale If is empty will use default locale
     *
     * @return string
     * @throws InvalidArgumentException
     */
    public function translate(string $key, array $params = [], string $locale = ''): string
    {
        $realKey = $this->getRealKey($key, $locale);
        $message = ArrayHelper::get($this->messages, $realKey);

        // Not exist, return key
        if (!is_string($message)) {
            return $key;
        }

        // Not params
        if (!$params) {
            return $message;
        }

        return $this->formatMessage($message, $params);
    }

    /**
     * @param string $key
     * @param string $locale
     *
     * @return array
     */
    public function get(string $key, string $locale = self::DEFAULT_LANG): array
    {
        $realKey = $this->getRealKey($key, $locale);

        return ArrayHelper::get($this->messages, $realKey);
    }

    /**
     * get messages
     *
     * @return array
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * get languages
     *
     * @return array
     */
    public function getLanguages(): array
    {
        return $this->languages;
    }

    /**
     * @param string $key
     * @param string $locale
     *
     * @return string
     */
    private function getRealKey(string $key, string $locale): string
    {
        $locale = $locale ?: $this->defaultLanguage;

        if (strpos($key, '.') === false) {
            $key = implode('.', [$this->defaultCategory, $key]);
        }

        return implode('.', [$locale, $key]);
    }

    /**
     * Format message
     *
     * @param string $message
     * @param array  $params
     *
     * @return string
     */
    private function formatMessage(string $message, array $params): string
    {
        $search = $replace = [];

        foreach ($params as $name => $value) {
            $search[]  = sprintf('{%s}', $name);
            $replace[] = $value;
        }

        return str_replace($search, $replace, $message);
    }
}
