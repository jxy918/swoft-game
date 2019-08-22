<?php declare(strict_types=1);


namespace Swoft\Proxy\Ast;

use Exception;
use function file_exists;
use function file_get_contents;
use PhpParser\ErrorHandler;
use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use Swoft\Proxy\Exception\ProxyException;
use Swoft\Stdlib\Helper\ComposerHelper;

/**
 * Class Parser
 *
 * @since 2.0
 */
class Parser
{
    /**
     * @var \PhpParser\Parser
     */
    private $parser;

    /**
     * Node visitors
     *
     * @var NodeVisitor[]
     *
     * @example
     * [
     *     'name' => new NodeVisitor(),
     *     'name' => new NodeVisitor(),
     *     'name' => new NodeVisitor(),
     * ]
     */
    private $nodeVisitors;

    /**
     * Traverser
     *
     * @var NodeTraverser
     */
    private $traverser;

    /**
     * Pretty printer
     *
     * @var Standard
     */
    private $printer;

    /**
     * Parser constructor.
     *
     * @param int        $kind
     * @param Lexer|null $lexer
     * @param array      $parserOptions
     */
    public function __construct(int $kind = ParserFactory::ONLY_PHP7, Lexer $lexer = null, array $parserOptions = [])
    {
        $this->printer   = new Standard();
        $this->traverser = new NodeTraverser();
        $this->parser    = (new ParserFactory())->create($kind, $lexer, $parserOptions);
    }

    /**
     * Parse class by ast
     *
     * @param string            $className
     * @param ErrorHandler|null $errorHandler
     *
     * @return string
     * @throws ProxyException
     */
    public function parse(string $className, ErrorHandler $errorHandler = null): string
    {
        $code = $this->getCodeByClassName($className);
        $ast  = $this->parser->parse($code, $errorHandler);

        // Add visitors
        foreach ($this->nodeVisitors as $name => $nodeVisitor) {
            if ($nodeVisitor instanceof NodeVisitor) {
                $this->traverser->addVisitor($nodeVisitor);
            }
        }

        // New code by traverse
        $nodes = $this->traverser->traverse($ast);

        return $this->printer->prettyPrint($nodes);
    }

    /**
     * Add node visitor
     *
     * @param string      $name
     * @param NodeVisitor $nodeVisitor
     */
    public function addNodeVisitor(string $name, NodeVisitor $nodeVisitor): void
    {
        $this->nodeVisitors[$name] = $nodeVisitor;
    }

    /**
     * Get php code by class name
     *
     * @param string $className
     *
     * @return string
     * @throws ProxyException|Exception
     */
    private function getCodeByClassName(string $className): string
    {
        // Get file by class name
        $file = ComposerHelper::getClassLoader()->findFile($className);

        if (!file_exists($file)) {
            throw new ProxyException(sprintf('%s file is not exist!', $file));
        }

        return file_get_contents($file);
    }
}