# API Documentation for `/games` Resource

This document gives the accepted inputs and behaviors for all operations related to the `/games` endpoint.

## Examples of Correct Inputs for /games

### GET /games

---

Supports:
- **Pagination**: `page`, `page_size`
- **Filtering**: `game_date` (string, format `YYYY-MM-DD`), `game_type` (string: `regular`, `playoffs`, `preseason`)
- **Sorting**: `sort_by`, `order_by`

Example:
/games?page=1&page_size=5&game_date=2024-10-01&game_type=regular&sort_by=home_score&order_by=desc

---

### GET /games/{game_id}
Returns the details of a specific game.

Example:
/games/12

---

### GET /games/{game_id}/stats
Supports:
- **Filtering**: `first_name` (string), `goals_scored` (integer), `assist` (integer), `sog` (integer)
- **Pagination**: `page`, `page_size`

Example:
/games/12/stats?first_name=Connor&goals_scored=2&page=1&page_size=10

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

---

game_id is mandatory
everything else is optional, but a minimum of one field is required
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
    "away_score": -1                  //must be >= 0
  },
  {
    "game_type": "semi-finals"        //invalid game_type
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
    "home_score": "fiftyfive"         //should be an integer
  },
  {
    "side_start": "down"              //must be left or right
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