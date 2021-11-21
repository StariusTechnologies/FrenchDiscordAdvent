<?php

namespace french\avent\Model;

class RenderView
{
    public static function render(string $template, string $view, array $data = null)
    {
        ob_start();

        require 'view/' . $view;

        $content = ob_get_clean();

        require 'view/' . $template;
    }    
}
