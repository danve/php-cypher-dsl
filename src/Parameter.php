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

namespace WikibaseSolutions\CypherDSL;

use WikibaseSolutions\CypherDSL\Traits\BooleanTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\DateTimeTrait;
use WikibaseSolutions\CypherDSL\Traits\ErrorTrait;
use WikibaseSolutions\CypherDSL\Traits\EscapeTrait;
use WikibaseSolutions\CypherDSL\Traits\HasNameTrait;
use WikibaseSolutions\CypherDSL\Traits\ListTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\LocalDateTimeTrait;
use WikibaseSolutions\CypherDSL\Traits\LocalTimeTrait;
use WikibaseSolutions\CypherDSL\Traits\MapTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\NumeralTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\PointTrait;
use WikibaseSolutions\CypherDSL\Traits\StringTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TimeTrait;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\DateTimeType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\DateType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\LocalDateTimeType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\LocalTimeType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PointType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\StringType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\TimeType;

/**
 * Represents a parameter.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/parameters/
 */
class Parameter implements
    BooleanType,
    DateType,
    DateTimeType,
    ListType,
    LocalDateTimeType,
    LocalTimeType,
    MapType,
    NumeralType,
    PointType,
    StringType,
    TimeType
{
    use EscapeTrait;
    use DateTimeTrait;
    use ListTypeTrait;
    use LocalDateTimeTrait;
    use LocalTimeTrait;
    use BooleanTypeTrait;
    use MapTypeTrait;
    use NumeralTypeTrait;
    use PointTrait;
    use StringTypeTrait;
    use TimeTrait;
    use HasNameTrait;
    use ErrorTrait;

    /**
     * Parameter constructor.
     *
     * @param string|null $parameter The parameter; this parameter may only consist of alphanumeric
     *                          characters and underscores
     */
    public function __construct(?string $parameter = null)
    {
        $this->configureName($parameter, 'param');
    }

    /**
     * Returns the parameter name.
     *
     * @return string
     */
    public function getParameter(): string
    {
        return $this->getName();
    }

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        return sprintf('$%s', $this->getName());
    }
}
