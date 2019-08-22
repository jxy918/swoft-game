<?php declare(strict_types=1);

namespace Swoft\Validator\Rule;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Context\Context;
use Swoft\Http\Message\Upload\UploadedFile;
use Swoft\Validator\Annotation\Mapping\FileSize;
use Swoft\Validator\Contract\RuleInterface;
use Swoft\Validator\Exception\ValidatorException;

/**
 * Class FileSizeRule
 *
 * @since 2.0
 *
 * @Bean(FileSize::class)
 */
class FileSizeRule implements RuleInterface
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
        /* @var FileSize $item */
        $size = $item->getSize();
        $message = $item->getMessage();
        $message = (empty($message)) ? sprintf('%s file oversize', $propertyName) : $message;
        $files = Context::mustGet()->getRequest()->getUploadedFiles();
        foreach ($files as $field) {
            if (!is_array($field)) {
                /* @var UploadedFile $field */
                if ($field->getSize() > $size) {
                    throw new ValidatorException($message);
                }
            } else {
                foreach ($field as $file) {
                    /* @var UploadedFile $field */
                    if ($file->getSize() > $size) {
                        throw new ValidatorException($message);
                    }
                }
            }
        }
        return $data;
    }
}
