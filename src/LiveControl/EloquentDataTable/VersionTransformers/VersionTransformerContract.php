<?php
namespace LiveControl\EloquentDataTable\VersionTransformers;

interface VersionTransformerContract
{
    public function transform($name);

    public function getSearchValue();

    public function isColumnSearched($columnIndex);
    public function getColumnSearchValue($columnIndex);


    public function isOrdered();
    public function getOrderedColumns();
}