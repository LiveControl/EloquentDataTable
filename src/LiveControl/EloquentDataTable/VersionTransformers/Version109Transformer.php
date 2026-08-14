<?php
namespace LiveControl\EloquentDataTable\VersionTransformers;

class Version109Transformer implements VersionTransformerContract
{
    private $translate = [
        'draw' => 'sEcho',
        'recordsTotal' => 'iTotalRecords',
        'recordsFiltered' => 'iTotalDisplayRecords',
        'data' => 'aaData',
        'start' => 'iDisplayStart',
        'length' => 'iDisplayLength',

    ];

    public function transform($name): string
    {
        return (isset($this->translate[$name]) ? $this->translate[$name] : $name);
    }

    public function isSearchRegex(): bool
    {
        return false;
    }
    public function getSearchValue(): string
    {
        if(isset($_POST['sSearch']))
            return $_POST['sSearch'];
        return '';
    }

    public function isColumnSearched($i): bool
    {
        return (isset($_POST['bSearchable_' . $i]) && $_POST['bSearchable_' . $i] == 'true' && $_POST['sSearch_' . $i] != '');
    }

    public function isColumnSearchRegex($columnIndex): bool
    {
        return false;
    }

    public function getColumnSearchValue($i): string
    {
        return $_POST['sSearch_' . $i];
    }

    public function isOrdered(): bool
    {
        return isset($_POST['iSortCol_0']);
    }

    public function getOrderedColumns(): array
    {
        $columns = [];
        for ($i = 0; $i < (int) $_POST['iSortingCols']; $i ++) {
            if ( $_POST['bSortable_' . ((int) $_POST['iSortCol_' . $i])] == 'true' ) {
                $columns[(int) $_POST['iSortCol_' . $i]] = ($_POST['sSortDir_' . $i] == 'asc' ? 'asc' : 'desc');
            }
        }
        return $columns;
    }
}