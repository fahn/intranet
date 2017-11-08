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

USE BRDB;

/**
 * This view combines the rank points from played games
 * together with the ones that have been manually introduced
 */
CREATE VIEW RankPlayerPoint AS
SELECT 
    GameMatch.dateTime as dateTime,
    userId,
    points,
    rankType
FROM
    GameMatch,
    GamePlayerRankPoint
WHERE
    GameMatch.matchId = GamePlayerRankPoint.matchId
UNION
SELECT 
    dateTime as dateTime,
    userId,
    points,
    rankType
FROM
    GamePlayerRankPointOverride
ORDER BY dateTime DESC;

CREATE VIEW RankTeamPoint AS
SELECT 
    GameMatch.dateTime as dateTime,
    teamId,
    points,
    rankType
FROM
    GameMatch,
    GameTeamRankPoint
WHERE
    GameMatch.matchId = GameTeamRankPoint.matchId
UNION
SELECT 
    dateTime as dateTime,
    teamId,
    points,
    rankType
FROM
    GameTeamRankPointOverride
ORDER BY dateTime DESC;

DELIMITER //

/**
 * Simple function that concatenates the Users name
 * always in the same manner.
 */
CREATE FUNCTION _UserFullName(
    firstName   VARCHAR(64),
    lastName    VARCHAR(64))
RETURNS VARCHAR(129) 
DETERMINISTIC
CONTAINS SQL
BEGIN
  RETURN CONCAT(firstName, " ", lastName);
END//

/**
 * This view displayes all activePlayers
 */
CREATE VIEW UserActivePlayer AS
SELECT
    *,
    userId as playerId,
    _UserFullName(firstName, lastName) as fullName
FROM
    User
WHERE
    activePlayer = TRUE
ORDER BY userId;

/**
 * This view displayes all activePlayers
 */
CREATE VIEW TeamActivePlayer AS
SELECT
    team.*,
    team.user1Id AS player1Id,
    team.user2Id AS player2Id,
    u1.firstName AS player1FirstName,
    u1.lastName  AS player1LastName,
    u2.firstName AS player2FirstName,
    u2.lastName  AS player2LastName,
    CONCAT(u1.lastName, "-", u2.lastName)    AS teamName
FROM
    Team AS team
LEFT JOIN User u1 ON (team.user1Id = u1.userId)
LEFT JOIN User u2 ON (team.user2Id = u2.userId)
WHERE
    u1.activePlayer = TRUE AND u2.activePlayer = TRUE
ORDER BY teamId;

/**
 * This method represents the constant for maximum rank points
 */
CREATE FUNCTION _RankPointMax()
RETURNS DOUBLE PRECISION(10,3) 
DETERMINISTIC
CONTAINS SQL
BEGIN
    RETURN 100.000;
END//

/**
 * This function represents the constant for minimum rank points 
 */
CREATE FUNCTION _RankPointMin()
RETURNS DOUBLE PRECISION(10,3) 
DETERMINISTIC
CONTAINS SQL
BEGIN
    RETURN 000.000;
END//

/**
 * Method to create a counter within a session for numbering data sets in tables
 */
CREATE FUNCTION _CountSession()
RETURNS INT
NOT DETERMINISTIC
NO SQL
BEGIN
  SET @countVar := IFNULL(@countVar,0) + 1;
  return @countVar;
END//

/**
 * This method checks and hands back the given point value.
 * if it is out of the rank point ranges it will be limited to it
 */
CREATE FUNCTION _RankPointLimit(
    rankPoints DOUBLE PRECISION(10,3)
)
RETURNS DOUBLE PRECISION(10,3) 
DETERMINISTIC
CONTAINS SQL
BEGIN
    IF rankPoints > _RankPointMax() THEN
       RETURN _RankPointMax();
    ELSEIF rankPoints < _RankPointMin() THEN
       RETURN _RankPointMin();
    END IF;
    RETURN rankPoints;
END//

/**
 * This method calculates if the game has been one of the following types
 * Single or Double
 */
CREATE FUNCTION _GameSideType(
    inMatchId       INT,
    inSide          ENUM("Side A", "Side B")
)
RETURNS ENUM("Single", "Double")
NOT DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE playerCount     INT;
    DECLARE gameSideType    ENUM ("Double", "Single");
 
    -- First check if it is a one player team or
    -- a two player game 
    SELECT COUNT(*) INTO playerCount FROM GamePlayer WHERE matchId = inMatchId AND side = inSide LOCK IN SHARE MODE;
    
    IF playerCount = 1 THEN
        SET gameSideType = "Single";
    ELSE
        SET gameSideType = "Double";
    END IF; 
    RETURN gameSideType;
END//

/**
 * This method calculates if the game has been one of the following types
 * Single or Double. This method is optimized to judge the game on the
 * gender input rather than on the game id and subsequent sql calls. this will
 * optimize access times in the result tables
 */
CREATE FUNCTION _GameTypeDirect(
    genderA1    ENUM("Male", "Female"),
    genderB1    ENUM("Male", "Female"),
    genderA2    ENUM("Male", "Female"),
    genderB2    ENUM("Male", "Female")
)
RETURNS ENUM("Single", "Double", "Other")
DETERMINISTIC
NO SQL
BEGIN
    DECLARE gameType        ENUM ("Double", "Single", "Other");
    
    IF (genderA2 IS NULL OR genderA2 = 0) AND (genderB2 IS NULL OR genderB2 = 0) THEN
        SET gameType = "Single";
    ELSEIF (genderA2 IS NULL OR genderA2 = 0) OR (genderB2 IS NULL OR genderB2 = 0) THEN
        SET gameType = "Other";
    ELSE
        SET gameType = "Double";
    END IF; 
    
    RETURN gameType;
END//

/**
 * This method calculates if the game has been one of the following types
 * Single or Double
 */
CREATE FUNCTION _GameType(
    inMatchId       INT
)
RETURNS ENUM("Single", "Double", "Other")
NOT DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE gameType        ENUM ("Double", "Single", "Other");
    DECLARE genderPlayerA1  ENUM("Male", "Female");
    DECLARE genderPlayerA2  ENUM("Male", "Female");
    DECLARE genderPlayerB1  ENUM("Male", "Female");
    DECLARE genderPlayerB2  ENUM("Male", "Female");
    DECLARE gameSideType    ENUM("Single Men", "Single Women", "Double Men", "Double Women", "Double Mixed");
   
    SELECT u.gender INTO genderPlayerA1 FROM GamePlayer p, User u WHERE p.matchId = inMatchId AND p.side = "Side A" AND p.position = "POS 1" AND p.userId = u.userId LOCK IN SHARE MODE;
    SELECT u.gender INTO genderPlayerA2 FROM GamePlayer p, User u WHERE p.matchId = inMatchId AND p.side = "Side A" AND p.position = "POS 2" AND p.userId = u.userId LOCK IN SHARE MODE;
    SELECT u.gender INTO genderPlayerB1 FROM GamePlayer p, User u WHERE p.matchId = inMatchId AND p.side = "Side B" AND p.position = "POS 1" AND p.userId = u.userId LOCK IN SHARE MODE;
    SELECT u.gender INTO genderPlayerB2 FROM GamePlayer p, User u WHERE p.matchId = inMatchId AND p.side = "Side B" AND p.position = "POS 2" AND p.userId = u.userId LOCK IN SHARE MODE;
    
    SET gameType = _GameTypeDirect(
        genderPlayerA1,
        genderPlayerB1,
        genderPlayerA2,
        genderPlayerB2);
    
    RETURN gameType;
