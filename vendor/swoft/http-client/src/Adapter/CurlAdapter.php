<?php

namespace Swoft\HttpClient\Adapter;

use Psr\Http\Message\RequestInterface;
use Swoft\App;
use Swoft\Helper\JsonHelper;
use Swoft\HttpClient\HttpResult;
use Swoft\HttpClient\HttpResultInterface;

/**
 * Curl driver adapter
 */
class CurlAdapter implements AdapterInterface
{
    use ResponseTrait;

    /**
     * @var array
     */
    protected $defaultOptions = [];

    /**
     * @param RequestInterface $request
     * @param array            $options
     * @return HttpResultInterface
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function request(RequestInterface $request, array $options = []): HttpResultInterface
    {
        $this->checkExtension();
        $options = $this->handleOptions(array_merge($this->defaultOptions, $options));

        $url = (string)$request->getUri();
        $profileKey = 'HttpClient.' . $url;

        App::profileStart($profileKey);

        $resource = curl_init();

        $this->applyOptions($resource, $request, $options);
        $this->applyMethod($resource, $request, $options);

        curl_setopt($resource, CURLOPT_URL, (string)$request->getUri()->withFragment(''));
        // Response do not contains Headers
        curl_setopt($resource, CURLOPT_HEADER, false);
        curl_setopt($resource, CURLINFO_HEADER_OUT, true);
        curl_setopt($resource, CURLOPT_RETURNTRANSFER, true);
        // HTTPS do not verify Certificate and HOST
        curl_setopt($resource, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($resource, CURLOPT_SSL_VERIFYHOST, 0);

        $result = curl_exec($resource);

        App::profileEnd($profileKey);

        $errorNo = curl_errno($resource);
        $errorString = curl_error($resource);
        if ($errorNo) {
            App::error(sprintf('HttpClient Request ERROR #%s url=%s', $errorNo, $url));
            throw new \RuntimeException($errorString, $errorNo);
        }

        $result = new HttpResult($result);
        $result->client = $resource;
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
        if (\extension_loaded('curl') && \function_exists('curl_version')) {
            $userAgent .= ' curl/' . \curl_version()['version'];
        }
        return $userAgent;
    }

    /**
     * @param array $options
     * @return array
     */
    private function handleOptions($options): array
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
                    $options['_options'][CURLOPT_HTTPAUTH] = CURLAUTH_DIGEST;
                    $options['_options'][CURLOPT_USERPWD] = "$value[0]:$value[1]";
                    break;
                case 'ntlm':
                    $options['_options'][CURLOPT_HTTPAUTH] = CURLAUTH_NTLM;
                    $options['_options'][CURLOPT_USERPWD] = "$value[0]:$value[1]";
                    break;
            }
        }

        // Timeout
        $timeoutRequiresNoSignal = false;
        if (isset($options['timeout'])) {
            $timeoutRequiresNoSignal |= $options['timeout'] < 1;
            $options['_options'][CURLOPT_TIMEOUT_MS] = $options['timeout'] * 1000;
        }
        if (isset($options['connect_timeout'])) {
            $timeoutRequiresNoSignal |= $options['connect_timeout'] < 1;
            $options['_options'][CURLOPT_CONNECTTIMEOUT_MS] = $options['connect_timeout'] * 1000;
        }
        if ($timeoutRequiresNoSignal && 0 !== stripos(PHP_OS, 'WIN')) {
            $options['_options'][CURLOPT_NOSIGNAL] = true;
        }

        // Ip resolve
        // CURL default value is CURL_IPRESOLVE_WHATEVER
        if (isset($options['force_ip_resolve'])) {
            if ('v4' === $options['force_ip_resolve']) {
                $options['_options'][CURLOPT_IPRESOLVE] = CURL_IPRESOLVE_V4;
            } else {
                if ('v6' === $options['force_ip_resolve']) {
                    $options['_options'][CURLOPT_IPRESOLVE] = CURL_IPRESOLVE_V6;
                }
            }
        }

        return $options;
    }

    /**
     * @param resource         $resource
     * @param RequestInterface $request
     * @param array            $options
     * @throws \InvalidArgumentException
     */
    private function applyOptions($resource, RequestInterface $request, array &$options)
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

        foreach ($options['_options'] ?? [] as $key => $value) {
            curl_setopt($resource, $key, $value);
        }

        $headers = value(function () use ($request, $options) {
            $headers = [];
            foreach ($request->getHeaders() as $key => $value) {
                $exploded = explode('-', $key);
                foreach ($exploded as &$str) {
                    $str = ucfirst($str);
                }
                $ucKey = implode('-', $exploded);
                $headers[$ucKey] = \is_array($value) ? current($value) : $value;
            }
            $headers = array_replace((array)($options['_headers'] ?? []), $headers);
            return $headers;
        });
        foreach ((array)$headers as $name => $value) {
            switch ($name) {
                case 'User-Agent':
                    curl_setopt($resource, CURLOPT_USERAGENT, $value);
                    unset($headers[$name]);
                    break;
            }
            $headers[] = implode(':', [$name, $value]);
            unset($headers[$name]);
        }
        curl_setopt($resource, CURLOPT_HTTPHEADER, $headers);
    }

    /**
     * @param resource         $resource
     * @param RequestInterface $request
     * @param array            $options
     */
    private function applyMethod($resource, RequestInterface $request, array &$options = [])
    {
        switch (strtoupper($request->getMethod())) {
            case 'GET':
                curl_setopt($resource, CURLOPT_HTTPGET, true);
                break;
            case 'POST':
                curl_setopt($resource, CURLOPT_POST, true);
                $postFields = $this->buildPostFields($options);
                curl_setopt($resource, CURLOPT_POSTFIELDS, $postFields);
                break;
            case 'PUT':
            case 'DELETE':
                $postFields = $this->buildPostFields($options);
                curl_setopt($resource, CURLOPT_CUSTOMREQUEST, strtoupper($request->getMethod()));
                curl_setopt($resource, CURLOPT_POSTFIELDS, $postFields);
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
        if (\extension_loaded('curl') && \function_exists('curl_version')) {
            $defaultAgent .= ' curl/' . \curl_version()['version'];
        }
        $defaultAgent .= ' PHP/' . PHP_VERSION;
        return $defaultAgent;
    }

    /**
     * @throws \RuntimeException
     */
    private function checkExtension()
    {
        $isInstalled = \extension_loaded('curl');
        if (! $isInstalled) {
            throw new \RuntimeException('Curl extension required');
        }
    }
}
