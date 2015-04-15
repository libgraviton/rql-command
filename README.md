RQL parser and lexer commands
=============================

This is console commands to visualize your RQL code
using [Symfony console](https://github.com/symfony/Console) and [RQL parser](https://github.com/mrix/rql-parser).


Installation
------------

```
composer require mrix/rql-command
```


Usage
-----

```
./vendor/bin/rql
```


### Lexer command ###

```
./vendor/bin/rql rql:lexer '(eq(a,1)|lt(b,2)|(c=string:3))'
```

![alt tag](https://raw.githubusercontent.com/mrix/rql-command/master/resources/example-lexer.png)


### Parser command ###

```
./vendor/bin/rql rql:parser '(eq(a,1)|lt(b,2)|(c=string:3))'
```

![alt tag](https://raw.githubusercontent.com/mrix/rql-command/master/resources/example-parser.png)


Resources
---------
 * [RQL parser](https://github.com/mrix/rql-parser)
 * [RQL Rules](https://github.com/persvr/rql)
 * [RQL documentation](https://doc.apsstandard.org/2.1/spec/rql)