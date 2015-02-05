<?php
namespace edsonmedina\php_testability\NodeVisitors;
use edsonmedina\php_testability\VisitorAbstract;
use edsonmedina\php_testability\Issues\StaticPropertyFetchIssue;
use PhpParser;
use PhpParser\Node\Expr;

class StaticPropertyFetchVisitor extends VisitorAbstract
{
    public function leaveNode (PhpParser\Node $node) 
    {
        // check for static property fetch from different class ($x = OtherClass::$nameOfThing)
        if ($node instanceof Expr\StaticPropertyFetch) 
        {
            if (!$this->isFetchingFromSelf ($node)) 
            {
                $this->data->addIssue (new StaticPropertyFetchIssue($node), $this->scope->getScopeName());
            } 
        }
    }

    public function isFetchingFromSelf (Expr\StaticPropertyFetch $node) 
    {
        if (!$this->scope->insideClass()) 
        {
            return false;
        }

        $name = end ($node->class->parts);

        return ($name === $this->scope->getClassName() || $name === 'self');
    }
}
