# Text-Template (Single Class, IF, FOR, FILTERS)

[![Downloads this Month](https://img.shields.io/packagist/dm/text/template.svg)](https://packagist.org/packages/text/template)
[<img src="https://travis-ci.org/dermatthes/text-template.svg">](https://travis-ci.org/dermatthes/text-template)
[![Coverage Status](https://coveralls.io/repos/github/dermatthes/text-template/badge.svg?branch=master)](https://coveralls.io/github/dermatthes/text-template?branch=master)
[![Latest Stable Version](https://poser.pugx.org/text/template/v/stable)](https://github.com/dermatthes/text-template/releases)
[![Supports PHP 5.4+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-5_4plus.png)](http://php.net/)
[![Supports PHP 7.0+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-7_0plus.png)](http://php.net/)
[![Homepage](https://img.shields.io/badge/info-website-blue.svg)](http://text-template.pub.leuffen.de)
[![Examples](https://img.shields.io/badge/see-examples-blue.svg)](http://text-template.pub.leuffen.de/examples.html)

```
{if user.searching=='template'}This is for you {= user.name }{else}Welcome {= user.name }{/if}
```


Single-Class PHP5/7 template engine with support for if/loops/filters

- __Easy__: No compiling or caching - just parse `input string` into `output string`
- __Secure__: No eval(); no code is generated. No filesystem access needed. Unit-tested.
- __Small__: No dependencies.
- __Features__: Nested loops, if/elseif/else, custom filters, auto-escaping

It is aimed to be a small string Template-Engine to meet e-Mailing or small html-Template demands. It is not meant
to be a replacement for pre-compiled full featured Template-Engines like Smarty or Twig.

TextTemplate uses Regular-Expressions for text-Parsing. No code is generated or evaluated - so this might
be a secure solution to use in no time-critical situations.

Whilst most template-engines rely on eval'ing generated code and filesystem-access, Text-Template uses a  
set of regular-expressions to parse the template. Nor any intermediate code is generated nor any 
code is eval'ed. So TextTemplate should be more secure than Smarty or Twig by design.

TextTemplate supports infinite-nested loops and sequences.

## Basic Example
```php

// 1. Define the Template
$tplStr = <<<EOT

Hello World {= name }
{if name == "Matthias"}
Hallo {= name | capitalize }
{elseif name == "Jan"}
Hi Buddy
{else}
You are not Matthias
{/if}

EOT;

// 2. Define the Data for the Template
$data = [
    "name" => "Matthias"
];

// 3. Parse
$tt = new TextTemplate($tplStr);
echo $tt->apply ($data);
```


## Install

I prefer and recommend using [composer](http://getcomposer.com):

```
composer require text/template
```


## Value injection

Use the value Tag
```
{= varName}
```

To inject a value to the Code. Any variables will be ```htmlspecialchars()``` encoded by default. To
output the RAW content use the ```raw```-Filter: ```{=htmlCode|raw}```

To access array elements or objects use "." to access sub-elements:
 
 ```
 {= users.0.name}
```

## Loops

You can insert loops:

```
{for curName in names}
Current Name: {= curName}
{/for}
```

Inside each loop, there are to magick-values ```@index0``` (index starting with 0) and ```@index1``` for a
index starting with amp1.

```
{for curName in names}
Line {= @index1 }: {= curName}
{/for}
```

Inside loops you can `{break}` or `{continue}` the loop.


## Conditions (if)

You can use if-conditions:

```
{if someVarName == "SomeValue"}
Hello World
{/if}
```

Shortcut: Test if a variable is null:

```
{if someVarName}
    someVarName is set!
{/if}
{if !someVarName}
    someVarName is not set!
{/if}
```

Limitation: Logical connections like OR / AND are not possible at the moment. Maybe in the future.

## Conditions (else)
```
{if someVarName == "SomeValue"}
Hello World
{else}
Goodbye World
{/if}
```

Lists of choices:

```
{if someVarName == "SomeValue"}
Hello World
{elseif someVarName == "OtherValue"}
Hello Moon
{else}
Goodbye World
{/if}
```

### Calling Functions

You can register user-defined functions.

```php
$template->addFunctampion("sayHello", 
    function ($paramArr, $command, $context, $cmdParam) {
        return "Hello " . $paramArr["msg"];
    }
);
```

Call the function and output into template

```text
{sayHello msg="Joe"}
```

or inject the Result into the context for further processing:

```text
{sayHello msg="Joe" > out}
{=out}
```

Processing Exceptions:

Use `!>` to catch exceptions and redirect them to the scope.

`{throw msg="SomeMsg" !> lastErr}`

Or use `!break` or `!continue` to break/continue a loop

### Comments

Use `{# #}` to add comments (will be stripped from output

```text
Template {# Some Comment #}
{# Some
Multiline
Comment #}
``` 


### Adding Filters

You can add custom filters or overwrite own filters. The default filter is `html` (htmlspecialchars).

Adding a new Filter:

```php
$tt->addFilter ("currency", function ($input, $decimals=2, $decSeparator=",", $thounsandsSeparator=".") {
    return number_format ($input, $decimals, $decSeparator, $thousandsSeparator);
});
```

Call the filter with parameters (parameter-separator `:`):

```text
{= variable | currency:2:,:. }
```

Use this filter inside your template

```text
{= someVariable | currency }
```

### Predefined Filters

| Name           | Description                                |
|----------------|--------------------------------------------|
| raw            | Display raw data (skip default escaping)   |
| singleLine     | Transform Line-breaks to spaces            |
| inivalue       | like singleLine including addslashes()     |
| html           | htmlspecialchars()                         |
| fixedLength:<length>:<pad_char: | Pad / shrink the output to <length> characters |
| inflect:tag | Convert to underline tag |
| sanitize:hostname | Convert to hostname |

### Replacing the default-Filter

By default and for security reason all values will be escaped using the "_DEFAULT_"-Filter. (except if
"raw" was selected within the filter section)

If you, for some reason, want to disable this functionality or change the escape function you can 
overwrite the _DEFAULT_-Filter:

```php
$tt->addFilter ("_DEFAULT_", function ($input) {
    return strip_tags ($input);
});
```

or

```php
$tt->setDefaultFilter("singleLine");
```

This example will replace the htmlspecialchars() escaper by the strip_tags() function.

## Sections

Sections are like functions but provide the content they enclose:

```text
{sectionxy name="someName"}
Some Content
{/sectionxy}
```

```text
{sectionxy name="someName" > out}
Some Content
{/sectionxy}

{= out}
```

To use sections you must just set the callback:

```php
$textTemplate->addSection("sectionxy", function ($content, $params, $command, $context, $cmdParam) {
    return "Content to replace section content with";
});
```


### Stripping empty lines



```text
{strip_empty_lines}
line1

line2
{/strip_empty_lines}
```


## Function return redirection

Append output to a variable.

```text
{print >> out}
A
{/print}
{print >> out}
B
{/print}

{= out}
```


## Debugging the Parameters

To see all Parameters passed to the template use:

```text
{= __CONTEXT__ | raw}
```

It will output the structure of the current context.


## Dealing with undefined Variables

By default text-template will not throw any exceptions when a template
tries to access an undefined variable. 

To improve debugging, you can switch this behaviour by setting `$softFail` to
`false` (Parameter 2 of `apply()` function):

```php
try {
    $tt = new TextTemplate("{=someUndefinedName}");
    echo $tt->apply([], false);
    //                  ^^^^^
} catch (UndefinedVariableExceptions $e) {
    echo "UndefinedVariable: {$e->getTriggerVarName()}"
}
```
will return
```
UndefinedVariable: someUndefinedName
```

## Changing the tag-open and tag-close chars

Sometimes `{tag}{\tag}` isn't suitable when parsting other template files. You can change the
opening and closing chars with the function `setOpenCloseTagChars()`

```php
$textTemplate->setOpenCloseTagChars("{{", "}}");
```

The above example will listen to `{{tag}}{{/tag}}`.

## Limitations

The logic-Expression-Parser won't handle logic connections (OR / AND) in conditions.

## Benchmark

Although the parser is build of pure regular-expressions, I tried to avoid too expensive constructions like
read ahead, etc.

And we got quite good results: 

| Template size | Parsing time[sec] |
|---------------|-------------------|
| 50kB          | 0.002             |
| 200kB         | 0.007             |



## Contributing, Bug-Reports, Enhancements

If you want to contribute, please send your Pull-Request or open
a github issue.

- __Bugs & Feature-Request:__ [GitHub Issues](https://github.com/dermatthes/text-template/issues)

__Keeping the tests green__: Please see / provide unit-tests. This project uses `nette/tester` for unit-testing.

This Project uses [kickstart](https://github.com/c7lab/kickstart)'s ready to use development
containers based on docker. Just run `./kickstart.sh` to run this project.

To start the development container

```bash
./kickstart.sh
```

To execute the tests run `kick test` inside the container. (See `.kick.yml`)


## About
Text-Template was written by Matthias Leuffen <http://leuffen.de>

Join [InfraCAMP](http://infracamp.org)

