<?php declare(strict_types=1);

namespace Swoft\Console\Output;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Console\Concern\FormatOutputAwareTrait;
use Swoft\Console\Console;
use Swoft\Console\Contract\OutputInterface;
use Swoft\Console\Helper\Show;
use Swoft\Console\Style\Style;
use Toolkit\Cli\Cli;
use const STDERR;
use const STDOUT;

/**
 * Class Output
 * @Bean("output")
 */
class Output implements OutputInterface
{
    use FormatOutputAwareTrait;

    /**
     * Normal output stream
     *
     * @var resource|null
     */
    protected $outputStream = STDOUT;

    /**
     * Error output stream
     *
     * @var null|resource
     */
    protected $errorStream = STDERR;

    /**
     * Console window (font/background) color addition processing
     *
     * @var Style
     */
    protected $style;

    /**
     * Output constructor.
     *
     * @param null|resource $outputStream
     */
    public function __construct($outputStream = null)
    {
        if ($outputStream) {
            $this->outputStream = $outputStream;
        }

        $this->getStyle();
    }

    /***************************************************************************
     * Output buffer
     ***************************************************************************/

    /**
     * start buffering
     */
    public function startBuffer(): void
    {
        Console::startBuffer();
    }

    /**
     * clear buffering
     */
    public function clearBuffer(): void
    {
        Console::clearBuffer();
    }

    /**
     * stop buffering and flush buffer text
     * {@inheritdoc}
     *
     * @see Console::stopBuffer()
     */
    public function stopBuffer(bool $flush = true, $nl = false, $quit = false, array $opts = []): void
    {
        Console::stopBuffer($flush, $nl, $quit, $opts);
    }

    /**
     * stop buffering and flush buffer text
     * {@inheritdoc}
     */
    public function flush(bool $nl = false, $quit = false, array $opts = []): void
    {
        Console::flushBuffer($nl, $quit, $opts);
    }

    /***************************************************************************
     * Output Message
     ***************************************************************************/

    /**
     * Read input information
     *
     * @param string $question 若不为空，则先输出文本
     * @param bool   $nl       true 会添加换行符 false 原样输出，不添加换行符
     *
     * @return string
     */
    public function read($question = null, $nl = false): string
    {
        return Console::read($question, $nl);
    }

    /**
     * Write a message to standard error output stream.
     *
     * @param string  $text
     * @param boolean $nl True (default) to append a new line at the end of the output string.
     *
     * @return int
     */
    public function stderr(string $text = '', $nl = true): int
    {
        return Console::write($text, $nl, [
            'steam' => $this->errorStream,
        ]);
    }

    /***************************************************************************
     * Getter/Setter
     ***************************************************************************/

    /**
     * @return Style
     */
    public function getStyle(): Style
    {
        if (!$this->style) {
            $this->style = Show::getStyle();
        }

        return $this->style;
    }

    /**
     * @return bool
     */
    public function supportColor(): bool
    {
        return Cli::isSupportColor();
    }

    /**
     * Method to get property ErrorStream
     *
     * @return resource|null
     */
    public function getOutputStream()
    {
        return $this->outputStream;
    }

    /**
     * Method to set property outputStream
     *
     * @param $outStream
     *
     * @return $this
     */
    public function setOutputStream($outStream): self
    {
        $this->outputStream = $outStream;

        return $this;
    }

    /**
     * Method to get property ErrorStream
     *
     * @return resource|null
     */
    public function getErrorStream()
    {
        return $this->errorStream;
    }

    /**
     * Method to set property errorStream
     *
     * @param $errorStream
     *
     * @return $this
     */
    public function setErrorStream($errorStream): self
    {
        $this->errorStream = $errorStream;

        return $this;
    }
}
