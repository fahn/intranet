/********************************************************
 * This file belongs to the Badminton Ranking Project.  *
 *                                                      *
 * Copyright 2017                                       *
 *                                                      *
 * All Rights Reserved                                  *
 *                                                      *
 * Copying, distribution, usage in any form is not      *
 * allowed without  written permit.                     *
 *                                                      *
 * Philipp M. Fischer (phil.m.fischer@googlemail.com)   *
 *                                                      *
 ********************************************************/

SET foreign_key_checks = 0;
SET autocommit = 0;
# SET GLOBAL event_scheduler = 1;



USE BRDB;

SET foreign_key_checks = 1;

/**
 * The table for a central game
 */
CREATE TABLE GameMatch (
    matchId             INT              AUTO_INCREMENT,
    dateTime            TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (matchId)
);

/**
 * This index assures that a player is not assigned twice to the same game
 */
CREATE UNIQUE INDEX IndexUniqueGameDate ON GameMatch (dateTime);


/**
 * The table for creating a game side. There are always two
 * game sides a side A and a side B. When creating a game
 * meaning inserting data make sure that both sides are created.
 * All rows create the PK, hence there cannot be more than an A
 * and B side per game. 
 */
CREATE TABLE GameSide (
    matchId             INT                             NOT NULL,
    side                ENUM ("Side A", "Side B")       NOT NULL, 

    PRIMARY KEY (matchId, side),
    FOREIGN KEY (matchId) REFERENCES GameMatch(matchId) ON DELETE CASCADE ON UPDATE CASCADE
);

/**
 * This table holds the points that were played per set.
 * Points belong to a team (side). Due to the PK there can be
 * only points for team A and B per set. Make sure that both 
 * are created per set. There is no need to create points in 
 * the table if e.g. the 2nd or 3rd set were not played
 */
CREATE TABLE GameSetPoint (
    matchId             INT                             NOT NULL,
    side                ENUM ("Side A", "Side B")       NOT NULL,
    setNr               INT                             NOT NULL,
    setPoints           INT                             NOT NULL,
    
    PRIMARY KEY (matchId, side, setNr),
    FOREIGN KEY (matchId)       REFERENCES GameMatch(matchId)       ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (matchId, side) REFERENCES GameSide(matchId, side)  ON DELETE CASCADE ON UPDATE CASCADE
);

/**
 * Each game has exactly one winner and one looser.
 * Make sure to enter both when a game is inserted to the DB.
 * The winner does not necessarily be the one with most set
 * points or won sets. it is still possible that a player
 * leading player gives up in third match e.g.
 */
CREATE TABLE GameWinner (
    matchId             INT                             NOT NULL,
    side                ENUM ("Side A", "Side B")       NOT NULL,
    
    PRIMARY KEY (matchId),
    FOREIGN KEY (matchId)        REFERENCES GameMatch(matchId)      ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (matchId, side)  REFERENCES GameSide(matchId, side) ON DELETE CASCADE ON UPDATE CASCADE
);

/**
 * This is the user table which contains all users
 * that can log in to the Badminton Ranking
 * Application. It also tells which player is active
 * a reporter and so on.
 */
CREATE TABLE User (
    userId              INT                     AUTO_INCREMENT,
    firstName           VARCHAR(64)             NOT NULL,
    lastName            VARCHAR(64)             NOT NULL,
    email               VARCHAR(64)             NOT NULL UNIQUE,
    gender              ENUM ("Male", "Female") NOT NULL DEFAULT "Male",
    password            VARCHAR(255)            NOT NULL DEFAULT "unset",
    admin               BOOLEAN                 NOT NULL DEFAULT 0,
    activePlayer        BOOLEAN                 NOT NULL DEFAULT 1,
    reporter            BOOLEAN                 NOT NULL DEFAULT 0,
    
    PRIMARY KEY (userId)
);

CREATE TABLE Team (
    teamId              INT                     AUTO_INCREMENT,
    user1Id             INT                     NOT NULL,
    user2Id             INT                     NOT NULL,
    PRIMARY KEY (teamId),
    FOREIGN KEY (user1Id)         REFERENCES User(userId)      ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (user2Id)         REFERENCES User(userId)      ON DELETE CASCADE ON UPDATE CASCADE
);

/**
 * This table contains all players that were part of a game.
 * A game can have two or four players by the Badminton rules
 * and technically a player can not play twice in one game. Therefore
 * the user is part of the PK preventing from assigning a User two
 * times to a game.
 */
