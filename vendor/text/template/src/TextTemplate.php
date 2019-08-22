<?php
/**
 *
 * The MIT License (MIT)
 *
 * Copyright (c) 2015-2017 Matthias Leuffen, Aachen, Germany
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * For further information about me or my projects visit
 *
 * http://leuffen.de
 * https://github.com/dermatthes
 *
 */

namespace Leuffen\TextTemplate;


// Require only when Class is first loaded by classloader
require_once __DIR__."/buildInFilters.inc.php";
require_once __DIR__."/buildInFunctions.inc.php";


class TextTemplate {

    const VERSION = "2.0.1";


    public static $__DEFAULT_FILTER = [];
    public static $__DEFAULT_FUNCTION = [];
    public static $__DEFAULT_SECTIONS = [];

    private $mTemplateText;
    private $mFilter = [];
    private $mFunctions = [];

    private $OC = "{";
    private $OCE = "\{";
    private $CC = "}";
    private $CCE = "\}";


    /**
     * @var callable[]
     */
    private $sections = [];

    public function __construct ($text="") {

        $this->mTemplateText = $text;
        $this->mFilter = self::$__DEFAULT_FILTER;
        $this->mFunctions = self::$__DEFAULT_FUNCTION;
        $this->sections = self::$__DEFAULT_SECTIONS;
    }

    public function setOpenCloseTagChars($open="{", $close="}")
    {
        $this->OC = $open;
        $this->OCE = addslashes($open);
        $this->CC = $close;
        $this->CCE = addslashes($close);
    }

    /**
     * Section mode.
     *
     * This callback is called whenever a {section} is found.
     *
     * @param $enableSectionMode
     */
    public function addSection($name, $sectionCallback)
    {
        $this->sections[$name] = $sectionCallback;
    }


    /**
     * Set the default Filter
     *
     * @param $filterName
     */
    public function setDefaultFilter ($filterName) {
        $this->mFilter["_DEFAULT_"] = $this->mFilter[$filterName];
    }


    /**
     * Add a user-defined filter function to the list of available filters.
     *
     * A filter function must accept at least one parameter: input and return the resulting
     * value.
     *
     * Example:
     *
     * addFilter("currency", function (input) {
     *      return number_format ($input, 2, ",", ".");
     * });
     *
     * @param $filterName
     * @param callable $filterFn
     * @return $this
     */
    public function addFilter ($filterName, callable $filterFn) {
        $this->mFilter[$filterName] = $filterFn;
        return $this;
    }


    /**
     * Register a Function you can call
     *
     *
     * @param          $functionName
     * @param callable $callback
     *
     * @return $this
     */
    public function addFunction ($functionName, callable $callback) {
        $this->mFunctions[$functionName] = $callback;
        return $this;
    }

    public function addPlugin (TextTemplatePlugin $plugin)
    {
        $plugin->registerPlugin($this);
    }

    /**
     * Register all public Functions from an Object
     *
     * @param \stdClass $obj
     *
     * @return $this
     */
    public function addFunctionClass ($obj) {
        $ref = new \ReflectionObject($obj);
        foreach ($ref->getMethods() as $curMethod) {
            if ( ! $curMethod->isPublic())
                continue;
            $this->addFunction($curMethod->getName(), [$obj, $curMethod->getName()]);
        }
        return $this;
    }


    public function _replaceElseIf ($input) {
        $lines = explode("\n", $input);
        for ($li=0; $li < count ($lines); $li++) {
            $lines[$li] = preg_replace_callback("/{$this->OCE}else(?<nestingLevel>[0-9]+){$this->CCE}/im",
                function ($matches) use (&$nestingIndex, &$indexCounter, &$li) {
                    return "{$this->OC}/if{$matches["nestingLevel"]}{$this->CC}{$this->OC}if{$matches["nestingLevel"]} ::NL_ELSE_FALSE{$this->CC}";
                },
                $lines[$li]
            );
            $lines[$li] = preg_replace_callback("/{$this->OCE}elseif(?<nestingLevel>[0-9]+)(?<params>.*){$this->CCE}/im",
                function ($matches) use (&$nestingIndex, &$indexCounter, &$li) {

                    return "{$this->OC}/if{$matches["nestingLevel"]}{$this->CC}{$this->OC}if{$matches["nestingLevel"]} ::NL_ELSE_FALSE {$matches["params"]}{$this->CC}";
                },
                $lines[$li]
            );

        }
        return implode ("\n", $lines);
    }