END//

/**
 * This method hands back the Team ID for a given side
 * of the game 
 */
CREATE FUNCTION _TeamIdForGameSide(
    inMatchId   INT,
    inSide      ENUM("Side A", "Side B")
)
RETURNS INT
NOT DETERMINISTIC
READS SQL DATA
BEGIN
	DECLARE user1Id INT;
	DECLARE user2Id INT;
	DECLARE retTeamId  INT;

	SELECT userId INTO user1Id FROM GamePlayer WHERE matchId = inMatchId AND side = inSide AND position = "Pos 1" LOCK IN SHARE MODE;
    SELECT userId INTO user2Id FROM GamePlayer WHERE matchId = inMatchId AND side = inSide AND position = "Pos 2" LOCK IN SHARE MODE;
    
    SET retTeamId = _TeamIdForUsers(user1Id, user2Id);
    
    RETURN retTeamId;
END//

/**
 * This method hands back the Team ID for a given side
 * of the game 
 */
CREATE FUNCTION _TeamIdForUsers(
    inUser1Id   INT,
    inUser2Id   INT
)
RETURNS INT
NOT DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE retTeamId INT;
    SET retTeamId = 0;
    SELECT teamId INTO retTeamId FROM Team WHERE (user1Id = inUser1Id AND user2Id = inUser2Id) OR (user1Id = inUser2Id AND user2Id = inUser1Id) LOCK IN SHARE MODE;
    
    RETURN retTeamId;
END//

/**
 * This function is needed if players of a team
 * for a Double Men, a Double Women or a Double Mixed
 */
CREATE FUNCTION _GetTeamGender(
    gender1    ENUM("Male", "Female"),
    gender2    ENUM("Male", "Female"))
RETURNS ENUM("Double Men", "Double Women", "Double Mixed") 
DETERMINISTIC
CONTAINS SQL
BEGIN
    DECLARE retTeamGender ENUM("Double Men", "Double Women", "Double Mixed");
   
    IF (gender1 = "Male" AND gender2 = "Male") THEN
        SET retTeamGender = "Double Men";
    ELSEIF (gender1 = "Female" AND gender2 = "Female") THEN
        SET retTeamGender = "Double Women";
    ELSE
        SET retTeamGender = "Double Mixed";
    END IF;
    
    RETURN retTeamGender;
END//

/**
 * Call this method to find out the rank type of the game
 * the rank type depends on who is playing in terms of gender
 * such as a double men is playing against another double men 
 * team or maybe against a mixed.
 */
CREATE FUNCTION _GameRankType(
    genderA1    ENUM("Male", "Female"),
    genderB1    ENUM("Male", "Female"),
    genderA2    ENUM("Male", "Female"),
    genderB2    ENUM("Male", "Female"))
RETURNS ENUM("Alltime", "Overall", "Discipline") 
DETERMINISTIC
CONTAINS SQL
BEGIN
    DECLARE retRankType ENUM("Alltime", "Overall", "Discipline");
    SET retRankType = "Overall";
    
    IF genderA2 IS NOT NULL AND genderB2 IS NOT NULL THEN
        IF _GetTeamGender(genderA1, genderA2) = _GetTeamGender(genderB1, genderB2) THEN
            SET retRankType = "Discipline";
        END IF;
    ELSEIF genderA2 IS NOT NULL OR genderB2 IS NOT NULL THEN
        SET retRankType = "Alltime";
    ELSE
        IF genderA1 = genderB1 THEN
            SET retRankType = "Discipline";
        END IF;
    END IF;
    
    RETURN retRankType;
END//

/**
 * This method calculates if the game has been one of the following types
 * It uses direct gender input rather than asking from the matchID. This 
 * method provides performance improvements in complex SQL Result queries.
 * Single Men
 * Single Women
 * Double Men
 * Double Women
 * Double Mixed
 */
CREATE FUNCTION _GameSideTypeGenderDirect(
    genderPlayer1   ENUM("Male", "Female"),
    genderPlayer2   ENUM("Male", "Female")
)
RETURNS ENUM("Single Men", "Single Women", "Double Men", "Double Women", "Double Mixed")
DETERMINISTIC
NO SQL 
BEGIN
    DECLARE gameSideType    ENUM("Single Men", "Single Women", "Double Men", "Double Women", "Double Mixed");
   
    IF genderPlayer2 IS NULL OR genderPlayer2 = 0 THEN
       -- Here we have the single player games
        IF genderPlayer1 = "Male" THEN
            SET gameSideType = "Single Men";
        ELSE
            SET gameSideType = "Single Women";
        END IF;
    ELSE
        SET gameSideType = _GetTeamGender(genderPlayer1, genderPlayer2);
    END IF; 
    RETURN gameSideType;
END//

/**
 * This method calculates if the game has been one of the following types
 * Single Men
 * Single Women
 * Double Men
 * Double Women
 * Double Mixed
 */
CREATE FUNCTION _GameSideTypeGender(
    inMatchId       INT,
    inSide          ENUM("Side A", "Side B")
)
RETURNS ENUM("Single Men", "Single Women", "Double Men", "Double Women", "Double Mixed")
NOT DETERMINISTIC
READS SQL DATA
BEGIN
	DECLARE playerCount     INT;
    DECLARE genderPlayer1   ENUM("Male", "Female");
    DECLARE genderPlayer2   ENUM("Male", "Female");
    DECLARE gameSideType    ENUM("Single Men", "Single Women", "Double Men", "Double Women", "Double Mixed");
   
   -- Here we have the two player games
    SELECT u.gender INTO genderPlayer1 FROM GamePlayer p, User u WHERE p.matchId = inMatchId AND p.side = inSide AND p.position = "POS 1" AND p.userId = u.userId LOCK IN SHARE MODE;
    SELECT u.gender INTO genderPlayer2 FROM GamePlayer p, User u WHERE p.matchId = inMatchId AND p.side = inSide AND p.position = "POS 2" AND p.userId = u.userId LOCK IN SHARE MODE;
    SET gameSideType = _GameSideTypeGenderDirect(genderPlayer1, genderPlayer2);
    RETURN gameSideType;
END//

/**
 * This function ahnds back the rank points for a single player
 * depending on the given type of "Alltime", "Overall" or "Discipline"
 * the last points up to the given date (before that date) will be given
 */
CREATE FUNCTION _RankPlayerPointUntil(
    inUserId     INT,
    inDateTime   TIMESTAMP,
    inType       ENUM ("Alltime", "Overall", "Discipline"))
RETURNS DOUBLE PRECISION(10,3)
NOT DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE rankPoints              DOUBLE PRECISION(10,3);  
	DECLARE rankPointAlltime        DOUBLE PRECISION(10,3);
    DECLARE rankPointOverall        DOUBLE PRECISION(10,3);
    DECLARE rankPointDiscipline     DOUBLE PRECISION(10,3);
    SET rankPoints            = 0.0;
    SET rankPointAlltime      = 0.0;
    SET rankPointOverall      = 0.0;
    SET rankPointDiscipline   = 0.0;
    SELECT
        points
    INTO
        rankPoints
    FROM 
        RankPlayerPoint
    WHERE
        RankPlayerPoint.userId = inUserId AND
        RankPlayerPoint.dateTime < inDateTime AND
        RankPlayerPoint.rankType = inType
    GROUP BY inUserId
    ORDER BY RankPlayerPoint.dateTime DESC
    LIMIT 1
    LOCK IN SHARE MODE;
  
    RETURN rankPoints;
