<?php

/*
 * Cypher DSL
 * Copyright (C) 2021  Wikibase Solutions
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;
use WikibaseSolutions\CypherDSL\XorOperator;

/**
 * @covers \WikibaseSolutions\CypherDSL\XorOperator
 */
class XorOperatorTest extends TestCase
{
    use TestHelper;

    public function testToQuery(): void
    {
        $xor = new XorOperator($this->getQueryConvertableMock(BooleanType::class, "true"), $this->getQueryConvertableMock(BooleanType::class, "false"));

        $this->assertSame("(true XOR false)", $xor->toQuery());

        $xor = new XorOperator($xor, $xor);

        $this->assertSame("((true XOR false) XOR (true XOR false))", $xor->toQuery());
    }

    public function testToQueryNoParentheses(): void
    {
        $xor = new XorOperator($this->getQueryConvertableMock(BooleanType::class, "true"), $this->getQueryConvertableMock(BooleanType::class, "false"), false);

        $this->assertSame("true XOR false", $xor->toQuery());

        $xor = new XorOperator($xor, $xor);

        $this->assertSame("(true XOR false XOR true XOR false)", $xor->toQuery());
    }

    public function testDoesNotAcceptAnyTypeAsOperands(): void
    {
        $this->expectException(TypeError::class);

        $and = new XorOperator($this->getQueryConvertableMock(AnyType::class, "true"), $this->getQueryConvertableMock(AnyType::class, "false"));

        $and->toQuery();
    }
}
