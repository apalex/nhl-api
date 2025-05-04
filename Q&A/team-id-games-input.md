# NHL API Documentation

## Examples of Correct Inputs for /teams/{team_id}/games

### GET /teams/{team_id}/games

Retrieves a specific team from the database. The response includes pagination metadata, response status, and games played by specified team.

- `team_id` (integer): Team ID to search by.

**Example:**

### /teams/6/games

---

### Pagination

Pagination parameters allow you to control the page number and the page size.

- `page` (integer): Page number to retrieve. Default is 1.
- `page_size` (integer): Number of results shown per page. Default is 3.

**Example:**

### /teams/6/games?page=1&page_size=1

---

### Filtering

You can filter by using the following parameters:

- `date` (string): Filter by date (Supported format: `YYYY-MM-DD`).
- `arena_name` (string): Filter by the arena the game was played in.
- `game_type` (string): Filter by the type of game the team played in (Supported queries: `regular`, `playoffs`, `preseason`).
- `side_start` (string): Filter by the side the team started on (Supported queries: `left`, `right`).

**Example:**

### /teams/6/games?game_type=regular&side_start=left
