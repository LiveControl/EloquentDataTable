<?php
namespace LiveControl\EloquentDataTable\VersionTransformers;

class Version110Transformer implements VersionTransformerContract
{
    public function transform($name)
    {
        return $name; // we use the same as the requested name
    }

    public function getSearchValue()
    {
        if(isset($_POST['search']) && isset($_POST['search']['value']))
            return $_POST['search']['value'];
        return '';
    }

    public function isColumnSearched($columnIndex)
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

    public function getColumnSearchValue($columnIndex)
    {
        return $_POST['columns'][$columnIndex]['search']['value'];
    }

    public function isOrdered()
    {
        return (isset($_POST['order']) && isset($_POST['order'][0]));
    }

    public function getOrderedColumns()
    {
        $columns = [];
        foreach($_POST['order'] as $i => $order)
        {
            $columns[(int) $order['column']] = ($order['dir'] == 'asc' ? 'asc' : 'desc');
        }
        return $columns;
    }
}