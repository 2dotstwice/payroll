<?php
namespace TwoDotsTwice\Payroll;

class BaseService
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }
}
