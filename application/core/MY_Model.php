<?php
class MY_Model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
}

class Base_Model extends MY_Model
{
    protected $table_name = null;

    public function __construct()
    {
        parent::__construct();
    }
}