<?php declare(strict_types=1);

namespace Swoft\Validator\Rule;

use Psr\Http\Message\UploadedFileInterface;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Context\Context;
use Swoft\Validator\Annotation\Mapping\File;
use Swoft\Validator\Contract\RuleInterface;
use Swoft\Validator\Exception\ValidatorException;

/**
 * Class IsFileRule
 *
 * @since 2.0
 *
 * @Bean(File::class)
 */
class FileRule implements RuleInterface
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
        $request = Context::mustGet()->getRequest();
        $filesFields = $request->getUploadedFiles();

        /* @var File $item */
        $message = $item->getMessage();
        $message = (empty($message)) ? sprintf('%s must be file!', $propertyName) : $message;
        if (!isset($filesFields[$propertyName])) {
            $message = (empty($message)) ? sprintf('%s must exist!', $propertyName) : $message;
            throw new ValidatorException($message);
        }
        if (isset($data[$propertyName])) {
            throw new ValidatorException($message);
        }
        foreach ($filesFields as $field) {
            if (!is_array($field)) {
                if (!$field instanceof UploadedFileInterface) {
                    throw new ValidatorException($message);
                }
            } else {
                foreach ($field as $file) {
                    if (!$file instanceof UploadedFileInterface) {
                        throw new ValidatorException($message);
                    }
                }
            }
        }
        return $data;
    }
}
