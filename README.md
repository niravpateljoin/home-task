# Home Task Api #

This README would normally document whatever steps are necessary to get your application up and running.

## Features

* You can store your movie data

* You can get the specific data for specific movie

* You can get all the movie data

A Symfony website for Test Api...

## System requirements

* Linux OR Windows OS

* Also Required Symfony installed in that system

* PHP 7.4+

* Web-server (XAMPP or WAMP);

** Initial Setup **

* git clone https://github.com/niravpateljoin/home-task.git

```
git clone ---.
```

* Initial command to run

```
composer install      // this will install all the dependencies for this project.
```

* Start server to run the project

```
symfony server:start
```
     [OK] Web server listening
      The Web server is using PHP CGI 7.4.9
      https://127.0.0.1:8000

* Add Movie - API

```
https://127.0.0.1:8000/api/v1/movies?name=test&release_date=05-05-1995&director=test&user_id=1
```

In above api, all the fields are required, If you don't pass any of the fields it's gives validation error.

* Get Movie Data - API

```
https://127.0.0.1:8000/api/v1/movies/1
```

Above api gives all data related to `id 1` & also check that user is exists with valid Id.

* Get Movie List - API

```
https://127.0.0.1:8000/api/v1/list/movies?user_id=1
```

Above api gives all movie data for the `user id=1`