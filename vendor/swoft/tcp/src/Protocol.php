<?php declare(strict_types=1);

namespace Swoft\Tcp;

use Swoft;
use Swoft\Stdlib\Helper\ObjectHelper;
use Swoft\Tcp\Contract\PackerInterface;
use Swoft\Tcp\Exception\ProtocolException;
use Swoft\Tcp\Packer\JsonPacker;
use Swoft\Tcp\Packer\PhpPacker;
use Swoft\Tcp\Packer\SimpleTokenPacker;
use function array_keys;
use function array_merge;
use function pack;
use function rtrim;
use function strlen;
use function substr;
use function unpack;

/**
 * Class Protocol
 *
 * @since 2.0.3
 */
class Protocol
{
    // Split package type
    public const EOF_SPLIT = 'EOF';
    public const LEN_SPLIT = 'LEN';

    /**
     * Default EOF chars
     */
    public const DEFAULT_EOF = "\r\n\r\n";

    /**
     * Use for pack data for length type
     */
    public const HEADER_PACK_FORMAT = 'NNNN';

    /**
     * Use for unpack data for length type
     */
    public const HEADER_UNPACK_FORMAT = 'Nuid/Ntype/Nlen/Nserid';

    /**
     * The default packers
     */
    public const DEFAULT_PACKERS = [
        PhpPacker::TYPE         => PhpPacker::class,
        JsonPacker::TYPE        => JsonPacker::class,
        // For test or demo
        SimpleTokenPacker::TYPE => SimpleTokenPacker::class,
    ];

    /**
     * The default packer type name
     *
     * @var string
     */
    private $type = SimpleTokenPacker::TYPE;

    /**
     * The available data packers
     *
     * @var array
     * [
     *  type name => packet bean name(PackerInterface)
     * ]
     */
    private $packers;

    /**
     * @var int
     */
    private $packageMaxLength = 81920;

    // -------------- use package eof check --------------

    /**
     * Open package EOF check
     *
     * swoole.setting => [
     *  'package_max_length' => 81920,
     *  'open_eof_check'     => true,
     *  'open_eof_split'     => true,
     *  'package_eof'        => "\r\n\r\n",
     * ]
     *
     * @link https://wiki.swoole.com/wiki/page/285.html
     * @var bool
     */
    private $openEofCheck = true;

    /**
     * @var bool
     */
    private $openEofSplit = false;

    /**
     * @var string
     */
    private $packageEof = self::DEFAULT_EOF;

    // -------------- use package length check --------------

    /**
     * Open package length check
     *
     * swoole.setting => [
     *  'package_max_length'    => 81920,
     *  'open_length_check'     => true,
     *  'package_length_type'   => 'N',
     *  'package_length_offset' => 8,
     *  'package_body_offset'   => 16,
     * ]
     *          8-11 length
     *            |
     * [0===4===8===12===16|BODY...]
     *
     * @link https://wiki.swoole.com/wiki/page/287.html
     * @link https://github.com/matyhtf/framework/blob/3.0/src/core/Protocol/RPCServer.php
     * @var bool
     */
    private $openLengthCheck = false;

    /**
     * @link https://wiki.swoole.com/wiki/page/463.html
     * @link https://www.php.net/manual/en/function.pack.php
     * @var string
     */
    private $packageLengthType = 'N';

    /**
     * The Nth byte is the value of the packet length
     *
     * @var int
     */
    private $packageLengthOffset = 8;

    /**
     * The first few bytes start to calculate the length
     *
     * @var int
     */
    private $packageBodyOffset = 16;

    /**
     * Class constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        // Ensure packers always available
        $this->packers = self::DEFAULT_PACKERS;

        ObjectHelper::init($this, $config);
    }

    /*********************************************************************
     * (Un)Packing data for server use
     ********************************************************************/

    /**
     * Unpacking the client request data as an [Package]
     *
     * @param string $data
     *
     * @return Package
     */
    public function unpack(string $data): Package
    {
        [$head, $body] = $this->unpackData($data);

        return $this->getPacker((string)$head['type'])->decode($body);
    }

    /**
     * Packing [Response] to string for response client
     *
     * @param Response $response
     *
     * @return string
     */
    public function packResponse(Response $response): string
    {
        $body = $this->getPacker()->encodeResponse($response);

        return $this->packBody($body);
    }

    /*********************************************************************
     * (Un)Packing data for client use
     ********************************************************************/

    /**
     * Unpacking the server response data as an [Response]
     *
     * @param string $data
     *
     * @return Response
     */
    public function unpackResponse(string $data): Response
    {
        [$head, $body] = $this->unpackData($data);

        return $this->getPacker((string)$head['type'])->decodeResponse($body);
    }

    /**
     * Packing [Package] to string for request server
     *
     * @param Package $package
     *
     * @return string
     */
    public function pack(Package $package): string
    {
        $body = $this->getPacker()->encode($package);

        return $this->packBody($body);
    }

    /*********************************************************************
     * Simple pack methods(For quick test)
     ********************************************************************/

    /**
     * @param string $body
     *
     * @return string
     */
    public function packBody(string $body): string
    {
        // Use eof check
        if ($this->openEofCheck) {
            return $body . $this->packageEof;
        }

        // Use length check
        $format = self::HEADER_PACK_FORMAT;

        // TODO
        // Args sort please see self::HEADER_UNPACK_FORMAT
        // return pack($format, 0, $this->type, strlen($body), 0) . $body;
        return pack($format, 0, 0, strlen($body), 0) . $body;
    }

