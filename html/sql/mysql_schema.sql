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
  `playerId` text NULL DEFAULT NULL,
  `bday` date NULL DEFAULT NULL,
  `phone` varchar(30) NOT NULL,
  `image` text NOT NULL,
  `dsgvo` int(11) NOT NULL,
  `dsgvo_timestamp` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `User` ADD PRIMARY KEY (`userId`);
ALTER TABLE `User` MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT;


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
-- PLAYER
--
CREATE TABLE `Player` (
  `playerId` int(11) NOT NULL,
  `playerNr` varchar(64) NULL,
  `clubId`   int(11) NOT NULL,
  `firstName` varchar(64) NOT NULL,
  `lastName` varchar(64) NOT NULL,
  `gender` enum('Male','Female') NOT NULL DEFAULT 'Male',
  `bday` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `Player` ADD PRIMARY KEY (`playerId`);
ALTER TABLE `Player` MODIFY `playerId` int(11) NOT NULL AUTO_INCREMENT;

--
--  CLUBS
--

CREATE TABLE `Club` (
  `clubId` int(11) NOT NULL,
  `sort` int(1) NOT NULL DEFAULT '1',
  `visible` int(1) DEFAULT '1',
  `name` text NOT NULL,
  `clubNr` text NOT NULL,
  `association` varchar(10)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `Club` ADD PRIMARY KEY (`clubId`);
ALTER TABLE `Club` MODIFY `clubId` int(11) NOT NULL AUTO_INCREMENT;

INSERT `Club` (name) VALUES ('FREI');

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

ALTER TABLE `UserStaff` ADD PRIMARY KEY (`staffId`);
ALTER TABLE `UserStaff` ADD UNIQUE(`staffId`);
ALTER TABLE `UserStaff` MODIFY `staffId` int(11) NOT NULL AUTO_INCREMENT;

--
--  tournament
--
CREATE TABLE `Tournament` (
  `tournamentId` int(11) NOT NULL,
  `reporterId` int(11) NOT NULL,
  `openSubscription` int(1) NOT NULL DEFAULT '1',
  `name` varchar(255) NOT NULL,
  `tournamentType` enum('NBV','FUN','OTHER') NOT NULL,
  `place` text NOT NULL,
  `startdate` datetime NOT NULL,
  `enddate` date NOT NULL,
  `deadline` datetime NOT NULL,
  `link` text NOT NULL,
  `classification` text NOT NULL,
  `additionalClassification` text NOT NULL,
  `discipline` text NOT NULL,
  `visible` int(1) NOT NULL DEFAULT '1',
  `latitude` float(10,6) NOT NULL,
  `longitude` float(10,6) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `Tournament` ADD PRIMARY KEY (`tournamentId`);
ALTER TABLE `Tournament` MODIFY `tournamentId` int(11) NOT NULL AUTO_INCREMENT;

--
--  TournamentClass
--
CREATE TABLE `TournamentClass` (
  `classId` int(11) NOT NULL,
  `tournamentId` int(11) NOT NULL,
  `name` varchar(10) NOT NULL,
  `description` text NOT NULL,
  `modus` varchar(10) NOT NULL,
  `visible` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `TournamentClass` ADD PRIMARY KEY (`classId`,`tournamentId`);
ALTER TABLE `TournamentClass` MODIFY `classId` int(11) NOT NULL AUTO_INCREMENT;

--
--  TournamentPlayer
--
CREATE TABLE `TournamentPlayer` (
  `tournamentPlayerId` int(11) NOT NULL,
  `tournamentId` int(11) NOT NULL,
  `playerId` int(11) NOT NULL,
  `partnerId` int(11) DEFAULT NULL,
  `classification` text NOT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `locked` tinyint(1) NOT NULL DEFAULT '0',
  `fillingDate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reporterId` int(11) NOT NULL,
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `TournamentPlayer` ADD PRIMARY KEY (`tournamentPlayerId`);
ALTER TABLE `TournamentPlayer` MODIFY `tournamentPlayerId` int(11) NOT NULL AUTO_INCREMENT;

--
-- TournamentBackup
--
CREATE TABLE `TournamentBackup` (
  `backupId` int(11) NOT NULL,
  `tournamentId` int(11) NOT NULL,
  `data` text NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `TournamentBackup` ADD PRIMARY KEY (`backupId`);
ALTER TABLE `TournamentBackup` MODIFY `backupId` int(11) NOT NULL AUTO_INCREMENT;





--
--  Table: eloRanking
--

CREATE TABLE `EloRanking` (
  `playerId` int(11) NOT NULL,
  `points` INT NOT NULL DEFAULT 1000,
  `serie`  varchar(64) NOT NULL,
  `win` int(11) NOT NULL DEFAULT '0',
  `loss` int(11) NOT NULL DEFAULT '0',
  `lastGame` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `EloRanking` ADD PRIMARY KEY (`playerId`);


CREATE TABLE `EloGames` (
  `gameId`      INT(11) NOT NULL,
  `hidden`      INT(1) NOT NULL DEFAULT '0',
  `playerId`    INT(11) NOT NULL,
  `opponentId`  INT(11) NOT NULL,
  `sets`        varchar(64) NOT NULL,
  `winnerId`    int(11) NOT NULL,
  `gameTime`    timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `EloGames` ADD PRIMARY KEY (`gameId`);


--
--  Table: Notification
--
CREATE TABLE `Notification` (
  `notificationId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `text` text NOT NULL,
  `isRead` bit(1) NOT NULL DEFAULT b'0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `Notification` ADD PRIMARY KEY (`notificationId`);
ALTER TABLE `Notification` MODIFY `notificationId` int(11) NOT NULL AUTO_INCREMENT;

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


--
--  Table: Faq
--
CREATE TABLE `Faq` (
  `faqId` int(11) NOT NULL,
  `categoryId` INT NOT NULL,
  `title` varchar(200) NOT NULL,
  `text` text NOT NULL,
  `createdBy` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastEdited` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `Faq` ADD PRIMARY KEY (`faqId`);
ALTER TABLE `Faq` MODIFY `faqId` int(11) NOT NULL AUTO_INCREMENT;

--
--  Table: Category
--

CREATE TABLE `Category` (
  `categoryId` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `title` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `Category` ADD PRIMARY KEY (`categoryId`);
ALTER TABLE `Category` MODIFY `categoryId` int(11) NOT NULL AUTO_INCREMENT;


--
--  Table: News
--
CREATE TABLE `News` (
  `newsId` int(11) NOT NULL,
  `categoryId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `text` text NOT NULL,
  `createdBy` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastEdited` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `News` ADD PRIMARY KEY (`newsId`);
ALTER TABLE `News` MODIFY `newsId` int(11) NOT NULL AUTO_INCREMENT;


--
--  Table: log
--
CREATE TABLE `Log` (
  `uid` int(11) NOT NULL,
  `userId` int(11) DEFAULT NULL,
  `action` varchar(200) CHARACTER SET latin1 NOT NULL,
  `fromTable` varchar(200) CHARACTER SET latin1 DEFAULT NULL,
  `tstamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `details` varchar(200) CHARACTER SET latin1 DEFAULT NULL,
  `logdata` text CHARACTER SET latin1 NOT NULL,
  `ip` varchar(15) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `Log` ADD PRIMARY KEY (`uid`);
ALTER TABLE `Log` MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT;
