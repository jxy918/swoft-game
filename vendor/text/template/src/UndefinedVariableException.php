<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 08.05.18
 * Time: 12:54
 */

namespace Leuffen\TextTemplate;


use Throwable;

class UndefinedVariableException extends TemplateParsingException
{

    protected $triggerVarName;
    public function __construct(
        $message = "",
        $triggerVarName,
        Throwable $previous = null
    ) {
        $this->triggerVarName = $triggerVarName;
        parent::__construct($message, 0, $previous);
    }

    public function getTriggerVarName ()
    {
        return $this->triggerVarName;
    }

}