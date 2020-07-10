# laminas-mongodb-paginator-adapter

[![Build Status](https://semaphoreci.com/api/v1/mariojrrc/laminas-mongodb-paginator-adapter/branches/master/badge.svg)](https://semaphoreci.com/mariojrrc/laminas-mongodb-paginator-adapter)

laminas-mongodb-paginator-adapter is a component to be used with [laminas-paginator](https://github.com/laminas/laminas-paginator) when paginating data from a `MongoDB\Collection`.

## Installation

Run the following to install this library:

```bash
$ composer require mariojrrc/laminas-mongodb-paginator-adapter
```

## Usage

```
$adapter = new \Mariojrrc\Laminas\Paginator\MongoDBAdapter(
    $mongoCollection,
    $conditions,
    $options
);
$paginator = new \Laminas\Paginator\Paginator($adapter);
```