    /**
     * Tag-Nesting is done by initially adding an index to both the opening and the
     * closing tag. (By the way some cleanup is done)
     *
     * Example
     *
     * {if xyz}
     * {/if}
     *
     * Becomes:
     *
     * {if0 xyz}
     * {/if0}
     *
     * This trick makes it possible to add tag nesting functionality
     *
     *
     * @param $input
     * @return mixed
     * @throws \Exception
     */
    public function _replaceNestingLevels ($input) {
        $indexCounter = 0;
        $nestingIndex = [];

        $blockTags = array_keys($this->sections);
        $blockTags[] = "if";
        $blockTags[] = "for";

        $lines = explode("\n", $input);
        for ($li=0; $li < count ($lines); $li++) {
            $lines[$li] = preg_replace_callback("/{$this->OCE}(?!=)\s*(\/?)\s*([a-z0-9\_]+)(.*?){$this->CCE}/im",
                function ($matches) use (&$nestingIndex, &$indexCounter, &$li, $blockTags) {
                    $slash = $matches[1];
                    $tag = $matches[2];
                    $rest = $matches[3];
                    if ($tag == "else" || $tag == "elseif"){

                        if ( ! isset ($nestingIndex["if"]))
                            throw new TemplateParsingException("Line {$li}: 'if' Opening tag not found for else/elseif tag: '{$matches[0]}'");
                        if (count ($nestingIndex["if"]) == 0)
                            throw new TemplateParsingException("Line {$li}: Nesting level does not match for closing tag: '{$matches[0]}'");
                        $curIndex = $nestingIndex["if"][count ($nestingIndex["if"])-1];
                        $out =  "{$this->OC}" . $tag . $curIndex[0] . rtrim($rest) . "{$this->CC}";
                        return $out;
                    }
                    if ($slash == "" && in_array($tag, $blockTags)) {
                        if ( ! isset ($nestingIndex[$tag]))
                            $nestingIndex[$tag] = [];
                        $nestingIndex[$tag][] = [$indexCounter, $li];
                        $out =  "{$this->OC}" . $tag . $indexCounter . rtrim($rest) . "{$this->CC}";
                        $indexCounter++;

                        return $out;
                    } else if ($slash == "/") {
                        if ( ! isset ($nestingIndex[$tag])) {
                            if ( ! isset ($this->sections[$tag]) && ! in_array($tag, ["if", "for"]))
                                throw new TemplateParsingException("Line {$li}: No callback registred for section {$this->OC}{$tag}{$this->CC}{$this->OC}/{$tag}{$this->CC}");
                            throw new TemplateParsingException(
                                "Line {$li}: Opening tag not found for closing tag: '{$matches[0]}'"
                            );
                        }
                        if (count ($nestingIndex[$tag]) == 0)
                            throw new TemplateParsingException("Line {$li}: Nesting level does not match for closing tag: '{$matches[0]}'");
                        $curIndex = array_pop($nestingIndex[$tag]);
                        return "{$this->OC}/" . $tag . $curIndex[0] . "{$this->CC}";
                    } else {
                        return $matches[0]; // ignore - is Function
                    }
                },
                $lines[$li]
            );

        }
        foreach ($nestingIndex as $tag => $curNestingIndex) {
            if (count ($curNestingIndex) > 0)
                throw new TemplateParsingException("Unclosed tag '{$tag}' opened in line {$curNestingIndex[0][1]} ");
        }
        return implode ("\n", $lines);
    }


    private function _removeComments ($input) {
        return preg_replace("/{$this->OCE}\#.*?\#{$this->CCE}/ism", "", $input);
    }


    private function _removeWhitespace ($input) {
        // Replace: All lines
        // - Starting with Newline and optional spaces
        // - With only {xyz}
        // - Not Starting with {=
        // And ending with newline by single line
        //
        // Caution: Lookahead at the end required to strip multiple lines!
        $input = preg_replace("#\\n\h*({$this->OCE}(?!=)[^\\n{$this->CC}]+?{$this->CCE})\h*\\n#m", "\$1\n", $input);
        $input = preg_replace("#{$this->CCE}\\h*\\n\h*({$this->OCE}(?!=))#m", "{$this->CC}\$1", $input);
        return $input;
    }


