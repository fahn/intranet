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
