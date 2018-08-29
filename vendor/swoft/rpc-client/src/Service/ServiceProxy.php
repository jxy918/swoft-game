<?php

namespace Swoft\Rpc\Client\Service;

/**
 * The proxy of service
 */
class ServiceProxy
{
    /**
     * @param string $className
     * @param string $interfaceClass
     */
    public static function loadProxyClass(string $className, string $interfaceClass)
    {
        $reflectionClass   = new \ReflectionClass($interfaceClass);
        $reflectionMethods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);

        $template = "class $className extends \\Swoft\\Rpc\\Client\\Service implements {$interfaceClass} {";

        // the template of methods
        $template .= self::getMethodsTemplate($reflectionMethods);
        $template .= "}";

        eval($template);
    }

    /**
     * return template of method
     *
     * @param \ReflectionMethod[] $reflectionMethods
     *
     * @return string
     */
    private static function getMethodsTemplate(array $reflectionMethods): string
    {
        $template = "";
        foreach ($reflectionMethods as $reflectionMethod) {
            $methodName = $reflectionMethod->getName();

            // not to overrided method
            if ($reflectionMethod->isConstructor() || $reflectionMethod->isStatic() || strpos($methodName, '__') !== false) {
                continue;
            }

            // the template of parameter
            $template .= " public function $methodName (";
            $template .= self::getParameterTemplate($reflectionMethod);
            $template .= ' ) ';

            // the template of return type
            $reflectionMethodReturn = $reflectionMethod->getReturnType();
            if ($reflectionMethodReturn !== null) {
                $returnType = $reflectionMethodReturn->__toString();
                $returnType = ($returnType == 'self') ? $reflectionMethod->getDeclaringClass()->getName() : $returnType;
                $template   .= " : $returnType";
            }

            // overrided method
            $template
                .= "{
                \$params = func_get_args();
                return \$this->call('{$methodName}', \$params);
            }
            ";
        }

        return $template;
    }

    /**
     * return the template of parameter
     *
     * @param \ReflectionMethod $reflectionMethod
     *
     * @return string
     */
    private static function getParameterTemplate(\ReflectionMethod $reflectionMethod): string
    {
        $template             = "";
        $reflectionParameters = $reflectionMethod->getParameters();
        $paramCount           = count($reflectionParameters);
        foreach ($reflectionParameters as $reflectionParameter) {
            $paramCount--;
            // the type of parameter
            $type = $reflectionParameter->getType();
            if ($type !== null) {
                $type     = $type->__toString();
                $template .= " $type ";
            }

            // the name of parameter
            $paramName = $reflectionParameter->getName();
            if ($reflectionParameter->isPassedByReference()) {
                $template .= " &\${$paramName} ";
            } elseif ($reflectionParameter->isVariadic()) {
                $template .= " ...\${$paramName} ";
            } else {
                $template .= " \${$paramName} ";
            }

            // the deault of parameter
            if ($reflectionParameter->isOptional() && $reflectionParameter->isVariadic() == false) {
                $template .= self::getParameterDefault($reflectionParameter);
            }

            if ($paramCount !== 0) {
                $template .= ',';
            }
        }

        return $template;
    }

    /**
     * the template of deault
     *
     * @param \ReflectionParameter $reflectionParameter
     *
     * @return string
     */
    private static function getParameterDefault(\ReflectionParameter $reflectionParameter): string
    {
        $template     = "";
        $defaultValue = $reflectionParameter->getDefaultValue();
        if ($reflectionParameter->isDefaultValueConstant()) {
            $defaultConst = $reflectionParameter->getDefaultValueConstantName();
            $template     = " = {$defaultConst}";
        } elseif (is_bool($defaultValue)) {
            $value    = ($defaultValue) ? "true" : "false";
            $template = " = {$value}";
        } elseif (is_string($defaultValue)) {
            $template = " = ''";
        } elseif (is_int($defaultValue)) {
            $template = " = 0";
        } elseif (is_array($defaultValue)) {
            $template = " = []";
        } elseif (is_float($defaultValue)) {
            $template = " = []";
        } elseif (is_object($defaultValue) || is_null($defaultValue)) {
            $template = " = null";
        }

        return $template;
    }
}