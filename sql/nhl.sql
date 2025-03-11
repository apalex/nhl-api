CREATE DATABASE nhl;

USE nhl;

-- Players Table
CREATE TABLE players (
    player_id INT NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    team_id INT,
    position VARCHAR(50),
    birth_date VARCHAR(10),
    nationality VARCHAR(50),
    height DECIMAL(5,2),
    weight DECIMAL(5,2),
    player_status VARCHAR(50),
    salary DECIMAL(10,2),
    CONSTRAINT PK_PLAYERS_PID PRIMARY KEY (player_id),
    CONSTRAINT FK_PLAYERS_TID FOREIGN KEY (team_id) REFERENCES teams(team_id) ON DELETE SET NULL
);

-- Teams Table
CREATE TABLE teams (
    team_id INT NOT NULL AUTO_INCREMENT,
    team_name VARCHAR(100),
    coach_id INT,
    arena_id INT,
    founding_year INT,
    championships INT,
    general_manager VARCHAR(100),
    abbreviation VARCHAR(10),
    CONSTRAINT PK_TEAMS_TID PRIMARY KEY (team_id),
    CONSTRAINT FK_TEAMS_CID FOREIGN KEY (coach_id) REFERENCES coaches(coach_id) ON DELETE SET NULL,
    CONSTRAINT FK_TEAMS_AID FOREIGN KEY (arena_id) REFERENCES arenas(arena_id) ON DELETE SET NULL
);

-- Coaches Table
CREATE TABLE coaches (
    coach_id INT NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    team_id INT,
    birth_date VARCHAR(10),
    nationality VARCHAR(50),
    games_coached INT,
    CONSTRAINT PK_COACHES_CID PRIMARY KEY (coach_id),
    CONSTRAINT FK_COACHES_TID FOREIGN KEY (team_id) REFERENCES teams(team_id) ON DELETE SET NULL
);

-- Arenas Table
CREATE TABLE arenas (
    arena_id INT NOT NULL AUTO_INCREMENT,
    arena_name VARCHAR(100),
    year_built INT,
    capacity INT,
    city VARCHAR(100),
    province VARCHAR(100),
    team_id INT,
    CONSTRAINT PK_ARENAS_AID PRIMARY KEY (arena_id),
    CONSTRAINT FK_ARENAS_TID FOREIGN KEY (team_id) REFERENCES teams(team_id) ON DELETE SET NULL
);

-- Seasons Table
CREATE TABLE seasons (
    season_id INT NOT NULL AUTO_INCREMENT,
    season_year VARCHAR(10),
    team_id INT,
    wins INT,
    losses INT,
    ties INT,
    points INT,
    playoff_qualification BOOLEAN,
    CONSTRAINT PK_SEASONS_SID PRIMARY KEY (season_id),
    CONSTRAINT FK_SEASONS_TID FOREIGN KEY (team_id) REFERENCES teams(team_id) ON DELETE CASCADE
);

-- Games Table
CREATE TABLE games (
    game_id INT NOT NULL AUTO_INCREMENT,
    game_date VARCHAR(10),
    home_team_id INT,
    away_team_id INT,
    home_score INT,
    away_score INT,
    arena_id INT,
    game_type VARCHAR(50),
    side_start VARCHAR(10),
    CONSTRAINT PK_GAMES_GID PRIMARY KEY (game_id),
    CONSTRAINT FK_GAMES_HTID FOREIGN KEY (home_team_id) REFERENCES teams(team_id) ON DELETE CASCADE,
    CONSTRAINT FK_GAMES_ATID FOREIGN KEY (away_team_id) REFERENCES teams(team_id) ON DELETE CASCADE,
    CONSTRAINT FK_GAMES_AID FOREIGN KEY (arena_id) REFERENCES arenas(arena_id) ON DELETE SET NULL
);

-- Statistics Table
CREATE TABLE statistics (
    stat_id INT NOT NULL AUTO_INCREMENT,
    game_id INT,
    player_id INT,
    goals_scored INT,
    assists INT,
    penalty_mins INT,
    plus_minus INT,
    shot_on_target INT,
    CONSTRAINT PK_STATS_SID PRIMARY KEY (stat_id),
    CONSTRAINT FK_STATS_GID FOREIGN KEY (game_id) REFERENCES games(game_id) ON DELETE CASCADE,
    CONSTRAINT FK_STATS_PID FOREIGN KEY (player_id) REFERENCES players(player_id) ON DELETE CASCADE
);

-- Populate
