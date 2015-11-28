<?php

namespace Barbon\HostedApi\AppBundle\Tests\Validator\Constraints;

use Barbon\HostedApi\AppBundle\Validator\Common\Constraints\DateRange;
use PHPUnit_Framework_TestCase;

class DateRangeTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $constraint = new DateRange(array(
            'min' => 1,
            'max' => 2,
        ));
    }
}
