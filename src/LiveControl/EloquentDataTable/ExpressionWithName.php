<?php
namespace LiveControl\EloquentDataTable;

use Illuminate\Database\Query\Expression;

class ExpressionWithName
{
    private $expression;
    private $name;

    public function __construct($expression, $name)
    {
        $this->expression = new Expression($expression);
        $this->name = $name;
    }

    /**
     * @return Expression
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * @param string $expression
     */
    public function setExpression($expression)
    {
        $this->expression = new Expression($expression);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}
