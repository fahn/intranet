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
DELIMITER //

/**
 * Call this method to insert a new user to the DB and also
 * create all the newly available teams to the database, thus
 * permuting over the already existing users and the newly added one.
 */
CREATE PROCEDURE InsertUser(
    IN inEmail        VARCHAR(64),
    IN inFirstName    VARCHAR(64),
    IN inLastName     VARCHAR(64),
    IN inGender       ENUM("Male", "Female"),
    IN inPassword     VARCHAR(255),
    IN inActivePlayer BOOLEAN,
    IN inAdmin        BOOLEAN,
    IN inReporter     BOOLEAN
)
NOT DETERMINISTIC
MODIFIES SQL DATA
BEGIN
    DECLARE userId INT;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Failed to add new User! Database rolled back!';
        ROLLBACK;
    END; 
    
    SET autocommit = 0;
    START TRANSACTION;
    
    -- Add the new User and get the Id
    INSERT INTO User (firstName, email, lastName, gender, password, activePlayer, reporter, admin) VALUES (inFirstName, inEmail, inLastName, inGender, inPassword, inActivePlayer, inReporter, inAdmin);
    SET userId = LAST_INSERT_ID();

    -- Now this player needs to permute with all other existing players
    -- for building the teams. Accordingly a new Team for each already existing
    -- player needs to be added to the DB. The following select statement creates
    -- the corss product of the users and takes one diagonal half of the resulting matrix
    -- this matrix is joine dincluding nulls of the existing teams. The idea is that this
    -- select detects the teams which still have a team ID of null, these are the ones that
    -- have to be add to the table of teams, their ID is auto incremented
    INSERT INTO 
        Team (user1Id, user2Id) 
    SELECT
        player1,
        player2
    FROM
        Team AS team
    RIGHT JOIN(
        SELECT 
           player1.userId as player1,
           player2.userId as player2
        FROM
           User AS player1,
           User AS player2
        WHERE
           player1.userId < player2.userId
        ORDER BY 
           player1 ASC,
           player2 ASC
        FOR UPDATE)
    AS
       allTeams
    ON (
       team.user1Id = allTeams.player1 AND
       team.user2Id = allTeams.player2)
    WHERE
       team.teamId IS NULL
    FOR UPDATE;
    
    COMMIT;
END//

CREATE PROCEDURE UpdateUser(
    IN inUserId       INT,
    IN inEmail        VARCHAR(64),
    IN inFirstName    VARCHAR(64),
    IN inLastName     VARCHAR(64),
    IN inGender       ENUM("Male", "Female"),
    IN inPassword     VARCHAR(255),
    IN inActivePlayer BOOLEAN,
    IN inAdmin        BOOLEAN,
    IN inReporter     BOOLEAN
)
NOT DETERMINISTIC
MODIFIES SQL DATA
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Failed to update User! Database rolled back!';
        ROLLBACK;
    END; 
    
    START TRANSACTION;
    
    IF (inActivePlayer IS NULL OR inReporter IS NULL OR inAdmin IS NULL) THEN
        UPDATE User
        SET
            User.email         = inEmail,
            User.firstName     = inFirstName,
            User.lastName      = inLastName
        WHERE
            User.userId = inUserId;
    ELSE        
        UPDATE User
        SET
            User.email         = inEmail,
            User.firstName     = inFirstName,
            User.lastName      = inLastName,
            User.gender        = inGender,
            User.activePlayer  = inActivePlayer,
            User.reporter      = inReporter,
            User.admin         = inAdmin
        WHERE
            User.userId = inUserId;
            
        SET @firstGameDateTime = _PlayerGetFirstGame(inUserId);
        
        CALL _SubProcUpdateGameRankPointStartingFrom(@firstGameDateTime);
    END IF;
    
    -- now see if the password needs an update as well
    IF (inPassword IS NOT NULL) THEN
        UPDATE User
        SET
            User.password = inPassword
        WHERE
            User.userId = inUserId;
    END IF;
        
    COMMIT;
END//

CREATE PROCEDURE DeleteUser(
    IN inUserId       INT
)
NOT DETERMINISTIC
MODIFIES SQL DATA
BEGIN
    DECLARE firstGameDateTime TIMESTAMP;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Failed to delete User! Database rolled back!';
        ROLLBACK;
    END; 
    
    START TRANSACTION;

    SET firstGameDateTime = _PlayerGetFirstGame(inUserId);

    DELETE FROM User WHERE User.userId = inUserId;
    DELETE gm FROM GameMatch gm, GamePlayer gp WHERE gm.matchId = gp.matchId AND gp.userId = inUserId;
    
    -- And now recalculate the ranking beginning from the first game
    -- the player initially played. All games should already be removed since
    -- the delete cascades through the tables
    CALL _SubProcUpdateGameRankPointStartingFrom(firstGameDateTime);
    
    COMMIT;
END//

DELIMITER ;

