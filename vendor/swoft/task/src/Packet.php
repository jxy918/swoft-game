<?php declare(strict_types=1);


namespace Swoft\Task;

use Swoft\Stdlib\Helper\JsonHelper;
use Swoft\Task\Exception\TaskException;

/**
 * Class Packet
 *
 * @since 2.0
 */
class Packet
{
    /**
     * @param string $type
     * @param string $name
     * @param string $method
     * @param array  $params
     * @param array  $ext
     *
     * @return string
     */
    public static function pack(string $type, string $name, string $method, array $params, array $ext = []): string
    {
        $data = [
            'type'   => $type,
            'name'   => $name,
            'method' => $method,
            'params' => $params,
            'ext'    => $ext,
        ];

        return JsonHelper::encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param string $string
     *
     * @return array
     * @throws TaskException
     */
    public static function unpack(string $string): array
    {
        $data = JsonHelper::decode($string, true);

        $type   = $data['type'] ?? '';
        $name   = $data['name'] ?? '';
        $method = $data['method'] ?? '';
        $params = $data['params'] ?? [];
        $ext    = $data['ext'] ?? [];

        if (empty($name) || empty($method) || empty($type)) {
            throw new TaskException(
                sprintf('Name or method(name=%s method=%s) can not be empty!', $name, $method)
            );
        }

        if (!is_array($params)) {
            throw new TaskException(
                sprintf('Params(%s) is not formated!', JsonHelper::encode($params, JSON_UNESCAPED_UNICODE))
            );
        }

        if (!is_array($ext)) {
            throw new TaskException(
                sprintf('Ext(%s) is not formated!', JsonHelper::encode($ext, JSON_UNESCAPED_UNICODE))
            );
        }

        return [$type, $name, $method, $params, $ext];
    }

    /**
     * @param mixed    $result
     * @param int|null $errorCode
     * @param string   $errorMessage
     *
     * @return string
     */
    public static function packResponse($result, int $errorCode = null, string $errorMessage = ''): string
    {
        if ($errorCode !== null) {
            $data = [
                'code'    => $errorCode,
                'message' => $errorMessage
            ];
        } else {
            $data['result'] = $result;
        }

        return JsonHelper::encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param string $string
     *
     * @return array
     */
    public static function unpackResponse(string $string): array
    {
        $data = JsonHelper::decode($string, true);

        // Fix isset result= null, must to use array_key_exists
        if (array_key_exists('result', $data)) {
            return [$data['result'], null, ''];
        }

        $errorCode    = $data['code'] ?? 0;
        $errorMessage = $data['message'] ?? '';

        return [null, $errorCode, $errorMessage];
    }
}