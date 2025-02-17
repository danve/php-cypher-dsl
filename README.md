# php-cypher-dsl

![Build status](https://github.com/WikibaseSolutions/php-cypher-dsl/actions/workflows/main.yml/badge.svg)

The `php-cypher-dsl` library provides a way to construct advanced Cypher queries in an object-oriented and type-safe manner.

## Documentation

[The documentation can be found on the wiki here.](https://github.com/WikibaseSolutions/php-cypher-dsl/wiki)

## Installation

### Requirements

`php-cypher-dsl` requires PHP 7.4 or greater; using the latest version of PHP is highly recommended.

### Installation through Composer

You can install `php-cypher-dsl` through composer by running the following command:

```
composer require "wikibase-solutions/php-cypher-dsl"
```

## Example

To construct a query to find all of Tom Hanks' co-actors, you can use the following code:

```php
$tom = Query::node("Person")->withProperties(["name" => Query::literal("Tom Hanks")]);
$coActors = Query::node();

$statement = Query::new()
    ->match($tom->relationshipTo(Query::node(), "ACTED_IN")->relationshipFrom($coActors, "ACTED_IN"))
    ->returning($coActors->property("name"))
    ->build();

$this->assertStringMatchesFormat("MATCH (:Person {name: 'Tom Hanks'})-[:`ACTED_IN`]->()<-[:`ACTED_IN`]-(%s) RETURN %s.name", $statement);
```
