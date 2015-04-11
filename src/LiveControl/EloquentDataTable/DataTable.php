<?php
namespace LiveControl\EloquentDataTable;

use LiveControl\EloquentDataTable\VersionTransformers\Version110Transformer;
use LiveControl\EloquentDataTable\VersionTransformers\VersionTransformerContract;

use Illuminate\Database\Query\Expression as raw;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DataTable
{
    private $builder;
    private $columns;
    private $formatRowFunction;
    
    protected static $versionTransformer;

    private $rawColumns;
    private $columnNames;

    private $total = 0;
    private $filtered = 0;

    private $rows = [];
    
    public function setVersionTransformer(VersionTransformerContract $versionTransformer)
    {
        static::$versionTransformer = $versionTransformer;
    }
    
    /**
     * @param Builder|Model $builder An eloquent model or eloquent builder.
     * @param array $columns An array of columns which to return and to make searchable.
     * @param callable|null $formatRowFunction A function to format the returned rows.
     * @return array
     * @throws Exception
     */
    public function make($builder, $columns, $formatRowFunction = null)
    {
        if ( ! ($builder instanceof Builder || $builder instanceof Model) ) {
            throw new Exception('$builder variable is not an instance of Builder or Model.');
        }
        
        if(static::$versionTransformer === null)
        {
            static::$versionTransformer = new Version110Transformer();
        }
        
        $this->builder = $builder;
        $this->columns = $columns;
        $this->formatRowFunction = $formatRowFunction;

        $this->total = $this->builder->count();

        $this->rawColumns = $this->getRawColumns($this->columns);
        $this->columnNames = $this->getColumnNames();

        $this->addSelect();
        $this->addFilters();
        $this->addOrderBy();

        $this->filtered = $this->builder->count();

        $this->addLimits();

        $this->rows = $this->builder->get();

        // format rows
        $rows = [];
        foreach ($this->rows as $row) {
            $rows[] = $this->formatRow($row);
        }

        return [
            static::$versionTransformer->transform('draw') => (isset($_POST[static::$versionTransformer->transform(
                    'draw'
                )]) ? (int)$_POST[static::$versionTransformer->transform('draw')] : 0),
            static::$versionTransformer->transform('recordsTotal') => $this->total,
            static::$versionTransformer->transform('recordsFiltered') => $this->filtered,
            static::$versionTransformer->transform('data') => $rows
        ];
    }

    public function setFormatRowFunction($function)
    {
        $this->formatRowFunction = $function;
    }

    private function formatRow($data)
    {
        // if we have a custom format row function we trigger it instead of the default handling.
        if ( $this->formatRowFunction !== null ) {
            $function = $this->formatRowFunction;

            return call_user_func($function, $data);
        }

        $result = [];
        foreach ($this->columnNames as $column) {
            $result[] = $data[$column];
        }
        $data = $result;

        $data = $this->formatRowIndexes($data);

        return $data;
    }

    private function formatRowIndexes($data)
    {
        if ( isset($data['id']) ) {
            $data[static::$versionTransformer->transform('DT_RowId')] = $data['id'];
        }
        return $data;
    }

    private function getColumnNames()
    {
        $names = [];
        foreach ($this->columns as $index => $column) {
            if ( $column instanceof ExpressionWithName ) {
                $names[] = $column->getName();
                continue;
            }
            $names[] = (is_array($column) ? $this->arrayToCamelcase($column) : $column);
        }
        return $names;
    }

    private function getRawColumns($columns)
    {
        $rawColumns = [];
        foreach ($columns as $column) {
            $rawColumns[] = $this->getRawColumnQuery($column);
        }
        return $rawColumns;
    }

    private function getRawColumnQuery($column)
    {
        if ( $column instanceof ExpressionWithName ) {
            return $column->getExpression();
        }

        if ( is_array($column) ) {
            if ( $this->getDatabaseDriver() == 'sqlite' ) {
                return '(' . implode(' || " " || ', $this->getRawColumns($column)) . ')';
            }
            return 'CONCAT(' . implode(', " ", ', $this->getRawColumns($column)) . ')';
        }

        return '`' . str_replace('.', '`.`', $column) . '`'; // user.firstname => `user`.`firstname`
    }

    private function getDatabaseDriver() {
        return Model::resolveConnection()->getDriverName();
    }

    private function addSelect()
    {
        $rawSelect = [];
        foreach ($this->columns as $index => $column) {
            if ( isset($this->rawColumns[$index]) ) {
                $rawSelect[] = $this->rawColumns[$index] . ' AS `' . $this->columnNames[$index] . '`';
            }
        }
        $this->builder = $this->builder->select(new raw(implode(', ', $rawSelect)));
    }

    private function arrayToCamelcase($array, $inForeach = false)
    {
        $result = [];
        foreach ($array as $value) {
            if ( is_array($value) ) {
                $result += $this->arrayToCamelcase($value, true);
            }
            $value = explode('.', $value);
            $value = end($value);
            $result[] = $value;
        }

        return (! $inForeach ? camel_case(implode('_', $result)) : $result);
    }

    private function addFilters()
    {
        $search = static::$versionTransformer->getSearchValue();
        if ( $search != '' ) {
            $this->addAllFilter($search);
        }
        $this->addColumnFilters();
        return $this;
    }

    private function addAllFilter($search)
    {
        $this->builder = $this->builder->where(
            function ($query) use ($search) {
                foreach ($this->rawColumns as $rawColumn) {
                    $query->orWhere(new raw($rawColumn), 'like', '%' . $search . '%');
                }
            }
        );
    }

    private function addColumnFilters()
    {
        foreach ($this->rawColumns as $i => $rawColumn) {
            if ( static::$versionTransformer->isColumnSearched($i) ) {
                $this->builder->where(new raw($rawColumn), 'like', '%' . static::$versionTransformer->getColumnSearchValue($i) . '%');
            }
        }
    }

    protected function addOrderBy()
    {
        if ( static::$versionTransformer->isOrdered() ) {
            foreach(static::$versionTransformer->getOrderedColumns() as $index => $direction)
            {
                if(isset($this->rawColumns[$index])) {
                    $this->builder->orderBy(
                        new raw($this->rawColumns[$index]),
                        $direction
                    );
                }
            }
        }
    }

    private function addLimits()
    {
        if ( isset($_POST[static::$versionTransformer->transform('start')]) && $_POST[static::$versionTransformer->transform('length')] != '-1' ) {
            $this->builder->skip((int) $_POST[static::$versionTransformer->transform('start')])->take((int) $_POST[static::$versionTransformer->transform('length')]);
        }
    }
}