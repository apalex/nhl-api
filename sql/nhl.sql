-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 12, 2025 at 05:37 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nhl`
--

-- --------------------------------------------------------

--
-- Table structure for table `arenas`
--

CREATE TABLE `arenas` (
  `arena_id` int(11) NOT NULL,
  `arena_name` varchar(100) DEFAULT NULL,
  `year_built` int(11) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `team_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `arenas`
--

INSERT INTO `arenas` (`arena_id`, `arena_name`, `year_built`, `capacity`, `city`, `province`, `team_id`) VALUES
(1, 'Scotiabank Arena', 1999, 19800, NULL, 'Ontario', 1),
(2, 'Bell Centre', 1996, 21273, NULL, 'Quebec', 2),
(3, 'TD Garden', 1995, 19700, NULL, 'Massachusetts', 3),
(4, 'United Center', 1994, 23500, NULL, 'Illinois', 4),
(5, 'Little Caesars Arena', 2017, 20000, NULL, 'Michigan', 5),
(6, 'Madison Square Garden', 1968, 18100, NULL, 'New York', 6),
(7, 'Rogers Place', 2016, 18500, NULL, 'Alberta', 7);

-- --------------------------------------------------------

--
-- Table structure for table `coaches`
--

CREATE TABLE `coaches` (
  `coach_id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `team_id` int(11) DEFAULT NULL,
  `birth_date` varchar(10) DEFAULT NULL,
  `nationality` varchar(50) DEFAULT NULL,
  `games_coached` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coaches`
--

INSERT INTO `coaches` (`coach_id`, `first_name`, `last_name`, `team_id`, `birth_date`, `nationality`, `games_coached`) VALUES
(1, 'Sheldon', 'Keefe', 1, '1980-09-17', 'Canada', 300),
(2, 'Martin', 'St. Louis', 2, '1975-06-18', 'Canada', 150),
(3, 'Jim', 'Montgomery', 3, '1969-06-30', 'Canada', 250),
(4, 'Luke', 'Richardson', 4, '1969-03-26', 'Canada', 100),
(5, 'Derek', 'Lalonde', 5, '1972-08-18', 'USA', 180),
(6, 'Peter', 'Laviolette', 6, '1964-12-07', 'USA', 1400),
(7, 'Jay', 'Woodcroft', 7, '1976-08-11', 'Canada', 200);

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `game_id` int(11) NOT NULL,
  `game_date` varchar(10) DEFAULT NULL,
  `home_team_id` int(11) DEFAULT NULL,
  `away_team_id` int(11) DEFAULT NULL,
  `home_score` int(11) DEFAULT NULL,
  `away_score` int(11) DEFAULT NULL,
  `arena_id` int(11) DEFAULT NULL,
  `game_type` varchar(50) DEFAULT NULL,
  `side_start` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`game_id`, `game_date`, `home_team_id`, `away_team_id`, `home_score`, `away_score`, `arena_id`, `game_type`, `side_start`) VALUES
(1, '2024-03-01', 1, 2, 4, 3, 1, 'REG', 'Left'),
(2, '2024-03-02', 3, 4, 2, 5, 3, 'REG', 'Right'),
(3, '2024-03-03', 5, 6, 3, 2, 5, 'REG', 'Left'),
(4, '2024-03-04', 7, 1, 5, 4, 7, 'REG', 'Right'),
(5, '2024-03-05', 2, 3, 1, 2, 2, 'REG', 'Left'),
(6, '2024-03-06', 4, 5, 3, 3, 4, 'REG', 'Right'),
(7, '2024-03-07', 6, 7, 2, 4, 6, 'REG', 'Left');

-- --------------------------------------------------------

--
-- Table structure for table `players`
--

