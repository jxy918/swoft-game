<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-03-31
 * Time: 19:08
 */

namespace Toolkit\Cli;

use RuntimeException;
use function basename;
use function error_get_last;
use function fclose;
use function file_put_contents;
use function fopen;
use function getcwd;
use function is_resource;
use function stream_context_create;
use function stream_context_set_params;
use function trim;
use const STREAM_NOTIFY_AUTH_REQUIRED;
use const STREAM_NOTIFY_AUTH_RESULT;
use const STREAM_NOTIFY_COMPLETED;
use const STREAM_NOTIFY_CONNECT;
use const STREAM_NOTIFY_FAILURE;
use const STREAM_NOTIFY_FILE_SIZE_IS;
use const STREAM_NOTIFY_MIME_TYPE_IS;
use const STREAM_NOTIFY_PROGRESS;
use const STREAM_NOTIFY_REDIRECTED;
use const STREAM_NOTIFY_RESOLVE;

/**
 * Class Download
 *
 * @package Toolkit\Cli
 */
final class Download
{
    public const PROGRESS_TEXT = 'text';
    public const PROGRESS_BAR  = 'bar';

    /** @var string */
    private $url;

    /** @var string */
    private $saveAs;

    /** @var int */
    private $fileSize;

    /** @var string */
    private $showType;

    /**
     * @param string $url
     * @param string $saveAs
     * @param string $type
     *
     * @return Download
     */
    public static function create(string $url, string $saveAs = '', string $type = self::PROGRESS_TEXT): Download
    {
        return new self($url, $saveAs, $type);
    }

    /**
     * eg: php down.php <http://example.com/file> <localFile>
     *
     * @param string $url
     * @param string $saveAs
     * @param string $type
     *
     * @return Download
     * @throws RuntimeException
     */
    public static function file(string $url, string $saveAs = '', string $type = self::PROGRESS_TEXT): Download
    {
        $d = new self($url, $saveAs, $type);

        return $d->start();
    }

    /**
     * Download constructor.
     *
     * @param string $url
     * @param string $saveAs
     * @param string $type
     */
    public function __construct(string $url, string $saveAs = '', $type = self::PROGRESS_TEXT)
    {
        $this->setUrl($url);
        $this->setSaveAs($saveAs);

        $this->showType = $type === self::PROGRESS_BAR ? self::PROGRESS_BAR : self::PROGRESS_TEXT;
    }

    /**
     * start download
     *
     * @return $this
     * @throws RuntimeException
     */
    public function start(): self
    {
        if (!$this->url) {
            throw new RuntimeException("Please the property 'url' and 'saveAs'.", -1);
        }

        // default save to current dir.
        if (!$save = $this->saveAs) {
            $save = getcwd() . '/' . basename($this->url);
            // reset
            $this->saveAs = $save;
        }

        $ctx = stream_context_create();

        // register stream notification callback
        stream_context_set_params($ctx, [
            'notification' => [$this, 'progressShow']
        ]);

        Cli::write("Download: {$this->url}\nSave As: {$save}\n");

        $fp = fopen($this->url, 'rb', false, $ctx);

        if (is_resource($fp) && file_put_contents($save, $fp)) {
            Cli::write("\nDone!");
        } else {
            $err = error_get_last();
            Cli::stderr("\nErr.rrr..orr...\n {$err['message']}\n", true, -1);
        }

        // close resource
        if (is_resource($fp)) {
            fclose($fp);
        }

        $this->fileSize = null;
        return $this;
    }

    /**
     * @param int    $notifyCode       stream notify code
     * @param int    $severity         severity code
     * @param string $message          Message text
     * @param int    $messageCode      Message code
     * @param int    $transferredBytes Have been transferred bytes
     * @param int    $maxBytes         Target max length bytes
     */
    public function progressShow($notifyCode, $severity, $message, $messageCode, $transferredBytes, $maxBytes): void
    {
        $msg = '';

        switch ($notifyCode) {
            case STREAM_NOTIFY_RESOLVE:
            case STREAM_NOTIFY_AUTH_REQUIRED:
            case STREAM_NOTIFY_COMPLETED:
            case STREAM_NOTIFY_FAILURE:
            case STREAM_NOTIFY_AUTH_RESULT:
                $msg = "NOTIFY: $message(NO: $messageCode, Severity: $severity)";
                /* Ignore */
                break;

            case STREAM_NOTIFY_REDIRECTED:
                $msg = "Being redirected to: $message";
                break;

            case STREAM_NOTIFY_CONNECT:
                $msg = 'Connected ...';
                break;

            case STREAM_NOTIFY_FILE_SIZE_IS:
                $this->fileSize = $maxBytes;
                // print size
                $size = sprintf('%2d', $maxBytes / 1024);
                $msg  = "Got the file size: <info>$size</info> kb";
                break;

            case STREAM_NOTIFY_MIME_TYPE_IS:
                $msg = "Found the mime-type: <info>$message</info>";
                break;

            case STREAM_NOTIFY_PROGRESS:
                if ($transferredBytes > 0) {
                    $this->showProgressByType($transferredBytes);
                }
                break;
        }

        $msg && Cli::write($msg);
    }

    /**
     * @param $transferredBytes
     *
     * @return string
     */
    public function showProgressByType($transferredBytes): string
    {
        if ($transferredBytes <= 0) {
            return '';
        }

        $tfKb = $transferredBytes / 1024;

        if ($this->showType === self::PROGRESS_BAR) {
            $size = $this->fileSize;

            if ($size === null) {
                printf("\rUnknown file size... %2d kb done..", $tfKb);
            } else {
                $length = ceil(($transferredBytes / $size) * 100); // ■ =
                printf("\r[%-100s] %d%% (%2d/%2d kb)", str_repeat('=', $length) . '>', $length, $tfKb, $size / 1024);
            }
        } else {
            printf("\r\rMade some progress, downloaded %2d kb so far", $tfKb);
            //$msg = "Made some progress, downloaded <info>$transferredBytes</info> so far";
        }

        return '';
    }

    /**
     * @return string
     */
    public function getShowType(): string
    {
        return $this->showType;
    }

    /**
     * @param string $showType
     */
    public function setShowType(string $showType): void
    {
        $this->showType = $showType;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = trim($url);
    }

    /**
     * @return string
     */
    public function getSaveAs(): string
    {
        return $this->saveAs;
    }

    /**
     * @param string $saveAs
     */
    public function setSaveAs(string $saveAs): void
    {
        $this->saveAs = trim($saveAs);
    }
}
