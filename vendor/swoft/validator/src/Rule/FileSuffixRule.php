<?php declare(strict_types=1);

namespace Swoft\Validator\Rule;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Context\Context;
use Swoft\Http\Message\Upload\UploadedFile;
use Swoft\Validator\Annotation\Mapping\FileSuffix;
use Swoft\Validator\Contract\RuleInterface;
use Swoft\Validator\Exception\ValidatorException;

/**
 * Class FileSuffixRule
 *
 * @since 2.0
 *
 * @Bean(FileSuffix::class)
 */
class FileSuffixRule implements RuleInterface
{
    /**
     * @param array $data
     * @param string $propertyName
     * @param object $item
     * @param null $default
     *
     * @return array
     * @throws ValidatorException
     */
    public function validate(array $data, string $propertyName, $item, $default = null): array
    {
        /* @var FileSuffix $item */
        $values = $item->getSuffix();
        $message = $item->getMessage();
        $message = (empty($message)) ? sprintf('%s file suffix name must be in ', $propertyName) : $message;
        $files = Context::mustGet()->getRequest()->getUploadedFiles();
        $suffixName = function ($file) {
            return pathinfo($file, PATHINFO_EXTENSION);
        };
        foreach ($files as $field) {
            if (!is_array($field)) {
                /* @var UploadedFile $field */
                if (!in_array(strtolower($suffixName($field->getClientFilename())), $values)) {
                    throw new ValidatorException($message);
                }
            } else {
                foreach ($field as $file) {
                    /* @var UploadedFile $file */
                    if (!in_array(strtolower($suffixName($file->getClientFilename())), $values)) {
                        throw new ValidatorException($message);
                    }
                }
            }
        }
        return $data;
    }
}
