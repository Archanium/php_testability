<?php

require_once __DIR__.'/../../vendor/autoload.php';
use edsonmedina\php_testability\NodeVisitors\GlobalVarVisitor;
use edsonmedina\php_testability\Contexts\RootContext;
use edsonmedina\php_testability\ContextStack;

class GlobalVarVisitorTest extends PHPUnit\Framework\TestCase
{
	public function setup ()
	{
		$this->context = new RootContext ('/');

		$this->stack = $this->getMockBuilder ('edsonmedina\php_testability\ContextStack')
		                    ->setConstructorArgs([$this->context])
		                    ->setMethods(['addIssue'])
		                    ->getMock();

		$this->node = $this->getMockBuilder ('PhpParser\Node\Stmt\Global_')
		                   ->disableOriginalConstructor()
		                   ->getMock();

		$this->node2 = $this->getMockBuilder ('PhpParser\Node\Expr\StaticCall')
		                    ->disableOriginalConstructor()
		                    ->getMock();
	}

	/**
	 * @covers edsonmedina\php_testability\NodeVisitors\GlobalVarVisitor::leaveNode
	 */
	public function testLeaveNodeWithDifferentType ()
	{
		$this->stack->expects($this->never())->method('addIssue');

		$visitor = new GlobalVarVisitor ($this->stack, $this->context);
		$visitor->leaveNode ($this->node2);
	}

	/**
	 * @covers edsonmedina\php_testability\NodeVisitors\GlobalVarVisitor::leaveNode
	 */
	public function testLeaveNodeInGlobalSpace ()
	{
		$this->stack->expects($this->never())->method('addIssue');
		
		$visitor = $this->getMockBuilder ('edsonmedina\php_testability\NodeVisitors\GlobalVarVisitor')
		                ->setConstructorArgs([$this->stack, $this->context])
		                ->setMethods(['inGlobalScope'])
		                ->getMock();

		$visitor->method ('inGlobalScope')->willReturn (true);
		$visitor->leaveNode ($this->node);
	}

	/**
	 * @covers edsonmedina\php_testability\NodeVisitors\GlobalVarVisitor::leaveNode
	 */
	public function testLeaveNode ()
	{
		$this->stack->expects($this->once())->method('addIssue');
		
		$visitor = $this->getMockBuilder ('edsonmedina\php_testability\NodeVisitors\GlobalVarVisitor')
		                ->setConstructorArgs([$this->stack, $this->context])
		                ->setMethods(['inGlobalScope'])
		                ->getMock();

		$visitor->method ('inGlobalScope')->willReturn (false);
		$visitor->leaveNode ($this->node);
	}
}
