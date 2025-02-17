<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Alias;
use WikibaseSolutions\CypherDSL\Variable;

/**
 * @covers \WikibaseSolutions\CypherDSL\Alias
 */
class AliasTest extends TestCase
{
    use TestHelper;

    private Alias $alias;

    protected function setUp(): void
    {
        parent::setUp();

        $this->alias = new Alias(
            $this->getQueryConvertableMock(Variable::class, "a"),
            $this->getQueryConvertableMock(Variable::class, "b")
        );
    }

    public function testToQuery(): void
    {
        $this->assertSame("a AS b", $this->alias->toQuery());
    }

    public function testGetOriginal(): void
    {
        $this->assertEquals($this->getQueryConvertableMock(Variable::class, "a"), $this->alias->getOriginal());
    }

    public function testGetVariable(): void
    {
        $this->assertEquals($this->getQueryConvertableMock(Variable::class, "b"), $this->alias->getVariable());
    }
}
