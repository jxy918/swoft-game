<?php declare(strict_types=1);

namespace Swoft;

use ReflectionException;
use Swoft;
use Swoft\Context\Context;
use Swoft\Exception\SwoftException;
use Swoft\Log\Debug;
use Swoft\Log\Error;
use Swoft\Log\Helper\CLog;
use Swoft\Server\Helper\ServerHelper;
use Swoft\Stdlib\Helper\PhpHelper;
use Swoole\Coroutine;
use Swoole\Coroutine\Scheduler;
use Swoole\Event;
use Throwable;
use function count;
use function sgo;

/**
 * Class Co
 *
 * @since   2.0
 */
class Co
{
    /**
     * Coroutine id mapping
     *
     * @var array
     * @example
     * [
     *    'child id'  => 'top id',
     *    'child id'  => 'top id',
     *    'child id'  => 'top id'
     * ]
     */
    private static $mapping = [];

    /**
     * Get current coroutine id
     *
     * @return int
     * -1   Not in coroutine
     * > -1 In coroutine
     */
    public static function id(): int
    {
        return Coroutine::getCid();
    }

    /**
     * Get the top coroutine ID
     *
     * @return int
     */
    public static function tid(): int
    {
        $id = self::id();
        return self::$mapping[$id] ?? $id;
    }

    /**
     * Start and wait execute complete
     *
     * @param callable $callable
     * @param mixed    ...$args
     *
     * @return bool
     */
    public static function run(callable $callable, ...$args): bool
    {
        // Is coroutine to return
        if (self::id() > 0) {
            CLog::warning('Already is in coroutine, not need to use `run`!');
            return PhpHelper::call($callable, ... $args);
        }

        // >= 4.4
        if (ServerHelper::isGteSwoole44()) {
            $scheduler = new Scheduler;
            $scheduler->add($callable, ...$args);

            return $scheduler->start();
        }

        // < 4.4
        Coroutine::create($callable, ...$args);
        Event::wait();

        return true;
    }

    /**
     * Create coroutine
     *
     * @param callable $callable
     * @param bool     $wait
     *
     * @return int If success, return coID
     */
    public static function create(callable $callable, bool $wait = true): int
    {
        $tid = self::tid();

        // return coroutine ID for created.
        return Coroutine::create(function () use ($callable, $tid, $wait) {
            // Current cid
            $id = Coroutine::getCid();
            try {
                // Storage fd
                self::$mapping[$id] = $tid;
                if ($wait) {
                    Context::getWaitGroup()->add();
                }

                PhpHelper::call($callable);
            } catch (Throwable $e) {
                Error::log(
                    "Coroutine internal error: %s\nAt File %s line %d\nTrace:\n%s",
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine(),
                    $e->getTraceAsString()
                );

                // Trigger co error event
                Swoft::trigger(SwoftEvent::COROUTINE_EXCEPTION, $e);
            }

            if ($wait) {
                // Trigger defer
                Swoft::trigger(SwoftEvent::COROUTINE_DEFER);

                Context::getWaitGroup()->done();
            }

            // Clean fd mapping
            unset(self::$mapping[$id]);
        });
    }

    /**
     * Write file
     *
     * @param string   $filename
     * @param string   $data
     * @param int|null $flags
     *
     * @return int
     */
    public static function writeFile(string $filename, string $data, int $flags = null): int
    {
        return Coroutine::writeFile($filename, $data, $flags);
    }

    /**
     * Read file
     *
     * @param string $filename
     *
     * @return string
     * @throws SwoftException
     */
    public static function readFile(string $filename): string
    {
        $result = Coroutine::readFile($filename);
        if ($result === false) {
            throw new SwoftException(sprintf('Read(%s) file error!', $filename));
        }

        return $result;
    }

    /**
     * Multi request
     *
     * @param array $requests
     * @param float $timeout
     *
     * @return array
     * @throws Bean\Exception\ContainerException
     * @throws ReflectionException
     */
    public static function multi(array $requests, float $timeout = 0): array
    {
        $count   = count($requests);
        $channel = new Coroutine\Channel($count);

        foreach ($requests as $key => $callback) {
            sgo(function () use ($key, $channel, $callback) {
                try {
                    $data = PhpHelper::call($callback);
                    $channel->push([$key, $data]);
                } catch (Throwable $e) {
                    Debug::log('Co multi error(key=%s) is %s', $key, $e->getMessage());

                    $channel->push(false);
                }
            });
        }

        $response = [];
        while ($count > 0) {
            $result = $channel->pop($timeout);
            if ($result === false) {
                Debug::log('Co::multi request fail!');
            } else {
                [$key, $value] = $result;
                $response[$key] = $value;
            }

            $count--;
        }

        return $response;
    }