    private function _getValueByName ($context, $name, $softFail) {
        $dd = explode (".", $name);
        $value = $context;
        $cur = "";
        foreach ($dd as $cur) {
            if (is_numeric($cur))
                $cur = (int)$cur;
            if (is_array($value)) {
                if ( ! isset ( $value[$cur] )) {
                    if ( ! $softFail) {
                        throw new UndefinedVariableException("ParsingError: Can't parse element: '{$name}' Error on subelement: '$cur'", $name);
                    }
                    $value = NULL;
                } else {
                    $value = $value[$cur];
                }

            } else {
                if (is_object($value)) {
                    if ( ! isset ( $value->$cur )) {
                        if ( ! $softFail) {
                            throw new UndefinedVariableException("ParsingError: Can't parse element: '{$name}' Error on subelement: '$cur'", $name);

                        }
                        $value = NULL;
                    } else {
                        $value = $value->$cur;
                    }
                } else {
                    if ( ! $softFail) {
                        throw new UndefinedVariableException("ParsingError: Can't parse element: '{$name}' Error on subelement: '$cur'", $name);
                    }
                    $value = NULL;
                }
            }
        }
        if (is_object($value) && ! method_exists($value, "__toString"))
            $value = "##ERR:OBJECT_IN_TEXT:[{$name}]ON[{$cur}]:" . gettype($value) . "###";

        return $value;
    }


    private function _applyFilter ($filterNameAndParams, $value) {
        $filterParameters = explode(":", $filterNameAndParams);
        $filterName = array_shift($filterParameters);

        if ( ! isset ($this->mFilter[$filterName]))
            throw new \Exception("Filter '$filterName' not defined");
        $fn = $this->mFilter[$filterName];


        array_unshift($filterParameters, $value);
        return call_user_func_array($fn, $filterParameters);

        // Change to variable-unpacking, when support for php5.4 ends:
        // return $fn($value, ...$filterParameters);

    }


    private function _parseValueOfTags ($context, $match, $softFail) {
        $chain = explode("|", $match);
        for ($i=0; $i<count ($chain); $i++)
            $chain[$i] = trim ($chain[$i]);

        if ( ! in_array("raw", $chain))
            $chain[] = "_DEFAULT_";

        $varName = trim (array_shift($chain));

        if ($varName === "__CONTEXT__") {
            $value = "\n----- __CONTEXT__ -----\n" . var_export($context, true) . "\n----- / __CONTEXT__ -----\n";
        } else {
            $value = $this->_getValueByName($context, $varName, $softFail);
        }

        foreach ($chain as $curName) {
            $value = $this->_applyFilter($curName, $value);
        }

        return $value;
    }



    private function _runFor (&$context, $content, $cmdParam, $softFail) {
        if ( ! preg_match ('/([a-z0-9\.\_]+) in ([a-z0-9\.\_]+)/i', $cmdParam, $matches)) {
            if ( ! $softFail)
                throw new TemplateParsingException("Invalid for-syntax '$cmdParam'");
            return "!!Invalid for-syntax '$cmdParam'!";
        }
        $iterateOverName = $matches[2];
        $localName = $matches[1];

        $repeatVal = $this->_getValueByName($context, $iterateOverName, $softFail);


        if ( ! is_array($repeatVal))
            return "";
        $index = 0;
        $result = "";
        foreach ($repeatVal as $key => $curVal) {
            $context[$localName] = $curVal;
            $context["@key"] = $key;
            $context["@index0"] = $index;
            $context["@index1"] = $index+1;
            try {
                $curContent = $this->_parseBlock($context, $content, $softFail);
            } catch (__BreakLoopException $e) {
                break;
            } catch (__ContinueLoopException $e) {
                continue;
            }


            $result .= $curContent;
            $index++;
        }
        return $result;
    }


    private function _getItemValue ($compName, $context, $softFail) {
        if (preg_match ('/^("|\')(.*?)\1$/i', $compName, $matches))
            return $matches[2]; // string Value
        if (is_numeric($compName)) {
            return $compName;
        }
        if (strtoupper($compName) == "FALSE")
            return FALSE;
        if (strtoupper($compName) == "TRUE")
            return TRUE;
        if (strtoupper($compName) == "NULL")
            return NULL;
        return $this->_getValueByName($context, $compName, $softFail);
    }


