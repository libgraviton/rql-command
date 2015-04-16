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
./vendor/bin/rql rql:lexer '(eq(a,false)|(c=string:3))&sort(a,+b,-c)&limit(1,2)'
```

![alt tag](https://raw.githubusercontent.com/mrix/rql-command/master/resources/example-lexer.png)


### Parser command ###

```
./vendor/bin/rql rql:parser '(eq(a,false)|(c=string:3)|(d=in=(4,5)&not(e=6)))&sort(a,+b,-c)&limit(1,2)'
```

![alt tag](https://raw.githubusercontent.com/mrix/rql-command/master/resources/example-parser.png)


Resources
---------
 * [RQL parser](https://github.com/mrix/rql-parser)
 * [RQL Rules](https://github.com/persvr/rql)
 * [RQL documentation](https://doc.apsstandard.org/2.1/spec/rql)