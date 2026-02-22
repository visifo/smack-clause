# Docker
Custom Docker for Laravel development

## Setup

- Copy **.env.example** to **.env**
```
cp .env.example .env
```

- Project name in the **.env**
```
COMPOSE_PROJECT_NAME=smack
```

- ⚠️ Jetbrains `docker compose` definition:

Set the `Environment variables:` to `COMPOSE_PROJECT_NAME=smack`

After that you can set `Lifecycle:` to `Connect to existing container ('docker-compose exec')`

Necessary for UI test execution

- 🔴 Set up UID and GID according to your user on your machine to avoid permission mismatches.

Run on your locale machine:
```
id
```
Copy the uid and gid numbers to the .env file.:
```
uid=501(user) gid=20(staff) ...
```

## Commands

Start and run the containers in the background
```
docker compose up -d
```
---
List the containers
```
docker compose ps
```
---
Stop the containers
```
docker compose stop
```
---
Delete everything, including images and orphan containers
```
docker compose down -v --rmi all --remove-orphans
```
---
Delete all unused images
```
docker image prune -a
```
---
Delete all unused containers
```
docker volume prune
```
