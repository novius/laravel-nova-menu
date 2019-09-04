<?php

namespace Novius\LaravelNovaMenu\Tools;

use Laravel\Nova\ResourceTool;

class BackToMenu extends ResourceTool
{
    public $showToolbar = true;
    /**
     * Get the displayable name of the resource tool.
     *
     * @return string
     */
    public function name()
    {
        return 'Back To menu';
    }

    /**
     * Get the component name for the resource tool.
     *
     * @return string
     */
    public function component()
    {
        return 'back-to-menu-tool';
    }
}