END//


/**
 * This method hands back the rank points for a team for the
 * scores overall which include all double games and the discipline
 * ones which only contains the mixed, double men or women games.
 */
CREATE FUNCTION _RankTeamPointUntil(
    inTeamId    INT,
    inDateTime  TIMESTAMP,
    inType      ENUM ("Overall", "Discipline"))
RETURNS DOUBLE PRECISION(10,3)
NOT DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE rankPoints            DOUBLE PRECISION(10,3);  
    DECLARE rankPointOverall      DOUBLE PRECISION(10,3);
    DECLARE rankPointDiscipline   DOUBLE PRECISION(10,3);
    SET rankPoints            = 0.0;
    SET rankPointOverall      = 0.0;
    SET rankPointDiscipline   = 0.0;
    SELECT
        points
    INTO
        rankPoints
    FROM 
        RankTeamPoint
    WHERE
        RankTeamPoint.teamId = inTeamId AND
        RankTeamPoint.dateTime < inDateTime AND
        RankTeamPoint.rankType = inType
    GROUP BY inTeamId
    ORDER BY RankTeamPoint.dateTime DESC
    LIMIT 1
    LOCK IN SHARE MODE;
   
    RETURN rankPoints;
END//

/**
 * This method hands back the dateTime timestamp
 * of the players first game. this method is used when
 * a player is changed and game standings need to be
 * recalcukated. This can happen if games were
 * played with wrong gender being entered
 */
CREATE FUNCTION _PlayerGetFirstGame(inUserId INT)
RETURNS TIMESTAMP
NOT DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE firstGameDateTime TIMESTAMP;
    SET firstGameDateTime = NOW();
    SELECT
        dateTime
    INTO
        firstGameDateTime
    FROM 
        GameMatch,
        GamePlayer
    WHERE
        GameMatch.matchId = GamePlayer.matchId AND
        GamePlayer.userId = inUserId
    GROUP BY inUserId
    ORDER BY GameMatch.dateTime ASC
    LIMIT 1
    LOCK IN SHARE MODE;
    RETURN firstGameDateTime;
END//

/**
 * This procedure gets the latest settings
 * for calculating the rank points. In case the rules have
 * to be changed the values can be adjusted and the time
 * is kept in mind for recalculation purposes. It means
 * adding new values for tomorrow will let all games until today
 * calculate with the old values.
 */
CREATE PROCEDURE _SubProcGetRankPointSetting(
    IN  inDateTime     TIMESTAMP,
    OUT outPointsWin   DOUBLE PRECISION(10,3),
    OUT outPointsLoss  DOUBLE PRECISION(10,3),
    OUT outPrcChase    DOUBLE PRECISION(10,3)
)
NOT DETERMINISTIC
READS SQL DATA
BEGIN
    SET outPointsWin  =  2.0;
    SET outPointsLoss = -1.0;
    SET outPrcChase   =  5.0;
    SELECT
        rps.pointsWin, rps.pointsLoss, rps.prcChase
    INTO
        outPointsWin, outPointsLoss, outPrcChase
    FROM 
        RankPointSetting rps
    WHERE 
        rps.validFrom < inDateTime
    ORDER BY rps.validFrom DESC
    LIMIT 1
    LOCK IN SHARE MODE;
END//

/**
 * This method calculates the average rank points for both
 * teams in a game. An average rank for team A and Team B
 * will be handed back.
 */
CREATE PROCEDURE _SubProcRankSidePointUntil(
    IN  inMatchId                INT,
    IN  inSide                   ENUM("Side A", "Side B"),
    OUT outRankPointAlltime      DOUBLE PRECISION(10,3),
    OUT outRankPointOverall      DOUBLE PRECISION(10,3),
    OUT outRankPointDiscipline   DOUBLE PRECISION(10,3)
)
NOT DETERMINISTIC
READS SQL DATA
BEGIN
    SET outRankPointAlltime     = 0.0;
    SET outRankPointOverall     = 0.0;
    SET outRankPointDiscipline  = 0.0;
    -- Select Average Alltime Ranks for the givenside
    SELECT
        SUM(_RankPlayerPointUntil(userId, dateTime, "Alltime"))
    INTO
        outRankPointAlltime
    FROM 
        GameMatch,
        GamePlayer
    WHERE
        GameMatch.matchId = inMatchId AND
        GameMatch.matchId = GamePlayer.matchId AND
        GamePlayer.side = inSide
    LOCK IN SHARE MODE;
        
    -- Now decide if it is  team match on the given side or a single match
    IF _GameSideType(inMatchId, inSide) = "Single" THEN
	    SELECT
	        _RankPlayerPointUntil(userId, dateTime, "Overall"),
	        _RankPlayerPointUntil(userId, dateTime, "Discipline")
	    INTO
	        outRankPointOverall,
	        outRankPointDiscipline
	    FROM 
	        GameMatch,
	        GamePlayer
	    WHERE
	        GameMatch.matchId = inMatchId AND
	        GameMatch.matchId = GamePlayer.matchId AND
	        GamePlayer.side = inSide
	    LOCK IN SHARE MODE;
    ELSE
        SELECT
            _RankTeamPointUntil(_TeamIdForGameSide(inMatchId, inSide), dateTime, "Overall"),
            _RankTeamPointUntil(_TeamIdForGameSide(inMatchId, inSide), dateTime, "Discipline")
        INTO
            outRankPointOverall,
            outRankPointDiscipline
        FROM 
            GameMatch
        WHERE
            GameMatch.matchId = inMatchId
        LOCK IN SHARE MODE;
    END IF;    
END//

/**
 * This table is used to trace the execution fo the cursor
 * for updating all games starting from a given point in time
 */
DROP TABLE IF EXISTS DebugUpdateRankPoint;
CREATE TABLE DebugUpdateRankPoint (
    countId             INT                             AUTO_INCREMENT,
    inDateTime          TIMESTAMP                       NOT NULL,
    cursorMatchId       INT                             NOT NULL,
    cursorDateTime      TIMESTAMP                       NOT NULL,
    cursorDone          BOOLEAN                         NOT NULL,
    
    PRIMARY KEY (countId)
);

/**
 * Use this method to update all games starting from a given date.
 * The procedure recalculates the resulting rnaks from the games that
 * have happened at the given time and later. They will be calculated in
 * ascending order. This method is called with the date of the newly added
 * game or with the minimal time when a game's date-time has been chnaged.
 * This ensures that if a game is shifted from a week ago to today, that all ranks
 * which were calculated on the result of this game, which is now later in order
 */
