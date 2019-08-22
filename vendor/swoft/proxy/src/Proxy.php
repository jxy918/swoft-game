<?php declare(strict_types=1);


namespace Swoft\Proxy;


use function file_put_contents;
use const PHP_EOL;
use function sprintf;
use Swoft\Proxy\Ast\Parser;
use Swoft\Proxy\Ast\Visitor\Visitor;
use Swoft\Proxy\Contract\VisitorInterface;
use Swoft\Proxy\Exception\ProxyException;
use Swoft\Stdlib\Helper\Sys;

class Proxy
{
    /**
     * New class name by proxy
     *
     * @param string  $className
     * @param Visitor $visitor
     *
     * @return string
     * @throws ProxyException
     */
    public static function newClassName(string $className, Visitor $visitor): string
    {
        $parser = new Parser();

        $visitorClassName = get_class($visitor);
        if (!$visitor instanceof VisitorInterface) {
            throw new ProxyException(
                sprintf('%s is not instance of %s', $visitorClassName, VisitorInterface::class)
            );
        }

        $parser->addNodeVisitor($visitorClassName, $visitor);

        $proxyCode = $parser->parse($className);
        $proxyName = $visitor->getProxyName();

        // Proxy file and proxy code
        $proxyFile = sprintf('%s/%s.php', Sys::getTempDir(), $proxyName);
        $proxyCode = sprintf('<?php %s %s', PHP_EOL, $proxyCode);

        // Generate proxy class
        $result = file_put_contents($proxyFile, $proxyCode);
        if ($result === false) {
            throw new ProxyException(sprintf('Proxy file(%s) generate fail', $proxyFile));
        }

        // Load proxy php file
        require $proxyFile;

        // Remove proxy file
        unlink($proxyFile);

        // Proxy class
        $proxyClassName = $visitor->getProxyClassName();
        if (!class_exists($proxyClassName)) {
            throw new ProxyException(sprintf('Proxy class(%s) is not exist!', $proxyClassName));
        }

        return $proxyClassName;
    }
}