# General

- Don't run or change tests unless explicitly requested.
- Avoid deeply nested if-else blocks; prefer flat structure with early returns.
- When asking questions or providing suggestions, always number them so responses can be referenced by number.
- Always remove to-do's that you have fixed.
- After finishing changes run `composer fix` to format the code.
- After formatting run `composer check`, evaluate the results, and ensure there are no issues to confirm type safety.


## Docker

- `docker compose` is used to manage the application's services.
- You have to execute `php` and `composer` commands inside the container. All other commands should be executed from the host machine.
- Don't start a new container for each command, connect to the existing one.
- You can do it by prefixing your command with the following command: `docker compose -f docker/docker-compose.yml exec -T workspace` and run it from the project root folder.
- Example: If you want to list php version instead of running `php --version` you have to run `docker compose -f docker/docker-compose.yml exec -T workspace php --version`