CREATE PROCEDURE _SubProcUpdateGameRankPointStartingFrom(IN inDateTime TIMESTAMP)
NOT DETERMINISTIC
MODIFIES SQL DATA
BEGIN
    DECLARE cursorMatchId   INT DEFAULT 0;
    DECLARE cursorDateTime  TIMESTAMP DEFAULT NOW();
    DECLARE cursorDone      BOOLEAN DEFAULT FALSE;
    DECLARE gameCursor CURSOR FOR 
        SELECT matchId, dateTime
        FROM GameMatch
        WHERE dateTime >= inDateTime
        ORDER BY dateTime ASC;
        -- FOR UPDATE;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET cursorDone = TRUE;
 
    -- Print Into the DebugTable
    DELETE FROM DebugUpdateRankPoint;
    INSERT INTO DebugUpdateRankPoint(inDateTime, cursorDateTime, cursorMatchId, cursorDone) VALUES (inDateTime, "2010-10-10 20:20:20", 0, FALSE);
        
    -- now see which games have followed timewise including the current one
    -- and use it as a cursor to loop over all data sets and update
    -- the game results which build up on each other in terms of time
    SET cursorDone = FALSE; 
    OPEN gameCursor;
 
    getGameId: LOOP
        FETCH gameCursor INTO cursorMatchId, cursorDateTime;
        
        INSERT INTO DebugUpdateRankPoint(inDateTime, cursorDateTime, cursorMatchId, cursorDone) VALUES (inDateTime, cursorDateTime, cursorMatchId, cursorDone);
        
        IF cursorDone THEN
            LEAVE getGameId;
        END IF;
   
        -- The call to calculate the actual game needs to be placed into
        -- its very own scope together with a continue handler. The underlaying
        -- procedure does some select into calls which can also trigger the NOT
        -- FOUND SET clause and create a premature end to the cursor. Accordingly
        -- we declare all not founds for the scope to be a FALSE cursor done.
        BEGIN
	        DECLARE CONTINUE HANDLER FOR NOT FOUND SET cursorDone = FALSE;
            CALL _SubProcUpdateGameRankPointForGame(cursorMatchId);
        END;
    END LOOP getGameId;
        
    CLOSE gameCursor;
END//

/**
 * This method is called to insert the rnak points for a played game
 */
CREATE PROCEDURE _SubProcInsertRankPoint(
    IN  inMatchId                INT,
    IN  inUserA1Id               INT,
    IN  inUserB1Id               INT,
    IN  inUserA2Id               INT,
    IN  inUserB2Id               INT,
    IN  inRankType               ENUM("Alltime", "Overall", "Discipline"),
    IN  inRankA1Alltime          DOUBLE PRECISION (10,3),
    IN  inRankB1Alltime          DOUBLE PRECISION (10,3),
    IN  inRankA2Alltime          DOUBLE PRECISION (10,3),
    IN  inRankB2Alltime          DOUBLE PRECISION (10,3),
    IN  inRankSideAOverall       DOUBLE PRECISION (10,3),
    IN  inRankSideBOverall       DOUBLE PRECISION (10,3),
    IN  inRankSideADiscipline    DOUBLE PRECISION (10,3),
    IN  inRankSideBDiscipline    DOUBLE PRECISION (10,3)
)
NOT DETERMINISTIC
MODIFIES SQL DATA
BEGIN
    DECLARE teamAId                 INT;
	DECLARE teamBId                 INT;
	DECLARE isSingleGame            BOOLEAN;
	DECLARE isTeamGame              BOOLEAN;
	DECLARE testTeamGame            BOOLEAN;
	
	SET isSingleGame = (inUserA2Id  = 0 OR inUserA2Id IS     NULL) AND (inUserB2Id  = 0 OR inUserB2Id IS     NULL);
    SET isTeamGame   = (inUserA2Id != 0) AND (inUserB2Id != 0);
    
	-- First insert the alltime scores for player A1 and B1 they do always play
    INSERT INTO GamePlayerRankPoint (matchId, userId, points, rankType) VALUES (inMatchId, inUserA1Id,  inRankA1Alltime,  "Alltime")
    ON DUPLICATE KEY UPDATE points = inRankA1Alltime, rankType = "Alltime";

    INSERT INTO GamePlayerRankPoint (matchId, userId, points, rankType) VALUES (inMatchId, inUserB1Id,  inRankB1Alltime,  "Alltime")
    ON DUPLICATE KEY UPDATE points = inRankB1Alltime, rankType = "Alltime";

    -- In case it is a single player game than add the overall ranks as well
    IF isSingleGame THEN
	    INSERT INTO GamePlayerRankPoint (matchId, userId, points, rankType) VALUES (inMatchId, inUserA1Id,  inRankSideAOverall,  "Overall")
	    ON DUPLICATE KEY UPDATE points = inRankSideAOverall, rankType = "Overall";
	
	    INSERT INTO GamePlayerRankPoint (matchId, userId, points, rankType) VALUES (inMatchId, inUserB1Id,  inRankSideBOverall,  "Overall")
	    ON DUPLICATE KEY UPDATE points = inRankSideBOverall, rankType = "Overall";
	    
	    IF inRankType = "Discipline" THEN
		    INSERT INTO GamePlayerRankPoint (matchId, userId, points, rankType) VALUES (inMatchId, inUserA1Id,  inRankSideADiscipline,  "Discipline")
	        ON DUPLICATE KEY UPDATE points = inRankSideADiscipline, rankType = "Discipline";
	       
	        INSERT INTO GamePlayerRankPoint (matchId, userId, points, rankType) VALUES (inMatchId, inUserB1Id,  inRankSideBDiscipline,  "Discipline")
	        ON DUPLICATE KEY UPDATE points = inRankSideBDiscipline, rankType = "Discipline";
	    ELSE
	        DELETE FROM GamePlayerRankPoint WHERE matchId = inMatchId AND (userId = inUserA1Id OR userId = inUserB1Id) AND rankType = "Discipline";       
	    END IF;
	ELSE
        DELETE FROM GamePlayerRankPoint WHERE matchId = inMatchId AND (userId = inUserA1Id OR userId = inUserB1Id) AND rankType = "Overall";       
    END IF;   
    
    -- get the team IDs , will be 0 if a side is not yet a team
    SET teamAId = _TeamIdForUsers(inUserA1Id, inUserA2Id);
    SET teamBId = _TeamIdForUsers(inUserB1Id, inUserB2Id);

    -- If side A is a team go on
    IF teamAId != 0 THEN
        -- Add the alltime score for the second player
        INSERT INTO GamePlayerRankPoint (matchId, userId, points, rankType) VALUES (inMatchId, inUserA2Id,  inRankA2Alltime,  "Alltime")
        ON DUPLICATE KEY UPDATE points = inRankA2Alltime, rankType = "Alltime";

        IF isTeamGame THEN
            INSERT INTO GameTeamRankPoint (matchId, teamId, points, rankType) VALUES (inMatchId, teamAId,  inRankSideAOverall,  "Overall")
            ON DUPLICATE KEY UPDATE points = inRankSideAOverall, rankType = "Overall";

            IF inRankType = "Discipline" THEN
                INSERT INTO GameTeamRankPoint (matchId, teamId, points, rankType) VALUES (inMatchId, teamAId,  inRankSideADiscipline,  "Discipline")
                ON DUPLICATE KEY UPDATE points = inRankSideADiscipline, rankType = "Discipline";
            ELSE
                DELETE FROM GameTeamRankPoint WHERE matchId = inMatchId AND teamId = teamAId AND rankType = "Discipline";       
            END IF;
        ELSE
            DELETE FROM GameTeamRankPoint WHERE matchId = inMatchId AND teamId = teamAId AND rankType = "Overall";       
        END IF;
	END IF;

    -- if time b is a game go on
   IF teamBId != 0 THEN
        -- Add the alltime score for the second player
        INSERT INTO GamePlayerRankPoint (matchId, userId, points, rankType) VALUES (inMatchId, inUserB2Id,  inRankB2Alltime,  "Alltime")
        ON DUPLICATE KEY UPDATE points = inRankB2Alltime, rankType = "Alltime";

        IF isTeamGame THEN
            INSERT INTO GameTeamRankPoint (matchId, teamId, points, rankType) VALUES (inMatchId, teamBId,  inRankSideBOverall,  "Overall")
            ON DUPLICATE KEY UPDATE points = inRankSideBOverall, rankType = "Overall";

            IF inRankType = "Discipline" THEN
                INSERT INTO GameTeamRankPoint (matchId, teamId, points, rankType) VALUES (inMatchId, teamBId,  inRankSideBDiscipline,  "Discipline")
                ON DUPLICATE KEY UPDATE points = inRankSideBDiscipline, rankType = "Discipline";
            ELSE
                DELETE FROM GameTeamRankPoint WHERE matchId = inMatchId AND teamId = teamBId AND rankType = "Discipline";       
            END IF;
        ELSE
            DELETE FROM GameTeamRankPoint WHERE matchId = inMatchId AND teamId = teamBId AND rankType = "Overall";       
        END IF;
    END IF;

    -- Clean Up ranks which are not needed anymore due to updates in the games
    DELETE FROM GameTeamRankPoint WHERE matchId = inMatchId AND (teamId != teamAId AND teamId != teamBId);       
    DELETE FROM GamePlayerRankPoint WHERE matchId = inMatchId AND (userId != inUserA1Id AND userId != inUserB1Id AND userId != inUserA2Id AND userId != inUserB2Id);       
