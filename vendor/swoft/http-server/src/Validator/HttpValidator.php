<?php

namespace Swoft\Http\Server\Validator;

use Swoft\Bean\Annotation\Bean;
use Swoft\Bean\Annotation\ValidatorFrom;
use Swoft\Helper\ArrayHelper;
use Swoft\Helper\JsonHelper;
use Swoft\Http\Message\Server\Request;
use Swoft\Http\Message\Stream\SwooleStream;
use Swoft\Validator\AbstractValidator;

/**
 * Http validator
 * @Bean()
 */
class HttpValidator extends AbstractValidator
{
    /**
     * Validate
     *
     * @param array ...$params
     * @return mixed
     * @throws \Swoft\Exception\ValidatorException
     */
    public function validate(...$params)
    {
        /**
         * @var Request $request
         * @var array   $matches
         */
        list($validators, $request, $matches) = $params;

        if (! \is_array($validators)) {
            return $request;
        }

        foreach ($validators as $type => $validatorAry) {
            $request = $this->validateField($request, $matches, $type, $validatorAry);
        }

        return $request;
    }

    /**
     * Validate field
     *
     * @param Request $request
     * @param array   $matches
     * @param string  $type
     * @param array   $validatorAry
     * @return mixed
     * @throws \Swoft\Exception\ValidatorException
     */
    private function validateField($request, array $matches, string $type, array $validatorAry)
    {
        $get = $request->getQueryParams();
        $post = $request->getParsedBody();
        $contentType = $request->getHeader('content-type');
        $isPostJson = false;
        if ($contentType && \in_array('application/json', $contentType)) {
            $isPostJson = true;
            $post = $request->json();
        }

        foreach ($validatorAry as $name => $info) {
            $default = array_pop($info['params']);
            if ($type === ValidatorFrom::GET) {
                if (!isset($get[$name])) {
                    $request = $request->addQueryParam($name, $default);
                    $this->doValidation($name, $default, $info);
                    continue;
                }
                $this->doValidation($name, $get[$name], $info);

                continue;
            }
            if ($type === ValidatorFrom::POST && \is_array($post)) {
                if (! ArrayHelper::has($post, $name)) {
                    ArrayHelper::set($post, $name, $default);
                    if ($isPostJson) {
                        $request = $request->withBody(new SwooleStream(JsonHelper::encode($post)));
                    } else {
                        $request = $request->addParserBody($name, $default);
                    }

                    $this->doValidation($name, $default, $info);
                    continue;
                }
                $this->doValidation($name, ArrayHelper::get($post, $name), $info);
                continue;
            }
            if ($type === ValidatorFrom::PATH) {
                if (!isset($matches[$name])) {
                    continue;
                }
                $this->doValidation($name, $matches[$name], $info);
                continue;
            }
        }

        return $request;
    }
}
