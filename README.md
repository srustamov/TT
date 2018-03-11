# TT
PHP simple and fastest mini mvc framework

[Download](https://github.com/srustamov/TT/archive/master.zip)

* Goto Command Line
```
   root@samir  ~/yourpath/TT# composer install
```

* Create app secret key
```
   root@samir  ~/yourpath/TT# php manage key:generate
```

* Create local server

```
   root@samir  ~/yourpath/TT# php manage runserver 8000
```
   [http://localhost:8000](http://localhost:8000) - Open browser


* Create controller:
```
  root@samir  ~/yourpath/TT# php manage create:controller MainController
```

* Create resource:
```
  root@samir  ~/yourpath/TT# php manage create:resource MainController
```

* Create model:
````
  root@samir  ~/yourpath/TT# php manage create:model ExampleModel
````

* Create middleware:
```
  root@samir  ~/yourpath/TT# php manage create:middleware ExampleMiddleware
```

* Create Database Session Table:
```
  root@samir  ~/yourpath/TT# php manage session:table [--create tableName]
```

* Create Users  Table:
```
  root@samir  ~/yourpath/TT# php manage users:table
```

* Template Engine cache clear:
```
  root@samir  ~/yourpath/TT# php manage view:cache
```

* Config cache clear:
```
  root@samir  ~/yourpath/TT# php manage config:cache
```

* Create cache config files:
```
  root@samir  ~/yourpath/TT# php manage config:cache --create
```
