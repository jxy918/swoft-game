<?php declare(strict_types=1);

namespace Swoft\Event;

use RuntimeException;
use Swoft;
use Swoft\Event\Manager\EventManager;
use function count;

/**
 * Class ListenerRegister
 *
 * @since 2.0
 */
final class ListenerRegister
{
    /**
     * @var array
     */
    private static $listeners = [];

    /**
     * @var array
     */
    private static $subscribers = [];

    /**
     * @param string $className
     * @param array  $definition [event name => listener priority]
     */
    public static function addListener(string $className, array $definition = []): void
    {
        // Collect listeners
        self::$listeners[$className] = $definition;
    }

    /**
     * @param string $className
     */
    public static function addSubscriber(string $className): void
    {
        self::$subscribers[] = $className;
    }

    /**
     * @param EventManager $em
     *
     * @return array
     */
    public static function register(EventManager $em): array
    {
        foreach (self::$listeners as $className => $eventInfo) {
            $listener = Swoft::getSingleton($className);

            if (!$listener instanceof EventHandlerInterface) {
                throw new RuntimeException("The event listener class '{$className}' must be instanceof EventHandlerInterface");
            }

            $em->addListener($listener, $eventInfo);
        }

        foreach (self::$subscribers as $className) {
            $subscriber = Swoft::getSingleton($className);
            if (!$subscriber instanceof EventSubscriberInterface) {
                throw new RuntimeException("The event subscriber class '{$className}' must be instanceof EventSubscriberInterface");
            }

            $em->addSubscriber($subscriber);
        }

        $count1 = count(self::$listeners);
        $count2 = count(self::$subscribers);
        // Clear data
        self::$listeners = self::$subscribers = [];

        return [$count1, $count2];
    }
}