    /**
     * @param array $options
     */
    public static function set(array $options): void
    {
        Coroutine::set($options);
    }

    /**
     * @return array
     */
    public static function stats(): array
    {
        return Coroutine::stats();
    }

    /**
     * @param int $cid
     *
     * @return bool
     */
    public static function exists(int $cid): bool
    {
        return Coroutine::exists($cid);
    }

    /**
     * @return int
     */
    public static function getPcid(): int
    {
        return Coroutine::getPcid();
    }

    /**
     * @param int|null $cid
     *
     * @return Coroutine\Context
     */
    public static function getContext(int $cid = null): Coroutine\Context
    {
        return Coroutine::getContext($cid);
    }

    /**
     * @param array|callable $callback
     */
    public static function defer($callback): void
    {
        Coroutine::defer($callback);
    }

    /**
     * @return Coroutine\Iterator
     */
    public static function list(): Coroutine\Iterator
    {
        return Coroutine::list();
    }

    /**
     * @param int $cid
     * @param int $options
     * @param int $limit
     *
     * @return array
     * @throws SwoftException
     */
    public static function getBackTrace(
        int $cid = 0,
        int $options = DEBUG_BACKTRACE_PROVIDE_OBJECT,
        int $limit = 0
    ): array {
        $result = Coroutine::getBackTrace($cid, $options, $limit);
        if ($result === false) {
            throw new SwoftException('cid is not exist!');
        }

        return $result;
    }

    /**
     * Yield
     */
    public static function yield(): void
    {
        Coroutine::yield();
    }

    /**
     * @param int $cid
     */
    public static function resume(int $cid): void
    {
        Coroutine::resume($cid);
    }

    /**
     * @param resource $handle
     * @param int      $length
     *
     * @return string
     * @throws SwoftException
     */
    public static function fread($handle, int $length = 0): string
    {
        $result = Coroutine::fread($handle, $length);
        if ($result === false) {
            throw new SwoftException('Fread file error!');
        }

        return $result;
    }

    /**
     * @param resource $handle
     *
     * @return string
     * @throws SwoftException
     */
    public static function fgets($handle): string
    {
        $result = Coroutine::fgets($handle);
        if ($result === false) {
            throw new SwoftException('Fgets file error!');
        }

        return $result;
    }

    /**
     * @param resource $handle
     * @param string   $data
     * @param int      $length
     *
     * @return int
     * @throws SwoftException
     */
    public static function fwrite($handle, string $data, int $length = 0): int
    {
        $result = Coroutine::fwrite($handle, $data, $length);
        if ($result === false) {
            throw new SwoftException('Fwrite file error! data=' . $data);
        }

        return $result;
    }

    /**
     * @param float $seconds
     */
    public static function sleep(float $seconds): void
    {
        Coroutine::sleep($seconds);
    }

    /**
     * @param string $domain
     * @param float  $timeout
     * @param int    $family
     *
     * @return string
     * @throws SwoftException
     */
    public static function getHostByName(string $domain, float $timeout, int $family = 2): string
    {
        $result = Coroutine::gethostbyname($domain, $family, $timeout);
        if ($result === false) {
            throw new SwoftException('GetHostByName error! domain=' . $domain);
        }

        return $result;
    }

    /**
     * @param string      $domain
     * @param int         $family
     * @param int         $socktype
     * @param int         $protocol
     * @param string|null $service
     *
     * @return array
     * @throws SwoftException
     */
    public static function getAddrInfo(
        string $domain,
        int $family = 2,
        int $socktype = 1,
        int $protocol = 6,
        string $service = null
    ): array {
        $result = Coroutine::getaddrinfo($domain, $family, $socktype, $protocol, $service);
        if ($result === false) {
            throw new SwoftException('GetAddrInfo error! domain=' . $domain);
        }

        return $result;
    }

    /**
     * @param string $cmd
     *
     * @return array
     * @throws SwoftException
     */
    public static function exec(string $cmd): array
    {
        $result = Coroutine::exec($cmd);
        if ($result === false) {
            throw new SwoftException('Exec error! command=' . $cmd);
        }

        return $result;
    }

    /**
     * @param string $path
     *
     * @return array
     * @throws SwoftException
     */
    public static function statVfs(string $path): array
    {
        $result = Coroutine::statvfs($path);
        if ($result === false) {
            throw new SwoftException('StatVfs error! path=' . $path);
        }

        return $result;
    }
}
