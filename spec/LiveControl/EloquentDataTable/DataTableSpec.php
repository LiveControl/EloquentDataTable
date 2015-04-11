<?php

namespace spec\LiveControl\EloquentDataTable;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DataTableSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('LiveControl\EloquentDataTable\DataTable');
    }
}