END//

/**
 * This procedure calculates the different amounts of wins
 * and losses for the various different game types than can be playerd
 * it hands back a win and loss value for alltime, overall and discipline
 * the procedure is clever enough to decide if overall and discipline is
 * base don a single player game or a team based match
 */
CREATE PROCEDURE _SubProcCalculateWinLossRankPoints(
    IN  inMatchId     INT,
    IN  inRankSideA   DOUBLE PRECISION (10,3),
    IN  inRankSideB   DOUBLE PRECISION (10,3),
    IN  inPointsWin   DOUBLE PRECISION (10,3),
    IN  inPointsLoss  DOUBLE PRECISION (10,3),
    IN  inPrcChase    DOUBLE PRECISION (10,3),
    OUT outRankSideA  DOUBLE PRECISION (10,3),
    OUT outRankSideB  DOUBLE PRECISION (10,3)
)
NOT DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE winnerSide         ENUM ("Side A", "Side B");
    DECLARE chaserSide         ENUM ("Side A", "Side B");
	DECLARE rankPointTotal     DOUBLE PRECISION (10,3);
	DECLARE rankPointPot       DOUBLE PRECISION (10,3);
    DECLARE gameSetPointsAll   DOUBLE PRECISION (10,3);
    DECLARE gameSetPointsChase DOUBLE PRECISION (10,3);
    DECLARE pointsChaserPot    DOUBLE PRECISION (10,3);
    DECLARE pointsChasedPot    DOUBLE PRECISION (10,3);
    DECLARE pointsWin          DOUBLE PRECISION (10,3);
    DECLARE pointsLoss         DOUBLE PRECISION (10,3);
    
    -- Which side own how much of the pot is decided
    -- by how many points a team won in the game
    -- we calculate the points by the chasing team
    SET gameSetPointsAll   = 0.0;
    SET gameSetPointsChase = 0.0;
    
	IF inRankSideA < inRankSideB THEN
	   SET chaserSide = "Side A";
	   SET pointsChaserPot = inRankSideA * (inPrcChase / 100.0);
	   SET pointsChasedPot = inRankSideB * (inPrcChase / 100.0);
       SET inRankSideA = inRankSideA - pointsChaserPot;
       SET inRankSideB = inRankSideB - pointsChasedPot;
	ELSE
	   SET chaserSide = "Side B";
       SET pointsChaserPot = inRankSideB * (inPrcChase / 100.0);
       SET pointsChasedPot = inRankSideA * (inPrcChase / 100.0);
       SET inRankSideB = inRankSideB - pointsChaserPot;
       SET inRankSideA = inRankSideA - pointsChasedPot;
    END IF;
    

    -- Calculate the pot which the players will fight for
    -- the pot is build up by each teams or players rank times
    -- the chase percentage plus the other sides rank times the percentage 
    SET rankPointPot = pointsChaserPot + pointsChasedPot;

    -- Get all set points and sum them up
    SELECT IFNULL(SUM(setPoints), 0.0)
    INTO   gameSetPointsAll
    FROM   GameSetPoint
    WHERE  GameSetPoint.matchId = inMatchId
    LOCK IN SHARE MODE; 
    
    SELECT IFNULL(SUM(setPoints), 0.0)
    INTO   gameSetPointsChase
    FROM   GameSetPoint
    WHERE  GameSetPoint.matchId = inMatchId AND GameSetPoint.side = chaserSide
    LOCK IN SHARE MODE; 
   
    -- A Safety check ususally there should always be set points but who knows
    -- in case the DB gets stale things may go wrong here very easily
    IF gameSetPointsAll > 0 THEN
       -- Now calculate how many points the loosing team and the winning team
       -- regains from the pot
        SET pointsChaserPot = rankPointPot * (gameSetPointsChase / gameSetPointsAll);
        SET pointsChasedPot = rankPointPot - pointsChaserPot;
    END IF;
    
    -- Pick the current winner
    SELECT  gw.side 
    INTO    winnerSide 
    FROM    GameMatch gm, GameWinner gw 
    WHERE   gm.matchId = inMatchId AND gw.matchId = gm.matchId 
    LOCK IN SHARE MODE;

    -- Finally calculate the win and loss points
    -- they are calculated by  the points from the settings for a win
    -- and a loss, plus the points which have been in the pot for the game
    -- depending on the winning side and the chasing side the chase  and chaser
    -- pots have to be assigned accrodingly.
    IF winnerSide = chaserSide THEN
        SET pointsWin  = inPointsWin  + pointsChaserPot;
        SET pointsLoss = inPointsLoss + pointsChasedPot;
    ELSE
        SET pointsWin  = inPointsWin  + pointsChasedPot;
        SET pointsLoss = inPointsLoss + pointsChaserPot;
    END IF;
    
    -- Now assign the new points to the winner
    IF winnerSide = "Side A" THEN
        SET outRankSideA = GREATEST(inRankSideA + pointsWin,  0);
        SET outRankSideB = GREATEST(inRankSideB + pointsLoss, 0);
    ELSE
        SET outRankSideB = GREATEST(inRankSideB + pointsWin,  0);
        SET outRankSideA = GREATEST(inRankSideA + pointsLoss, 0);
    END IF;
END//

/**
 * This method calculates the rank points for a specific
 * game and player. It actually redistributes the alltime points of a team
 * back to the individual players, based on their initial score.
 * this procedure is usually getting called by the method
 * that recalculates all the games from a given date.
 */
