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

* git clone

```
git clone https://github.com/niravpateljoin/home-task.git
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

* Add Movie - API['POST']

```
https://127.0.0.1:8000/api/v1/movies?name=test_movie&imdb=5.2&director=bhansali&release_date=10-12-1997&user_id=1&casts[]=DiCaprio&rotten_tomatto=8.2
```

In above api, all the fields are required, If you don't pass any of the fields it's gives validation error.

* Get Movie Data - API['GET']

```
https://127.0.0.1:8000/api/v1/movies/1
```

Above api gives all data of `movieId 1`.

* Get Movie List - API['GET']

```
https://127.0.0.1:8000/api/v1/movies?user_id=1
```

Above api gives all movie data for the `user id=1`