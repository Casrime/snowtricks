[![SymfonyInsight](https://insight.symfony.com/projects/93622212-3fac-4359-956e-dfeb1e801e77/big.svg)](https://insight.symfony.com/projects/93622212-3fac-4359-956e-dfeb1e801e77)

SnowTricks
========================

Requirements
------------

* PHP 8.2 or higher;
* PDO-SQLite PHP extension enabled;
* and the [usual Symfony application requirements][1].

Installation
------------
You need to install:
- [Docker Engine][2]
- [Docker Compose][3]

Install the project:
```bash
$ make install
```

Start Symfony Messenger worker:
```bash
$ make sf c='messenger:consume async'
```

Usage
------------

Boot containers:
```bash
$ make dc-up
```

To interact with the PHP container:
```bash
$ make dc-exec
```

Create database, run migrations and load fixtures:
```bash
$ make db-reset
```

Tests
------------

Execute this command to run tests:
```bash
$ make tests
```

Reset tests
------------

Execute this command to reset database and run tests:
```bash
$ make tests-reset
```

Diagrams
------------
[Link](diagrams.md)

[1]: https://symfony.com/doc/current/reference/requirements.html
[2]: https://docs.docker.com/installation/
[3]: https://docs.docker.com/compose/