CREATE PROCEDURE _SubProcDistributeAlltimeRankPoints(
    IN inDateTime                 TIMESTAMP,
    IN inUserA1Id                 INT,
    IN inUserB1Id                 INT,
    IN inUserA2Id                 INT,
    IN inUserB2Id                 INT,
    IN inRankPointSideAAlltime    DOUBLE PRECISION(10,3),
    IN inRankPointSideBAlltime    DOUBLE PRECISION(10,3),
    OUT outRankPointA1Alltime     DOUBLE PRECISION(10,3),
    OUT outRankPointB1Alltime     DOUBLE PRECISION(10,3),
    OUT outRankPointA2Alltime     DOUBLE PRECISION(10,3),
    OUT outRankPointB2Alltime     DOUBLE PRECISION(10,3)
)
NOT DETERMINISTIC
MODIFIES SQL DATA
BEGIN
	DECLARE currentRankA1Alltime DOUBLE PRECISION(10,3);
	DECLARE currentRankB1Alltime DOUBLE PRECISION(10,3);
	DECLARE currentRankA2Alltime DOUBLE PRECISION(10,3);
	DECLARE currentRankB2Alltime DOUBLE PRECISION(10,3);
	
	DECLARE remainingPoints DOUBLE PRECISION(10,3);
    
	
	SET currentRankA1Alltime = _RankPlayerPointUntil(inUserA1Id, inDateTime, "Alltime");
	SET currentRankB1Alltime = _RankPlayerPointUntil(inUserB1Id, inDateTime, "Alltime");
	SET currentRankA2Alltime = _RankPlayerPointUntil(inUserA2Id, inDateTime, "Alltime");
	SET currentRankB2Alltime = _RankPlayerPointUntil(inUserB2Id, inDateTime, "Alltime");
	
	IF inUserA2Id IS NULL OR inUserA2Id = 0 THEN
        SET outRankPointA1Alltime = inRankPointSideAAlltime;
        SET outRankPointA2Alltime = 0.0;
	ELSE
	    SET remainingPoints = inRankPointSideAAlltime - (currentRankA1Alltime + currentRankA2Alltime);
	    SET outRankPointA1Alltime = GREATEST(currentRankA1Alltime + (remainingPoints / 2.0), 0.0);
        SET outRankPointA2Alltime = GREATEST(currentRankA2Alltime + (remainingPoints / 2.0), 0.0);
	END IF;

	IF inUserB2Id IS NULL OR inUserB2Id = 0 THEN
        SET outRankPointB1Alltime = inRankPointSideBAlltime;
        SET outRankPointB2Alltime = 0.0;
    ELSE
        SET remainingPoints = inRankPointSideBAlltime - (currentRankB1Alltime + currentRankB2Alltime);
        SET outRankPointB1Alltime = GREATEST(currentRankB1Alltime + (remainingPoints / 2.0), 0.0);
        SET outRankPointB2Alltime = GREATEST(currentRankB2Alltime + (remainingPoints / 2.0), 0.0);
    END IF;
END//

/**
 * This method calculates the rank points for a specific
 * game. this procedure is usually getting called by the method
 * that recalculates all the games from a given date.
 */
CREATE PROCEDURE _SubProcUpdateGameRankPointForGame(
    IN  inMatchId     INT
)
NOT DETERMINISTIC
MODIFIES SQL DATA
BEGIN
    DECLARE dateTime                TIMESTAMP;
    DECLARE rankSideAAlltime        DOUBLE PRECISION(10,3);
    DECLARE rankSideBallTime        DOUBLE PRECISION(10,3);
    DECLARE rankSideAOverall        DOUBLE PRECISION(10,3);
    DECLARE rankSideBOverall        DOUBLE PRECISION(10,3);
    DECLARE rankSideADiscipline     DOUBLE PRECISION(10,3);
    DECLARE rankSideBDiscipline     DOUBLE PRECISION(10,3);
    DECLARE newRankSideAAlltime     DOUBLE PRECISION(10,3);
    DECLARE newRankSideBallTime     DOUBLE PRECISION(10,3);
    DECLARE newRankSideAOverall     DOUBLE PRECISION(10,3);
    DECLARE newRankSideBOverall     DOUBLE PRECISION(10,3);
    DECLARE newRankSideADiscipline  DOUBLE PRECISION(10,3);
    DECLARE newRankSideBDiscipline  DOUBLE PRECISION(10,3);
    DECLARE newRankA1Alltime        DOUBLE PRECISION(10,3);
    DECLARE newRankB1AllTime        DOUBLE PRECISION(10,3);
    DECLARE newRankA2Alltime        DOUBLE PRECISION(10,3);
    DECLARE newRankB2AllTime        DOUBLE PRECISION(10,3);
    DECLARE pointsWin               DOUBLE PRECISION(10,3);
    DECLARE pointsLoss              DOUBLE PRECISION(10,3);
    DECLARE prcChase                DOUBLE PRECISION(10,3);
    DECLARE rankType                ENUM("Alltime", "Overall", "Discipline");
    DECLARE userA1Id                INT;
    DECLARE userA2Id                INT;
    DECLARE userB1Id                INT;
    DECLARE userB2Id                INT;
    
    -- First get player all players in case second players for a team
    -- are missing, a null is expected to be returned
    SELECT userId INTO userA1Id FROM GamePlayer WHERE matchId = inMatchId AND side = "Side A" AND position = "POS 1" LOCK IN SHARE MODE;
    SELECT userId INTO userB1Id FROM GamePlayer WHERE matchId = inMatchId AND side = "Side B" AND position = "POS 1" LOCK IN SHARE MODE;
    SELECT userId INTO userA2Id FROM GamePlayer WHERE matchId = inMatchId AND side = "Side A" AND position = "POS 2" LOCK IN SHARE MODE;
    SELECT userId INTO userB2Id FROM GamePlayer WHERE matchId = inMatchId AND side = "Side B" AND position = "POS 2" LOCK IN SHARE MODE;

    SELECT 
        _GameRankType(uA1.gender, uB1.gender, uA2.gender, uB2.gender),
        uA1.userId, uB1.userId, uA2.userId, uB2.userId
    INTO 
        rankType,
        userA1Id, userB1Id, userA2Id, userB2Id
    FROM GameMatch gm
    LEFT JOIN GamePlayer gpA1 ON (gm.matchId = gpA1.matchId AND gpA1.side = "Side A" AND gpA1.position = "Pos 1" )
    LEFT JOIN GamePlayer gpA2 ON (gm.matchId = gpA2.matchId AND gpA2.side = "Side A" AND gpA2.position = "Pos 2" )
    LEFT JOIN GamePlayer gpB1 ON (gm.matchId = gpB1.matchId AND gpB1.side = "Side B" AND gpB1.position = "Pos 1" )
    LEFT JOIN GamePlayer gpB2 ON (gm.matchId = gpB2.matchId AND gpB2.side = "Side B" AND gpB2.position = "Pos 2" )
    LEFT JOIN User uA1 ON (gpA1.userId = uA1.userId)
    LEFT JOIN User uA2 ON (gpA2.userId = uA2.userId)
    LEFT JOIN User uB1 ON (gpB1.userId = uB1.userId)
    LEFT JOIN User uB2 ON (gpB2.userId = uB2.userId)
    WHERE gm.matchId = inMatchId;
    
    -- First get the time of the game
    SELECT  GameMatch.dateTime
    INTO    dateTime
    FROM    GameMatch
    WHERE   GameMatch.matchId = inMatchId
    LOCK IN SHARE MODE;

    -- Here we get the settings for getting win loss and chase percentages starting from a given time
    CALL _SubProcGetRankPointSetting(dateTime, pointsWin, pointsLoss, prcChase);

    -- Here we calculate the current ranks in the different disciplines until the games date
    CALL _SubProcRankSidePointUntil(inMatchId, "Side A", rankSideAAlltime, rankSideAOverall, rankSideADiscipline);
    CALL _SubProcRankSidePointUntil(inMatchId, "Side B", rankSideBAlltime, rankSideBOverall, rankSideBDiscipline);
    
    SET newRankSideAAlltime     = rankSideAAlltime;
    SET newRankSideBAlltime     = rankSideBAlltime;
    SET newRankSideAOverall     = rankSideAOverall;
    SET newRankSideBOverall     = rankSideBOverall;
    SET newRankSideADiscipline  = rankSideADiscipline;
    SET newRankSideBDiscipline  = rankSideBDiscipline;
    
    -- Always calculate the new Alltime ranks
    CALL _SubProcCalculateWinLossRankPoints(inMatchId, rankSideAAlltime,    rankSideBAlltime,    pointsWin, pointsLoss, prcChase, newRankSideAAlltime,    newRankSideBAlltime);
    
    -- Now redistribute the shares of the wins, which means in a team of a weak and a strong
    -- palyer, the strong player earns more points of the win, the weaker player gets the smaller share 
    CALL _SubProcDistributeAlltimeRankPoints(dateTime,
            userA1Id, userB1Id, userA2Id, userB2Id,
            newRankSideAAlltime, newRankSideBAlltime,
            newRankA1Alltime, newRankB1Alltime, newRankA2Alltime, newRankB2Alltime);
            
    IF rankType = "Overall" OR rankType = "Discipline" THEN
        CALL _SubProcCalculateWinLossRankPoints(inMatchId, rankSideAOverall,    rankSideBOverall,    pointsWin, pointsLoss, prcChase, newRankSideAOverall,    newRankSideBOverall);
        IF rankType = "Discipline" THEN
            CALL _SubProcCalculateWinLossRankPoints(inMatchId, rankSideADiscipline, rankSideBDiscipline, pointsWin, pointsLoss, prcChase, newRankSideADiscipline, newRankSideBDiscipline);
        END IF;            
    END IF;
    
    -- And finally use the percentages to calculate and add the new ranks to the rank tables
    CALL _SubProcInsertRankPoint(inMatchId,
        userA1Id, userB1Id, userA2Id, userB2Id, rankType,
        newRankA1Alltime, newRankB1Alltime, newRankA2Alltime, newRankB2Alltime,
        newRankSideAOverall, newRankSideBOverall,
        newRankSideADiscipline, newRankSideBDiscipline);
    
    -- Recalculate all cool downs after this game as well
    -- since they are based on the results of this game
    CALL _SubProcRecalcCoolDownRanks(dateTime);        
