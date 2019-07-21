# TT
PHP simple and fastest mini mvc framework


* Goto Command Line
```
   $ composer create-project  --prefer-dist srustamov/tt
```


* Create local server

```
   $ php manage runserver 8000
```
   [http://localhost:8000](http://localhost:8000) - Open browser


* Getting Application in Production:
```
  $ php manage build
```

* Create controller:
```
  $ php manage create:controller MainController
```


* Create model:
````
  $ php manage create:model ExampleModel
````

* Create middleware:
```
  $ php manage create:middleware ExampleMiddleware
```

* Create Database Session Table:
```
  $ php manage session:table [--create tableName]
```

* Create Users  Table:
```
  $ php manage users:table
```

* Template Engine cache clear:
```
  $ php manage view:cache
```

* Config cache clear:
```
  $ php manage config:cache
```

* Create cache config files:
```
  $ php manage config:cache --create
```


* Route cache clear:
```
  $ php manage route:cache
```

* Routes cached:
```
  $ php manage route:cache --create
```
