<?php
namespace LiveControl\EloquentDataTable\VersionTransformers;

interface VersionTransformerContract
{
    public function transform($name): string;

    public function isSearchRegex(): bool;
    public function getSearchValue(): string;

    public function isColumnSearched($columnIndex): bool;
    public function isColumnSearchRegex($columnIndex): bool;
    public function getColumnSearchValue($columnIndex): string;


    public function isOrdered(): bool;
    public function getOrderedColumns(): array;
}