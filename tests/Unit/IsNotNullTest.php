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
use WikibaseSolutions\CypherDSL\IsNotNull;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Not
 */
class IsNotNullTest extends TestCase
{
    use TestHelper;

    public function testToQuery(): void
    {
        $not = new IsNotNull($this->getQueryConvertableMock(BooleanType::class, "true"), false);

        $this->assertFalse($not->insertsParentheses());

        $this->assertSame("true IS NOT NULL", $not->toQuery());

        $not = new IsNotNull($not);

        $this->assertSame("(true IS NOT NULL IS NOT NULL)", $not->toQuery());

        $this->assertTrue($not->insertsParentheses());
    }
}
