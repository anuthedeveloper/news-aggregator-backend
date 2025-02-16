## About News Aggregator Backend

```
cd backend
```

This buid and start the container

```
docker-compose up --build
```

Start up the container and migrate the database tables

```
docker-compose up -d
docker exec -it laravel-app php artisan migrate

```

Run the scrape news command to save data locally

```
docker exec -it laravel-app php artisan scrape:news
``

container name: laravel-container

If you're running the container without Docker Compose, you can specify the name using the --name flag like this:

```

docker run --name laravel-container -p 8000:9000 laravel-app

```

To check the container running

```

docker ps

```

```