END//



/**
 * Procedure to insert a new game into the DB.
 * This method hands back the ID of the match that
 * was just created
 */
CREATE PROCEDURE _SubProcInsertGame(
    INOUT   inOutMatchId    INT,
    IN      inDateTime      TIMESTAMP,
    IN      inWinner        ENUM("Side A", "Side B")
)
NOT DETERMINISTIC
MODIFIES SQL DATA
BEGIN
    IF inOutMatchId = 0 THEN
        INSERT INTO GameMatch (dateTime) VALUES (inDateTime);
        SET inOutMatchId = LAST_INSERT_ID();
    ELSE
        UPDATE 
            GameMatch
        SET
            dateTime = inDateTime
        WHERE
            matchId = inOutMatchId;
    END IF;
    
    INSERT IGNORE INTO GameSide (matchId, side) VALUES (inOutMatchId, "Side A");
    INSERT IGNORE INTO GameSide (matchId, side) VALUES (inOutMatchId, "Side B");

    INSERT INTO 
        GameWinner (matchId, side) 
    VALUES 
        (inOutMatchId, inWinner)
    ON DUPLICATE KEY UPDATE 
        side = inWinner;
END//

/**
 * Procedure to add set points to a given game. Set agme points
 * to 0 for sets that were not played, thus they will not be created
 * in the data base
 */
CREATE PROCEDURE _SubProcInsertGameSets(
    IN inMatchId      INT,
    IN inSet1PointsA  INT,
    IN inSet1PointsB  INT,
    IN inSet2PointsA  INT,
    IN inSet2PointsB  INT,
    IN inSet3PointsA  INT,
    IN inSet3PointsB  INT
)
NOT DETERMINISTIC
MODIFIES SQL DATA
BEGIN
    IF inSet1PointsA > 0 OR inSet1PointsB > 0 THEN
        INSERT INTO GameSetPoint (matchId, side, setNr, setPoints) VALUES (inMatchId, "Side A", 1, inSet1PointsA) ON DUPLICATE KEY UPDATE setPoints = inSet1PointsA;
        INSERT INTO GameSetPoint (matchId, side, setNr, setPoints) VALUES (inMatchId, "Side B", 1, inSet1PointsB) ON DUPLICATE KEY UPDATE setPoints = inSet1PointsB;
    ELSE
        DELETE FROM GameSetPoint WHERE matchId = inMatchId AND setNr = 1;
    END IF;
    
    IF inSet2PointsA > 0 OR inSet2PointsB > 0 THEN
        INSERT INTO GameSetPoint (matchId, side, setNr, setPoints) VALUES (inMatchId, "Side A", 2, inSet2PointsA) ON DUPLICATE KEY UPDATE setPoints = inSet2PointsA;
        INSERT INTO GameSetPoint (matchId, side, setNr, setPoints) VALUES (inMatchId, "Side B", 2, inSet2PointsB) ON DUPLICATE KEY UPDATE setPoints = inSet2PointsB;
    ELSE
        DELETE FROM GameSetPoint WHERE matchId = inMatchId AND setNr = 2;
    END IF;

    IF inSet3PointsA > 0 OR inSet3PointsB > 0 THEN
        INSERT INTO GameSetPoint (matchId, side, setNr, setPoints) VALUES (inMatchId, "Side A", 3, inSet3PointsA) ON DUPLICATE KEY UPDATE setPoints = inSet3PointsA;
        INSERT INTO GameSetPoint (matchId, side, setNr, setPoints) VALUES (inMatchId, "Side B", 3, inSet3PointsB) ON DUPLICATE KEY UPDATE setPoints = inSet3PointsB;
    ELSE
        DELETE FROM GameSetPoint WHERE matchId = inMatchId AND setNr = 3;
    END IF;
END//

/**
 * This method adds four players into the DB for a given game
 * player 2A and player 2B can be assigend with 0 which will
 * make them not be set. The method also supports functionality
 * to update the game players. players being updated to 0, thus
 * taken from the game, will be correctly removed from the game.
 */
CREATE PROCEDURE _SubProcInsertPlayer(
    IN inMatchId      INT,
    IN inPlayer1IdA   INT,
    IN inPlayer1IdB   INT,
    IN inPlayer2IdA   INT,
    IN inPlayer2IdB   INT
)
NOT DETERMINISTIC
MODIFIES SQL DATA
BEGIN
	 /**
	  * First delete all gamePlayers since updating is very complicated
	  * due to the unique constraint. Reordering can easily break tha constraint.
	  * This is not very nice since an update would be more apreciated but
	  * this one is safe and should work as expected.
	  */
     DELETE FROM GamePlayer WHERE matchId = inMatchId;
     
     /**
      * Now insert the players again 
      */
     IF (inPlayer2IdA != 0 AND inPlayer2IdB != 0) THEN
        /**
         * insert four player game
         */
        INSERT INTO 
            GamePlayer (matchId, side, position, userId)
        VALUES
            (inMatchId, "Side A", "Pos 1", inPlayer1IdA),
            (inMatchId, "Side B", "Pos 1", inPlayer1IdB),
            (inMatchId, "Side A", "Pos 2", inPlayer2IdA),
            (inMatchId, "Side B", "Pos 2", inPlayer2IdB);
    ELSEIF (inPlayer2IdA != 0 AND inPlayer2IdB = 0) THEN
        /**
         * insert three player game with two A and  one B
         */
        INSERT INTO 
            GamePlayer (matchId, side, position, userId)
        VALUES
            (inMatchId, "Side A", "Pos 1", inPlayer1IdA),
            (inMatchId, "Side B", "Pos 1", inPlayer1IdB),
            (inMatchId, "Side A", "Pos 2", inPlayer2IdA);
    ELSEIF (inPlayer2IdA = 0 AND inPlayer2IdB != 0) THEN
        /**
         * insert three player game with one A and two B
         */
        INSERT INTO 
            GamePlayer (matchId, side, position, userId)
        VALUES
            (inMatchId, "Side A", "Pos 1", inPlayer1IdA),
            (inMatchId, "Side B", "Pos 1", inPlayer1IdB),
            (inMatchId, "Side B", "Pos 2", inPlayer2IdB);
    ELSE 
        /**
         * finally insert the two player game
         */
        INSERT INTO 
            GamePlayer (matchId, side, position, userId)
        VALUES
            (inMatchId, "Side A", "Pos 1", inPlayer1IdA),
            (inMatchId, "Side B", "Pos 1", inPlayer1IdB);
    END IF;
