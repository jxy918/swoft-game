<?php

namespace Swoft\HttpClient\Adapter;

use Psr\Http\Message\RequestInterface;
use Swoft\App;
use Swoft\Helper\JsonHelper;
use Swoft\Http\Message\Uri\Uri;
use Swoft\HttpClient\HttpCoResult;
use Swoft\HttpClient\HttpResultInterface;
use Swoole\Coroutine;
use Swoole\Coroutine\Http\Client as CoHttpClient;

/**
 * Swoole coroutine client driver adapter
 */
class CoroutineAdapter implements AdapterInterface
{
    use ResponseTrait;

    /**
     * @var array
     */
    protected $defaultOptions = [];

    /**
     * TODO add Middleware, and handle redirect situation
     *
     * @param RequestInterface $request
     * @param array            $options
     * @return HttpResultInterface
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function request(RequestInterface $request, array $options = []): HttpResultInterface
    {
        $options = $this->handleOptions(array_merge($this->defaultOptions, $options));

        $url = (string)$request->getUri();
        $profileKey = 'HttpClient.' . $url;

        App::profileStart($profileKey);

        list($host, $port) = $this->ipResolve($request, $options);

        $ssl = $request->getUri()->getScheme() === 'https';
        $client = new CoHttpClient($host, $port, $ssl);
        $this->applyOptions($client, $request, $options);
        $this->applyMethod($client, $request, $options);
        $client->setDefer();
        $client->execute((string)$request->getUri()->withFragment(''));

        App::profileEnd($profileKey);

        if (null !== $client->errCode && $client->errCode !== 0) {
            App::error(sprintf('HttpClient Request ERROR #%s url=%s', $client->errCode, $url));
            throw new \RuntimeException(\socket_strerror($client->errCode), $client->errCode);
        }

        $result = new HttpCoResult(null, $client, $profileKey);
        return $result;
    }

    /**
     * Get the adapter default user agent
     *
     * @return string
     */
    public function getUserAgent(): string
    {
        $userAgent = 'Swoft/' . App::version();
        $userAgent .= ' Swoft/' . SWOOLE_VERSION;
        $userAgent .= ' PHP/' . PHP_VERSION;
        return $userAgent;
    }

    /**
     * DNS lookup
     *
     * @param RequestInterface $request
     * @param array            $options
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function ipResolve(RequestInterface $request, array &$options = []): array
    {
        $host = $request->getUri()->getHost();
        if (isset($options['port']) && is_numeric($options['port'])) {
            $port = (int)$options['port'];
        } elseif ($request->getUri()->getPort()) {
            $port = $request->getUri()->getPort();
        } elseif ($request->getUri() instanceof Uri) {
            /** @var Uri $uri */
            $uri = $request->getUri();
            $port = $uri->isDefaultPort() ? $uri->getDefaultPort() : 80;
        } else {
            $port = 80;
        }
        $ipLong = ip2long($host);

        if ($ipLong !== false) {
            return [$host, $port];
        }

        // DHS Lookup
        $ip = Coroutine::gethostbyname($host);
        if (! $ip) {
            $message = sprintf('DNS lookup failure, domain = %s', $host);
            App::error($message);
            throw new \InvalidArgumentException($message);
        }
        return [$ip, $port];
    }

    /**
     * @param array $options
     * @return array
     */
    private function handleOptions(array $options): array
    {
        // Auth
        if (! empty($options['auth']) && \is_array($options['auth'])) {
            $value = $options['auth'];
            $type = isset($value[2]) ? strtolower($value[2]) : 'basic';
            switch ($type) {
                case 'basic':
                    $options['_headers']['Authorization'] = 'Basic ' . base64_encode("$value[0]:$value[1]");
                    break;
                case 'digest':
                    // TODO complete digest
                    $options['_headers']['headers'] = "$value[0]:$value[1]";
                    break;
                case 'ntlm':
                    // TODO complete ntlm
                    $options['_headers'][CURLOPT_HTTPAUTH] = CURLAUTH_NTLM;
                    $options['_headers'][CURLOPT_USERPWD] = "$value[0]:$value[1]";
                    break;
            }
        }

        // Timeout
        if (isset($options['timeout']) && is_numeric($options['timeout'])) {
            $options['_options']['timeout'] = $options['timeout'];
        }

        return $options;
    }

    /**
     * @param CoHttpClient     $client
     * @param RequestInterface $request
     * @param array            $options
     * @throws \InvalidArgumentException
     */
    protected function applyOptions(CoHttpClient $client, RequestInterface $request, array &$options)
    {
        if (! empty($options['body'])) {
            $options['_headers']['Content-Type'] = 'text/plain';
        }

        if (isset($options['form_params'])) {
            if (isset($options['multipart'])) {
                throw new \InvalidArgumentException('You cannot use ' . 'form_params and multipart at the same time. Use the ' . 'form_params option if you want to send application/' . 'x-www-form-urlencoded requests, and the multipart ' . 'option to send multipart/form-data requests.');
            }
            $options['body'] = http_build_query($options['form_params'], '', '&');
            unset($options['form_params']);
            $options['_headers']['Content-Type'] = 'application/x-www-form-urlencoded';
        }

        if (isset($options['multipart'])) {
            // TODO add multipart support
            unset($options['multipart']);
        }

        if (isset($options['json'])) {
            $jsonOptions = $options['json_options'] ?? JSON_UNESCAPED_UNICODE;
            $options['body'] = JsonHelper::encode($options['json'], $jsonOptions);
            unset($options['json']);
            $options['_headers']['Content-Type'] = 'application/json';
        }
        $client->set($options['_options'] ?? []);

        $headers = value(function () use ($request, $options) {
            $headers = [
                'Accept' => '*',
            ];
            foreach ($request->getHeaders() as $key => $value) {
                $exploded = explode('-', $key);
                foreach ($exploded as &$str) {
                    $str = ucfirst($str);
                }
                $ucKey = implode('-', $exploded);
                $headers[$ucKey] = \is_array($value) ? current($value) : $value;
            }
            unset($str);
            $headers = array_replace((array)($options['_headers'] ?? []), $headers);
            return $headers;
        });
        $client->setHeaders($headers ?? []);
    }

    /**
     * @param CoHttpClient     $client
     * @param RequestInterface $request
     * @param array            $options
     * @return void
     */
    protected function applyMethod(CoHttpClient $client, RequestInterface $request, array &$options)
    {
        $client->setMethod($request->getMethod());
        switch (strtoupper($request->getMethod())) {
            case 'POST':
            case 'PUT':
            case 'DELETE':
                $postFields = $this->buildPostFields($options);
                $client->setData($postFields);
                break;
        }
    }

    /**
     * @param array $options
     * @return mixed|string
     */
    private function buildPostFields(array $options)
    {
        $postFields = '';
        if (isset($options['body']) && \is_string($options['body'])) {
            $postFields = $options['body'];
        }
        return (string)$postFields;
    }

    /**
     * Get the adapter User-Agent string
     *
     * @return string
     */
    public function getDefaultUserAgent(): string
    {
        $defaultAgent = 'Swoft/' . App::version();
        $defaultAgent .= ' PHP/' . PHP_VERSION;
        return $defaultAgent;
    }
}
