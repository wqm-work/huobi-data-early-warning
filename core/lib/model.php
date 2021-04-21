<?php
namespace core\lib;

use core\config;
use Medoo\Medoo;

class model extends Medoo {
    public function __construct()
    {

        $arr = config::all('database');
        parent::__construct($arr);

    }
}
