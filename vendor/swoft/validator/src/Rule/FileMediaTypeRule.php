<?php declare(strict_types=1);

namespace Swoft\Validator\Rule;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Context\Context;
use Swoft\Http\Message\Upload\UploadedFile;
use Swoft\Validator\Annotation\Mapping\FileMediaType;
use Swoft\Validator\Contract\RuleInterface;
use Swoft\Validator\Exception\ValidatorException;

/**
 * Class FileMediaTypeRule
 *
 * @since 2.0
 *
 * @Bean(FileMediaType::class)
 */
class FileMediaTypeRule implements RuleInterface
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
        /* @var FileMediaType $item */
        $values = $item->getMediaType();
        $message = $item->getMessage();
        $message = (empty($message)) ? sprintf('%s file media type must be  exists in media type',
            $propertyName) : $message;
        $files = Context::mustGet()->getRequest()->getUploadedFiles();
        foreach ($files as $field) {
            if (!is_array($field)) {
                /* @var UploadedFile $field */

                if (!in_array($field->getClientMediaType(), $values)) {

                    throw new ValidatorException($message);
                }
            } else {
                foreach ($field as $file) {
                    /* @var UploadedFile $field */
                    if (!in_array($file->getClientMediaType(), $values)) {
                        throw new ValidatorException($message);
                    }
                }
            }
        }
        return $data;
    }
}
