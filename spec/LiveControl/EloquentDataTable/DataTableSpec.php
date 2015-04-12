<?php

namespace spec\LiveControl\EloquentDataTable;

use Illuminate\Database\Eloquent\Builder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use LiveControl\EloquentDataTable\VersionTransformers\Version109Transformer;

class DataTableSpec extends ObjectBehavior
{
    function let(Builder $builder)
    {
        $this->beConstructedWith($builder, ['id']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('LiveControl\EloquentDataTable\DataTable');
    }

    function it_should_not_change_its_version_transformer_to_anything_other_then_a_version_transformer()
    {
        $this->shouldThrow('\Exception')->during('setVersionTransformer', ['109']);
    }

    function it_should_change_its_version_transformer(Version109Transformer $versionTransformer)
    {
        $this->setVersionTransformer($versionTransformer)->shouldHaveType('LiveControl\EloquentDataTable\DataTable');
    }
}