CREATE TABLE `players` (
  `player_id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `team_id` int(11) DEFAULT NULL,
  `position` varchar(50) DEFAULT NULL,
  `birth_date` varchar(10) DEFAULT NULL,
  `nationality` varchar(50) DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `player_status` varchar(50) DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `players`
--

INSERT INTO `players` (`player_id`, `first_name`, `last_name`, `team_id`, `position`, `birth_date`, `nationality`, `height`, `weight`, `player_status`, `salary`) VALUES
(1, 'Auston', 'Matthews', 1, 'Center', '1997-09-17', 'USA', 191.00, 92.00, 'Active', 11640000.00),
(2, 'Connor', 'McDavid', 7, 'Center', '1997-01-13', 'Canada', 185.00, 88.00, 'Active', 12500000.00),
(3, 'Sidney', 'Crosby', 6, 'Center', '1987-08-07', 'Canada', 180.00, 91.00, 'Active', 8700000.00),
(4, 'Nathan', 'MacKinnon', 5, 'Center', '1995-09-01', 'Canada', 183.00, 89.00, 'Active', 12600000.00),
(5, 'Leon', 'Draisaitl', 7, 'Center', '1995-10-27', 'Germany', 188.00, 95.00, 'Active', 8500000.00),
(6, 'David', 'Pastrnak', 3, 'Right Wing', '1996-05-25', 'Czech Republic', 183.00, 88.00, 'Active', 11500000.00),
(7, 'Alex', 'Ovechkin', 4, 'Left Wing', '1985-09-17', 'Russia', 191.00, 107.00, 'Active', 9500000.00);

-- --------------------------------------------------------

--
-- Table structure for table `seasons`
--

CREATE TABLE `seasons` (
  `season_id` int(11) NOT NULL,
  `season_year` varchar(10) DEFAULT NULL,
  `team_id` int(11) DEFAULT NULL,
  `wins` int(11) DEFAULT NULL,
  `losses` int(11) DEFAULT NULL,
  `ties` int(11) DEFAULT NULL,
  `points` int(11) DEFAULT NULL,
  `playoff_qualification` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seasons`
--

INSERT INTO `seasons` (`season_id`, `season_year`, `team_id`, `wins`, `losses`, `ties`, `points`, `playoff_qualification`) VALUES
(1, '2023', 1, 50, 22, 10, 110, 0),
(2, '2023', 2, 40, 30, 12, 92, 0),
(3, '2023', 3, 45, 25, 12, 102, 0),
(4, '2023', 4, 35, 38, 9, 79, 0),
(5, '2023', 5, 42, 28, 12, 96, 0),
(6, '2023', 6, 48, 24, 10, 106, 0),
(7, '2023', 7, 52, 20, 10, 114, 0);

-- --------------------------------------------------------

--
-- Table structure for table `statistics`
--

CREATE TABLE `statistics` (
  `stat_id` int(11) NOT NULL,
  `game_id` int(11) DEFAULT NULL,
  `player_id` int(11) DEFAULT NULL,
  `goals_scored` int(11) DEFAULT NULL,
  `assists` int(11) DEFAULT NULL,
  `penalty_mins` int(11) DEFAULT NULL,
  `plus_minus` int(11) DEFAULT NULL,
  `shot_on_target` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `statistics`
--

INSERT INTO `statistics` (`stat_id`, `game_id`, `player_id`, `goals_scored`, `assists`, `penalty_mins`, `plus_minus`, `shot_on_target`) VALUES
(1, 1, 1, 2, 1, 0, 2, 5),
(2, 1, 2, 1, 2, 0, 1, 4),
(3, 2, 3, 3, 0, 2, 3, 6),
(4, 3, 4, 0, 3, 4, 1, 2),
(5, 4, 5, 1, 1, 0, 0, 3),
(6, 5, 6, 2, 2, 1, 2, 4),
(7, 6, 7, 1, 0, 0, -1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE `teams` (
  `team_id` int(11) NOT NULL,
  `team_name` varchar(100) DEFAULT NULL,
  `coach_id` int(11) DEFAULT NULL,
  `arena_id` int(11) DEFAULT NULL,
  `founding_year` int(11) DEFAULT NULL,
  `championships` int(11) DEFAULT NULL,
  `general_manager` varchar(100) DEFAULT NULL,
  `abbreviation` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teams`
--

INSERT INTO `teams` (`team_id`, `team_name`, `coach_id`, `arena_id`, `founding_year`, `championships`, `general_manager`, `abbreviation`) VALUES
(1, 'Toronto Maple Leafs', 1, 1, 1917, 13, 'Brad Treliving', 'TOR'),
(2, 'Montreal Canadiens', 2, 2, 1909, 24, 'Kent Hughes', 'MTL'),
(3, 'Boston Bruins', 3, 3, 1924, 6, 'Don Sweeney', 'BOS'),
(4, 'Chicago Blackhawks', 4, 4, 1926, 6, 'Kyle Davidson', 'CHI'),
(5, 'Detroit Red Wings', 5, 5, 1926, 11, 'Steve Yzerman', 'DET'),
(6, 'New York Rangers', 6, 6, 1926, 4, 'Chris Drury', 'NYR'),
(7, 'Edmonton Oilers', 7, 7, 1972, 5, 'Ken Holland', 'EDM');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `arenas`
--
ALTER TABLE `arenas`
  ADD PRIMARY KEY (`arena_id`),
  ADD KEY `FK_ARENAS_TID` (`team_id`);

--
-- Indexes for table `coaches`
--
ALTER TABLE `coaches`
  ADD PRIMARY KEY (`coach_id`),
  ADD KEY `FK_COACHES_TID` (`team_id`);

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`game_id`),
  ADD KEY `FK_GAMES_HTID` (`home_team_id`),
  ADD KEY `FK_GAMES_ATID` (`away_team_id`),
  ADD KEY `FK_GAMES_AID` (`arena_id`);

--
-- Indexes for table `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`player_id`),
  ADD KEY `FK_PLAYERS_TID` (`team_id`);

--
-- Indexes for table `seasons`
--
ALTER TABLE `seasons`
  ADD PRIMARY KEY (`season_id`),
  ADD KEY `FK_SEASONS_TID` (`team_id`);

--
-- Indexes for table `statistics`
--
ALTER TABLE `statistics`
  ADD PRIMARY KEY (`stat_id`),
  ADD KEY `FK_STATS_GID` (`game_id`),
  ADD KEY `FK_STATS_PID` (`player_id`);

--
-- Indexes for table `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`team_id`),
  ADD KEY `FK_TEAMS_CID` (`coach_id`),
  ADD KEY `FK_TEAMS_AID` (`arena_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `arenas`
--
ALTER TABLE `arenas`
  MODIFY `arena_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `coaches`
--
ALTER TABLE `coaches`
  MODIFY `coach_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `game_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `players`
--
ALTER TABLE `players`
  MODIFY `player_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `seasons`
--
ALTER TABLE `seasons`
  MODIFY `season_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `statistics`
--
ALTER TABLE `statistics`
  MODIFY `stat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `teams`
--
ALTER TABLE `teams`
  MODIFY `team_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `arenas`
--
ALTER TABLE `arenas`
  ADD CONSTRAINT `FK_ARENAS_TID` FOREIGN KEY (`team_id`) REFERENCES `teams` (`team_id`) ON DELETE SET NULL;

--
-- Constraints for table `coaches`
--
ALTER TABLE `coaches`
  ADD CONSTRAINT `FK_COACHES_TID` FOREIGN KEY (`team_id`) REFERENCES `teams` (`team_id`) ON DELETE SET NULL;

--
-- Constraints for table `games`
--
ALTER TABLE `games`
  ADD CONSTRAINT `FK_GAMES_AID` FOREIGN KEY (`arena_id`) REFERENCES `arenas` (`arena_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `FK_GAMES_ATID` FOREIGN KEY (`away_team_id`) REFERENCES `teams` (`team_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_GAMES_HTID` FOREIGN KEY (`home_team_id`) REFERENCES `teams` (`team_id`) ON DELETE CASCADE;

--
-- Constraints for table `players`
--
ALTER TABLE `players`
  ADD CONSTRAINT `FK_PLAYERS_TID` FOREIGN KEY (`team_id`) REFERENCES `teams` (`team_id`) ON DELETE SET NULL;

--
-- Constraints for table `seasons`
--
ALTER TABLE `seasons`
  ADD CONSTRAINT `FK_SEASONS_TID` FOREIGN KEY (`team_id`) REFERENCES `teams` (`team_id`) ON DELETE CASCADE;

--
-- Constraints for table `statistics`
--
ALTER TABLE `statistics`
  ADD CONSTRAINT `FK_STATS_GID` FOREIGN KEY (`game_id`) REFERENCES `games` (`game_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_STATS_PID` FOREIGN KEY (`player_id`) REFERENCES `players` (`player_id`) ON DELETE CASCADE;

--
-- Constraints for table `teams`
--
ALTER TABLE `teams`
  ADD CONSTRAINT `FK_TEAMS_AID` FOREIGN KEY (`arena_id`) REFERENCES `arenas` (`arena_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `FK_TEAMS_CID` FOREIGN KEY (`coach_id`) REFERENCES `coaches` (`coach_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
