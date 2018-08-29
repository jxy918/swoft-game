---
layout: default
title: examples
---

# Text-Template Examples

## Render a navigation-bar in Bootstrap4

```html
<nav class="navbar navbar-expand-lg {switch(default='navbar-light bg-light' src=class)}">
    <a class="navbar-brand" href="{= titleHref}">
        {if titleImg != null}
        <img src="{=titleImg}" width="30" height="30" class="d-inline-block align-top">
        {/if}
        {=title}</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            {for curNavi in navi}
            <li class="nav-item {if curNavi.children != null}dropdown{/if}">
                {if curNavi.children != null}
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown{=@index0}" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {= curNavi.name }
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown{=@index0}">
                    {for curSubNavi in curNavi.children}
                        {if curSubNavi.type == "divider"}
                        <div class="dropdown-divider"></div>
                        {else}
                        <a class="dropdown-item" href="{=curSubNavi.href}">{=curSubNavi.name}</a>
                        {/if}
                    {/for}
                </div>
                {else}
                <a class="nav-link" href="{=curNavi.href}">{=curNavi.name}</a>
                {/if}
            </li>
            {/for}
        </ul>
        <form class="form-inline my-2 my-lg-0">
            <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
    </div>
</nav>
```

The PHP-Code:

```php
<?php
$textTemplate = new TextTemplate();
$textTemplate->loadTemplate(file_get_contents("template.tpl.html"));
echo $textTemplate->apply([ ..data.. ]);
```

More examples coming soon. Do you have good examples? [Contact me.](https://github.com/dermatthes/text-template/issues)