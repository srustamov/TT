# TT
PHP simple mini mvc framework

Download Project


[Download](https://github.com/SamirRustamov/TT/archive/master.zip)

* Goto Command Line
```
   root@samir  ~/Desktop/www/TT# composer install
```

* Create app secret key
```
   root@samir  ~/Desktop/www/TT# php manage key:generate
```

* Create local server

```
   root@samir  ~/Desktop/www/TT# php manage runserver 8000
```
   [http://localhost:8000](http://localhost:8000) - Open browser


* Create controller:
```
  root@samir  ~/Desktop/www/TT# php manage create:controller MainController
```

* Create model:
````
  root@samir  ~/Desktop/www/TT# php manage create:model ExampleModel
````

* Create middleware:
```
  root@samir  ~/Desktop/www/TT# php manage create:middleware ExampleMiddleware
```

* Create Database Session Table:
```
  root@samir  ~/Desktop/www/TT# php manage session:table [--create tableName]
```

* Create Users  Table:
```
  root@samir  ~/Desktop/www/TT# php manage users:table
```

* Template Engine cache clear:
```
  root@samir  ~/Desktop/www/TT# php manage view:cache
```

* Config cache clear:
```
  root@samir  ~/Desktop/www/TT# php manage config:cache
```

* Create cache config files:
```
  root@samir  ~/Desktop/www/TT# php manage config:cache --create
```