    private function _runIf (&$context, $content, $cmdParam, $softFail, &$ifConditionDidMatch) {
        //echo $cmdParam;
        $doIf = false;

        $cmdParam = trim ($cmdParam);
        //echo "\n+ $cmdParam " . strpos($cmdParam, "::NL_ELSE_FALSE");
        // Handle {else}{elseif} constructions
        if ($cmdParam === "::NL_ELSE_FALSE") {
            // This is the {else} path of a if construction
            if ($ifConditionDidMatch == true) {
                return ""; // Do not run else block
            }
            $cmdParam = "TRUE==TRUE";
        } elseif (strpos($cmdParam, "::NL_ELSE_FALSE") === 0) {
            // This is a {elseif (condition)} block
            if ($ifConditionDidMatch == true) {
                return ""; // Do not run ifelse block, if block before succeeded
            }

            $cmdParam = substr($cmdParam, strlen ("::NL_ELSE_FALSE")+1);
        } else {
            // This is the original {if}
            $ifConditionDidMatch = false;
        }

        if ( ! preg_match('/(([\"\']?.*?[\"\']?)\s*(==|<|>|!=)\s*([\"\']?.*[\"\']?)|((!?)\s*(.*)))/i', $cmdParam, $matches)) {
            return "!! Invalid command sequence: '$cmdParam' !!";
        }
        if(count($matches) == 8) {
          $comp1 = $this->_getItemValue(trim($matches[7]), $context, $softFail);
          $operator = '==';
          $comp2 = $matches[6] ? FALSE : TRUE; // ! prefix
        } elseif(count($matches) == 5){
          $comp1 = $this->_getItemValue(trim($matches[2]), $context, $softFail);
          $operator = trim($matches[3]);
          $comp2 = $this->_getItemValue(trim($matches[4]), $context, $softFail);
        } else {
          return "!! Invalid command sequence: '$cmdParam' !!";
        }

        switch ($operator) {
            case "==":
                $doIf = ($comp1 == $comp2);
                break;
            case "!=":
                $doIf = ($comp1 != $comp2);
                break;
            case "<":
                $doIf = ($comp1 < $comp2);
                break;
            case ">":
                $doIf = ($comp1 > $comp2);
                break;

        }

        if ( ! $doIf) {
            return "";
        }

        $ifConditionDidMatch = true; // Skip further else / elseif execution
        $content = $this->_parseBlock($context, $content, $softFail);
        return $content;

    }

    private $ifConditionMatch = [];



    private function _runSection($command, &$context, $content, $cmdParam, $softFail)
    {
        if ( ! isset($this->sections[$command])) {
            if ($softFail === true)
                return "!!ERR:Undefined section '$command'!!";
            throw new TemplateParsingException("Undefined section {$command}...{/$command} in block '$cmdParam'");
        }

        $funcParams = $this->_parseFunctionParameters($cmdParam,  $context, $softFail);
        $content = $this->_parseBlock($context, $content, $softFail);

        try {
            $func = $this->sections[$command];
            $out = $func(
                $content, $funcParams["paramArr"], $command, $context, $cmdParam
            );
            if ($funcParams["retAs"] !== null) {
                if ($funcParams["append"]) {
                    if ( ! isset ($context[$funcParams["retAs"]]))
                        $context[$funcParams["retAs"]] = "";
                    $context[$funcParams["retAs"]] .= $out;
                } else {
                    $context[$funcParams["retAs"]] = $out;
                }
                return "";
            } else {
                return $out;
            }
        } catch (Exception $e) {
            if ($funcParams["exAs"] !== null) {
                $context[$funcParams["exAs"]] = $e->getMessage();
                return "";
            } else {
                throw $e;
            }
        }
    }


    private function _parseFunctionParameters ($cmdParam, &$context, $softFail)
    {
        $paramArr = [];
        $cmdParamRest = preg_replace_callback('/(?<name>[a-z0-9_]+)\s*=\s*(?<sval>((\"|\')(.*?)\4)|[a-z0-9\.\_]+)/i', function ($matches) use(&$paramArr, &$context, $softFail) {
            $paramArr[$matches["name"]] = $this->_getItemValue($matches["sval"], $context, $softFail);
        }, $cmdParam);

        $retAs = null;
        $exAs = null;

        $cmdParamRest = preg_replace_callback("/\!\>\s*([a-z0-9\_]+)/i", function ($matches) use (&$exAs) {
            $exAs = $matches[1];
        }, $cmdParamRest);

        $append = false;
        $cmdParamRest = preg_replace_callback("/(\>|\>\>)\s*([a-z0-9\_]+)/i", function ($matches) use (&$retAs, &$append) {
            if ($matches[1] == ">>") {
                $append = true;
            }
            $retAs = $matches[2];
        }, $cmdParamRest);

        return ["paramArr" => $paramArr, "retAs" => $retAs, "exAs" => $exAs, "append" => $append];
    }



