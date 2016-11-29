<?php

namespace TwoDotsTwice\Payroll;

use Silex\Application;

class PayrollApplication extends Application
{
    use Application\TwigTrait;
    use Application\FormTrait;
    use Application\UrlGeneratorTrait;

    public function __construct(array $values)
    {
        parent::__construct($values);
    }
}
