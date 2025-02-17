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

namespace WikibaseSolutions\CypherDSL\Patterns;

use DomainException;
use InvalidArgumentException;
use LogicException;
use WikibaseSolutions\CypherDSL\Property;
use WikibaseSolutions\CypherDSL\Traits\EscapeTrait;
use WikibaseSolutions\CypherDSL\Traits\RelationshipTypeTrait;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\RelationshipType;

/**
 * This class represents an arbitrary relationship between two nodes, a node and a
 * relationship or between two relationships.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/patterns/#cypher-pattern-relationship
 */
class Relationship implements RelationshipType
{
    use EscapeTrait;
    use RelationshipTypeTrait;

    public const DIR_RIGHT = ["-", "->"];
    public const DIR_LEFT = ["<-", "-"];
    public const DIR_UNI = ["-", "-"];

    /**
     * @var string[] The direction of the relationship
     */
    private array $direction;

    /**
     * @var string[]
     */
    private array $types = [];

    /**
     * @var int|null The minimum number of `relationship->node` hops away to search
     */
    private ?int $minHops = null;

    /**
     * @var int|null The maximum number of `relationship->node` hops away to search
     */
    private ?int $maxHops = null;

    /**
     * @var int|null The exact number of `relationship->node` hops away to search
     */
    private ?int $exactHops = null;

    /**
     * Path constructor.
     *
     * @param array $direction The direction of the relationship, should be either:
     *                           - Path::DIR_RIGHT (for a relation of
     *                           (a)-->(b)) - Path::DIR_LEFT (for a relation
     *                           of (a)<--(b)) - Path::DIR_UNI (for a
     *                           relation of (a)--(b))
     */
    public function __construct(array $direction)
    {
        if ($direction !== self::DIR_RIGHT && $direction !== self::DIR_LEFT && $direction !== self::DIR_UNI) {
            throw new InvalidArgumentException("The direction must be either 'DIR_LEFT', 'DIR_RIGHT' or 'RELATED_TO'");
        }

        $this->direction = $direction;
    }

    /**
     * @return string[]
     */
    public function getDirection(): array
    {
        return $this->direction;
    }

    /**
     * Set the minimum number of `relationship->node` hops away to search.
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/match/#varlength-rels
     *
     * @param int $minHops
     * @return Relationship
     */
    public function withMinHops(int $minHops): self
    {
        if ($minHops < 0) {
            throw new DomainException("minHops cannot be less than 0");
        }

        if (isset($this->maxHops) && $minHops > $this->maxHops) {
            throw new DomainException("minHops cannot be greater than maxHops");
        }

        if (isset($this->exactHops)) {
            throw new LogicException("Cannot use minHops in combination with exactHops");
        }

        $this->minHops = $minHops;

        return $this;
    }

    /**
     * Set the maximum number of `relationship->node` hops away to search.
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/match/#varlength-rels
     *
     * @param int $maxHops
     * @return Relationship
     */
    public function withMaxHops(int $maxHops): self
    {
        if ($maxHops < 1) {
            throw new DomainException("maxHops cannot be less than 1");
        }

        if (isset($this->minHops) && $maxHops < $this->minHops) {
            throw new DomainException("maxHops cannot be less than minHops");
        }

        if (isset($this->exactHops)) {
            throw new LogicException("Cannot use maxHops in combination with exactHops");
        }

        $this->maxHops = $maxHops;

        return $this;
    }

    /**
     * Set the exact number of `relationship->node` hops away to search.
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/match/#varlength-rels
     *
     * @param int $exactHops
     * @return Relationship
     */
    public function withExactHops(int $exactHops): self
    {
        if ($exactHops < 1) {
            throw new DomainException("exactHops cannot be less than 1");
        }

        if (isset($this->minHops) || isset($this->maxHops)) {
            throw new LogicException("Cannot use exactHops in combination with minHops or maxHops");
        }

        $this->exactHops = $exactHops;

        return $this;
    }

    /**
     * @param string $type
     * @return Relationship
     */
    public function withType(string $type): self
    {
        $this->types[] = $type;

        return $this;
    }

    /**
     * Returns the string representation of this relationship that can be used directly
     * in a query.
     *
     * @return string
     */
    public function toQuery(): string
    {
        return $this->direction[0] . $this->conditionToString() . $this->direction[1];
    }

    /**
     * @return string
     */
    private function conditionToString(): string
    {
        $conditionInner = "";

        // The condition always starts with the variable
        if (isset($this->variable)) {
            $conditionInner .= $this->variable->toQuery();
        }

        $types = array_filter($this->types);

        if (count($types) !== 0) {
            // If we have at least one condition type, escape them and insert them into the query
            $escapedTypes = array_map(fn (string $type): string => $this->escape($type), $types);
            $conditionInner .= sprintf(":%s", implode("|", $escapedTypes));
        }

        if (isset($this->minHops) || isset($this->maxHops)) {
            // We have either a minHop, maxHop or both
            $conditionInner .= "*";

            if (isset($this->minHops)) {
                $conditionInner .= $this->minHops;
            }

            $conditionInner .= '..';

            if (isset($this->maxHops)) {
                $conditionInner .= $this->maxHops;
            }
        } elseif (isset($this->exactHops)) {
            $conditionInner .= '*' . $this->exactHops;
        }

        if (isset($this->properties)) {
            if ($conditionInner !== "") {
                // Add some padding between the property list and the preceding structure
                $conditionInner .= " ";
            }

            $conditionInner .= $this->properties->toQuery();
        }

        return sprintf("[%s]", $conditionInner);
    }

    /**
     * Returns the exact amount of hops configured.
     *
     * @return int|null
     */
    public function getExactHops(): ?int
    {
        return $this->exactHops;
    }

    /**
     * Returns the maximum amount of hops configured
     *
     * @return int|null
     */
    public function getMaxHops(): ?int
    {
        return $this->maxHops;
    }

    /**
     * Returns the minimum amount of hops configured.
     *
     * @return int|null
     */
    public function getMinHops(): ?int
    {
        return $this->minHops;
    }

    /**
     * Returns the types of the relationship.
     *
     * @return string[]
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    public function property(string $property): Property
    {
        return new Property($this->getName(), $property);
    }
}
