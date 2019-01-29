--
-- Tabellenstruktur für Tabelle `User`
--

CREATE TABLE `User` (
  `userId` int(11) NOT NULL,
  `firstName` varchar(64) NOT NULL,
  `lastName` varchar(64) NOT NULL,
  `email` varchar(64) NOT NULL,
  `gender` enum('Male','Female') NOT NULL DEFAULT 'Male',
  `password` varchar(255) NOT NULL DEFAULT 'unset',
  `last_login` date NOT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `activePlayer` tinyint(1) NOT NULL DEFAULT '1',
  `reporter` tinyint(1) NOT NULL DEFAULT '0',
  `playerId` text NOT NULL,
  `clubId` int(11) NOT NULL,
  `bday` date NOT NULL,
  `phone` varchar(30) NOT NULL,
  `image` text NOT NULL,
  `dsgvo` int(11) NOT NULL,
  `dsgvo_timestamp` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `User`
  ADD PRIMARY KEY (`userId`);

ALTER TABLE `User`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT;


--
-- Tabellenstruktur für Tabelle `UserPassHash`
--

CREATE TABLE `UserPassHash` (
  `userId` int(11) NOT NULL,
  `token` text NOT NULL,
  `ip` text NOT NULL,
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `valid` int(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


--
--  CLUBS
--

CREATE TABLE `Club` (
  `clubId` int(11) NOT NULL,
  `sort` int(1) NOT NULL DEFAULT '1',
  `visible` int(1) DEFAULT '1',
  `name` text NOT NULL,
  `clubNumber` text NOT NULL,
  `association` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `Club`
  ADD PRIMARY KEY (`clubId`);

ALTER TABLE `Club`
  MODIFY `clubId` int(11) NOT NULL AUTO_INCREMENT;

--
--  Team
--
CREATE TABLE `Team` (
  `teamId` int(11) NOT NULL,
  `user1Id` int(11) NOT NULL,
  `user2Id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `Team`
  ADD PRIMARY KEY (`teamId`),
  ADD KEY `user1Id` (`user1Id`),
  ADD KEY `user2Id` (`user2Id`);

ALTER TABLE `Team`
  MODIFY `teamId` int(11) NOT NULL AUTO_INCREMENT;

--
--  UserStaff
--
CREATE TABLE `UserStaff` (
  `staffId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `position` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `row` int(11) NOT NULL,
  `sort` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `UserStaff`
  ADD PRIMARY KEY (`staffId`);


ALTER TABLE `UserStaff`
  MODIFY `staffId` int(11) NOT NULL AUTO_INCREMENT;

--
--  tournament
--
CREATE TABLE `Tournament` (
  `tournamentID` int(11) NOT NULL,
  `reporterId` int(11) NOT NULL,
  `openSubscription` int(1) NOT NULL DEFAULT '1',
  `name` varchar(255) NOT NULL,
  `tournamentType` enum('NBV','FUN','OTHER') NOT NULL,
  `place` text NOT NULL,
  `startdate` date NOT NULL,
  `enddate` date NOT NULL,
  `deadline` date NOT NULL,
  `link` text NOT NULL,
  `classification` text NOT NULL,
  `additionalClassification` text NOT NULL,
  `discipline` text NOT NULL,
  `visible` int(1) NOT NULL DEFAULT '1',
  `latitude` float(10,6) NOT NULL,
  `longitude` float(10,6) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `Tournament`
  ADD PRIMARY KEY (`tournamentID`),
  ADD UNIQUE KEY `tournamentID` (`tournamentID`),
  ADD KEY `tournamentID_2` (`tournamentID`);


ALTER TABLE `Tournament`
  MODIFY `tournamentID` int(11) NOT NULL AUTO_INCREMENT;

--
--  TournamentClass
--
CREATE TABLE `TournamentClass` (
  `classID` int(11) NOT NULL,
  `tournamentID` int(11) NOT NULL,
  `name` varchar(10) NOT NULL,
  `description` text NOT NULL,
  `modus` varchar(10) NOT NULL,
  `visible` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `TournamentClass`
  ADD PRIMARY KEY (`classID`,`tournamentID`);


ALTER TABLE `TournamentClass`
  MODIFY `classID` int(11) NOT NULL AUTO_INCREMENT;

--
--  TournamentPlayer
--
CREATE TABLE `TournamentPlayer` (
  `tournamentPlayerId` int(11) NOT NULL,
  `tournamentID` int(11) NOT NULL,
  `playerID` int(11) NOT NULL,
  `partnerID` int(11) DEFAULT NULL,
  `classification` text NOT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `fillingDate` datetime NOT NULL,
  `reporterID` int(11) NOT NULL,
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `TournamentPlayer`
  ADD PRIMARY KEY (`tournamentPlayerId`);


ALTER TABLE `TournamentPlayer`
  MODIFY `tournamentPlayerId` int(11) NOT NULL AUTO_INCREMENT;

--
-- TournamentBackup
--
CREATE TABLE `TournamentBackup` (
  `backupId` int(11) NOT NULL,
  `TournamentId` int(11) NOT NULL,
  `data` text NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `TournamentBackup`
  ADD PRIMARY KEY (`backupId`);


ALTER TABLE `TournamentBackup`
  MODIFY `backupId` int(11) NOT NULL AUTO_INCREMENT;







--
--  Table: eloRanking
--
/*
CREATE TEMPORARY TABLE `EloRanking` (
  `userId` int(11) NOT NULL,
  `points` INT NOT NULL DEFAULT 1000,
  `serie`  varchar(64) NOT NULL,
  `lastGame` timestamp NOT NULL
)
*/

CREATE TABLE `eloGames` (
  `gameId`      int(11) NOT NULL,
  `playerId`    int(11) NOT NULL,
  `opponentId`  int(11) NOT NULL,
  `sets`        varchar(64) NOT NULL,
  `winnerId`    int(11) NOT NULL,
  `time`        timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
)





--
--  SETTINGS
-- @TODO: upcoming feature -> from ini to sql
/*
CREATE TABLE `Settings` (
  `id` int(11) NOT NULL,
  `dataType` enum('Integer','Boolean','Text','String') NOT NULL,
  `name` varchar(100) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `Settings`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `Settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
*/