    private function _parseBlock (&$context, $block, $softFail) {
        // (?!\{): Lookahead Regex: Don't touch double {{
        $bCommands = implode("|", array_keys($this->sections));
        $result = preg_replace_callback("/({$this->OCE}(?!=)((?<bcommand>if|for|{$bCommands})(?<bnestingLevel>[0-9]+))(?<bcmdParam>.*?){$this->CCE}(?<bcontent>.*?)\\n?{$this->OCE}\/\\2{$this->CCE}|{$this->OCE}(?!=)(?<command>[a-z]+)\s*(?<cmdParam>.*?){$this->CCE}|{$this->OCE}\=(?<value>.+?){$this->CCE})/ism",
            function ($matches) use (&$context, $softFail) {
                if (isset ($matches["value"]) && $matches["value"] != null) {
                    return $this->_parseValueOfTags($context, $matches["value"], $softFail);
                } else if (isset ($matches["bcommand"]) && $matches["bcommand"] != null) {

                    // Block-Commands
                    $command = $matches["bcommand"];
                    $cmdParam = $matches["bcmdParam"];
                    $content = $matches["bcontent"];
                    $nestingLevel = $matches["bnestingLevel"];

                    switch ($command) {
                        case "for":
                            return $this->_runFor(
                                $context,
                                $content,
                                $cmdParam,
                                $softFail
                            );

                        case "if":
                            return $this->_runIf(
                                $context,
                                $content,
                                $cmdParam,
                                $softFail,
                                $this->ifConditionMatch[$nestingLevel]
                            );


                        default:
                            return $this->_runSection($command, $context, $content, $cmdParam, $softFail);
                    }
                } else {
                    // Regular Commands
                    $command = $matches["command"];
                    $cmdParam = $matches["cmdParam"];

                    $funcParams = $this->_parseFunctionParameters($cmdParam, $context, $softFail);

                    $context["lastErr"] = null;

                    if ( ! isset ($this->mFunctions[$command])) {
                        if ($softFail === true)
                            return "!!ERR:Undefined function '$command'!!";
                        throw new TemplateParsingException("Undefined function '$command' in block '$matches[0]'");
                    }
                    try {
                        $func = $this->mFunctions[$command];
                        $out = $func(
                            $funcParams["paramArr"], $command, $context, $cmdParam
                        );
                        if ($funcParams["retAs"] !== null) {
                            if ($funcParams["append"]) {
                                if ( ! isset ($context[$funcParams["retAs"]]))
                                    $context[$funcParams["retAs"]] = "";
                                $context[$funcParams["retAs"]] .= $out;
                            } else {
                                $context[$funcParams["retAs"]] = $out;
                            }
                        } else {
                            return $out;
                        }
                    } catch (\Exception $e) {
                        if ($funcParams["exAs"] !== null) {
                            $context[$funcParams["exAs"]] = $e->getMessage();
                        } else {
                            throw $e;
                        }
                    }
                    return "";
                }
            }, $block);
        if ($result === NULL) {
            throw new \Exception("preg_replace_callback() returned NULL: preg_last_error() returns: " . preg_last_error() . " (error == 2: preg.backtracklimit to low)");
        }
        return $result;
    }


    /**
     *
     * @param $template
     * @return $this
     */
    public function loadTemplate ($template) {
        $this->mTemplateText = $template;
        return $this;
    }

    public function setTemplate($text)
    {
        $this->mTemplateText = $text;
    }

    /**
     * Parse Tokens in Text (Search for $(name.subname.subname)) of
     *
     *
     * @throws TemplateParsingException
     * @return string
     */
    public function apply ($params, $softFail=TRUE, &$context=[]) {
        $text = $this->mTemplateText;

        $context = $params;


        $text = $this->_removeComments($text);
        $text = $this->_replaceNestingLevels($text);
        $text = $this->_replaceElseIf($text);
        $text = $this->_removeWhitespace($text);

        $result = $this->_parseBlock($context, $text, $softFail);


        return $result;
    }


}