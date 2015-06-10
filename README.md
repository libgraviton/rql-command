RQL parser and lexer commands
=============================

This is console commands to visualize your RQL code
using [Symfony console](https://github.com/symfony/Console) and [RQL parser](https://github.com/xiag-ag/rql-parser).


Installation
------------

```
composer require xiag/rql-command
```


Usage
-----

```
./vendor/bin/rql
```


### Lexer command ###

```
./vendor/bin/rql rql:lexer '(eq(a,false)|(c=string:3))&sort(+a,-b)&limit(1,2)'
```

![alt tag](https://raw.githubusercontent.com/xiag-ag/rql-command/master/resources/example-lexer.png)


### Parser command ###

```
./vendor/bin/rql rql:parser '(eq(a,false)|(c=string:3)|(d=in=(4,5)&not(e=6.0))|f>2015-04-19)&like(g,a*ab%2Ac?def)&sort(+a,-b)&limit(1,2)'
```

![alt tag](https://raw.githubusercontent.com/xiag-ag/rql-command/master/resources/example-parser.png)


Resources
---------
 * [RQL parser](https://github.com/xiag-ag/rql-parser)
 * [RQL Rules](https://github.com/persvr/rql)
 * [RQL documentation](https://doc.apsstandard.org/2.1/spec/rql)