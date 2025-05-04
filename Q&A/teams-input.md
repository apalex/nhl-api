# NHL API Documentation

## Examples of Correct Inputs for /teams

### GET /teams

Retrieves a list of teams from the database. The response includes metadata for pagination and supports query parameters for filtering and sorting.

**Example:**

### /teams

---

### Pagination

Pagination parameters allow you to control the page number and the page size.

- `page` (integer): Page number to retrieve. Default is 1.
- `page_size` (integer): Number of results shown per page. Default is 3.

**Example:**

### /teams?page=5&page_size=1

---

### Filtering

You can filter by using the following parameters:

- `team_name` (string): Filter by team name (Partial match supported).
- `founding_year` (integer): Filter by the year the team was founded.

**Example:**

### /teams?team_name=Oilers

---

### Sorting

You can `sort by` and `order by` using the following parameters:

- `sort_by` (string): Fields to sort by: `team_name`, `founding_year`, `championships`, `general_manager`, `abbreviation`.
- `order_by` (integer):  Sorting by options: `asc` (default), `desc`.

**Example:**

### /teams?sort_by=championships&order_by=desc

---

### POST /teams

Mandatory Fields:

- `team_name` (string): Name of the team.
- `coach_id` (integer): ID of the coach. Coach ID must exist and not be in use by another team.
- `arena_id` (integer): ID of the arena. Arena ID must exist and not be in use by another team.
- `founding_year` (integer): Year the team was founded.
- `championships` (integer): The amount of championships the team has won.
- `general_manager` (string): General Manager name of the team.
- `abbreviation` (string): Team abbreviation. Must be 3 letters.

**Example:**

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

Mandatory Fields:

- `team_id` (integer): Team ID is must be specified.

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

### DELETE /teams

Mandatory Fields:

- `team_id` (integer): Team ID is must be specified.

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

## Examples of Incorrect Inputs for /teams

---

### POST /teams

**Example:**

```json
[
  {
    "team_name": "Real Madrid",
    "coach_id": 1,
    "arena_id": 1,
    "founding_year": 1902s, #Must be Integer
    "championships": _100, #Must be Integer
    "general_manager": "Zinedine Zidane",
    "abbreviation": "RMA1213" #Must be 3 letters.
  }
]

```

---

### PUT /teams

```json
[
  {
    # Team ID is not specified
    "team_name": "Young Boys",
    "founding_year": 2001,
    "championships": 199,
    "abbreviation": "YBC"
  },
  {
    Etc.
  }
]
```

### DELETE /teams

```json
[
  {
    "team_id": 8987987 # Team ID does not exist
  }
]
```
