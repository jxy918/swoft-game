<?php declare(strict_types=1);


namespace Swoft\Rpc\Client\Proxy\Ast;


use function array_unshift;
use function sprintf;
use Swoft\Proxy\Ast\Visitor\Visitor;
use Swoft\Rpc\Client\Concern\ServiceTrait;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use function uniqid;

/**
 * Class ProxyVisitor
 *
 * @since 2.0
 */
class ProxyVisitor extends Visitor
{
    /**
     * Namespace
     *
     * @var string
     */
    private $namespace = '';

    /**
     * New class name
     *
     * @var string
     */
    private $proxyId;

    /**
     * Origin class name
     *
     * @var string
     */
    private $originalInterfaceName = '';

    /**
     * Proxy class name without namespace
     *
     * @var string
     */
    private $proxyName = '';

    /**
     * Aop class name
     *
     * @var string
     */
    private $serviceTrait;

    /**
     * ProxyVisitor constructor.
     *
     * @param string $proxyId
     * @param string $TraitClassName
     */
    public function __construct(string $proxyId = '', string $TraitClassName = ServiceTrait::class)
    {
        $this->serviceTrait = $TraitClassName;
        $this->proxyId      = $proxyId ?: uniqid();
    }

    /**
     * Enter node
     *
     * @param Node $node
     *
     * @return int|Node|null]
     */
    public function enterNode(Node $node)
    {
        // Namespace for proxy class name
        if ($node instanceof Node\Stmt\Namespace_) {

            $this->namespace = $node->name->toString();
            return null;
        }

        // Origin interface node
        if ($node instanceof Node\Stmt\Interface_) {
            $name                        = $node->name->toString();
            $this->proxyName             = sprintf('%s_%s', $name, $this->proxyId);
            $this->originalInterfaceName = sprintf('%s\\%s', $this->namespace, $name);

            return null;
        }

        return null;
    }

    /**
     * Leave node
     *
     * @param Node $node
     *
     * @return int|Node|Node[]|null
     */
    public function leaveNode(Node $node)
    {
        // Parse new interface node
        if ($node instanceof Node\Stmt\Interface_) {
            $newClassNodes = [
                'flags'      => 0,
                'stmts'      => $node->stmts,
                'implements' => [
                    new Node\Name('\\' . $this->originalInterfaceName)
                ],
            ];

            return new Node\Stmt\Class_($this->proxyName, $newClassNodes);
        }

        // Parse class method and rewrite public and protected
        if ($node instanceof ClassMethod) {
            if ($node->isPrivate() || $node->isStatic()) {
                return NodeTraverser::REMOVE_NODE;
            }

            return $this->proxyMethod($node);
        }

        return $node;
    }

    /**
     * After traverse
     *
     * @param array $nodes
     *
     * @return array|Node[]|null
     */
    public function afterTraverse(array $nodes)
    {
        $nodeFinder = new NodeFinder();

        /** @var Node\Stmt\Class_ $classNode */
        $classNode = $nodeFinder->findFirstInstanceOf($nodes, Node\Stmt\Class_::class);

        $traitNode          = $this->getTraitNode();
        $originalMethodNode = $this->getOriginalClassNameMethodNode();

        array_unshift($classNode->stmts, $traitNode, $originalMethodNode);
        return $nodes;
    }

    /**
     * Proxy method
     *
     * @param ClassMethod $node
     *
     * @return ClassMethod
     */
    private function proxyMethod(ClassMethod $node): ClassMethod
    {
        $methodName = $node->name->toString();

        // TODO Origin method params
        $params = [];
        foreach ($node->params as $key => $param) {
            $params[] = $param;
        }

        // Proxy method params
        $newParams = [
            new Node\Scalar\String_($this->originalInterfaceName),
            new Node\Scalar\String_($methodName),
            new Node\Expr\FuncCall(new Node\Name('func_get_args')),
        ];

        // Proxy method call
        $proxyCall = new Node\Expr\MethodCall(
            new Node\Expr\Variable('this'),
            '__proxyCall',
            $newParams
        );

        // New method stmts
        $type = $node->returnType;
        $stmt = new Node\Stmt\Return_($proxyCall);
        if ($type && $type instanceof Node\Identifier && $type->name === 'void') {
            $stmt = new Node\Stmt\Expression($proxyCall);
        }

        // Return `self` to return `originalClassName`
        $returnType = $node->returnType;
        if ($returnType instanceof Node\Name && $returnType->toString() === 'self') {
            $returnType->parts = [
                sprintf('\\%s', $this->originalInterfaceName)
            ];
        }

        // New method nodes
        $newMethodNodes = [
            'flags'      => $node->flags,
            'byRef'      => $node->byRef,
            'name'       => $node->name,
            'params'     => $node->params,
            'returnType' => $returnType,
            'stmts'      => [
                $stmt
            ],
        ];

        return new ClassMethod($methodName, $newMethodNodes);
    }

    /**
     * Get proxy class name
     *
     * @return string
     */
    public function getProxyClassName(): string
    {
        return sprintf('%s\\%s', $this->namespace, $this->proxyName);
    }

    /**
     * Get proxy class name
     *
     * @return string
     */
    public function getOriginalInterfaceName(): string
    {
        return $this->originalInterfaceName;
    }

    /**
     * @return string
     */
    public function getProxyName(): string
    {
        return $this->proxyName;
    }

    /**
     * Get aop trait node
     *
     * @return Node\Stmt\TraitUse
     */
    private function getTraitNode(): Node\Stmt\TraitUse
    {
        return new Node\Stmt\TraitUse([
            new Node\Name('\\' . $this->serviceTrait)
        ]);
    }

    /**
     * Get original class method node
     *
     * @return ClassMethod
     */
    private function getOriginalClassNameMethodNode(): ClassMethod
    {
        // Add getOriginalClassName method
        return new ClassMethod('getOriginalClassName', [
            'flags'      => Node\Stmt\Class_::MODIFIER_PUBLIC,
            'returnType' => 'string',
            'stmts'      => [
                new Node\Stmt\Return_(new Node\Scalar\String_($this->getOriginalInterfaceName()))
            ],
        ]);
    }
}