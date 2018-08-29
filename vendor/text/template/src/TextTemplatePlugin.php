<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 28.03.18
 * Time: 15:05
 */

namespace Leuffen\TextTemplate;


interface TextTemplatePlugin
{
    public function registerPlugin (TextTemplate $textTemplate);
}