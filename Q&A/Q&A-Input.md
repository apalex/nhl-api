# NHL API Documentation

## Examples of Correct Inputs for /teams

## GET /teams

Retrieves a list of teams from the database. The response includes metadata for pagination and supports query parameters for filtering and sorting.

**Example:**

```json
{
  "status": {
    "Type": "successful",
    "Code": 200,
    "Content-Type": "application/json",
    "Message": "Teams fetched successfully"
  },
  "meta": {
    "total": 7,
    "offset": 0,
    "current_page": 1,
    "page_size": 3,
    "total_pages": 3
  },
  "data": [
    {
      "team_id": 1,
      "team_name": "Toronto Maple Leafs",
      "coach_id": 1,
      "arena_id": 1,
      "founding_year": 1917,
      "championships": 13,
      "general_manager": "Brad Treliving",
      "abbreviation": "TOR"
    },
    {
        Etc.
    }
  ]
}
```

---

### Pagination

Pagination parameters allow you to control the page number and the page size.

- `page` (integer): Page number to retrieve. Default is 1.
- `page_size` (integer): Number of results shown per page. Default is 3.

**Example:**

### GET /teams?page=5&page_size=1

```json
{
  "status": {
    "Type": "successful",
    "Code": 200,
    "Content-Type": "application/json",
    "Message": "Teams fetched successfully"
  },
  "meta": {
    "total": 7,
    "offset": 4,
    "current_page": 5,
    "page_size": 1,
    "total_pages": 7
  },
  "data": [
    {
      "team_id": 5,
      "team_name": "Detroit Red Wings",
      "coach_id": 5,
      "arena_id": 5,
      "founding_year": 1926,
      "championships": 11,
      "general_manager": "Steve Yzerman",
      "abbreviation": "DET"
    }
  ]
}
```

---

### Filtering

You can filter by using the following parameters:

- `team_name` (string): Filter by team name (Partial match supported).
- `founding_year` (integer): Filter by the year the team was founded.

**Example:**

### GET /teams?team_name=Oilers

```json
{
  "status": {
    "Type": "successful",
    "Code": 200,
    "Content-Type": "application/json",
    "Message": "Teams fetched successfully"
  },
  "meta": {
    "total": 1,
    "offset": 0,
    "current_page": 1,
    "page_size": 3,
    "total_pages": 1
  },
  "data": [
    {
      "team_id": 7,
      "team_name": "Edmonton Oilers",
      "coach_id": 7,
      "arena_id": 7,
      "founding_year": 1972,
      "championships": 5,
      "general_manager": "Ken Holland",
      "abbreviation": "EDM"
    }
  ]
}
```

---

### Sorting

You can `sort by` and `order by` using the following parameters:

- `sort_by` (string): Fields to sort by: `team_name`, `founding_year`, `championships`, `general_manager`, `abbreviation`.
- `order_by` (integer):  Sorting by options: `asc` (default), `desc`.

**Example:**

### GET /teams?sort_by=championships&order_by=desc

```json
{
  "status": {
    "Type": "successful",
    "Code": 200,
    "Content-Type": "application/json",
    "Message": "Teams fetched successfully"
  },
  "meta": {
    "total": 7,
    "offset": 0,
    "current_page": 1,
    "page_size": 3,
    "total_pages": 3
  },
  "data": [
    {
      "team_id": 2,
      "team_name": "Montreal Canadiens",
      "coach_id": 2,
      "arena_id": 2,
      "founding_year": 1909,
      "championships": 24,
      "general_manager": "Kent Hughes",
      "abbreviation": "MTL"
    },
    {
        Etc.
    }
  ]
}
```
