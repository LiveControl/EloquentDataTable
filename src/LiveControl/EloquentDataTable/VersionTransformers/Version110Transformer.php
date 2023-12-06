<?php
namespace LiveControl\EloquentDataTable\VersionTransformers;

class Version110Transformer implements VersionTransformerContract
{
    public function transform($name): string
    {
        return $name; // we use the same as the requested name
    }

    public function isSearchRegex(): bool
    {
        if(isset($_POST['search']) && isset($_POST['search']['regex']))
            return filter_var($_POST['search']['regex'], FILTER_VALIDATE_BOOLEAN);
        return false;
    }

    public function getSearchValue(): string
    {
        if(isset($_POST['search']) && isset($_POST['search']['value']))
            return $_POST['search']['value'];
        return '';
    }

    public function isColumnSearched($columnIndex): bool
    {
        return (
            isset($_POST['columns'])
            &&
            isset($_POST['columns'][$columnIndex])
            &&
            isset($_POST['columns'][$columnIndex]['search'])
            &&
            isset($_POST['columns'][$columnIndex]['search']['value'])
            &&
            $_POST['columns'][$columnIndex]['search']['value'] != ''
        );
    }

    public function isColumnSearchRegex($columnIndex): bool
    {
        if (isset($_POST['columns'][$columnIndex]['search']['regex']))
            return filter_var($_POST['columns'][$columnIndex]['search']['regex'], FILTER_VALIDATE_BOOLEAN);
        return false;
    }

    public function getColumnSearchValue($columnIndex): string
    {
        return $_POST['columns'][$columnIndex]['search']['value'];
    }

    public function isOrdered(): bool
    {
        return (isset($_POST['order']) && isset($_POST['order'][0]));
    }

    public function getOrderedColumns(): array
    {
        $columns = [];
        foreach($_POST['order'] as $i => $order)
        {
            $columns[(int) $order['column']] = ($order['dir'] == 'asc' ? 'asc' : 'desc');
        }
        return $columns;
    }
}