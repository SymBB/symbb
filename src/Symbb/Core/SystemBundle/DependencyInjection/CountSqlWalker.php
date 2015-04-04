<?php

namespace Symbb\Core\SystemBundle\DependencyInjection;

use Doctrine\ORM\Query\AST\AggregateExpression;
use Doctrine\ORM\Query\AST\PathExpression;
use Doctrine\ORM\Query\AST\SelectExpression;
use Doctrine\ORM\Query\AST\SelectStatement;
use Doctrine\ORM\Query\TreeWalkerAdapter;

class CountSqlWalker extends TreeWalkerAdapter
{
    /**
     * Walks down a SelectStatement AST node, thereby generating the appropriate SQL.
     *
     * @return string The SQL.
     */
    public function walkSelectStatement(SelectStatement $AST)
    {
        $parent = null;
        $parentName = null;
        foreach ($this->_getQueryComponents() as $dqlAlias => $qComp) {
            if ($qComp['parent'] === null && $qComp['nestingLevel'] == 0) {
                $parent = $qComp;
                $parentName = $dqlAlias;
                break;
            }
        }

        $pathExpression = new PathExpression(
            PathExpression::TYPE_STATE_FIELD | PathExpression::TYPE_SINGLE_VALUED_ASSOCIATION, $parentName,
            $parent['metadata']->getSingleIdentifierFieldName()
        );
        $pathExpression->type = PathExpression::TYPE_STATE_FIELD;

        $AST->selectClause->selectExpressions = array(
            new SelectExpression(
                new AggregateExpression('count', $pathExpression, true), null
            )
        );
    }
}