CREATE TABLE GamePlayer (
    matchId             INT                             NOT NULL,
    side                ENUM ("Side A", "Side B")       NOT NULL,
    position            ENUM ("Pos 1", "Pos 2")         NOT NULL,
    userId              INT                             NOT NULL,
 
    PRIMARY KEY (matchId, side, position),
    FOREIGN KEY (matchId, side)         REFERENCES GameSide(matchId, side)      ON DELETE CASCADE ON UPDATE CASCADE
);

/**
 * This index assures that a player is not assigned twice to the same game
 */
CREATE UNIQUE INDEX IndexUniqueGamePlayer ON GamePlayer (matchId, userId);

/**
 * This table holds the rank points that were given by the game.
 * A User can only get once points per game in this table.
 */
CREATE TABLE GamePlayerRankPoint (
    matchId             INT                      NOT NULL,
    userId              INT                      NOT NULL,
    points              DOUBLE PRECISION(10,3)   NOT NULL    DEFAULT 0.0,
    rankType            ENUM("Alltime", "Overall", "Discipline"),
    
    PRIMARY KEY (matchId, userId, rankType),
    FOREIGN KEY (matchId)       REFERENCES GameMatch(matchId)   ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (userId)        REFERENCES User(userId)         ON DELETE CASCADE ON UPDATE CASCADE
);    

/**
 * Manual rank points are inteneded to override points.
 * Usually points are calculated by the result of a match
 * but sometimes need adjustments. This table will be merged
 * with the Game rank points into a DB View.
 */
CREATE TABLE GamePlayerRankPointOverride (
    dateTime            TIMESTAMP                   NOT NULL    DEFAULT CURRENT_TIMESTAMP,
    userId              INT                         NOT NULL,
    points              DOUBLE PRECISION(10,3)      NOT NULL    DEFAULT 0.0,
    rankType            ENUM("Alltime", "Overall", "Discipline"),
    type                ENUM("Cooldown", "Manual")  NOT NULL    DEFAULT "Manual",
    
    PRIMARY KEY (dateTime, userId, rankType),
    FOREIGN KEY (userId)        REFERENCES User(userId)         ON DELETE CASCADE ON UPDATE CASCADE
);  

/**
 * This table holds the rank points that were given by the game.
 * A Team can only get once points per game in this table.
 */
CREATE TABLE GameTeamRankPoint (
    matchId             INT                     NOT NULL,
    teamId              INT                     NOT NULL,
    points              DOUBLE PRECISION(10,3)  NOT NULL    DEFAULT 0.0,
    rankType            ENUM("Overall", "Discipline"),
    
    PRIMARY KEY (matchId, teamId, rankType),
    FOREIGN KEY (matchId)       REFERENCES GameMatch(matchId)   ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (teamId)        REFERENCES Team(teamId)         ON DELETE CASCADE ON UPDATE CASCADE
);    

/**
 * Manual rank points are inteneded to override points.
 * Usually points are calculated by the result of a match
 * but sometimes need adjustments. This table will be merged
 * with the Game rank points into a DB View.
 */
CREATE TABLE GameTeamRankPointOverride (
    dateTime            TIMESTAMP                   NOT NULL    DEFAULT CURRENT_TIMESTAMP,
    teamId              INT                         NOT NULL,
    points              DOUBLE PRECISION(10,3)      NOT NULL    DEFAULT 0.0,
    rankType            ENUM("Overall", "Discipline"),
    type                ENUM("Cooldown", "Manual")  NOT NULL    DEFAULT "Manual",
    
    PRIMARY KEY (dateTime, teamId, rankType),
    FOREIGN KEY (teamId)        REFERENCES Team(teamId)         ON DELETE CASCADE ON UPDATE CASCADE
);    

/**
 * This tables contains the settings used for calculating the rank
 * points all methods will look up this table to get there
 * correct settings
 */
CREATE TABLE RankPointSetting (
    validFrom           TIMESTAMP                NOT NULL    DEFAULT CURRENT_TIMESTAMP,
    pointsWin           DOUBLE PRECISION(10,3)   NOT NULL    DEFAULT  2.0,
    pointsLoss          DOUBLE PRECISION(10,3)   NOT NULL    DEFAULT -1.0,
    prcChase            DOUBLE PRECISION(10,3)   NOT NULL    DEFAULT  5.0,
    prcCoolDown         DOUBLE PRECISION(10,3)   NOT NULL    DEFAULT  5.0,
    PRIMARY KEY (validFrom)
);