    /**
     * @param string $data
     *
     * @return array Return like: [head(null|array), body(string)]
     */
    public function unpackData(string $data): array
    {
        // Use eof check
        if ($this->openEofCheck) {
            return [
                ['type' => $this->type], // head
                rtrim($data, $this->packageEof), // body
            ];
        }

        // Use length check
        $format  = self::HEADER_UNPACK_FORMAT;
        $headLen = $this->packageBodyOffset;

        // Like: ['type' => 'json', 'len' => 254, ]
        $headers = (array)unpack($format, substr($data, 0, $headLen));

        return [
            $headers,
            substr($data, $headLen), // body
        ];
    }

    /*********************************************************************
     * Getter/Setter methods
     ********************************************************************/

    /**
     * Get data packer instance
     *
     * @param string $type
     *
     * @return PackerInterface
     */
    public function getPacker(string $type = ''): PackerInterface
    {
        $class  = $this->getPackerClass($type ?: $this->type);
        $packer = Swoft::getSingleton($class);

        if (!$packer instanceof PackerInterface) {
            throw new ProtocolException("The data packer '{$class}' must be implements PackerInterface!");
        }

        return $packer;
    }

    /**
     * @param string $type
     *
     * @return string
     */
    public function getPackerClass(string $type = ''): string
    {
        $type = $type ?: $this->type;

        if (!isset($this->packers[$type])) {
            throw new ProtocolException("The data packer(type: $type) is not exist!");
        }

        return $this->packers[$type];
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        // Use EOF check
        if ($this->openEofCheck) {
            return [
                'open_eof_check'     => true,
                'open_eof_split'     => $this->openEofSplit,
                'package_eof'        => $this->packageEof,
                'package_max_length' => $this->packageMaxLength,
                'open_length_check'  => false,
            ];
        }

        // Use length check
        return [
            'open_length_check'     => true,
            'package_length_type'   => $this->packageLengthType,
            'package_length_offset' => $this->packageLengthOffset,
            'package_body_offset'   => $this->packageBodyOffset,
            'package_max_length'    => $this->packageMaxLength,
            'open_eof_check'        => false,
        ];
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        if ($type) {
            $this->type = $type;
        }
    }

    /**
     * @return string
     */
    public function getSplitType(): string
    {
        return $this->openEofCheck ? self::EOF_SPLIT : self::LEN_SPLIT;
    }

    /**
     * @param string $type
     * @param string $packerClass
     */
    public function setPacker(string $type, string $packerClass): void
    {
        $this->packers[$type] = $packerClass;
    }

    /**
     * @return array
     */
    public function getPackers(): array
    {
        return $this->packers;
    }

    /**
     * @param array $packers
     */
    public function setPackers(array $packers): void
    {
        $this->packers = array_merge($this->packers, $packers);
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function isValidType(string $type): bool
    {
        return isset($this->packers[$type]);
    }

    /**
     * @return array
     */
    public function getPackerNames(): array
    {
        return array_keys($this->packers);
    }

    /**
     * @return bool
     */
    public function isOpenLengthCheck(): bool
    {
        return $this->openLengthCheck;
    }

    /**
     * @param bool $openLengthCheck
     */
    public function setOpenLengthCheck($openLengthCheck): void
    {
        $this->openLengthCheck = (bool)$openLengthCheck;

        $this->openEofCheck = !$this->openLengthCheck;
    }

    /**
     * @return string
     */
    public function getPackageLengthType(): string
    {
        return $this->packageLengthType;
    }

    /**
     * @param string $packageLengthType
     */
    public function setPackageLengthType(string $packageLengthType): void
    {
        $this->packageLengthType = $packageLengthType;
    }

    /**
     * @return int
     */
    public function getPackageLengthOffset(): int
    {
        return $this->packageLengthOffset;
    }

    /**
     * @param int $packageLengthOffset
     */
    public function setPackageLengthOffset(int $packageLengthOffset): void
    {
        $this->packageLengthOffset = $packageLengthOffset;
    }

    /**
     * @return int
     */
    public function getPackageBodyOffset(): int
    {
        return $this->packageBodyOffset;
    }

    /**
     * @param int $packageBodyOffset
     */
    public function setPackageBodyOffset(int $packageBodyOffset): void
    {
        $this->packageBodyOffset = $packageBodyOffset;
    }

    /**
     * @return bool
     */
    public function isOpenEofCheck(): bool
    {
        return $this->openEofCheck;
    }

    /**
     * @param bool $openEofCheck
     */
    public function setOpenEofCheck($openEofCheck): void
    {
        $this->openEofCheck = (bool)$openEofCheck;

        $this->openLengthCheck = !$this->openEofCheck;
    }

    /**
     * @return bool
     */
    public function isOpenEofSplit(): bool
    {
        return $this->openEofSplit;
    }

    /**
     * @param bool $openEofSplit
     */
    public function setOpenEofSplit($openEofSplit): void
    {
        $this->openEofSplit = (bool)$openEofSplit;
    }

    /**
     * @return string
     */
    public function getPackageEof(): string
    {
        return $this->packageEof;
    }

    /**
     * @param string $packageEof
     */
    public function setPackageEof(string $packageEof): void
    {
        $this->packageEof = $packageEof;
    }

    /**
     * @return int
     */
    public function getPackageMaxLength(): int
    {
        return $this->packageMaxLength;
    }

    /**
     * @param int $packageMaxLength
     */
    public function setPackageMaxLength(int $packageMaxLength): void
    {
        $this->packageMaxLength = $packageMaxLength;
    }
}
