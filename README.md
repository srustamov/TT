# TT
PHP simple and fastest mini mvc framework


* Goto Command Line
```
   root@samir  ~/yourpath# composer create-project  --stability dev --prefer-dist srustamov/tt
```


* Create local server

```
   root@samir  ~/yourpath# php manage runserver 8000
```
   [http://localhost:8000](http://localhost:8000) - Open browser


* Getting Application in Production:
```
  root@samir  ~/yourpath# php manage build
```

* Create controller:
```
  root@samir  ~/yourpath# php manage create:controller MainController
```


* Create model:
````
  root@samir  ~/yourpath# php manage create:model ExampleModel
````

* Create middleware:
```
  root@samir  ~/yourpath/TT# php manage create:middleware ExampleMiddleware
```

* Create Database Session Table:
```
  root@samir  ~/yourpath# php manage session:table [--create tableName]
```

* Create Users  Table:
```
  root@samir  ~/yourpath# php manage users:table
```

* Template Engine cache clear:
```
  root@samir  ~/yourpath# php manage view:cache
```

* Config cache clear:
```
  root@samir  ~/yourpath# php manage config:cache
```

* Create cache config files:
```
  root@samir  ~/yourpath# php manage config:cache --create
```


* Route cache clear:
```
  root@samir  ~/yourpath# php manage route:cache
```

* Routes cached:
```
  root@samir  ~/yourpath# php manage route:cache --create
```
