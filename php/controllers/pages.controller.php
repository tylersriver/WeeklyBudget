<?php

class PagesController 
{
    /**
     * Lands on the home page
     */
    public function overview() 
    {
        require_once('php/views/pages/overview.php');
    }

    /** 
     * Show error page
     */
    public function error() 
    {
        require_once('php/views/pages/error.php');
    }

}

