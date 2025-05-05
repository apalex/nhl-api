# NHL API Documentation

---

## /teams Resource

### GET /teams

Retrieves a list of teams from the database. The response includes metadata for pagination and supports query parameters for filtering and sorting.

## Examples of Correct Inputs for /teams

**Example:** `/teams`

#### Pagination

- `page` (integer): Page number to retrieve. Default is 1.
- `page_size` (integer): Number of results shown per page. Default is 3.

**Example:** `/teams?page=5&page_size=1`

#### Filtering

- `team_name` (string): Partial match supported.
- `founding_year` (integer)

**Example:** `/teams?team_name=Oilers`

#### Sorting

- `sort_by` (string): `team_name`, `founding_year`, `championships`, `general_manager`, `abbreviation`
- `order_by` (string): `asc`, `desc`

**Example:** `/teams?sort_by=championships&order_by=desc`

---

### GET /teams/{team_id}

Retrieves a specific team by ID.

**Example:** `/teams/6`

---

### GET /teams/{team_id}/games

Retrieves games played by a specific team.

**Example:** `/teams/6/games`

#### Pagination

- `page` (integer)
- `page_size` (integer)

**Example:** `/teams/6/games?page=1&page_size=1`

#### Filtering

- `date` (string): Format `YYYY-MM-DD`
- `arena_name` (string)
- `game_type` (string): `regular`, `playoffs`, `preseason`
- `side_start` (string): `left`, `right`

**Example:** `/teams/6/games?game_type=regular&side_start=left`

---

### POST /teams

Mandatory Field(s):

- coach_id (integer): Coach ID must exist and not be in use by another team.
- arena_id (integer): Arena ID must exist and not be in use by another team.
- founding_year (integer)
- championships (integer)
- general_manager (string)
- abbreviation (string): Format: Must be 3 letters.

```json
[
  {
    "team_name": "Real Madrid",
    "coach_id": 1,
    "arena_id": 1,
    "founding_year": 1902,
    "championships": 100,
    "general_manager": "Zinedine Zidane",
    "abbreviation": "RMA"
  },
  {
    Etc.
  }
]
```

---

### PUT /teams

Mandatory Field(s):

- `team_id` (integer)

```json
[
  {
    "team_id": 8,
    "team_name": "Young Boys",
    "founding_year": 2001,
    "championships": 199,
    "general_manager": "Pep Guardiola",
    "abbreviation": "YBC"
  },
  {
    Etc.
  }
]
```

---

### DELETE /teams

Mandatory Field(s):

- `team_id` (integer)

```json
[
  {
    "team_id": 8
  },
  {
    Etc.
  }
]
```

---

### Examples of Incorrect Inputs for /teams

### POST /teams

```json
[
  {
    "team_name": "Real Madrid",
    "coach_id": 1,
    "arena_id": 1,
    "founding_year": "1902s",             // Must be Integer
    "championships": _100,                // Must be Integer
    "general_manager": "Zinedine Zidane",
    "abbreviation": "RMA1213"             // Abbreviation must be 3 letters
  }
]
```

### PUT /teams

```json
[
  {
                                        // Missing Team ID
    "team_name": "Young Boys",
    "founding_year": 2001,
    "championships": 199,
    "abbreviation": "YBC"
  }
]
```

### DELETE /teams

```json
[
  {
    "team_id": P-898-7987           // Incorrect Team ID Format
  }
]
```

---

## /games Resource

Retrieves a list of games from the database. The response includes metadata for pagination and supports query parameters for filtering and sorting.

## Examples of Correct Inputs for /games

### GET /games

---

Supports:

- **Pagination**: `page`, `page_size`
- **Filtering**: `game_date` (string, format `YYYY-MM-DD`), `game_type` (string: `regular`, `playoffs`, `preseason`)
- **Sorting**: `sort_by`, `order_by`

**Example:** `/games?page=1&page_size=5&game_date=2024-10-01&game_type=regular&sort_by=home_score&order_by=desc`

---

### GET /games/{game_id}

Returns the details of a specific game.

**Example:** `/games/12`

---

### GET /games/{game_id}/stats
Supports:
- **Filtering**: `first_name` (string), `goals_scored` (integer), `assist` (integer), `sog` (integer)
- **Pagination**: `page`, `page_size`

**Example:** `/games/12/stats?first_name=Connor&goals_scored=2&page=1&page_size=10`

---

### POST /games

---

```json
[
  {
    "game_date": "2025-05-01",
    "home_team_id": 1,
    "away_team_id": 2,
    "home_score": 4,
    "away_score": 2,
    "arena_id": 3,
    "game_type": "regular",
    "side_start": "left"
  }
]
```

---

### PUT /games

Mandatory Field(s):

- `game_id` (integer)

```json
[
  {
    "game_id": 1,
    "home_score": 5,
    "away_score": 3,
    "game_type": "playoffs"
  },
  {
    "game_id": 2,
    "side_start": "right"
  }
]
```

---

### DELETE /games

---

Must include a game ID to delete

```json
[
  {
    "game_id": 5
  }
]
```

---

## Examples of Incorrect Inputs for /games

---

### POST /games

---

```json
[
  {
    "game_date": "May 62nd, 2025",    //wrong format
    "home_team_id": "sixty9",         //should be an integer
    "away_score": -1,                 //must be >= 0
  },
  {
    "game_type": "semi-finals",       //invalid game_type
  }
]
```

---

### PUT /games

---

```json
[
  {
    "game_id": G438514,               //must be numbers
    "home_score": "fiftyfive",        //should be an integer
  },
  {
    "side_start": "down",             //must be left or right
  }
]
```

---

### DELETE /games

---

```json
[
  {
    "id": P-02220                     //missing 'game_id' key or should be an integer
  }
]
```

## /arenas Resource

### GET /arenas

Retrieves a list of arenas from the database. The response includes metadata for pagination and supports query parameters for filtering and sorting.

**Example:** `/arenas`

#### Pagination

- `page` (integer): Page number to retrieve. Default is 1.
- `page_size` (integer): Number of results per page. Default is 3.

**Example:**  `/arenas?page=2&page_size=5`

#### Filtering

- `arena_name` (string): Partial match supported.
- `city` (string): Partial match supported.
- `capacity` (integer)

**Example:**  `/arenas?arena_name=Bell&city=Montreal&capacity=21000`

#### Sorting

- `sort_by` (string): `arena_name`, `city`, `capacity`
- `order_by` (string): `asc`, `desc`

**Example:**  `/arenas?sort_by=capacity&order_by=desc`

---

### GET /arenas/{arena_id}

Retrieves a specific arena by ID.

**Example:**  `/arenas/3`

---

### POST /arenas

Creates one or more arena entries.

```json
[
  {
    "arena_name": "Scotiabank Arena",
    "city": "Toronto",
    "capacity": 19800
  }
]
```

---

### PUT /arenas

Mandatory Field(s):

- `arena_id` (integer)

```json
[
  {
    "arena_id": 3,
    "capacity": 21000,
    "city": "Ottawa"
  }
]
```

---

### DELETE /arenas

Mandatory Field(s):

- `arena_id` (integer)

```json
[
  {
    "arena_id": 3
  }
]

```

---

### Examples of Incorrect Inputs for /arenas

### POST /arenas

```json
[
  {
    "arena_name": 1234,
    "city": "Toronto",
    "capacity": "twenty thousand"
  }
]
```

---

### PUT /arenas

```json
[
  {
    "capacity": 19000
  },
  {
    "arena_id": "three",
    "city": 456
  }
]
```

---

### DELETE /arenas

```json
[
  {
    "id": "remove arena 3"
  }
]
```
