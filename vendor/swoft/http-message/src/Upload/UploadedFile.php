<?php declare(strict_types=1);

namespace Swoft\Http\Message\Upload;

use InvalidArgumentException;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;
use Swoft;
use Swoft\Stdlib\Helper\Dir;
use const UPLOAD_ERR_OK;

/**
 * Class UploadedFile
 *
 * @since 2.0
 */
class UploadedFile implements UploadedFileInterface
{
    /**
     * @var int
     */
    private $size;

    /**
     * @var int
     */
    private $errorCode;

    /**
     * Temp file path
     *
     * @var string
     */
    private $file = '';

    /**
     * @var string
     */
    private $clientFilename;

    /**
     * @var string
     */
    private $clientMediaType;

    /**
     * @var boolean
     */
    private $moved;

    /**
     * @var string
     */
    private $path;

    /**
     * @param string $tmpFile
     * @param int    $size
     * @param int    $errorStatus
     * @param string $clientFilename
     * @param string $clientMediaType
     */
    public function __construct(
        string $tmpFile,
        int $size,
        int $errorStatus,
        string $clientFilename = '',
        string $clientMediaType = ''
    ) {
        $this->setError($errorStatus)->setSize($size)->setClientFilename($clientFilename)
             ->setClientMediaType($clientMediaType);

        $this->isOk() && $this->setFile($tmpFile);
    }

    /**
     * Retrieve a stream representing the uploaded file.
     *
     * This method MUST return a StreamInterface instance, representing the
     * uploaded file. The purpose of this method is to allow utilizing native PHP
     * stream functionality to manipulate the file upload, such as
     * stream_copy_to_stream() (though the result will need to be decorated in a
     * native PHP stream wrapper to work with such functions).
     *
     * If the moveTo() method has been called previously, this method MUST raise
     * an exception.
     *
     * @return void Stream representation of the uploaded file.
     */
    public function getStream()
    {
        // TODO ...
    }

    /**
     * Move the uploaded file to a new location.
     *
     * @inheritdoc
     *
     * @see http://php.net/is_uploaded_file
     * @see http://php.net/move_uploaded_file
     *
     * @param string $targetPath Path to which to move the uploaded file.
     *
     * @throws InvalidArgumentException if the $targetPath specified is invalid.
     * @throws RuntimeException on any error during the move operation, or on
     *     the second or subsequent call to the method.
     */
    public function moveTo($targetPath): void
    {
        $targetPath = Swoft::getAlias($targetPath);
        $this->validateActive();
        if (!$this->isStringNotEmpty($targetPath)) {
            throw new InvalidArgumentException('Invalid path provided for move operation');
        }

        if ($this->file) {
            $this->validateSavePath($targetPath);
            $this->moved = move_uploaded_file($this->file, $targetPath);
        }

        if (!$this->moved) {
            throw new RuntimeException(sprintf('Uploaded file could not be move to %s', $targetPath));
        }
    }

    /**
     * Retrieve the file size.
     *
     * Implementations SHOULD return the value stored in the "size" key of
     * the file in the $_FILES array if available, as PHP calculates this based
     * on the actual size transmitted.
     *
     * @return int|null The file size in bytes or null if unknown.
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Retrieve the error associated with the uploaded file.
     *
     * The return value MUST be one of PHP's UPLOAD_ERR_XXX constants.
     *
     * If the file was uploaded successfully, this method MUST return
     * UPLOAD_ERR_OK.
     *
     * Implementations SHOULD return the value stored in the "error" key of
     * the file in the $_FILES array.
     *
     * @see http://php.net/manual/en/features.file-upload.errors.php
     * @return int One of PHP's UPLOAD_ERR_XXX constants.
     */
    public function getError(): int
    {
        return $this->errorCode;
    }

    /**
     * Retrieve the filename sent by the client.
     *
     * Do not trust the value returned by this method. A client could send
     * a malicious filename with the intention to corrupt or hack your
     * application.
     *
     * Implementations SHOULD return the value stored in the "name" key of
     * the file in the $_FILES array.
     *
     * @return string|null The filename sent by the client or null if none
     *     was provided.
     */
    public function getClientFilename(): string
    {
        return $this->clientFilename;
    }

    /**
     * Retrieve the media type sent by the client.
     *
     * Do not trust the value returned by this method. A client could send
     * a malicious media type with the intention to corrupt or hack your
     * application.
     *
     * Implementations SHOULD return the value stored in the "type" key of
     * the file in the $_FILES array.
     *
     * @return string|null The media type sent by the client or null if none
     *     was provided.
     */
    public function getClientMediaType(): string
    {
        return $this->clientMediaType;
    }

    /**
     * @param int $errorCode
     *
     * @return UploadedFile
     */
    public function setError(int $errorCode): self
    {
        $this->errorCode = $errorCode;
        return $this;
    }

    /**
     * @param string $tmpFile
     *
     * @return UploadedFile
     */
    private function setFile(string $tmpFile): self
    {
        $this->file = $tmpFile;
        return $this;
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'size'     => $this->getSize(),
            'type'     => $this->getClientMediaType(),
            'file'     => $this->file,
            'path'     => $this->path,
            'fileName' => $this->getClientFilename()
        ];
    }

    /**
     * @return bool
     */
    public function isOk(): bool
    {
        return $this->errorCode === UPLOAD_ERR_OK;
    }

    /**
     * @param int $size
     *
     * @return UploadedFile
     */
    public function setSize(int $size): UploadedFile
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @param string $clientFilename
     *
     * @return UploadedFile
     */
    public function setClientFilename(string $clientFilename): UploadedFile
    {
        $this->clientFilename = $clientFilename;
        return $this;
    }

    /**
     * @param string $clientMediaType
     *
     * @return UploadedFile
     */
    public function setClientMediaType(string $clientMediaType): UploadedFile
    {
        $this->clientMediaType = $clientMediaType;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isMoved(): bool
    {
        return $this->moved;
    }

    /**
     * @param mixed $param
     *
     * @return boolean
     */
    private function isStringNotEmpty($param): bool
    {
        return is_string($param) && false === empty($param);
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * check file upload
     */
    public function validateActive(): void
    {
        if (false === $this->isOk()) {
            throw new RuntimeException('Cannot retrieve stream due to upload error');
        }

        if ($this->getSize() <= 0) {
            throw new RuntimeException('Cannot retrieve stream due to upload error');
        }
    }

    /**
     * check file savePath
     *
     * @param $targetPath
     */
    public function validateSavePath($targetPath): void
    {
        Dir::make(dirname($targetPath));

        $this->setPath($targetPath);
    }
}