END//

/**
 * Call this method to remove a game from the played games.
 * calling this method also triggers recalculation for all games
 * that have been played after the deleted one.
 */
CREATE PROCEDURE DeleteGame(
    IN inMatchId INT
)
NOT DETERMINISTIC
MODIFIES SQL DATA
BEGIN
	DECLARE recalcDatetime TIMESTAMP;
	
    SELECT datetime INTO recalcDatetime FROM GameMatch WHERE matchId = inMatchId;

    DELETE FROM GameMatch WHERE matchId = inMatchId;
    
    CALL _SubProcUpdateGameRankPointStartingFrom(recalcDatetime);
    
    COMMIT;
END//


/**
 * Call this method to add a 4 Player game to the db.
 * Sets that were not played should be set to 0 points.
 * This procedure is transactional and rolls back in case
 * things go wrong.
 */
CREATE PROCEDURE InsertGame(
    INOUT inOutMatchId INT,
    IN inDateTime   TIMESTAMP,
    IN player1IdA   INT,
    IN player1IdB   INT,
    IN player2IdA   INT,
    IN player2IdB   INT,
    IN set1PointsA  INT,
    IN set1PointsB  INT,
    IN set2PointsA  INT,
    IN set2PointsB  INT,
    IN set3PointsA  INT,
    IN set3PointsB  INT,
    IN winner       ENUM("Side A", "Side B")
)
NOT DETERMINISTIC
MODIFIES SQL DATA
BEGIN
	DECLARE updateDateTime TIMESTAMP;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Failed to add game! Database rolled back!';
        ROLLBACK;
    END; 
    
    START TRANSACTION;
    
    -- Set the updateDateTime which is the one, the game had
    -- before it got updated. Thus if inOutMatchId is 0 a new game
    -- will be added. If it is bigger than 0 it is an existing game
    -- thus it already had a dateTime
    SET updateDateTime = inDateTime;
    IF (inOutMatchId > 0) THEN
        SELECT LEAST(inDateTime, dateTime) INTO updateDateTime
        FROM GameMatch WHERE matchId = inOutMatchId;
    END IF;
     
    CALL _SubProcInsertGame(
       inOutMatchId,
       inDateTime,
       winner
    );

    CALL _SubProcInsertPlayer(inOutMatchId,
       player1IdA, player1IdB,
       player2IdA, player2IdB
    );
    
    CALL _SubProcInsertGameSets(
        inOutMatchId,
        set1PointsA, set1PointsB,
        set2PointsA, set2PointsB,
        set3PointsA, set3PointsB
    );
    
    CALL _SubProcUpdateGameRankPointStartingFrom(updateDateTime);
    
    COMMIT;
END//

CREATE PROCEDURE _SubProcCoolDownRanks(
    IN inDateTime TIMESTAMP
)
NOT DETERMINISTIC
MODIFIES SQL DATA
BEGIN
    DECLARE prcCoolDown     DOUBLE PRECISION(10,3);
	DECLARE currentDateTime TIMESTAMP;
    
    SELECT
        rps.prcCoolDown
    INTO
        prcCoolDown
    FROM 
        RankPointSetting rps
    WHERE 
        rps.validFrom < inDateTime
    ORDER BY rps.validFrom DESC
    LIMIT 1
    LOCK IN SHARE MODE;
    
	INSERT INTO GamePlayerRankPointOverride (dateTime, userId, points, rankType, type) 
    SELECT inDateTime, userId,_RankPlayerPointUntil(userId, inDateTime, "Alltime") * (100.0 - prcCoolDown) / 100.0,
        "Alltime", "Cooldown"
    FROM User FOR UPDATE;
	
    INSERT INTO GamePlayerRankPointOverride (dateTime, userId, points, rankType, type) 
    SELECT inDateTime, userId,_RankPlayerPointUntil(userId, inDateTime, "Overall") * (100.0 - prcCoolDown) / 100.0,
        "Overall", "Cooldown"
    FROM User FOR UPDATE;
    
    INSERT INTO GamePlayerRankPointOverride (dateTime, userId, points, rankType, type) 
    SELECT inDateTime, userId,_RankPlayerPointUntil(userId, inDateTime, "Discipline") * (100.0 - prcCoolDown) / 100.0,
        "Discipline", "Cooldown"
    FROM User FOR UPDATE;
    
    INSERT INTO GameTeamRankPointOverride (dateTime, teamId, points, rankType, type) 
    SELECT inDateTime, teamId, _RankTeamPointUntil(teamId, inDateTime, "Overall")    * (100.0 - prcCoolDown) / 100.0,
        "Overall", "Cooldown"
    FROM Team FOR UPDATE;

    INSERT INTO GameTeamRankPointOverride (dateTime, teamId, points, rankType, type) 
    SELECT inDateTime, teamId, _RankTeamPointUntil(teamId, inDateTime, "Discipline")    * (100.0 - prcCoolDown) / 100.0,
        "Discipline", "Cooldown"
    FROM Team FOR UPDATE;

END//

CREATE PROCEDURE _SubProcRecalcCoolDownRanks(
    IN inDateTime TIMESTAMP
)
NOT DETERMINISTIC
MODIFIES SQL DATA
BEGIN
    DECLARE prcCoolDown     DOUBLE PRECISION(10,3);
    DECLARE currentDateTime TIMESTAMP;
    
    SELECT
        rps.prcCoolDown
    INTO
        prcCoolDown
    FROM 
        RankPointSetting rps
    WHERE 
        rps.validFrom < inDateTime
    ORDER BY rps.validFrom DESC
    LIMIT 1
    LOCK IN SHARE MODE;
    
    UPDATE GamePlayerRankPointOverride
    SET points = _RankPlayerPointUntil(userId, dateTime, rankType) * (100.0 - prcCoolDown) / 100.0
    WHERE dateTime >= inDateTime;

    UPDATE GameTeamRankPointOverride
    SET points = _RankTeamPointUntil(teamId, dateTime, rankType) * (100.0 - prcCoolDown) / 100.0
    WHERE dateTime >= inDateTime;
END//

CREATE EVENT CoolDownEvent
ON SCHEDULE EVERY '1' MONTH
STARTS '2017-05-01 00:00:00'
DO 
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Failed to run scheduled Rank CoolDown! Database rolled back!';
        ROLLBACK;
    END; 
    
    START TRANSACTION;
  
    CALL _SubProcCoolDownRanks(NOW());
    
    COMMIT;	
END//

DELIMITER ;

