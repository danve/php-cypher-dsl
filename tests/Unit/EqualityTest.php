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
use WikibaseSolutions\CypherDSL\Equality;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PropertyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Equality
 */
class EqualityTest extends TestCase
{
    use TestHelper;

    public function testToQuery(): void
    {
        $equality = new Equality($this->getQueryConvertableMock(PropertyType::class, "10"), $this->getQueryConvertableMock(PropertyType::class, "15"));

        $this->assertSame("(10 = 15)", $equality->toQuery());

        $equality = new Equality($equality, $equality);

        $this->assertSame("((10 = 15) = (10 = 15))", $equality->toQuery());
    }

    public function testToQueryNoParentheses(): void
    {
        $equality = new Equality($this->getQueryConvertableMock(PropertyType::class, "10"), $this->getQueryConvertableMock(PropertyType::class, "15"), false);

        $this->assertSame("10 = 15", $equality->toQuery());

        $equality = new Equality($equality, $equality);

        $this->assertSame("(10 = 15 = 10 = 15)", $equality->toQuery());
    }

    public function testDoesNotAcceptAnyTypeAsOperands(): void
    {
        $this->expectException(TypeError::class);

        $equality = new Equality($this->getQueryConvertableMock(AnyType::class, "10"), $this->getQueryConvertableMock(AnyType::class, "15"));

        $equality->toQuery();
    }
}
