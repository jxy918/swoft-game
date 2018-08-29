<?php
if (! function_exists('translate')) {
    /**
     * I18n translate
     *
     * @param string $key 翻译文件类别，比如xxx.xx/xx
     * @param array  $params   参数
     * @param string $language 当前语言环境
     * @return string
     */
    function translate(string $key, array $params = [], string $language = null)
    {
        /** @var \Swoft\I18n\Translator $translator */
        $translator = bean(\Swoft\I18n\Translator::class);
        return $translator->translate($key, $params, $language);
    }
}
