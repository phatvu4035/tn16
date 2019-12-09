<?php

namespace App\Helpers;

class HtmlWidget
{
    public static function a($href = "#", $content = "")
    {
        return "<a href=".$href.">".$content."</a>";
    }
}