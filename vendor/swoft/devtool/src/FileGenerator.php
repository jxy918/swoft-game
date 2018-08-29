<?php

namespace Swoft\Devtool;

use Leuffen\TextTemplate\TextTemplate;
use Swoft\App;

/**
 * Class FileGenerator
 * @package Swoft\Devtool
 */
class FileGenerator
{
    /**
     * @var TextTemplate
     */
    private $parser;

    /**
     * @var string Template file dir.
     */
    protected $tplDir = '';

    /**
     * @var string Template file ext.
     */
    protected $tplExt = '.stub';

    /**
     * @var string Template file name.
     */
    protected $tplFilename = '';

    /**
     * @var array
     */
    protected $data = [];

    /**
     * FileGenerator constructor.
     * @param array $config
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function __construct(array $config = [])
    {
        if ($config) {
            $this->config($config);
        }

        $this->parser = new TextTemplate();

        // usage: {include file="some.tpl"}
        $this->parser->addFunction('include', function (
            $paramArr,
            $command,
            $context,
            $cmdParam
        ) {
            if (!$partFile = $paramArr['file']) {
                return '';
            }

            $firstChar = $partFile[0];

            if ($firstChar === '@') {
                $partFile = App::getAlias($partFile);
            } elseif ($firstChar !== '/') {
                $relativePath = \dirname($this->getTplFile());
                $partFile = \realpath($relativePath . '/' . $partFile);
            }

            return PHP_EOL . \file_get_contents($partFile);
        });
    }

    /**
     * @param array $config
     * @return $this
     */
    public function config(array $config = []): self
    {
        foreach ($config as $name => $value) {
            $setter = 'set' . \ucfirst($name);

            if (\method_exists($this, $setter)) {
                $this->$setter($value);
            } elseif (\property_exists($this, $name)) {
                $this->$name = $value;
            }
        }

        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function addData(array $data): self
    {
        $this->data = \array_merge($this->data, $data);

        return $this;
    }

    /**
     * @param array $data
     * @return bool|int
     * @throws \RuntimeException
     * @throws \Leuffen\TextTemplate\TemplateParsingException
     */
    public function render(array $data = [])
    {
        if ($data) {
            $this->addData($data);
        }

        $tplFile = $this->getTplFile();
        $text = $this->parser
            ->loadTemplate(\file_get_contents($tplFile))
            ->apply($this->data);

        return $text;
    }

    /**
     * @param string $file
     * @param array $data
     * @return bool|int
     * @throws \RuntimeException
     * @throws \Leuffen\TextTemplate\TemplateParsingException
     */
    public function renderAs(string $file, array $data = [])
    {
        if ($data) {
            $this->addData($data);
        }

        $tplFile = $this->getTplFile();
        $text = $this->parser
            ->loadTemplate(\file_get_contents($tplFile))
            ->apply($this->data);

        return \file_put_contents($file, $text) > 0;
    }

    /**
     * @param bool $checkIt
     * @return string
     * @throws \RuntimeException
     */
    public function getTplFile(bool $checkIt = true): string
    {
        $file = $this->tplDir . $this->tplFilename . $this->tplExt;

        if ($checkIt && !\file_exists($file)) {
            throw new \RuntimeException("Template file not exists! File: $file");
        }

        return $file;
    }

    /**
     * @return TextTemplate
     */
    public function getParser(): TextTemplate
    {
        return $this->parser;
    }


    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getTplDir(): string
    {
        return $this->tplDir;
    }

    /**
     * @return string
     */
    public function getTplExt(): string
    {
        return $this->tplExt;
    }

    /**
     * @return string
     */
    public function getTplFilename(): string
    {
        return $this->tplFilename;
    }

    /**
     * @param string $tplFilename
     * @return FileGenerator
     */
    public function setTplFilename(string $tplFilename): self
    {
        $this->tplFilename = $tplFilename;

        return $this;
    }

    /**
     * @param string $tplDir
     * @return FileGenerator
     */
    public function setTplDir(string $tplDir): self
    {
        $this->tplDir = \rtrim($tplDir, '/ ') . '/';

        return $this;
    }

    /**
     * @param string $tplExt
     * @return FileGenerator
     */
    public function setTplExt(string $tplExt): self
    {
        $this->tplExt = '.' . \trim($tplExt, '.');

        return $this;
    }
}
