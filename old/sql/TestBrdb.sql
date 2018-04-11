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

SET autocommit = 0;
SET GLOBAL event_scheduler = 0; -- Disable during tests

/**
 * First tests to check the basic rank point methods / functions
 * 
 */
SELECT tap.plan(9);

INSERT INTO RankPointSetting (validFrom, pointsWin, pointsLoss, prcChase) VALUES ("2017-01-01 1:00:00", 5.0, 6.0, 7.0);
INSERT INTO RankPointSetting (validFrom, pointsWin, pointsLoss, prcChase) VALUES ("2017-01-02 2:01:00", 8.0, 8.0, 8.0);
INSERT INTO RankPointSetting (validFrom, pointsWin, pointsLoss, prcChase) VALUES ("2017-01-03 12:04:00", 9.0, 9.0, 9.0);

SET @prcWin = 0;
SET @prsLoss = 0;
SET @prcChase = 0;

CALL _SubProcGetRankPointSetting("2017-01-01 1:00:00", @prcWin, @prcLoss, @prcChase);

SELECT tap.eq(@prcWin,      2, 'RankPointSetting - Got correct win score from ranking settings');
SELECT tap.eq(@prcLoss,    -1, 'RankPointSetting - Got correct loss score from ranking settings');
SELECT tap.eq(@prcChase,    5, 'RankPointSetting - Got correct chase score from ranking settings');

CALL _SubProcGetRankPointSetting("2017-01-01 1:00:01", @prcWin, @prcLoss, @prcChase);

SELECT tap.eq(@prcWin,      5, 'RankPointSetting - Got correct win score from ranking settings');
SELECT tap.eq(@prcLoss,     6, 'RankPointSetting - Got correct loss score from ranking settings');
SELECT tap.eq(@prcChase,    7, 'RankPointSetting - Got correct chase score from ranking settings');

CALL _SubProcGetRankPointSetting("2017-01-04 1:00:01", @prcWin, @prcLoss, @prcChase);

SELECT tap.eq(@prcWin,      9, 'RankPointSetting - Got correct win score from ranking settings');
SELECT tap.eq(@prcLoss,     9, 'RankPointSetting - Got correct loss score from ranking settings');
SELECT tap.eq(@prcChase,    9, 'RankPointSetting - Got correct chase score from ranking settings');

CALL tap.finish();

/**
 * Test the min and max rank points
 * 
 */
SELECT tap.plan(2);

SELECT tap.eq(_RankPointMax(), 100, 'RankPointMax - Got correct value');
SELECT tap.eq(_RankPointMin(),   0, 'RankPointMin - Got correct value');

CALL tap.finish();

-- ---------------------------------------------------------------------
-- Test the rank point limits
-- ---------------------------------------------------------------------

SELECT tap.plan(5);

SELECT tap.eq(_RankPointLimit(110.341 ), 100, 'RankPointLimit - Got correct ceiling');
SELECT tap.eq(_RankPointLimit(100.000 ), 100, 'RankPointLimit - Got correct ceiling');
SELECT tap.eq(_RankPointLimit(  3.3456),   3.346, 'RankPointLimit - Got correct value');
SELECT tap.eq(_RankPointLimit(  0.000 ),   0, 'RankPointLimit - Got correct floor');
SELECT tap.eq(_RankPointLimit( -5.000 ),   0, 'RankPointLimit - Got correct floor');

CALL tap.finish();


SELECT tap.plan(14);

SELECT tap.eq(COUNT(*), 0, 'InsertUser - There is no user yet!' ) FROM User;
SELECT tap.eq(COUNT(*), 0, 'InsertUser - There are no teams yet!' ) FROM Team;

CALL InsertUser("pf@bc-comet.de", "Phil", "F.", "Male", "$2y$10$caqHxWaKHlV5u0TAdwvvWOVBotrPCJFQ978J.pehzPqatlk4b6vEm", TRUE, TRUE, TRUE);

SELECT tap.eq(COUNT(*), 1, 'InsertUser - There is one user!' ) FROM User;
SELECT tap.eq(COUNT(*), 0, 'InsertUser - There are no teams yet!' ) FROM Team;

CALL InsertUser("mk@bc-comet.de", "Marie", "K.", "Female", "$2y$10$caqHxWaKHlV5u0TAdwvvWOVBotrPCJFQ978J.pehzPqatlk4b6vEm", TRUE, FALSE, FALSE);
SELECT tap.eq(COUNT(*), 2, 'InsertUser - There are two user!' ) FROM User;
SELECT tap.eq(COUNT(*), 1, 'InsertUser - There is one team!' ) FROM Team;

SELECT tap.eq(user1Id, 1, 'InsertUser - First player in team 1 is correct!' )  FROM Team WHERE teamId = 1;
SELECT tap.eq(user2Id, 2, 'InsertUser - Second player in team 1 is correct!' ) FROM Team WHERE teamId = 1;    

CALL InsertUser("js@bc-comet.de", "Joe", "S.", "Male", "$2y$10$caqHxWaKHlV5u0TAdwvvWOVBotrPCJFQ978J.pehzPqatlk4b6vEm", TRUE, TRUE, FALSE);

SELECT tap.eq(COUNT(*), 3, 'InsertUser - There are three user!' ) FROM User;
SELECT tap.eq(COUNT(*), 3, 'InsertUser - There are three team!' ) FROM Team;

SELECT tap.eq(user1Id, 2, 'InsertUser - First player in team 2 is correct!' ) FROM Team WHERE teamId = 2;  
SELECT tap.eq(user2Id, 3, 'InsertUser - Second player in team 2 is correct!' ) FROM Team WHERE teamId = 2;

SELECT tap.eq(user1Id, 1, 'InsertUser - First player in team 3 is correct!' ) FROM Team WHERE teamId = 3;  
SELECT tap.eq(user2Id, 3, 'InsertUser - Second player in team 3 is correct!' ) FROM Team WHERE teamId = 3;

CALL tap.finish();

/**************************************
 * Test cases for inserting a game
 * 
 **************************************/
SELECT tap.plan(19);

SET @gameId1 = 0;
SET @gameId2 = 0;

SELECT tap.eq(COUNT(*), 0, 'InsertGame - There are no matches!' ) FROM GameMatch;
SELECT tap.eq(COUNT(*), 0, 'InsertGame - There are no sides yet!' ) FROM GameSide;
SELECT tap.eq(COUNT(*), 0, 'InsertGame - There is now winner or looser yet!' ) FROM GameWinner;

CALL _SubProcInsertGame(@gameId1, "2017-01-01 1:00:00", "Side A");

SELECT tap.eq(@gameId1, 1, 'InsertGame - The first game is inserted');

SELECT tap.eq(COUNT(*), 1, 'InsertGame - There is now one game!' ) FROM GameMatch;
SELECT tap.eq(COUNT(*), 2, 'InsertGame - There are two sides for the one game!' ) FROM GameSide;
SELECT tap.eq(COUNT(*), 1, 'InsertGame - There is one results for the game!' ) FROM GameWinner;

SELECT tap.eq(side, "Side A", 'InsertGame - Side A is winner for game 1!' ) FROM GameWinner WHERE matchId = @gameId1;

CALL _SubProcInsertGame(@gameId1, "2017-01-01 0:59:00", "Side B");

SELECT tap.eq(@gameId1, 1, 'InsertGame - The first game is updated');

SELECT tap.eq(COUNT(*), 1, 'InsertGame - There is still one game!' ) FROM GameMatch;
SELECT tap.eq(COUNT(*), 2, 'InsertGame - There are still two sides for the one game!' ) FROM GameSide;
SELECT tap.eq(COUNT(*), 1, 'InsertGame - There is still one results for the game!' ) FROM GameWinner;

SELECT tap.eq(dateTime, "2017-01-01 00:59:00", 'InsertGame - Date correctly updated') FROM GameMatch;
SELECT tap.eq(side, "Side B", 'InsertGame - Side A is winner for game 1!' ) FROM GameWinner WHERE matchId = @gameId1;

CALL _SubProcInsertGame(@gameId2, "2017-01-01 1:00:00", "Side B");

SELECT tap.eq(@gameId2, 2, 'InsertGame - The second game is inserted');

SELECT tap.eq(COUNT(*), 2, 'InsertGame - There is now one game!' ) FROM GameMatch;
SELECT tap.eq(COUNT(*), 4, 'InsertGame - There are two sides for the one game!' ) FROM GameSide;
SELECT tap.eq(COUNT(*), 2, 'InsertGame - There are two results for the game!' ) FROM GameWinner;

SELECT tap.eq(side, "Side B", 'InsertGame - Side B is winner for game 1!' ) FROM GameWinner WHERE matchId = @gameId2;

CALL tap.finish();

/**************************************************************
 * test cases for adding the points from the sets
 * 
 **************************************************************/
SELECT tap.plan(22);

SELECT tap.eq(COUNT(*), 0, 'InsertGameSets - No Sets defined yet' ) FROM GameSetPoint;

CALL _SubProcInsertGame(@gameId1, "2017-01-01 0:59:00", "Side A");
CALL _SubProcInsertGame(@gameId2, "2017-01-01 1:00:01", "Side B");

CALL _SubProcInsertGameSets(@gameId1, 21, 14, 22, 24, 27, 25);

SELECT tap.eq(COUNT(*), 6, 'InsertGameSets - 3 game sets for each side' ) FROM GameSetPoint;

SELECT tap.eq(setPoints, 21, 'InsertGameSets - Correct standings Side A Set 1' ) FROM GameSetPoint WHERE matchId = @gameId1 AND setNr = 1 AND side = "Side A";
SELECT tap.eq(setPoints, 14, 'InsertGameSets - Correct standings Side B Set 1' ) FROM GameSetPoint WHERE matchId = @gameId1 AND setNr = 1 AND side = "Side B";
SELECT tap.eq(setPoints, 22, 'InsertGameSets - Correct standings Side A Set 2' ) FROM GameSetPoint WHERE matchId = @gameId1 AND setNr = 2 AND side = "Side A";
SELECT tap.eq(setPoints, 24, 'InsertGameSets - Correct standings Side B Set 2' ) FROM GameSetPoint WHERE matchId = @gameId1 AND setNr = 2 AND side = "Side B";
SELECT tap.eq(setPoints, 27, 'InsertGameSets - Correct standings Side A Set 3' ) FROM GameSetPoint WHERE matchId = @gameId1 AND setNr = 3 AND side = "Side A";
SELECT tap.eq(setPoints, 25, 'InsertGameSets - Correct standings Side B Set 3' ) FROM GameSetPoint WHERE matchId = @gameId1 AND setNr = 3 AND side = "Side B";

CALL _SubProcInsertGameSets(@gameId1, 22, 20, 24, 22, 25, 27);

SELECT tap.eq(COUNT(*), 6, 'InsertGameSets - 3 game sets for each side - They just got updated' ) FROM GameSetPoint;

SELECT tap.eq(setPoints, 22, 'InsertGameSets - Correct standings Side A Set 1' ) FROM GameSetPoint WHERE matchId = @gameId1 AND setNr = 1 AND side = "Side A";
SELECT tap.eq(setPoints, 20, 'InsertGameSets - Correct standings Side B Set 1' ) FROM GameSetPoint WHERE matchId = @gameId1 AND setNr = 1 AND side = "Side B";
SELECT tap.eq(setPoints, 24, 'InsertGameSets - Correct standings Side A Set 2' ) FROM GameSetPoint WHERE matchId = @gameId1 AND setNr = 2 AND side = "Side A";
SELECT tap.eq(setPoints, 22, 'InsertGameSets - Correct standings Side B Set 2' ) FROM GameSetPoint WHERE matchId = @gameId1 AND setNr = 2 AND side = "Side B";
SELECT tap.eq(setPoints, 25, 'InsertGameSets - Correct standings Side A Set 3' ) FROM GameSetPoint WHERE matchId = @gameId1 AND setNr = 3 AND side = "Side A";
SELECT tap.eq(setPoints, 27, 'InsertGameSets - Correct standings Side B Set 3' ) FROM GameSetPoint WHERE matchId = @gameId1 AND setNr = 3 AND side = "Side B";

CALL _SubProcInsertGameSets(@gameId2, 21, 14, 21, 17, 0, 0);

SELECT tap.eq(COUNT(*), 10, 'InsertGameSets - 2 game sets for each side for the second game' ) FROM GameSetPoint;

CALL _SubProcInsertGameSets(@gameId1, 22, 20, 24, 22, 0, 0);

SELECT tap.eq(COUNT(*), 4, 'InsertGameSets - 2 game sets let for game 1' ) FROM GameSetPoint WHERE matchId = @gameId1 AND setNr != 3;
SELECT tap.eq(COUNT(*), 0, 'InsertGameSets - Game 1 set three is gone' ) FROM GameSetPoint WHERE matchId = @gameId1 AND setNr = 3;

CALL _SubProcInsertGameSets(@gameId1, 22, 20, 0, 0, 0, 0);

SELECT tap.eq(COUNT(*), 2, 'InsertGameSets - 1 game sets let for game 1' ) FROM GameSetPoint WHERE matchId = @gameId1 AND setNr != 2;
SELECT tap.eq(COUNT(*), 0, 'InsertGameSets - Game 1 set three is gone' ) FROM GameSetPoint WHERE matchId = @gameId1 AND setNr = 2;

CALL _SubProcInsertGameSets(@gameId1, 0, 0, 0, 0, 0, 0);

SELECT tap.eq(COUNT(*), 0, 'InsertGameSets - No sets left' ) FROM GameSetPoint WHERE matchId = @gameId1;

CALL _SubProcInsertGameSets(@gameId1, 21, 13, 19, 21, 17, 21);

SELECT tap.eq(COUNT(*), 6, 'InsertGameSets - Spiele erneut gesetzt' ) FROM GameSetPoint WHERE matchId = @gameId1;

CALL tap.finish();

/******************************************************
 * tests for inserting the players into the games
 * 
 ******************************************************/
SELECT tap.plan(28);

SELECT tap.eq(COUNT(*), 0, 'SubProcInsertPlayer - No Players assigned yet' ) FROM GamePlayer;

CALL _SubProcInsertGame(@gameId1, "2017-01-01 1:00:02", "Side A");

CALL _SubProcInsertPlayer(@gameId1, 1, 2, 0, 0);

SELECT tap.eq(COUNT(*), 2, 'SubProcInsertPlayer - Now we have 2 game player') FROM GamePlayer;
SELECT tap.eq(userId, 1, 'SubProcInsertPlayer - Player 1 is on side A and postion 1') FROM GamePlayer WHERE side = "Side A" AND position = "Pos 1";
SELECT tap.eq(userId, 2, 'SubProcInsertPlayer - Player 2 is on side B and postion 1') FROM GamePlayer WHERE side = "Side B" AND position = "Pos 1";

CALL _SubProcInsertPlayer(@gameId1, 2, 1, 0, 0);

SELECT tap.eq(COUNT(*), 2, 'SubProcInsertPlayer - Still we have 2 game player') FROM GamePlayer;
SELECT tap.eq(userId, 2, 'SubProcInsertPlayer - Player 2 is on side A and postion 1') FROM GamePlayer WHERE side = "Side A" AND position = "Pos 1";
SELECT tap.eq(userId, 1, 'SubProcInsertPlayer - Player 1 is on side B and postion 1') FROM GamePlayer WHERE side = "Side B" AND position = "Pos 1";

CALL InsertUser("as@bc-comet.de", "Anni", "S.", "Female", "$2y$10$caqHxWaKHlV5u0TAdwvvWOVBotrPCJFQ978J.pehzPqatlk4b6vEm", TRUE, FALSE, FALSE);

CALL _SubProcInsertPlayer(@gameId1, 2, 3, 4, 1);

SELECT tap.eq(COUNT(*), 4, 'SubProcInsertPlayer - Now we have 4 game player') FROM GamePlayer;
SELECT tap.eq(userId, 2, 'SubProcInsertPlayer - Player 2 is on side A and postion 1') FROM GamePlayer WHERE side = "Side A" AND position = "Pos 1";
SELECT tap.eq(userId, 3, 'SubProcInsertPlayer - Player 3 is on side B and postion 1') FROM GamePlayer WHERE side = "Side B" AND position = "Pos 1";
SELECT tap.eq(userId, 4, 'SubProcInsertPlayer - Player 4 is on side A and postion 2') FROM GamePlayer WHERE side = "Side A" AND position = "Pos 2";
SELECT tap.eq(userId, 1, 'SubProcInsertPlayer - Player 1 is on side B and postion 2') FROM GamePlayer WHERE side = "Side B" AND position = "Pos 2";

CALL _SubProcInsertPlayer(@gameId1, 3, 1, 2, 4);

SELECT tap.eq(COUNT(*), 4, 'SubProcInsertPlayer - Now we have 4 game player') FROM GamePlayer;
SELECT tap.eq(userId, 3, 'SubProcInsertPlayer - Updated Player 3 is on side A and postion 1') FROM GamePlayer WHERE side = "Side A" AND position = "Pos 1";
SELECT tap.eq(userId, 1, 'SubProcInsertPlayer - Updated Player 1 is on side B and postion 1') FROM GamePlayer WHERE side = "Side B" AND position = "Pos 1";
SELECT tap.eq(userId, 2, 'SubProcInsertPlayer - Updated Player 2 is on side A and postion 2') FROM GamePlayer WHERE side = "Side A" AND position = "Pos 2";
SELECT tap.eq(userId, 4, 'SubProcInsertPlayer - Updated Player 4 is on side B and postion 2') FROM GamePlayer WHERE side = "Side B" AND position = "Pos 2";

CALL _SubProcInsertPlayer(@gameId1, 3, 1, 0, 4);

SELECT tap.eq(COUNT(*), 3, 'SubProcInsertPlayer - Now we have a 3 player game') FROM GamePlayer;
SELECT tap.eq(userId, 3, 'SubProcInsertPlayer - Updated Player 3 is on side A and postion 1') FROM GamePlayer WHERE side = "Side A" AND position = "Pos 1";
SELECT tap.eq(userId, 1, 'SubProcInsertPlayer - Updated Player 1 is on side B and postion 1') FROM GamePlayer WHERE side = "Side B" AND position = "Pos 1";
SELECT tap.eq(userId, 4, 'SubProcInsertPlayer - Updated Player 4 is on side B and postion 2') FROM GamePlayer WHERE side = "Side B" AND position = "Pos 2";

CALL _SubProcInsertPlayer(@gameId1, 3, 1, 2, 0);

SELECT tap.eq(COUNT(*), 3, 'SubProcInsertPlayer - Now we have the other 3 player game') FROM GamePlayer;
SELECT tap.eq(userId, 3, 'SubProcInsertPlayer - Updated Player 3 is on side A and postion 1') FROM GamePlayer WHERE side = "Side A" AND position = "Pos 1";
SELECT tap.eq(userId, 1, 'SubProcInsertPlayer - Updated Player 1 is on side B and postion 1') FROM GamePlayer WHERE side = "Side B" AND position = "Pos 1";
SELECT tap.eq(userId, 2, 'SubProcInsertPlayer - Updated Player 2 is on side A and postion 2') FROM GamePlayer WHERE side = "Side A" AND position = "Pos 2";

CALL _SubProcInsertPlayer(@gameId1, 3, 1, 0, 0);

SELECT tap.eq(COUNT(*), 2, 'SubProcInsertPlayer - Now we have are back to a 2 player game') FROM GamePlayer;
SELECT tap.eq(userId, 3, 'SubProcInsertPlayer - Updated Player 3 is on side A and postion 1') FROM GamePlayer WHERE side = "Side A" AND position = "Pos 1";
SELECT tap.eq(userId, 1, 'SubProcInsertPlayer - Updated Player 1 is on side B and postion 1') FROM GamePlayer WHERE side = "Side B" AND position = "Pos 1";

CALL tap.finish();

/**
 * testing the game type function
 * 
 */
SELECT tap.plan(6);

CALL _SubProcInsertPlayer(@gameId1, 1, 2, 0, 0);

SELECT tap.eq(_GameSideTypeGender(@gameId1, "Side A"), "Single Men" , 'GameSideTypeGender - Detected Single Men Correctly'); 
SELECT tap.eq(_GameSideTypeGender(@gameId1, "Side B"), "Single Women" , 'GameSideTypeGender - Detected Single Women Correctly'); 

CALL _SubProcInsertPlayer(@gameId1, 1, 3, 2, 4);

SELECT tap.eq(_GameSideTypeGender(@gameId1, "Side A"), "Double Mixed" , 'GameSideTypeGender - Detected Double Mixed Correctly'); 
SELECT tap.eq(_GameSideTypeGender(@gameId1, "Side B"), "Double Mixed" , 'GameSideTypeGender - Detected Double Mixed Correctly'); 

CALL _SubProcInsertPlayer(@gameId1, 1, 2, 3, 4);

SELECT tap.eq(_GameSideTypeGender(@gameId1, "Side A"), "Double Men" , 'GameSideTypeGender - Detected Double Men Correctly'); 
SELECT tap.eq(_GameSideTypeGender(@gameId1, "Side B"), "Double Women" , 'GameSideTypeGender - Detected Double Women Correctly'); 

CALL tap.finish();

/**
 * testing the function te get the game type side meaning single or double game
 * 
 */

SELECT tap.plan(4);

CALL _SubProcInsertPlayer(@gameId1, 1, 2, 0, 0);

SELECT tap.eq(_GameSideType(@gameId1, "Side A"), "Single" , 'GameSideType - Detected Single for side A'); 
SELECT tap.eq(_GameSideType(@gameId1, "Side B"), "Single" , 'GameSideType - Detected Single for side B'); 

CALL _SubProcInsertPlayer(@gameId1, 1, 2, 3, 4);

SELECT tap.eq(_GameSideType(@gameId1, "Side A"), "Double" , 'GameSideType - Detected Double for side A'); 
SELECT tap.eq(_GameSideType(@gameId1, "Side B"), "Double" , 'GameSideType - Detected Double for side B'); 

CALL tap.finish();

/**
 * testing the function te get the game type meaning single or double or other type of game
 * 
 */

SELECT tap.plan(4);

CALL _SubProcInsertPlayer(@gameId1, 1, 2, 0, 0);

SELECT tap.eq(_GameType(@gameId1), "Single" , 'GameType - Detected Single '); 

CALL _SubProcInsertPlayer(@gameId1, 1, 2, 3, 4);

SELECT tap.eq(_GameType(@gameId1), "Double" , 'GameType - Detected Double '); 

CALL _SubProcInsertPlayer(@gameId1, 1, 2, 0, 4);

SELECT tap.eq(_GameType(@gameId1), "Other" , 'GameType - Detected Other'); 

CALL _SubProcInsertPlayer(@gameId1, 1, 2, 3, 0);

SELECT tap.eq(_GameType(@gameId1), "Other" , 'GameType - Detected Other'); 

CALL tap.finish();

/**
 * testing the rank point function for single players
 * 
 */
SET @tempGameId1 = 1;
SET @tempGameId2 = 2;

CALL _SubProcInsertGame(@tempGameId1, "2017-01-01 0:59:03", "Side B");
CALL _SubProcInsertGame(@tempGameId2, "2017-01-03 0:59:04", "Side A");

SELECT tap.plan(12);

SELECT tap.eq(_RankPlayerPointUntil(@tempGameId1, "2017-01-02 1:00:00", "Alltime"),    0 , 'RankPlayerPointUntil - Alltime points are still 0'); 
SELECT tap.eq(_RankPlayerPointUntil(@tempGameId1, "2017-01-02 1:00:00", "Overall"),    0 , 'RankPlayerPointUntil - Overall points are still 0'); 
SELECT tap.eq(_RankPlayerPointUntil(@tempGameId1, "2017-01-02 1:00:00", "Discipline"), 0 , 'RankPlayerPointUntil - Dicipline points are still 0'); 

INSERT INTO GamePlayerRankPoint (matchId, userId, points, rankType) VALUES (@tempGameId1, 1,  2, "Alltime") , (@tempGameId1, 1,  3, "Overall"),  (@tempGameId1, 1,  4, "Discipline");
INSERT INTO GamePlayerRankPoint (matchId, userId, points, rankType) VALUES (@tempGameId1, 2,  5, "Alltime") , (@tempGameId1, 2,  6, "Overall"),  (@tempGameId1, 2,  7, "Discipline");
INSERT INTO GamePlayerRankPoint (matchId, userId, points, rankType) VALUES (@tempGameId2, 1, 10, "Alltime") , (@tempGameId2, 1, 11, "Overall"),  (@tempGameId2, 1, 12, "Discipline");
INSERT INTO GamePlayerRankPoint (matchId, userId, points, rankType) VALUES (@tempGameId2, 2, 15, "Alltime") , (@tempGameId2, 2, 16, "Overall"),  (@tempGameId2, 2, 17, "Discipline");

SELECT tap.eq(_RankPlayerPointUntil(@tempGameId1, "2017-01-02 1:00:00", "Alltime"),    2 , 'RankPlayerPointUntil - Alltime points are 2'); 
SELECT tap.eq(_RankPlayerPointUntil(@tempGameId1, "2017-01-02 1:00:00", "Overall"),    3 , 'RankPlayerPointUntil - Overall points are 3'); 
SELECT tap.eq(_RankPlayerPointUntil(@tempGameId1, "2017-01-02 1:00:00", "Discipline"), 4 , 'RankPlayerPointUntil - Dicipline points are 4'); 

INSERT INTO GamePlayerRankPointOverride (dateTime, userId, points, rankType) VALUES ("2017-01-01 1:00:00", 1, 7, "Alltime") , ("2017-01-01 1:00:00", 1, 8, "Overall"),  ("2017-01-01 1:00:00", 1, 9, "Discipline");

SELECT tap.eq(_RankPlayerPointUntil(@tempGameId1, "2017-01-02 1:00:00", "Alltime"),    7 , 'RankPlayerPointUntil - Alltime points overridel 7'); 
SELECT tap.eq(_RankPlayerPointUntil(@tempGameId1, "2017-01-02 1:00:00", "Overall"),    8 , 'RankPlayerPointUntil - Overall points override 8'); 
SELECT tap.eq(_RankPlayerPointUntil(@tempGameId1, "2017-01-02 1:00:00", "Discipline"), 9 , 'RankPlayerPointUntil - Dicipline points override 9'); 

SELECT tap.eq(_RankPlayerPointUntil(@tempGameId1, "2017-01-04 1:00:00", "Alltime"),    10 , 'RankPlayerPointUntil - Alltime points second game 10'); 
SELECT tap.eq(_RankPlayerPointUntil(@tempGameId1, "2017-01-04 1:00:00", "Overall"),    11 , 'RankPlayerPointUntil - Overall points second game 11'); 
SELECT tap.eq(_RankPlayerPointUntil(@tempGameId1, "2017-01-04 1:00:00", "Discipline"), 12 , 'RankPlayerPointUntil - Dicipline points second game 12'); 

CALL tap.finish();

/**
 * testing the rank point function for teams
 * 
 */
SET @tempGameId1 = 1;
SET @tempGameId2 = 2;

CALL _SubProcInsertGame(@tempGameId1, "2017-01-01 0:59:05", "Side B");
CALL _SubProcInsertGame(@tempGameId2, "2017-01-03 0:59:06", "Side A");

SELECT tap.plan(8);

SELECT tap.eq(_RankTeamPointUntil(@tempGameId1, "2017-01-02 1:00:00", "Overall"),    0 , 'RankTeamPointUntil - Overall points are still 0'); 
SELECT tap.eq(_RankTeamPointUntil(@tempGameId1, "2017-01-02 1:00:00", "Discipline"), 0 , 'RankTeamPointUntil - Dicipline points are still 0'); 

INSERT INTO GameTeamRankPoint (matchId, teamId, points, rankType) VALUES (@tempGameId1, 1,  3, "Overall"),  (@tempGameId1, 1,  4, "Discipline");
INSERT INTO GameTeamRankPoint (matchId, teamId, points, rankType) VALUES (@tempGameId1, 2,  6, "Overall"),  (@tempGameId1, 2,  7, "Discipline");
INSERT INTO GameTeamRankPoint (matchId, teamId, points, rankType) VALUES (@tempGameId2, 1, 11, "Overall"),  (@tempGameId2, 1, 12, "Discipline");
INSERT INTO GameTeamRankPoint (matchId, teamId, points, rankType) VALUES (@tempGameId2, 2, 16, "Overall"),  (@tempGameId2, 2, 17, "Discipline");

SELECT tap.eq(_RankTeamPointUntil(@tempGameId1, "2017-01-02 1:00:00", "Overall"),    3 , 'RankTeamPointUntil - Overall points are 3'); 
SELECT tap.eq(_RankTeamPointUntil(@tempGameId1, "2017-01-02 1:00:00", "Discipline"), 4 , 'RankTeamPointUntil - Dicipline points are 4'); 

INSERT INTO GameTeamRankPointOverride (dateTime, teamId, points, rankType) VALUES  ("2017-01-01 1:00:00", 1, 8, "Overall"),  ("2017-01-01 1:00:00", 1, 9, "Discipline");

SELECT tap.eq(_RankTeamPointUntil(@tempGameId1, "2017-01-02 1:00:00", "Overall"),    8 , 'RankTeamPointUntil - Overall points override 8'); 
SELECT tap.eq(_RankTeamPointUntil(@tempGameId1, "2017-01-02 1:00:00", "Discipline"), 9 , 'RankTeamPointUntil - Dicipline points override 9'); 

SELECT tap.eq(_RankTeamPointUntil(@tempGameId1, "2017-01-04 1:00:00", "Overall"),    11 , 'RankTeamPointUntil - Overall points second game 11'); 
SELECT tap.eq(_RankTeamPointUntil(@tempGameId1, "2017-01-04 1:00:00", "Discipline"), 12 , 'RankTeamPointUntil - Dicipline points second game 12'); 

CALL tap.finish();

/**
 * testing the function to easily get the teams from userIds
 * 
 */
CALL _SubProcInsertGame(@tempGameId1, "2017-01-02 0:59:07", "Side B");


SELECT tap.plan(5);

-- these test cases may go bust depending on different settings on autoincrement
-- check the IDs on the team table and adjust the results accrodingly

CALL _SubProcInsertPlayer(@tempGameId1, 1, 2, 0, 0);
SELECT tap.eq(_TeamIdForGameSide(1, "Side A"),    0 , 'TeamIdForGameSide - An invalid team has id 0');

CALL _SubProcInsertPlayer(@tempGameId1, 1, 3, 2, 4);
SELECT tap.eq(_TeamIdForGameSide(1, "Side A"),    1 , 'TeamIdForGameSide - Got team one forward order'); 
SELECT tap.eq(_TeamIdForGameSide(1, "Side B"),    5 , 'TeamIdForGameSide - Got team one reverse order'); 

CALL _SubProcInsertPlayer(@tempGameId1, 2, 4, 1, 3);
SELECT tap.eq(_TeamIdForGameSide(1, "Side A"),    1 , 'TeamIdForGameSide - Got team three forward order'); 
SELECT tap.eq(_TeamIdForGameSide(1, "Side B"),    5 , 'TeamIdForGameSide - Got team three reverse order'); 

CALL tap.finish();

/**
 * testing the function to get the correct rankings for a complete
 * side, no matter if it is a single player game or a team game on
 * the given side where the average of the rank points need to be calcualted
 * 
 */

TRUNCATE TABLE GameTeamRankPoint;
TRUNCATE TABLE GameTeamRankPointOverride;
TRUNCATE TABLE GamePlayerRankPoint;
TRUNCATE TABLE GamePlayerRankPointOverride;

SET @tempGameId1 = 1;
CALL _SubProcInsertGame(@tempGameId1, "2017-01-02 0:59:08", "Side B");
CALL _SubProcInsertPlayer(@tempGameId1, 1, 2, 0, 0);
INSERT INTO GamePlayerRankPointOverride (dateTime, userId, points, rankType) VALUES ("2017-01-01 1:00:00", 1,  2, "Alltime") , ("2017-01-01 1:00:00", 1,  3, "Overall"),  ("2017-01-01 1:00:00", 1,  4, "Discipline");
INSERT INTO GamePlayerRankPointOverride (dateTime, userId, points, rankType) VALUES ("2017-01-01 1:00:00", 2,  5, "Alltime") , ("2017-01-01 1:00:00", 2,  6, "Overall"),  ("2017-01-01 1:00:00", 2,  7, "Discipline");
INSERT INTO GamePlayerRankPointOverride (dateTime, userId, points, rankType) VALUES ("2017-01-01 1:00:00", 3, 10, "Alltime") , ("2017-01-01 1:00:00", 3, 11, "Overall"),  ("2017-01-01 1:00:00", 3, 12, "Discipline");
INSERT INTO GamePlayerRankPointOverride (dateTime, userId, points, rankType) VALUES ("2017-01-01 1:00:00", 4, 15, "Alltime") , ("2017-01-01 1:00:00", 4, 16, "Overall"),  ("2017-01-01 1:00:00", 4, 17, "Discipline");

INSERT INTO GameTeamRankPointOverride (dateTime, teamId, points, rankType) VALUES  ("2017-01-01 1:00:00", 5, 22, "Overall"),  ("2017-01-01 1:00:00", 5, 24, "Discipline");
INSERT INTO GameTeamRankPointOverride (dateTime, teamId, points, rankType) VALUES  ("2017-01-01 1:00:00", 1, 32, "Overall"),  ("2017-01-01 1:00:00", 1, 34, "Discipline");

SELECT tap.plan(12);

SET @rankPointsAlltime = 0;
SET @rankPointsOverall = 0;
SET @rankPointsDiscipline = 0;

CALL _SubProcRankSidePointUntil(@tempGameId1, "Side A", @rankPointsAlltime, @rankPointsOverall, @rankPointsDiscipline);
SELECT tap.eq(@rankPointsAlltime,    2 , 'SubProcRankSidePointUntil - Single game Rank Points Alltime for Side A'); 
SELECT tap.eq(@rankPointsOverall,    3 , 'SubProcRankSidePointUntil - Single game Rank Points Overall for Side A'); 
SELECT tap.eq(@rankPointsDiscipline, 4 , 'SubProcRankSidePointUntil - Single game Rank Points Discipline for Side A'); 

CALL _SubProcRankSidePointUntil(@tempGameId1, "Side B", @rankPointsAlltime, @rankPointsOverall, @rankPointsDiscipline);
SELECT tap.eq(@rankPointsAlltime,    5 , 'SubProcRankSidePointUntil - Single game Rank Points Alltime for Side B'); 
SELECT tap.eq(@rankPointsOverall,    6 , 'SubProcRankSidePointUntil - Single game Rank Points Overall for Side B'); 
SELECT tap.eq(@rankPointsDiscipline, 7 , 'SubProcRankSidePointUntil - Single game Rank Points Discipline for Side B'); 

CALL _SubProcInsertPlayer(@tempGameId1, 1, 3, 2, 4);
CALL _SubProcRankSidePointUntil(@tempGameId1, "Side A", @rankPointsAlltime, @rankPointsOverall, @rankPointsDiscipline);
SELECT tap.eq(@rankPointsAlltime,     7 , 'SubProcRankSidePointUntil - Double game Rank Points Alltime for Side A'); 
SELECT tap.eq(@rankPointsOverall,    32 , 'SubProcRankSidePointUntil - Double game Rank Points Overall for Side A'); 
SELECT tap.eq(@rankPointsDiscipline, 34 , 'SubProcRankSidePointUntil - Double game Rank Points Discipline for Side A'); 

CALL _SubProcRankSidePointUntil(@tempGameId1, "Side B", @rankPointsAlltime, @rankPointsOverall, @rankPointsDiscipline);
SELECT tap.eq(@rankPointsAlltime,    25 , 'SubProcRankSidePointUntil - Double game Rank Points Alltime for Side B'); 
SELECT tap.eq(@rankPointsOverall,    22 , 'SubProcRankSidePointUntil - Double game Rank Points Overall for Side B'); 
SELECT tap.eq(@rankPointsDiscipline, 24 , 'SubProcRankSidePointUntil - Double game Rank Points Discipline for Side B'); 

CALL tap.finish();


/**
 * testing the method for calculating the win and loss depending
 * on who is chasing who and who is the winner
 * 
 */

SELECT tap.plan(15);
SET @tempGameId1 = 1;
CALL _SubProcInsertGame(@tempGameId1, "2017-01-02 0:59:09", "Side B");
CALL _SubProcInsertPlayer(@tempGameId1, 1, 2, 0, 0);
CALL _SubProcInsertGameSets(@tempGameId1, 0, 0, 0, 0, 0, 0);

SELECT tap.eq(COUNT(*), 0, 'SubProcCalculateWinLossRankPoints - There are no Sets added') FROM GameSetPoint WHERE matchId = @tempGameId1;

SET @newRankSideA = 0;
SET @newRankSideB = 0;

-- the total pot was 10% of A and 10% of B thus a total pot 2.5 rank Points
-- a win is worth additional 2 points and a loss reduces one point. There was no chase
-- since no sets were added to the game. As a consequence the pot is returned to the team sides
-- and only the win and loss is assigned 
-- accordingly Player A should have a loss of only 1 point and B a win of 2 Points
CALL _SubProcCalculateWinLossRankPoints(@tempGameId1, 10, 15, 2.0, -1.0, 10.0, @newRankSideA, @newRankSideB);
SELECT tap.eq(@newRankSideA,     9 , 'SubProcCalculateWinLossRankPoints - Player A Lost only 1 point since ther was no chase'); 
SELECT tap.eq(@newRankSideB,    17 , 'SubProcCalculateWinLossRankPoints - Player B Won only 2 points since there was no chase'); 

-- Only change the winner side
CALL _SubProcInsertGame(@tempGameId1, "2017-01-02 0:59:09", "Side A");
CALL _SubProcCalculateWinLossRankPoints(@tempGameId1, 10, 15, 2.0, -1.0, 10.0, @newRankSideA, @newRankSideB);
SELECT tap.eq(@newRankSideA,    12 , 'SubProcCalculateWinLossRankPoints - Player A is now the winner and won 2 points but no chase'); 
SELECT tap.eq(@newRankSideB,    14 , 'SubProcCalculateWinLossRankPoints - Player B is now the looser and lost 1 point without a chase'); 

-- now check that no rank lower than 0 is ahnded out
CALL _SubProcInsertGame(@tempGameId1, "2017-01-02 0:59:09", "Side B");
CALL _SubProcCalculateWinLossRankPoints(@tempGameId1, 0, 0, -2.0, -1.0, 10.0, @newRankSideA, @newRankSideB);
SELECT tap.eq(@newRankSideA,     0 , 'SubProcCalculateWinLossRankPoints - Player A did not get below 0'); 
SELECT tap.eq(@newRankSideB,     0 , 'SubProcCalculateWinLossRankPoints - Player B did not get below 0'); 

-- Only change the winner side
CALL _SubProcInsertGame(@tempGameId1, "2017-01-02 0:59:09", "Side A");
CALL _SubProcCalculateWinLossRankPoints(@tempGameId1, 0, 0, -2.0, -1.0, 10.0, @newRankSideA, @newRankSideB);
SELECT tap.eq(@newRankSideA,    0 , 'SubProcCalculateWinLossRankPoints - Player A did not get below 0'); 
SELECT tap.eq(@newRankSideB,    0 , 'SubProcCalculateWinLossRankPoints - Player B did not get below 0'); 

-- now lets put in some sets B is the winner as before but now there was chase after the pot
-- A is the chase and additionaly won 20/42 of the pot which is 1.190
-- thus A should have a new rank of 9.0 + 0.806 - 1.0 = 8.806;  
-- player B should have a new rank of 13.5 + 1.694 + 2.0 = 16.809
CALL _SubProcInsertGame(@tempGameId1, "2017-01-02 0:59:09", "Side B");
CALL _SubProcInsertGameSets(@tempGameId1, 10, 21, 10, 21, 0, 0);
CALL _SubProcCalculateWinLossRankPoints(@tempGameId1, 10, 15, 2.0, -1.0, 10.0, @newRankSideA, @newRankSideB);
SELECT tap.eq(FORMAT(@newRankSideA, 3),    8.806 , 'SubProcCalculateWinLossRankPoints - Player A lost 1 point and only got 0.806 of the pot.'); 
SELECT tap.eq(FORMAT(@newRankSideB, 3),   17.194 , 'SubProcCalculateWinLossRankPoints - Player B won 2 points and additional 1.694 of the pot.'); 

CALL _SubProcInsertGame(@tempGameId1, "2017-01-02 0:59:10", "Side A");
CALL _SubProcCalculateWinLossRankPoints(@tempGameId1, 10, 15, 2.0, -1.0, 10.0, @newRankSideA, @newRankSideB);
SELECT tap.eq(FORMAT(@newRankSideA, 3),   11.806 , 'SubProcCalculateWinLossRankPoints - Player A now won 2 point and only got 0.806 of the pot.'); 
SELECT tap.eq(FORMAT(@newRankSideB, 3),   14.194 , 'SubProcCalculateWinLossRankPoints - Player B now lost 2 points and additional 1.694 of the pot.'); 

-- a test case from a later test where the specific sets are added
CALL _SubProcInsertGame(@tempGameId1, "2017-01-02 0:59:11", "Side B");
CALL _SubProcInsertGameSets(@tempGameId1, 16, 21, 21, 16, 18, 21);
CALL _SubProcCalculateWinLossRankPoints(@tempGameId1, 10, 15, 2.0, -1.0, 10.0, @newRankSideA, @newRankSideB);
SELECT tap.eq(FORMAT(@newRankSideA, 3),   9.217  , 'SubProcCalculateWinLossRankPoints - Player A now with a chase of 3 sets.'); 
SELECT tap.eq(FORMAT(@newRankSideB, 3),   16.783 , 'SubProcCalculateWinLossRankPoints - Player B now with a chase of 3 sets.'); 

CALL tap.finish();

/**
 * testing functionality to calculate new rankings based on previous rankings
 * this time the test calculates the team rank scores
 * 
 */
TRUNCATE TABLE GameTeamRankPoint;
TRUNCATE TABLE GameTeamRankPointOverride;
TRUNCATE TABLE GamePlayerRankPoint;
TRUNCATE TABLE GamePlayerRankPointOverride;

SET @tempGameId1 = 1;
CALL _SubProcInsertGame(@tempGameId1, "2017-01-02 0:59:16", "Side A");
INSERT INTO GamePlayerRankPointOverride (dateTime, userId, points, rankType) VALUES ("2017-01-01 1:00:00", 1, 10, "Alltime") , ("2017-01-01 1:00:00", 1, 10, "Overall"),  ("2017-01-01 1:00:00", 1, 10, "Discipline");
INSERT INTO GamePlayerRankPointOverride (dateTime, userId, points, rankType) VALUES ("2017-01-01 1:00:00", 2, 10, "Alltime") , ("2017-01-01 1:00:00", 2, 10, "Overall"),  ("2017-01-01 1:00:00", 2, 10, "Discipline");
INSERT INTO GamePlayerRankPointOverride (dateTime, userId, points, rankType) VALUES ("2017-01-01 1:00:00", 3, 10, "Alltime") , ("2017-01-01 1:00:00", 3, 10, "Overall"),  ("2017-01-01 1:00:00", 3, 10, "Discipline");
INSERT INTO GamePlayerRankPointOverride (dateTime, userId, points, rankType) VALUES ("2017-01-01 1:00:00", 4, 10, "Alltime") , ("2017-01-01 1:00:00", 4, 10, "Overall"),  ("2017-01-01 1:00:00", 4, 10, "Discipline");

INSERT INTO GamePlayerRankPointOverride (dateTime, userId, points, rankType) VALUES ("2017-01-03 1:00:00", 1, 20, "Alltime") , ("2017-01-03 1:00:00", 1, 10, "Overall"),  ("2017-01-03 1:00:00", 1, 10, "Discipline");
INSERT INTO GamePlayerRankPointOverride (dateTime, userId, points, rankType) VALUES ("2017-01-03 1:00:00", 2, 30, "Alltime") , ("2017-01-03 1:00:00", 2, 10, "Overall"),  ("2017-01-03 1:00:00", 2, 10, "Discipline");
INSERT INTO GamePlayerRankPointOverride (dateTime, userId, points, rankType) VALUES ("2017-01-03 1:00:00", 3, 40, "Alltime") , ("2017-01-03 1:00:00", 3, 10, "Overall"),  ("2017-01-03 1:00:00", 3, 10, "Discipline");
INSERT INTO GamePlayerRankPointOverride (dateTime, userId, points, rankType) VALUES ("2017-01-03 1:00:00", 4, 50, "Alltime") , ("2017-01-03 1:00:00", 4, 10, "Overall"),  ("2017-01-03 1:00:00", 4, 10, "Discipline");

INSERT INTO GamePlayerRankPointOverride (dateTime, userId, points, rankType) VALUES ("2017-01-05 1:00:00", 1,  0, "Alltime") , ("2017-01-05 1:00:00", 1, 10, "Overall"),  ("2017-01-05 1:00:00", 1, 10, "Discipline");
INSERT INTO GamePlayerRankPointOverride (dateTime, userId, points, rankType) VALUES ("2017-01-05 1:00:00", 2,  0, "Alltime") , ("2017-01-05 1:00:00", 2, 10, "Overall"),  ("2017-01-05 1:00:00", 2, 10, "Discipline");
INSERT INTO GamePlayerRankPointOverride (dateTime, userId, points, rankType) VALUES ("2017-01-05 1:00:00", 3,  0, "Alltime") , ("2017-01-05 1:00:00", 3, 10, "Overall"),  ("2017-01-05 1:00:00", 3, 10, "Discipline");
INSERT INTO GamePlayerRankPointOverride (dateTime, userId, points, rankType) VALUES ("2017-01-05 1:00:00", 4,  0, "Alltime") , ("2017-01-05 1:00:00", 4, 10, "Overall"),  ("2017-01-05 1:00:00", 4, 10, "Discipline");

INSERT INTO GamePlayerRankPointOverride (dateTime, userId, points, rankType) VALUES ("2017-01-07 1:00:00", 1,  0, "Alltime") , ("2017-01-07 1:00:00", 1, 10, "Overall"),  ("2017-01-07 1:00:00", 1, 10, "Discipline");
INSERT INTO GamePlayerRankPointOverride (dateTime, userId, points, rankType) VALUES ("2017-01-07 1:00:00", 2,  10, "Alltime") , ("2017-01-07 1:00:00", 2, 10, "Overall"),  ("2017-01-07 1:00:00", 2, 10, "Discipline");
INSERT INTO GamePlayerRankPointOverride (dateTime, userId, points, rankType) VALUES ("2017-01-07 1:00:00", 3,  20, "Alltime") , ("2017-01-07 1:00:00", 3, 10, "Overall"),  ("2017-01-07 1:00:00", 3, 10, "Discipline");
INSERT INTO GamePlayerRankPointOverride (dateTime, userId, points, rankType) VALUES ("2017-01-07 1:00:00", 4,  0, "Alltime") , ("2017-01-07 1:00:00", 4, 10, "Overall"),  ("2017-01-07 1:00:00", 4, 10, "Discipline");

SELECT tap.plan(16);

SET @newRankA1Alltime = 0;
SET @newRankB1Alltime = 0;
SET @newRankA2Alltime = 0;
SET @newRankB2Alltime = 0;

CALL _SubProcDistributeAlltimeRankPoints("2017-01-02 1:00:00",
            1, 2, 3, 4,
            30, 40,
            @newRankA1Alltime, @newRankB1Alltime, @newRankA2Alltime, @newRankB2Alltime);
-- Expecting an equal distribution of new rank points
SELECT tap.eq(@newRankA1Alltime, 15, 'SubProcDistributeAlltimeRankPoints - A1 Distribution across 4 players all on level 10') ;
SELECT tap.eq(@newRankB1Alltime, 20, 'SubProcDistributeAlltimeRankPoints - B1 Distribution across 4 players all on level 10') ;
SELECT tap.eq(@newRankA2Alltime, 15, 'SubProcDistributeAlltimeRankPoints - A2 Distribution across 4 players all on level 10') ;
SELECT tap.eq(@newRankB2Alltime, 20, 'SubProcDistributeAlltimeRankPoints - B2 Distribution across 4 players all on level 10') ;


-- Team A has a share of 20:40 Team B has a share of 30:50
CALL _SubProcDistributeAlltimeRankPoints("2017-01-04 1:00:00",
            1, 2, 3, 4,
            60, 80,
            @newRankA1Alltime, @newRankB1Alltime, @newRankA2Alltime, @newRankB2Alltime);
SELECT tap.eq(ROUND(@newRankA1Alltime, 1), 20, 'SubProcDistributeAlltimeRankPoints - A1 with a share of 20') ;
SELECT tap.eq(ROUND(@newRankB1Alltime, 1), 30, 'SubProcDistributeAlltimeRankPoints - B1 with a share of 30') ;
SELECT tap.eq(ROUND(@newRankA2Alltime, 1), 40, 'SubProcDistributeAlltimeRankPoints - A2 with a share of 40') ;
SELECT tap.eq(ROUND(@newRankB2Alltime, 1), 50, 'SubProcDistributeAlltimeRankPoints - B2 with a share of 50') ;

-- Single Player Games with second players set to null
CALL _SubProcDistributeAlltimeRankPoints("2017-01-04 1:00:00",
            1, 2, NULL, NULL,
            60, 80,
            @newRankA1Alltime, @newRankB1Alltime, @newRankA2Alltime, @newRankB2Alltime);
SELECT tap.eq(ROUND(@newRankA1Alltime, 1), 60, 'SubProcDistributeAlltimeRankPoints - A1 with full share') ;
SELECT tap.eq(ROUND(@newRankB1Alltime, 1), 80, 'SubProcDistributeAlltimeRankPoints - B1 with full share') ;
SELECT tap.eq(ROUND(@newRankA2Alltime, 1),  0, 'SubProcDistributeAlltimeRankPoints - A2 is NULL') ;
SELECT tap.eq(ROUND(@newRankB2Alltime, 1),  0, 'SubProcDistributeAlltimeRankPoints - B2 is NULL') ;

-- Single Player Games with second players set to zero
CALL _SubProcDistributeAlltimeRankPoints("2017-01-04 1:00:00",
            1, 2, 0, 0,
            60, 80,
            @newRankA1Alltime, @newRankB1Alltime, @newRankA2Alltime, @newRankB2Alltime);
SELECT tap.eq(ROUND(@newRankA1Alltime, 1), 60, 'SubProcDistributeAlltimeRankPoints - A1 with full share') ;
SELECT tap.eq(ROUND(@newRankB1Alltime, 1), 80, 'SubProcDistributeAlltimeRankPoints - B1 with full share') ;
SELECT tap.eq(ROUND(@newRankA2Alltime, 1),  0, 'SubProcDistributeAlltimeRankPoints - A2 is 0') ;
SELECT tap.eq(ROUND(@newRankB2Alltime, 1),  0, 'SubProcDistributeAlltimeRankPoints - B2 is 0') ;

-- Zero Ranks to avoid devide by zeor problems
CALL _SubProcDistributeAlltimeRankPoints("2017-01-06 1:00:00",
            1, 2, 3, 4,
            60, 80,
            @newRankA1Alltime, @newRankB1Alltime, @newRankA2Alltime, @newRankB2Alltime);
SELECT tap.eq(ROUND(@newRankA1Alltime, 1), 30, 'SubProcDistributeAlltimeRankPoints - A1 with half share') ;
SELECT tap.eq(ROUND(@newRankB1Alltime, 1), 40, 'SubProcDistributeAlltimeRankPoints - B1 with half share') ;
SELECT tap.eq(ROUND(@newRankA2Alltime, 1), 30, 'SubProcDistributeAlltimeRankPoints - A2 with half share') ;
SELECT tap.eq(ROUND(@newRankB2Alltime, 1), 40, 'SubProcDistributeAlltimeRankPoints - B2 with half share') ;

-- Zero Ranks to avoid devide by zeor problems
CALL _SubProcDistributeAlltimeRankPoints("2017-01-08 1:00:00",
            1, 2, 3, 4,
            100, 100,
            @newRankA1Alltime, @newRankB1Alltime, @newRankA2Alltime, @newRankB2Alltime);
SELECT tap.eq(ROUND(@newRankA1Alltime, 3), 40, 'SubProcDistributeAlltimeRankPoints - A1 with half share') ;
SELECT tap.eq(ROUND(@newRankB1Alltime, 3), 55, 'SubProcDistributeAlltimeRankPoints - B1 with half share') ;
SELECT tap.eq(ROUND(@newRankA2Alltime, 3), 60, 'SubProcDistributeAlltimeRankPoints - A2 with half share') ;
SELECT tap.eq(ROUND(@newRankB2Alltime, 3), 45, 'SubProcDistributeAlltimeRankPoints - B2 with half share') ;


CALL tap.finish();            
            
/**
 * testing functionality to calculate new rankings based on previous rankings
 * this time the test calculates the team rank scores
 * 
 */
TRUNCATE TABLE GameTeamRankPoint;
TRUNCATE TABLE GameTeamRankPointOverride;
TRUNCATE TABLE GamePlayerRankPoint;
TRUNCATE TABLE GamePlayerRankPointOverride;

SET @tempGameId1 = 1;
CALL _SubProcInsertGame(@tempGameId1, "2017-01-02 0:59:16", "Side A");
INSERT INTO GamePlayerRankPointOverride (dateTime, userId, points, rankType) VALUES ("2017-01-01 1:00:00", 1, 10, "Alltime") , ("2017-01-01 1:00:00", 1, 10, "Overall"),  ("2017-01-01 1:00:00", 1, 10, "Discipline");
INSERT INTO GamePlayerRankPointOverride (dateTime, userId, points, rankType) VALUES ("2017-01-01 1:00:00", 2, 10, "Alltime") , ("2017-01-01 1:00:00", 2, 10, "Overall"),  ("2017-01-01 1:00:00", 2, 10, "Discipline");
INSERT INTO GamePlayerRankPointOverride (dateTime, userId, points, rankType) VALUES ("2017-01-01 1:00:00", 3, 10, "Alltime") , ("2017-01-01 1:00:00", 3, 10, "Overall"),  ("2017-01-01 1:00:00", 3, 10, "Discipline");
INSERT INTO GamePlayerRankPointOverride (dateTime, userId, points, rankType) VALUES ("2017-01-01 1:00:00", 4, 10, "Alltime") , ("2017-01-01 1:00:00", 4, 10, "Overall"),  ("2017-01-01 1:00:00", 4, 10, "Discipline");

SELECT tap.plan(49);

SELECT tap.eq(COUNT(*), 0, 'SubProcInsertPlayer - Player rank points is still empty') FROM GamePlayerRankPoint;
SELECT tap.eq(COUNT(*), 0, 'SubProcInsertPlayer - Team   rank points is still empty') FROM GameTeamRankPoint;

-- test a single mens match first
CALL _SubProcInsertGame(@tempGameId1, "2017-01-02 0:59:17", "Side A");
CALL _SubProcInsertPlayer(@tempGameId1, 1, 3, 0, 0); 
CALL _SubProcInsertRankPoint(@tempGameId1, 1, 3, 0, 0, "Discipline", 10, 20, 0, 0, 30, 40, 50, 60); 
SELECT tap.eq(COUNT(*), 6, 'SubProcInsertRankPoint A1 - (Single Discipline Winner A) 2 player game added 6 player rank entries') FROM GamePlayerRankPoint;
SELECT tap.eq(COUNT(*), 0, 'SubProcInsertRankPoint A2 - (Single Discipline Winner A) 2 player game added no team rank entries') FROM GameTeamRankPoint;
SELECT tap.eq(points , 10.000, 'SubProcInsertRankPoint A3 - (Single Discipline Winner A) A correct alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 1 AND rankType = "Alltime";
SELECT tap.eq(points , 30.000, 'SubProcInsertRankPoint A4 - (Single Discipline Winner A) A correct overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 1 AND rankType = "Overall";
SELECT tap.eq(points , 50.000, 'SubProcInsertRankPoint A5 - (Single Discipline Winner A) A correct discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 1 AND rankType = "Discipline";
SELECT tap.eq(points , 20.000, 'SubProcInsertRankPoint A6 - (Single Discipline Winner A) B correct alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 3 AND rankType = "Alltime";
SELECT tap.eq(points , 40.000, 'SubProcInsertRankPoint A7 - (Single Discipline Winner A) B correct overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 3 AND rankType = "Overall";
SELECT tap.eq(points , 60.000, 'SubProcInsertRankPoint A8 - (Single Discipline Winner A) B correct discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 3 AND rankType = "Discipline";

-- test a single mens match first save it as Overall only
CALL _SubProcInsertGame(@tempGameId1, "2017-01-02 0:59:17", "Side A");
CALL _SubProcInsertPlayer(@tempGameId1, 1, 3, 0, 0); 
CALL _SubProcInsertRankPoint(@tempGameId1, 1, 3, NULL, NULL, "Overall", 10, 20, 0, 0, 30, 40, 50, 60); 
SELECT tap.eq(COUNT(*), 4, 'SubProcInsertRankPoint B1 - (Single Overall Winner A) 2 player game added 4 player rank entries') FROM GamePlayerRankPoint;
SELECT tap.eq(COUNT(*), 0, 'SubProcInsertRankPoint B2 - (Single Overall Winner A) 2 player game added no team rank entries') FROM GameTeamRankPoint;
SELECT tap.eq(points , 10.000, 'SubProcInsertRankPoint B3 - (Single Overall Winner A) A correct alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 1 AND rankType = "Alltime";
SELECT tap.eq(points , 30.000, 'SubProcInsertRankPoint B4 - (Single Overall Winner A) A correct overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 1 AND rankType = "Overall";
SELECT tap.eq(points , 20.000, 'SubProcInsertRankPoint B6 - (Single Overall Winner A) B correct alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 3 AND rankType = "Alltime";
SELECT tap.eq(points , 40.000, 'SubProcInsertRankPoint B7 - (Single Overall Winner A) B correct overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 3 AND rankType = "Overall";

-- test a single mens match first save it as Alltime only
CALL _SubProcInsertGame(@tempGameId1, "2017-01-02 0:59:17", "Side A");
CALL _SubProcInsertPlayer(@tempGameId1, 1, 3, 4, 0); 
CALL _SubProcInsertRankPoint(@tempGameId1, 1, 3, 4, 0, "Alltime", 10, 20, 25, 0, 30, 40, 50, 60); 
SELECT tap.eq(COUNT(*), 3, 'SubProcInsertRankPoint C1 - (Fun Alltime Winner A) 3 player game added 3 player rank entries') FROM GamePlayerRankPoint;
SELECT tap.eq(COUNT(*), 0, 'SubProcInsertRankPoint C2 - (Fun Alltime Winner A) 3 player game added no team rank entries') FROM GameTeamRankPoint;
SELECT tap.eq(points , 10.000, 'SubProcInsertRankPoint C3 - (Fun Alltime Winner A) A correct alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 1 AND rankType = "Alltime";
SELECT tap.eq(points , 20.000, 'SubProcInsertRankPoint C6 - (Fun Alltime Winner A) B correct alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 3 AND rankType = "Alltime";
SELECT tap.eq(points , 25.000, 'SubProcInsertRankPoint C7 - (Fun Alltime Winner A) A2 correct alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 4 AND rankType = "Alltime";

-- test a fun game in another constellation and say its a discipline game still only alltime scores should be recorded
CALL _SubProcInsertGame(@tempGameId1, "2017-01-02 0:59:17", "Side A");
CALL _SubProcInsertPlayer(@tempGameId1, 1, 3, 0, 4); 
CALL _SubProcInsertRankPoint(@tempGameId1, 1, 3, 0, 4, "Discipline", 10, 20, 0, 35, 30, 40, 50, 60); 
SELECT tap.eq(COUNT(*), 3, 'SubProcInsertRankPoint D1 - (Fun Discipline Winner A) 3 player game added 3 player rank entries') FROM GamePlayerRankPoint;
SELECT tap.eq(COUNT(*), 0, 'SubProcInsertRankPoint D2 - (Fun Discipline Winner A) 3 player game added no team rank entries') FROM GameTeamRankPoint;
SELECT tap.eq(points , 10.000, 'SubProcInsertRankPoint D3 - (Fun Discipline Winner A) A correct alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 1 AND rankType = "Alltime";
SELECT tap.eq(points , 20.000, 'SubProcInsertRankPoint D6 - (Fun Discipline Winner A) B correct alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 3 AND rankType = "Alltime";
SELECT tap.eq(points , 35.000, 'SubProcInsertRankPoint D7 - (Fun Discipline Winner A) A2 correct alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 4 AND rankType = "Alltime";

-- test a double mixed match with writing scores as discipline
CALL _SubProcInsertGame(@tempGameId1, "2017-01-02 0:59:17", "Side A");
CALL _SubProcInsertPlayer(@tempGameId1, 1, 3, 2, 4); 
CALL _SubProcInsertRankPoint(@tempGameId1, 1, 3, 2, 4, "Discipline", 10, 20, 15, 25, 30, 40, 50, 60); 
SET @teamA = _TeamIdForGameSide(@tempGameId1, "Side A");
SET @teamB = _TeamIdForGameSide(@tempGameId1, "Side B");
SELECT tap.eq(COUNT(*), 4, 'SubProcInsertRankPoint E1  - (Double Discipline Winner A) 4 player game added 4 player rank entries') FROM GamePlayerRankPoint;
SELECT tap.eq(COUNT(*), 4, 'SubProcInsertRankPoint E2  - (Double Discipline Winner A) 4 player game added 4 team rank entries') FROM GameTeamRankPoint;
SELECT tap.eq(points , 10.000, 'SubProcInsertRankPoint E3  - (Double Discipline Winner A) A correct alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 1 AND rankType = "Alltime";
SELECT tap.eq(points , 20.000, 'SubProcInsertRankPoint E4  - (Double Discipline Winner A) B correct alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 3 AND rankType = "Alltime";
SELECT tap.eq(points , 15.000, 'SubProcInsertRankPoint E5  - (Double Discipline Winner A) A2 correct alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 2 AND rankType = "Alltime";
SELECT tap.eq(points , 25.000, 'SubProcInsertRankPoint E6  - (Double Discipline Winner A) B2 correct alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 4 AND rankType = "Alltime";

SELECT tap.eq(points , 30.000, 'SubProcInsertRankPoint E7  - (Double Discipline Winner A) Team A correct Overall    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId1 AND teamId = @teamA AND rankType = "Overall";
SELECT tap.eq(points , 40.000, 'SubProcInsertRankPoint E8  - (Double Discipline Winner A) Team B correct Overall    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId1 AND teamId = @teamB AND rankType = "Overall";
SELECT tap.eq(points , 50.000, 'SubProcInsertRankPoint E9  - (Double Discipline Winner A) Team A correct Discipline    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId1 AND teamId = @teamA AND rankType = "Discipline";
SELECT tap.eq(points , 60.000, 'SubProcInsertRankPoint E10 - (Double Discipline Winner A) Team B correct Discipline    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId1 AND teamId = @teamB AND rankType = "Discipline";


-- test a double mixed match with writing scores as discipline
CALL _SubProcInsertGame(@tempGameId1, "2017-01-02 0:59:17", "Side A");
CALL _SubProcInsertPlayer(@tempGameId1, 1, 3, 2, 4); 
CALL _SubProcInsertRankPoint(@tempGameId1, 1, 3, 2, 4, "Overall", 10, 20, 15, 25, 30, 40, 50, 60); 
SET @teamA = _TeamIdForGameSide(@tempGameId1, "Side A");
SET @teamB = _TeamIdForGameSide(@tempGameId1, "Side B");
SELECT tap.eq(COUNT(*), 4, 'SubProcInsertRankPoint F1  - (Double Overall Winner A) 4 player game added 4 player rank entries') FROM GamePlayerRankPoint;
SELECT tap.eq(COUNT(*), 2, 'SubProcInsertRankPoint F2  - (Double Overall Winner A) 4 player game added 2 team rank entries') FROM GameTeamRankPoint;
SELECT tap.eq(points , 10.000, 'SubProcInsertRankPoint F3  - (Double Overall Winner A) A correct alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 1 AND rankType = "Alltime";
SELECT tap.eq(points , 20.000, 'SubProcInsertRankPoint F4  - (Double Overall Winner A) B correct alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 3 AND rankType = "Alltime";
SELECT tap.eq(points , 15.000, 'SubProcInsertRankPoint F5  - (Double Overall Winner A) A2 correct alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 2 AND rankType = "Alltime";
SELECT tap.eq(points , 25.000, 'SubProcInsertRankPoint F6  - (Double Overall Winner A) B2 correct alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 4 AND rankType = "Alltime";

SELECT tap.eq(points , 30.000, 'SubProcInsertRankPoint F7  - (Double Overall Winner A) Team A correct Overall    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId1 AND teamId = @teamA AND rankType = "Overall";
SELECT tap.eq(points , 40.000, 'SubProcInsertRankPoint F8  - (Double Overall Winner A) Team B correct Overall    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId1 AND teamId = @teamB AND rankType = "Overall";

CALL InsertUser("kb@bc-comet.de",   "Kane", "B.", "Male",   "$2y$10$caqHxWaKHlV5u0TAdwvvWOVBotrPCJFQ978J.pehzPqatlk4b6vEm", TRUE, FALSE, FALSE);
CALL InsertUser("fd@bc-comet.de",   "Frank",  "D.", "Male",   "$2y$10$caqHxWaKHlV5u0TAdwvvWOVBotrPCJFQ978J.pehzPqatlk4b6vEm", TRUE, FALSE, FALSE);
CALL InsertUser("ia@bc-comet.de", "Indra", "A.",    "Female", "$2y$10$caqHxWaKHlV5u0TAdwvvWOVBotrPCJFQ978J.pehzPqatlk4b6vEm", TRUE, FALSE, FALSE);
CALL InsertUser("vk@bc-comet.de", "Valerie", "K.",   "Female", "$2y$10$caqHxWaKHlV5u0TAdwvvWOVBotrPCJFQ978J.pehzPqatlk4b6vEm", TRUE, FALSE, FALSE);

-- Changing all players see that tables get cleaned up as intended
CALL _SubProcInsertGame(@tempGameId1, "2017-01-02 0:59:17", "Side A");
CALL _SubProcInsertPlayer(@tempGameId1, 5, 6, 7, 0); 
CALL _SubProcInsertRankPoint(@tempGameId1, 5, 6, 7, 0, "Overall", 10, 20, 15, 25, 30, 40, 50, 60); 
SET @teamA = _TeamIdForGameSide(@tempGameId1, "Side A");
SET @teamB = _TeamIdForGameSide(@tempGameId1, "Side B");
SELECT tap.eq(COUNT(*), 3, 'SubProcInsertRankPoint G1  - (Double Discipline Winner A) 4 player game added 4 player rank entries') FROM GamePlayerRankPoint;
SELECT tap.eq(COUNT(*), 0, 'SubProcInsertRankPoint G2  - (Double Discipline Winner A) 4 player game added 2 team rank entries') FROM GameTeamRankPoint;
SELECT tap.eq(points , 10.000, 'SubProcInsertRankPoint G3  - (Double Discipline Winner A) A correct alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 5 AND rankType = "Alltime";
SELECT tap.eq(points , 20.000, 'SubProcInsertRankPoint G4  - (Double Discipline Winner A) B correct alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 6 AND rankType = "Alltime";
SELECT tap.eq(points , 15.000, 'SubProcInsertRankPoint G5  - (Double Discipline Winner A) A2 correct alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 7 AND rankType = "Alltime";



CALL tap.finish();

/**
 * test the method for determining the team type
 */
SELECT tap.plan(4);

SELECT tap.eq(_GetTeamGender("Male",   "Male")    ,  "Double Men",     'GetTeamGender - Double Men detected');
SELECT tap.eq(_GetTeamGender("Female", "Female")  ,  "Double Women",   'GetTeamGender - Double Women detected');
SELECT tap.eq(_GetTeamGender("Male",   "Female")  ,  "Double Mixed",   'GetTeamGender - Double Mixed detected');
SELECT tap.eq(_GetTeamGender("Female", "Male")    ,  "Double Mixed",   'GetTeamGender - Double Mixed detected');

CALL tap.finish();


/**
 * test the method for determining the rank type of two and four player games
 */

SELECT tap.plan(36);

SELECT tap.eq(_GameRankType("Male",   "Male",   NULL,     NULL) , "Discipline", 'GameRankType - Single Discipline Rank Detected');
SELECT tap.eq(_GameRankType("Female", "Male",   NULL,     NULL) , "Overall",    'GameRankType - Single Overall Rank Type detected');
SELECT tap.eq(_GameRankType("Female", "Female", NULL,     NULL) , "Discipline", 'GameRankType - Single Discipline Rank Type detected');
SELECT tap.eq(_GameRankType("Male",   "Female", NULL,     NULL) , "Overall",    'GameRankType - Single Overall Rank Type detected');

SELECT tap.eq(_GameRankType("Male",   "Male",   "Female", NULL) , "Alltime", 'GameRankType - Three Player Game Overall Rank Type detected');
SELECT tap.eq(_GameRankType("Female", "Male",   "Female", NULL) , "Alltime", 'GameRankType - Three Player Game Overall Rank Type detected');
SELECT tap.eq(_GameRankType("Female", "Female", "Female", NULL) , "Alltime", 'GameRankType - Three Player Game Overall Rank Type detected');
SELECT tap.eq(_GameRankType("Male",   "Female", "Female", NULL) , "Alltime", 'GameRankType - Three Player Game Overall Rank Type detected');

SELECT tap.eq(_GameRankType("Male",   "Male",   "Male", NULL) , "Alltime", 'GameRankType - Three Player Game Overall Rank Type detected');
SELECT tap.eq(_GameRankType("Female", "Male",   "Male", NULL) , "Alltime", 'GameRankType - Three Player Game Overall Rank Type detected');
SELECT tap.eq(_GameRankType("Female", "Female", "Male", NULL) , "Alltime", 'GameRankType - Three Player Game Overall Rank Type detected');
SELECT tap.eq(_GameRankType("Male",   "Female", "Male", NULL) , "Alltime", 'GameRankType - Three Player Game Overall Rank Type detected');

SELECT tap.eq(_GameRankType("Male",   "Male",   NULL, "Female") , "Alltime", 'GameRankType - Three Player Game Overall Rank Type detected');
SELECT tap.eq(_GameRankType("Female", "Male",   NULL, "Female") , "Alltime", 'GameRankType - Three Player Game Overall Rank Type detected');
SELECT tap.eq(_GameRankType("Female", "Female", NULL, "Female") , "Alltime", 'GameRankType - Three Player Game Overall Rank Type detected');
SELECT tap.eq(_GameRankType("Male",   "Female", NULL, "Female") , "Alltime", 'GameRankType - Three Player Game Overall Rank Type detected');

SELECT tap.eq(_GameRankType("Male",   "Male",   "Female", "Female") , "Discipline", 'GameRankType - Double Discipline Rank Type detected');
SELECT tap.eq(_GameRankType("Female", "Male",   "Female", "Female") , "Overall",    'GameRankType - Double Overall Rank Type detected');
SELECT tap.eq(_GameRankType("Female", "Female", "Female", "Female") , "Discipline", 'GameRankType - Double Discipline Rank Type detected');
SELECT tap.eq(_GameRankType("Male",   "Female", "Female", "Female") , "Overall",    'GameRankType - Double Overall Rank Type detected');

SELECT tap.eq(_GameRankType("Male",   "Male",   "Male", "Female") , "Overall",    'GameRankType - Dobule Overall Rank Type detected');
SELECT tap.eq(_GameRankType("Female", "Male",   "Male", "Female") , "Discipline", 'GameRankType - Mixed Discipline Rank Type detected');
SELECT tap.eq(_GameRankType("Female", "Female", "Male", "Female") , "Overall",    'GameRankType - Double Overall Rank Type detected');
SELECT tap.eq(_GameRankType("Male",   "Female", "Male", "Female") , "Overall",    'GameRankType - Dobule Overall Rank Type detected');

SELECT tap.eq(_GameRankType("Male",   "Male",   NULL, "Male") , "Alltime", 'GameRankType - Three Player Game Overall Rank Type detected');
SELECT tap.eq(_GameRankType("Female", "Male",   NULL, "Male") , "Alltime", 'GameRankType - Three Player Game Overall Rank Type detected');
SELECT tap.eq(_GameRankType("Female", "Female", NULL, "Male") , "Alltime", 'GameRankType - Three Player Game Overall Rank Type detected');
SELECT tap.eq(_GameRankType("Male",   "Female", NULL, "Male") , "Alltime", 'GameRankType - Three Player Game Overall Rank Type detected');

SELECT tap.eq(_GameRankType("Male",   "Male",   "Female", "Male") , "Overall",    'GameRankType - Dobule Overall Rank Type detected');
SELECT tap.eq(_GameRankType("Female", "Male",   "Female", "Male") , "Overall",    'GameRankType - Dobule Overall Rank Type detected');
SELECT tap.eq(_GameRankType("Female", "Female", "Female", "Male") , "Overall",    'GameRankType - Double Overall Rank Type detected');
SELECT tap.eq(_GameRankType("Male",   "Female", "Female", "Male") , "Discipline", 'GameRankType - Dobule Discipline Rank Type detected');

SELECT tap.eq(_GameRankType("Male",   "Male",   "Male", "Male") , "Discipline", 'GameRankType - Dobule Discipline Rank Type detected');
SELECT tap.eq(_GameRankType("Female", "Male",   "Male", "Male") , "Overall",    'GameRankType - Dobule Overall Rank Type detected');
SELECT tap.eq(_GameRankType("Female", "Female", "Male", "Male") , "Discipline", 'GameRankType - Dobule Discipline Rank Type detected');
SELECT tap.eq(_GameRankType("Male",   "Female", "Male", "Male") , "Overall",    'GameRankType - Dobule Overall Rank Type detected');

CALL tap.finish();

/**
 * now testing the procedure that completly calculates a game
 * using the rank settings as well as the sets that were played
 * during the game etc.
 * 
 */
TRUNCATE TABLE GameTeamRankPoint;
TRUNCATE TABLE GameTeamRankPointOverride;
TRUNCATE TABLE GamePlayerRankPoint;
TRUNCATE TABLE GamePlayerRankPointOverride;
TRUNCATE TABLE RankPointSetting;

INSERT INTO RankPointSetting (validFrom, pointsWin, pointsLoss, prcChase) VALUES ("2017-01-01 1:00:00", 2.0, -1.0, 10.0);

SELECT tap.plan(30);

SET @tempGameId1 = 1;
-- test first game a mens double
CALL _SubProcInsertGame(@tempGameId1, "2017-01-02 0:59:33", "Side A");
CALL _SubProcInsertPlayer(@tempGameId1, 1, 3, 5, 6);
CALL _SubProcInsertGameSets(@tempGameId1, 22, 20, 21, 16, 0, 0);
CALL _SubProcUpdateGameRankPointForGame(@tempGameId1);
SET @teamA = _TeamIdForGameSide(@tempGameId1, "Side A");
SET @teamB = _TeamIdForGameSide(@tempGameId1, "Side B");
SELECT tap.eq(COUNT(*), 4, 'SubProcUpdateGameRankPointForGame - (Game 1) 4 player game added 4 player rank entries') FROM GamePlayerRankPoint;
SELECT tap.eq(COUNT(*), 4, 'SubProcUpdateGameRankPointForGame - (Game 1) 4 player game added 2 team rank entries') FROM GameTeamRankPoint;
SELECT tap.eq(points    ,  1.000, 'SubProcUpdateGameRankPointForGame - (Game 1) Player 1 won  1  alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 1 AND rankType = "Alltime";
SELECT tap.eq(points    ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 1) Player 1 won  no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 1 AND rankType = "Overall";
SELECT tap.eq(points    ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 1) Player 1 won  no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 1 AND rankType = "Discipline";
SELECT tap.eq(points    ,  1.000, 'SubProcUpdateGameRankPointForGame - (Game 1) Player 5 won  1  alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 5 AND rankType = "Alltime";
SELECT tap.eq(points    ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 1) Player 5 won  no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 5 AND rankType = "Overall";
SELECT tap.eq(points    ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 1) Player 5 won  no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 5 AND rankType = "Discipline";
SELECT tap.eq(points    ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 1) Player 3 lost  0 alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 3 AND rankType = "Alltime";
SELECT tap.eq(points    ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 1) Player 3 lost no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 3 AND rankType = "Overall";
SELECT tap.eq(points    ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 1) Player 3 lost no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 3 AND rankType = "Discipline";
SELECT tap.eq(points    ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 1) Player 6 lost  0 alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 6 AND rankType = "Alltime";
SELECT tap.eq(points    ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 1) Player 6 lost no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 6 AND rankType = "Overall";
SELECT tap.eq(points    ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 1) Player 6 lost no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 6 AND rankType = "Discipline";

SELECT tap.eq(points    ,  2.000, 'SubProcUpdateGameRankPointForGame - (Game 1) Team  A won   2 overall    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId1 AND teamId = @teamA AND rankType = "Overall";
SELECT tap.eq(points    ,  2.000, 'SubProcUpdateGameRankPointForGame - (Game 1) Team  A won   2 discipline points') FROM GameTeamRankPoint WHERE matchId = @tempGameId1 AND teamId = @teamA AND rankType = "Discipline";
SELECT tap.eq(points    ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 1) Team  B lost  0 alltime    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId1 AND teamId = @teamB AND rankType = "Overall";
SELECT tap.eq(points    ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 1) Team  B lost  0 overall    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId1 AND teamId = @teamB AND rankType = "Discipline";

-- Playing a mixed game
SET @tempGameId2 = 2;
CALL _SubProcInsertGame(@tempGameId2, "2017-01-03 0:59:34", "Side B");
CALL _SubProcInsertPlayer(@tempGameId2, 1, 2, 4, 5);
CALL _SubProcInsertGameSets(@tempGameId2, 18, 21, 17, 21, 0, 0);

CALL _SubProcUpdateGameRankPointForGame(@tempGameId2);
SET @teamA = _TeamIdForGameSide(@tempGameId2, "Side A");
SET @teamB = _TeamIdForGameSide(@tempGameId2, "Side B");
SELECT tap.eq(COUNT(*), 8, 'SubProcUpdateGameRankPointForGame - (Game 2) 4 player game added 8 player rank entries') FROM GamePlayerRankPoint;
SELECT tap.eq(COUNT(*), 8, 'SubProcUpdateGameRankPointForGame - (Game 2) 4 player game added 4 team rank entries') FROM GameTeamRankPoint;
SELECT tap.eq(points ,  0.500, 'SubProcUpdateGameRankPointForGame - (Game 2) Player 1 lost 0.5 alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId2 AND userId = 1 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 2) Player 1 lost  no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId2 AND userId = 1 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 2) Player 1 lost  no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId2 AND userId = 1 AND rankType = "Discipline";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 2) Player 4 lost  0  alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId2 AND userId = 4 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 2) Player 4 lost  no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId2 AND userId = 4 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 2) Player 4 lost  no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId2 AND userId = 4 AND rankType = "Discipline";
SELECT tap.eq(points ,  1.004, 'SubProcUpdateGameRankPointForGame - (Game 2) Player 2 won  1 alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId2 AND userId = 2 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 2) Player 2 won no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId2 AND userId = 2 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 2) Player 2 won no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId2 AND userId = 2 AND rankType = "Discipline";
SELECT tap.eq(points ,  2.005, 'SubProcUpdateGameRankPointForGame - (Game 2) Player 5 won  2 alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId2 AND userId = 5 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 2) Player 5 won no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId2 AND userId = 5 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 2) Player 5 won no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId2 AND userId = 5 AND rankType = "Discipline";

SELECT tap.eq(points ,  2.000, 'SubProcUpdateGameRankPointForGame - (Game 2) Team  A won   2 overall    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId2 AND teamId = @teamB AND rankType = "Overall";
SELECT tap.eq(points ,  2.000, 'SubProcUpdateGameRankPointForGame - (Game 2) Team  A won   2 discipline points') FROM GameTeamRankPoint WHERE matchId = @tempGameId2 AND teamId = @teamB AND rankType = "Discipline";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 2) Team  B lost  0 alltime    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId2 AND teamId = @teamA AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 2) Team  B lost  0 overall    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId2 AND teamId = @teamA AND rankType = "Discipline";


-- Third game is a retry of the second. In the second the team B won again in 3 sets
SET @tempGameId3 = 0;
CALL _SubProcInsertGame(@tempGameId3, "2017-01-04 0:59:35", "Side B");
CALL _SubProcInsertPlayer(@tempGameId3, 1, 2, 4, 5);
CALL _SubProcInsertGameSets(@tempGameId3, 16, 21, 21, 16, 18, 21);

-- 0.05 of team A in the pot 0.301 of team B in the pot -> total pot of 0.351
-- Team A scored 55/113=0.487 * 0.351 = 0.170 Team A share of Pot
-- Team B scored 55/113=0.513 * 0.351 = 0.180 Team B share of Pot
-- Team A gets -1 plus 0.170 of the pot is a total team alltime loss of -0.830
-- Team B gets +2 plus 0.180 of the pot is a total team alltime win  of  1.820
-- Team A has 0.500 and 0.000 points

CALL _SubProcUpdateGameRankPointForGame(@tempGameId3);
SET @teamA = _TeamIdForGameSide(@tempGameId3, "Side A");
SET @teamB = _TeamIdForGameSide(@tempGameId3, "Side B");
SELECT tap.eq(COUNT(*), 12, 'SubProcUpdateGameRankPointForGame - (Game 3) 4 player game added 8 player rank entries') FROM GamePlayerRankPoint;
SELECT tap.eq(COUNT(*), 12, 'SubProcUpdateGameRankPointForGame - (Game 3) 4 player game added 4 team rank entries') FROM GameTeamRankPoint;
SELECT tap.eq(points ,  1.944, 'SubProcUpdateGameRankPointForGame - (Game 3) Player 2 won  2  alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 2 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 3) Player 2 won  no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 2 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 3) Player 2 won  no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 2 AND rankType = "Discipline";
SELECT tap.eq(points ,  2.944, 'SubProcUpdateGameRankPointForGame - (Game 3) Player 5 won  3  alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 5 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 3) Player 5 won  no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 5 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 3) Player 5 won  no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 5 AND rankType = "Discipline";
SELECT tap.eq(points ,  0.250, 'SubProcUpdateGameRankPointForGame - (Game 3) Player 1 lost 0.2alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 1 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 3) Player 1 lost no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 1 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 3) Player 1 lost no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 1 AND rankType = "Discipline";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 3) Player 4 lost  0 alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 4 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 3) Player 4 lost no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 4 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 3) Player 4 lost no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 4 AND rankType = "Discipline";

SELECT tap.eq(points ,  3.903, 'SubProcUpdateGameRankPointForGame - (Game 3) Team  A won   4 overall    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId3 AND teamId = @teamB AND rankType = "Overall";
SELECT tap.eq(points ,  3.903, 'SubProcUpdateGameRankPointForGame - (Game 3) Team  A won   4 discipline points') FROM GameTeamRankPoint WHERE matchId = @tempGameId3 AND teamId = @teamB AND rankType = "Discipline";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 3) Team  B lost  0 alltime    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId3 AND teamId = @teamA AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 3) Team  B lost  0 overall    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId3 AND teamId = @teamA AND rankType = "Discipline";

CALL tap.finish();

/**
 * this time we will delete all the scores and use the procedure which crawls over all games to
 * recalculate the rankings. the procedure is needed for example in case a game is changed, than all
 * games based on the changed one, (currently the simple case, games played after the given one) 
 * have to be updated accrodingly
 * 
 */
TRUNCATE TABLE GameTeamRankPoint;
TRUNCATE TABLE GameTeamRankPointOverride;
TRUNCATE TABLE GamePlayerRankPoint;
TRUNCATE TABLE GamePlayerRankPointOverride;

SELECT tap.plan(26);

-- Date of the first game was "2017-01-02 0:59:00"

-- Now recalculate all games
CALL _SubProcUpdateGameRankPointStartingFrom("2017-01-02 0:59:00");

-- Do the same checks as before, the results should be the same
-- recheck the tables
SELECT tap.eq(COUNT(*), 12, 'SubProcUpdateGameRankPointStartingFrom - (Game 3) 4 player game added 12 player rank entries') FROM GamePlayerRankPoint;
SELECT tap.eq(COUNT(*), 12, 'SubProcUpdateGameRankPointStartingFrom - (Game 3) 4 player game added 6 team rank entries') FROM GameTeamRankPoint;

-- check the first game
SET @tempGameId1 = 1;
SET @teamA = _TeamIdForGameSide(@tempGameId1, "Side A");
SET @teamB = _TeamIdForGameSide(@tempGameId1, "Side B");
SELECT tap.eq(points    ,  1.000, 'SubProcUpdateGameRankPointForGame - (Game 1) Player 1 won  1  alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 1 AND rankType = "Alltime";
SELECT tap.eq(points    ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 1) Player 1 won  no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 1 AND rankType = "Overall";
SELECT tap.eq(points    ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 1) Player 1 won  no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 1 AND rankType = "Discipline";
SELECT tap.eq(points    ,  1.000, 'SubProcUpdateGameRankPointForGame - (Game 1) Player 5 won  1  alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 5 AND rankType = "Alltime";
SELECT tap.eq(points    ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 1) Player 5 won  no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 5 AND rankType = "Overall";
SELECT tap.eq(points    ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 1) Player 5 won  no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 5 AND rankType = "Discipline";
SELECT tap.eq(points    ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 1) Player 3 lost  0 alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 3 AND rankType = "Alltime";
SELECT tap.eq(points    ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 1) Player 3 lost no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 3 AND rankType = "Overall";
SELECT tap.eq(points    ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 1) Player 3 lost no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 3 AND rankType = "Discipline";
SELECT tap.eq(points    ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 1) Player 6 lost  0 alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 6 AND rankType = "Alltime";
SELECT tap.eq(points    ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 1) Player 6 lost no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 6 AND rankType = "Overall";
SELECT tap.eq(points    ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 1) Player 6 lost no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 6 AND rankType = "Discipline";

SELECT tap.eq(points    ,  2.000, 'SubProcUpdateGameRankPointForGame - (Game 1) Team  A won   2 overall    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId1 AND teamId = @teamA AND rankType = "Overall";
SELECT tap.eq(points    ,  2.000, 'SubProcUpdateGameRankPointForGame - (Game 1) Team  A won   2 discipline points') FROM GameTeamRankPoint WHERE matchId = @tempGameId1 AND teamId = @teamA AND rankType = "Discipline";
SELECT tap.eq(points    ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 1) Team  B lost  0 alltime    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId1 AND teamId = @teamB AND rankType = "Overall";
SELECT tap.eq(points    ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 1) Team  B lost  0 overall    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId1 AND teamId = @teamB AND rankType = "Discipline";

-- Check the second game
SET @tempGameId2 = 2;
SET @teamA = _TeamIdForGameSide(@tempGameId2, "Side A");
SET @teamB = _TeamIdForGameSide(@tempGameId2, "Side B");
SELECT tap.eq(points ,  0.500, 'SubProcUpdateGameRankPointForGame - (Game 2) Player 1 lost 0.5 alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId2 AND userId = 1 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 2) Player 1 lost  no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId2 AND userId = 1 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 2) Player 1 lost  no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId2 AND userId = 1 AND rankType = "Discipline";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 2) Player 4 lost  0  alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId2 AND userId = 4 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 2) Player 4 lost  no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId2 AND userId = 4 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 2) Player 4 lost  no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId2 AND userId = 4 AND rankType = "Discipline";
SELECT tap.eq(points ,  1.004, 'SubProcUpdateGameRankPointForGame - (Game 2) Player 2 won  1 alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId2 AND userId = 2 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 2) Player 2 won no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId2 AND userId = 2 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 2) Player 2 won no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId2 AND userId = 2 AND rankType = "Discipline";
SELECT tap.eq(points ,  2.005, 'SubProcUpdateGameRankPointForGame - (Game 2) Player 5 won  2 alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId2 AND userId = 5 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 2) Player 5 won no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId2 AND userId = 5 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 2) Player 5 won no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId2 AND userId = 5 AND rankType = "Discipline";

SELECT tap.eq(points ,  2.000, 'SubProcUpdateGameRankPointForGame - (Game 2) Team  A won   2 overall    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId2 AND teamId = @teamB AND rankType = "Overall";
SELECT tap.eq(points ,  2.000, 'SubProcUpdateGameRankPointForGame - (Game 2) Team  A won   2 discipline points') FROM GameTeamRankPoint WHERE matchId = @tempGameId2 AND teamId = @teamB AND rankType = "Discipline";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 2) Team  B lost  0 alltime    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId2 AND teamId = @teamA AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 2) Team  B lost  0 overall    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId2 AND teamId = @teamA AND rankType = "Discipline";

-- Check the third game
SET @tempGameId3 = 3;
SET @teamA = _TeamIdForGameSide(@tempGameId3, "Side A");
SET @teamB = _TeamIdForGameSide(@tempGameId3, "Side B");
SELECT tap.eq(points ,  1.944, 'SubProcUpdateGameRankPointForGame - (Game 3) Player 2 won  2  alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 2 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 3) Player 2 won  no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 2 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 3) Player 2 won  no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 2 AND rankType = "Discipline";
SELECT tap.eq(points ,  2.944, 'SubProcUpdateGameRankPointForGame - (Game 3) Player 5 won  3  alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 5 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 3) Player 5 won  no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 5 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 3) Player 5 won  no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 5 AND rankType = "Discipline";
SELECT tap.eq(points ,  0.250, 'SubProcUpdateGameRankPointForGame - (Game 3) Player 1 lost 0.2alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 1 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 3) Player 1 lost no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 1 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 3) Player 1 lost no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 1 AND rankType = "Discipline";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 3) Player 4 lost  0 alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 4 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 3) Player 4 lost no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 4 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 3) Player 4 lost no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 4 AND rankType = "Discipline";

SELECT tap.eq(points ,  3.903, 'SubProcUpdateGameRankPointForGame - (Game 3) Team  A won   4 overall    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId3 AND teamId = @teamB AND rankType = "Overall";
SELECT tap.eq(points ,  3.903, 'SubProcUpdateGameRankPointForGame - (Game 3) Team  A won   4 discipline points') FROM GameTeamRankPoint WHERE matchId = @tempGameId3 AND teamId = @teamB AND rankType = "Discipline";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 3) Team  B lost  0 alltime    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId3 AND teamId = @teamA AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'SubProcUpdateGameRankPointForGame - (Game 3) Team  B lost  0 overall    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId3 AND teamId = @teamA AND rankType = "Discipline";

CALL tap.finish();

/**
 * Now delete all games and reenter the games using the insert game emthod
 * 
 */

DELETE FROM GameMatch;

SELECT tap.plan(31);

SELECT tap.eq(COUNT(*), 0, 'InsertGame - All games are removed') FROM GameMatch;

SET @tempGameId1 = 0;
CALL InsertGame(@tempGameId1, "2017-01-02 0:59:37", 1,3,5,6  ,  22,20 , 21,16 , 0,0  ,  "Side A");

SET @teamA = _TeamIdForGameSide(@tempGameId1, "Side A");
SET @teamB = _TeamIdForGameSide(@tempGameId1, "Side B");
SELECT tap.eq(points ,  1.000, 'InsertGame - (Game 1) Player 1 won  1  alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 1 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 1) Player 1 won  no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 1 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 1) Player 1 won  no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 1 AND rankType = "Discipline";
SELECT tap.eq(points ,  1.000, 'InsertGame - (Game 1) Player 5 won  1  alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 5 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 1) Player 5 won  no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 5 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 1) Player 5 won  no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 5 AND rankType = "Discipline";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 1) Player 3 lost  0 alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 3 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 1) Player 3 lost no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 3 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 1) Player 3 lost no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 3 AND rankType = "Discipline";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 1) Player 6 lost  0 alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 6 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 1) Player 6 lost no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 6 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 1) Player 6 lost no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 6 AND rankType = "Discipline";

SELECT tap.eq(points ,  2.000, 'InsertGame - (Game 1) Team  A won   2 overall    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId1 AND teamId = @teamA AND rankType = "Overall";
SELECT tap.eq(points ,  2.000, 'InsertGame - (Game 1) Team  A won   2 discipline points') FROM GameTeamRankPoint WHERE matchId = @tempGameId1 AND teamId = @teamA AND rankType = "Discipline";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 1) Team  B lost  0 overall    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId1 AND teamId = @teamB AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 1) Team  B lost  0 discipline points') FROM GameTeamRankPoint WHERE matchId = @tempGameId1 AND teamId = @teamB AND rankType = "Discipline";

SET @tempGameId2 = 0;
CALL InsertGame(@tempGameId2, "2017-01-03 0:59:38", 1,2,5,4  ,  18,21 , 17,21 , 0,0  ,  "Side B");

SET @teamA = _TeamIdForGameSide(@tempGameId2, "Side A");
SET @teamB = _TeamIdForGameSide(@tempGameId2, "Side B");
SELECT tap.eq(points ,  1.054, 'InsertGame - (Game 2) Player 2 won  1  alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId2 AND userId = 2 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 2) Player 2 won  no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId2 AND userId = 2 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 2) Player 2 won  no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId2 AND userId = 2 AND rankType = "Discipline";
SELECT tap.eq(points ,  1.054, 'InsertGame - (Game 2) Player 4 won  1  alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId2 AND userId = 4 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 2) Player 4 won  no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId2 AND userId = 4 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 2) Player 4 won  no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId2 AND userId = 4 AND rankType = "Discipline";
SELECT tap.eq(points ,  0.446, 'InsertGame - (Game 2) Player 1 lost  1 alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId2 AND userId = 1 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 2) Player 1 lost no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId2 AND userId = 1 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 2) Player 1 lost no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId2 AND userId = 1 AND rankType = "Discipline";
SELECT tap.eq(points ,  0.446, 'InsertGame - (Game 2) Player 5 lost  1 alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId2 AND userId = 5 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 2) Player 5 lost no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId2 AND userId = 5 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 2) Player 5 lost no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId2 AND userId = 5 AND rankType = "Discipline";

SELECT tap.eq(points ,  2.109, 'InsertGame - (Game 2) Team  A won   2 overall    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId2 AND teamId = @teamB AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 2) Team  A won   0 discipline points') FROM GameTeamRankPoint WHERE matchId = @tempGameId2 AND teamId = @teamB AND rankType = "Discipline";
SELECT tap.eq(points ,  0.891, 'InsertGame - (Game 2) Team  B lost  1 overall    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId2 AND teamId = @teamA AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 2) Team  B lost  0 discipline points') FROM GameTeamRankPoint WHERE matchId = @tempGameId2 AND teamId = @teamA AND rankType = "Discipline";

SET @tempGameId3 = 0;
CALL InsertGame(@tempGameId3, "2017-01-04 0:59:39", 1,2,4,5  ,  16,21 , 21,16 , 18,21  ,  "Side B");

SET @teamA = _TeamIdForGameSide(@tempGameId3, "Side A");
SET @teamB = _TeamIdForGameSide(@tempGameId3, "Side B");
SELECT tap.eq(points ,  2.056, 'InsertGame - (Game 3) Player 2 won  2  alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 2 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Player 2 won  no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 2 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Player 2 won  no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 2 AND rankType = "Discipline";
SELECT tap.eq(points ,  1.448, 'InsertGame - (Game 3) Player 5 won  1.5  alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 5 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Player 5 won  no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 5 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Player 5 won  no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 5 AND rankType = "Discipline";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Player 1 lost  0 alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 1 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Player 1 lost no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 1 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Player 1 lost no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 1 AND rankType = "Discipline";
SELECT tap.eq(points ,  0.552, 'InsertGame - (Game 3) Player 4 lost  0.5 alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 4 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Player 4 lost no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 4 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Player 4 lost no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 4 AND rankType = "Discipline";

SELECT tap.eq(points ,  2.000, 'InsertGame - (Game 3) Team  A won   2 overall    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId3 AND teamId = @teamB AND rankType = "Overall";
SELECT tap.eq(points ,  2.000, 'InsertGame - (Game 3) Team  A won   2 discipline points') FROM GameTeamRankPoint WHERE matchId = @tempGameId3 AND teamId = @teamB AND rankType = "Discipline";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Team  B lost  0 overall    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId3 AND teamId = @teamA AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Team  B lost  0 discipline points') FROM GameTeamRankPoint WHERE matchId = @tempGameId3 AND teamId = @teamA AND rankType = "Discipline";

-- Updating game 1 and see how it affects game 3
CALL InsertGame(@tempGameId1, "2017-01-02 0:59:40", 1,3,5,6  ,  20,22 , 16,21 , 0,0  ,  "Side B");
SET @teamA = _TeamIdForGameSide(@tempGameId3, "Side A");
SET @teamB = _TeamIdForGameSide(@tempGameId3, "Side B");
SELECT tap.eq(points ,  2.002, 'InsertGame - (Game 3) Player 2 won  2  alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 2 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Player 2 won  no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 2 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Player 2 won  no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 2 AND rankType = "Discipline";
SELECT tap.eq(points ,  1.002, 'InsertGame - (Game 3) Player 5 won  0.5  alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 5 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Player 5 won  no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 5 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Player 5 won  no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 5 AND rankType = "Discipline";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Player 1 lost  0 alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 1 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Player 1 lost no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 1 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Player 1 lost no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 1 AND rankType = "Discipline";
SELECT tap.eq(points ,  0.500, 'InsertGame - (Game 3) Player 4 lost  1 alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 4 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Player 4 lost no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 4 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Player 4 lost no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 4 AND rankType = "Discipline";

SELECT tap.eq(points ,  2.000, 'InsertGame - (Game 3) Team  A won   2 overall    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId3 AND teamId = @teamB AND rankType = "Overall";
SELECT tap.eq(points ,  2.000, 'InsertGame - (Game 3) Team  A won   2 discipline points') FROM GameTeamRankPoint WHERE matchId = @tempGameId3 AND teamId = @teamB AND rankType = "Discipline";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Team  B lost  0 overall    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId3 AND teamId = @teamA AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Team  B lost  0 discipline points') FROM GameTeamRankPoint WHERE matchId = @tempGameId3 AND teamId = @teamA AND rankType = "Discipline";

-- Changing back to the original standings
CALL InsertGame(@tempGameId1, "2017-01-02 0:59:41", 1,3,5,6  ,  22,20 , 21,16 , 0,0  ,  "Side A");

CALL tap.finish();

/**
 * Testing the method to get the date of the player's first game
 * 
 */

SELECT tap.plan(3);

SELECT tap.eq(_PlayerGetFirstGame(1), "2017-01-02 00:59:41", 'PlayerGetFirstGame - Got correct Date of player 1');
SELECT tap.eq(_PlayerGetFirstGame(5), "2017-01-02 00:59:41", 'PlayerGetFirstGame - Got correct Date of player 5');
SELECT tap.eq(_PlayerGetFirstGame(4), "2017-01-03 00:59:38", 'PlayerGetFirstGame - Got correct Date of player 4');

CALL tap.finish();

/**
 * Test of updating a user
 * 
 */
SELECT tap.plan(14);

-- Changing player 3 to Jenny, therefore the very first game will change and account differently
CALL UpdateUser(3, "jk@bc-comet.de", "Jenny", "K.", "Female", "$2y$10$caqHxWaKHlV5u0TAdwvvWOVBotrPCJFQ978J.pehzPqatlk4b6vEm", TRUE, TRUE, FALSE);

SET @teamA = _TeamIdForGameSide(@tempGameId1, "Side A");
SET @teamB = _TeamIdForGameSide(@tempGameId1, "Side B");
SELECT tap.eq(points ,  1.000, 'InsertGame - (Game 1) Player 1 won  1  alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 1 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 1) Player 1 won  no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 1 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 1) Player 1 won  no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 1 AND rankType = "Discipline";
SELECT tap.eq(points ,  1.000, 'InsertGame - (Game 1) Player 5 won  1  alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 5 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 1) Player 5 won  no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 5 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 1) Player 5 won  no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 5 AND rankType = "Discipline";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 1) Player 3 lost  0 alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 3 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 1) Player 3 lost no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 3 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 1) Player 3 lost no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 3 AND rankType = "Discipline";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 1) Player 6 lost  0 alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 6 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 1) Player 6 lost no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 6 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 1) Player 6 lost no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId1 AND userId = 6 AND rankType = "Discipline";

SELECT tap.eq(points ,  2.000, 'InsertGame - (Game 1) Team  A won   2 overall    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId1 AND teamId = @teamA AND rankType = "Overall";
SELECT tap.eq(points ,  2.000, 'InsertGame - (Game 1) Team  A won   2 discipline points') FROM GameTeamRankPoint WHERE matchId = @tempGameId1 AND teamId = @teamA AND rankType = "Discipline";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 1) Team  B lost  0 overall    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId1 AND teamId = @teamB AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 1) Team  B lost  0 discipline points') FROM GameTeamRankPoint WHERE matchId = @tempGameId1 AND teamId = @teamB AND rankType = "Discipline";

SET @teamA = _TeamIdForGameSide(@tempGameId3, "Side A");
SET @teamB = _TeamIdForGameSide(@tempGameId3, "Side B");
SELECT tap.eq(points ,  2.056, 'InsertGame - (Game 3) Player 2 won  2  alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 2 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Player 2 won  no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 2 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Player 2 won  no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 2 AND rankType = "Discipline";
SELECT tap.eq(points ,  1.448, 'InsertGame - (Game 3) Player 5 won  0.5  alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 5 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Player 5 won  no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 5 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Player 5 won  no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 5 AND rankType = "Discipline";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Player 1 lost  0 alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 1 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Player 1 lost no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 1 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Player 1 lost no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 1 AND rankType = "Discipline";
SELECT tap.eq(points ,  0.552, 'InsertGame - (Game 3) Player 4 lost  1 alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 4 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Player 4 lost no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 4 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Player 4 lost no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 4 AND rankType = "Discipline";

SELECT tap.eq(points ,  2.000, 'InsertGame - (Game 3) Team  A won   2 overall    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId3 AND teamId = @teamB AND rankType = "Overall";
SELECT tap.eq(points ,  2.000, 'InsertGame - (Game 3) Team  A won   2 discipline points') FROM GameTeamRankPoint WHERE matchId = @tempGameId3 AND teamId = @teamB AND rankType = "Discipline";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Team  B lost  0 overall    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId3 AND teamId = @teamA AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Team  B lost  0 discipline points') FROM GameTeamRankPoint WHERE matchId = @tempGameId3 AND teamId = @teamA AND rankType = "Discipline";


CALL tap.finish();
/**
 * Test of deleting a user
 * 
 */
SELECT tap.plan(16);

SELECT tap.eq(COUNT(*), 3, 'DeleteUser - All games are present') FROM GameMatch;

SELECT tap.eq(COUNT(*), 7, 'DeleteUser - Player 3 in 7 teams') FROM Team WHERE user1Id = 3 OR user2Id = 3;

CALL DeleteUser(3); --  This removes game number 1

SELECT tap.eq(COUNT(*), 0, 'DeleteUser - player 3 removed from all teams') FROM Team WHERE user1Id = 3 OR user2Id = 3;
SELECT tap.eq(COUNT(*), 2, 'DeleteUser - one game got removed') FROM GameMatch;

SET @teamA = _TeamIdForGameSide(@tempGameId3, "Side A");
SET @teamB = _TeamIdForGameSide(@tempGameId3, "Side B");
SELECT tap.eq(points ,  2.002, 'InsertGame - (Game 3) Player 2 won  2  alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 2 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Player 2 won  no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 2 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Player 2 won  no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 2 AND rankType = "Discipline";
SELECT tap.eq(points ,  1.002, 'InsertGame - (Game 3) Player 5 won  0.5  alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 5 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Player 5 won  no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 5 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Player 5 won  no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 5 AND rankType = "Discipline";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Player 1 lost  0 alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 1 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Player 1 lost no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 1 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Player 1 lost no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 1 AND rankType = "Discipline";
SELECT tap.eq(points ,  0.500, 'InsertGame - (Game 3) Player 4 lost  1 alltime    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 4 AND rankType = "Alltime";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Player 4 lost no overall    points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 4 AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Player 4 lost no discipline points') FROM GamePlayerRankPoint WHERE matchId = @tempGameId3 AND userId = 4 AND rankType = "Discipline";

SELECT tap.eq(points ,  2.000, 'InsertGame - (Game 3) Team  A won   2 overall    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId3 AND teamId = @teamB AND rankType = "Overall";
SELECT tap.eq(points ,  2.000, 'InsertGame - (Game 3) Team  A won   2 discipline points') FROM GameTeamRankPoint WHERE matchId = @tempGameId3 AND teamId = @teamB AND rankType = "Discipline";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Team  B lost  0 overall    points') FROM GameTeamRankPoint WHERE matchId = @tempGameId3 AND teamId = @teamA AND rankType = "Overall";
SELECT tap.eq(points ,  0.000, 'InsertGame - (Game 3) Team  B lost  0 discipline points') FROM GameTeamRankPoint WHERE matchId = @tempGameId3 AND teamId = @teamA AND rankType = "Discipline";

CALL DeleteUser(5); --  This also removes all games
SELECT tap.eq(COUNT(*), 0, 'DeleteUser - player 5 removed from all teams') FROM Team WHERE user1Id = 5 OR user2Id = 5;

SELECT tap.eq(COUNT(*), 0, 'DeleteUser - All games removed') FROM GameMatch;
SELECT tap.eq(COUNT(*), 0, 'DeleteUser - All player rank points removed') FROM GamePlayerRankPoint;
SELECT tap.eq(COUNT(*), 0, 'DeleteUser - All team rank points removed') FROM GameTeamRankPoint;

CALL tap.finish();

/**
 * test the cool down functionality
 */

SELECT tap.plan(18);

CALL InsertUser("js@bc-comet.de",  "Joe", "S.",   "Male", "$2y$10$caqHxWaKHlV5u0TAdwvvWOVBotrPCJFQ978J.pehzPqatlk4b6vEm", TRUE, TRUE, FALSE);
CALL InsertUser("jd@bc-comet.de", "Jens", "D.", "Male", "$2y$10$caqHxWaKHlV5u0TAdwvvWOVBotrPCJFQ978J.pehzPqatlk4b6vEm", TRUE, TRUE, FALSE);

-- Insert a bunch of games
SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:01:00", 1,9,6,10  ,  16,21 , 21,16 , 18,21  ,  "Side B");
SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:02:00", 1,9,6,10  ,  18,21 , 16,21 ,  0,0   ,  "Side B");
SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:03:00", 9,1,6,10  ,  21,14 , 21,18 ,  0,0   ,  "Side A");
SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:04:00", 9,6,0,0   ,  21,14 , 21,18 ,  0,0   ,  "Side A");
SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:05:00", 1,6,0,0   ,  16,21 , 14,21 ,  0,0   ,  "Side B");
SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:06:00", 8,2,0,0   ,  16,21 , 14,21 ,  0,0   ,  "Side B");
SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:07:00", 7,2,0,0   ,  16,21 , 14,21 ,  0,0   ,  "Side B");
SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:08:00", 7,1,2,6   ,  16,21 , 14,21 ,  0,0   ,  "Side B");
SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:09:00", 7,4,2,8   ,  21,14 , 16,21 , 21,18  ,  "Side A");

SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:10:00", 6,9,1,10  ,  16,21 , 21,16 , 18,21  ,  "Side B");
SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:11:00", 6,1,9,10  ,  18,21 , 16,21 ,  0,0   ,  "Side B");
SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:12:00", 10,6,9,1  ,  21,14 , 21,18 ,  0,0   ,  "Side A");
SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:13:00", 6,9,0,0   ,  21,14 , 21,18 ,  0,0   ,  "Side A");
SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:14:00", 6,1,0,0   ,  16,21 , 14,21 ,  0,0   ,  "Side B");
SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:15:00", 2,8,0,0   ,  16,21 , 14,21 ,  0,0   ,  "Side B");
SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:16:00", 2,7,0,0   ,  16,21 , 14,21 ,  0,0   ,  "Side B");
SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:17:00", 7,2,1,6   ,  16,21 , 14,21 ,  0,0   ,  "Side B");
SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:18:00", 8,2,4,7   ,  21,14 , 16,21 , 21,18  ,  "Side A");

SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:19:00", 6,9,1,10  ,  16,21 , 21,16 , 18,21  ,  "Side B");

SELECT tap.eq(COUNT(*), 0, 'CoolDownRanks - No Player Overrides yet') FROM GamePlayerRankPointOverride;
SELECT tap.eq(COUNT(*), 0, 'CoolDownRanks - No Team Overrides yet') FROM GameTeamRankPointOverride;

SELECT tap.eq(_RankPlayerPointUntil(9, "2017-01-02 01:20:00", "Alltime")    ,  5.772, 'SubProcCoolDownRanks - Player 9 Rank Alltime    ok');
SELECT tap.eq(_RankPlayerPointUntil(9, "2017-01-02 01:20:00", "Overall")    ,  0.973 , 'SubProcCoolDownRanks - Player 9 Rank Overall    ok');
SELECT tap.eq(_RankPlayerPointUntil(9, "2017-01-02 01:20:00", "Discipline") ,  0.973 , 'SubProcCoolDownRanks - Player 9 Rank Discipline ok');

SELECT tap.eq(_RankPlayerPointUntil(6, "2017-01-02 01:20:00", "Alltime")    ,  3.367, 'SubProcCoolDownRanks - Player 6 Rank Alltime    ok');
SELECT tap.eq(_RankPlayerPointUntil(6, "2017-01-02 01:20:00", "Overall")    ,  2.792, 'SubProcCoolDownRanks - Player 6 Rank Overall    ok');
SELECT tap.eq(_RankPlayerPointUntil(6, "2017-01-02 01:20:00", "Discipline") ,  2.792, 'SubProcCoolDownRanks - Player 6 Rank Discipline ok');

SET @teamId = _TeamIdForUsers(9, 10);
SELECT tap.eq(_RankTeamPointUntil(@teamId, "2017-01-02 01:20:00", "Overall")    , 9.276, 'SubProcCoolDownRanks - Team Rank Overall    ok');
SELECT tap.eq(_RankTeamPointUntil(@teamId, "2017-01-02 01:20:00", "Discipline") , 9.11 , 'SubProcCoolDownRanks - Team Rank Discipline ok');

CALL _SubProcCoolDownRanks("2017-01-02 02:00:00");

SELECT tap.eq(_RankPlayerPointUntil(9, "2017-01-02 03:21:00", "Alltime")    ,  5.483, 'SubProcCoolDownRanks - Player 9 Rank Alltime    ok');
SELECT tap.eq(_RankPlayerPointUntil(9, "2017-01-02 03:21:00", "Overall")    ,  0.924 , 'SubProcCoolDownRanks - Player 9 Rank Overall    ok');
SELECT tap.eq(_RankPlayerPointUntil(9, "2017-01-02 03:21:00", "Discipline") ,  0.924 , 'SubProcCoolDownRanks - Player 9 Rank Discipline ok');

SELECT tap.eq(_RankPlayerPointUntil(6, "2017-01-02 03:21:00", "Alltime")    ,  3.199, 'SubProcCoolDownRanks - Player 6 Rank Alltime    ok');
SELECT tap.eq(_RankPlayerPointUntil(6, "2017-01-02 03:21:00", "Overall")    ,  2.652, 'SubProcCoolDownRanks - Player 6 Rank Overall    ok');
SELECT tap.eq(_RankPlayerPointUntil(6, "2017-01-02 03:21:00", "Discipline") ,  2.652, 'SubProcCoolDownRanks - Player 6 Rank Discipline ok');

SET @teamId = _TeamIdForUsers(9, 10);
SELECT tap.eq(_RankTeamPointUntil(@teamId, "2017-01-02 03:21:00", "Overall")    , 8.812, 'SubProcCoolDownRanks - Team Rank Overall    ok');
SELECT tap.eq(_RankTeamPointUntil(@teamId, "2017-01-02 03:21:00", "Discipline") , 8.654, 'SubProcCoolDownRanks - Team Rank Discipline ok');
CALL tap.finish();

SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:20:00", 6,1,0,10  ,  16,21 , 21,16 , 18,21  ,  "Side B");

/**
 * Test the recalculation of cooldowns
 */

SELECT tap.plan(35);

DELETE FROM GameMatch;
DELETE FROM GamePlayerRankPoint;
DELETE FROM GamePlayerRankPointOverride;
DELETE FROM GameTeamRankPoint;
DELETE FROM GameTeamRankPointOverride;

SELECT tap.eq(COUNT(*), 0, 'SubProcRecalcCoolDownRanks - All games deleted') FROM GameMatch;
SELECT tap.eq(COUNT(*), 0, 'SubProcRecalcCoolDownRanks - All player rank points removed') FROM GamePlayerRankPoint;
SELECT tap.eq(COUNT(*), 0, 'SubProcRecalcCoolDownRanks - All player rank point overrides removed') FROM GamePlayerRankPointOverride;
SELECT tap.eq(COUNT(*), 0, 'SubProcRecalcCoolDownRanks - All team rank points removed') FROM GameTeamRankPoint;
SELECT tap.eq(COUNT(*), 0, 'SubProcRecalcCoolDownRanks - All team rank override points removed') FROM GameTeamRankPointOverride;

-- Insert two games
SET @timeGame1              = "2017-05-12 12:00:00";
SET @timeAfterGame1         = "2017-05-12 12:30:00";
SET @timeGame2              = "2017-05-13 14:30:00";
SET @timeAfterGame2         = "2017-05-13 14:45:00";
SET @timeGame3              = "2017-05-13 15:35:00";
SET @timeAfterGame3         = "2017-05-13 15:40:00";
SET @timeGame4              = "2017-05-13 15:45:00";
SET @timeAfterGame4         = "2017-05-13 15:45:50";
SET @timeCoolDown1          = "2017-05-13 18:30:00";
SET @timeAfterCoolDown1     = "2017-05-13 18:30:51";
SET @timeGame4              = "2017-05-13 18:45:00";
SET @timeAfterGame4         = "2017-05-13 18:45:50";
SET @timeCoolDown2          = "2017-05-15 18:30:00";
SET @timeAfterCoolDown2     = "2017-05-15 18:30:51";

-- ****************************************************
-- TEST 1
-- Check that the recalculation does not change the
-- result in the cool down tables
-- ****************************************************

-- Inserting two games a double men and a single alltime game
SET @tempGameId1 = 0; CALL InsertGame(@tempGameId1, @timeGame1, 1,9,6,10  ,  21,16 , 18,21 , 21,20  ,  "Side A");
SET @tempGameId2 = 0; CALL InsertGame(@tempGameId2, @timeGame2, 1,2,0,0   ,  21,14 , 21,18 ,  0,0   ,  "Side A");
SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, @timeGame3, 6,1,0,0   ,  16,21 , 17,21 ,  0,0   ,  "Side B");
SET @teamId = _TeamIdForUsers(1, 6);

-- Now check the rankins after game 2
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame3, "Alltime"   ), 3), 4.883, 'SubProcRecalcCoolDownRanks - Test 1 - After Game 2 - No Cool Down - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame3, "Overall"   ), 3), 3.912, 'SubProcRecalcCoolDownRanks - Test 1 - After Game 2 - No Cool Down - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame3, "Discipline"), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 1 - After Game 2 - No Cool Down - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame3, "Overall"   ), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 1 - After Game 2 - No Cool Down -Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame3, "Discipline"), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 1 - After Game 2 - No Cool Down -Team Rank Discipline ok');

-- Now check the rankings after Cool down 1 but the cool down was not yet processed
-- accordingly the result has to be the same as after game 2
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown1, "Alltime"   ), 3), 4.883, 'SubProcRecalcCoolDownRanks - Test 1 - After CD1 - No Cool Down - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown1, "Overall"   ), 3), 3.912, 'SubProcRecalcCoolDownRanks - Test 1 - After CD1 - No Cool Down - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown1, "Discipline"), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 1 - After CD1 - No Cool Down - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown1, "Overall"   ), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 1 - After CD1 - No Cool Down - Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown1, "Discipline"), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 1 - After CD1 - No Cool Down - Team Rank Discipline ok');

-- Calculate a cool down after game 2 and check the results again
CALL _SubProcCoolDownRanks(@timeCoolDown1);

-- Now check the rankins after game 2
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame3, "Alltime"   ), 3), 4.883, 'SubProcRecalcCoolDownRanks - Test 1 - After Game 2 - Calc Cool Down - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame3, "Overall"   ), 3), 3.912, 'SubProcRecalcCoolDownRanks - Test 1 - After Game 2 - Calc Cool Down - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame3, "Discipline"), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 1 - After Game 2 - Calc Cool Down - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame3, "Overall"   ), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 1 - After Game 2 - Calc Cool Down -Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame3, "Discipline"), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 1 - After Game 2 - Calc Cool Down -Team Rank Discipline ok');

-- the following check asks for the ranks after the cool down has happened.
-- accordingly the results have to be less and around 2% dropped. 2% is depending on the
-- rank point settings. Cool down of 2 % is default settings
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown1, "Alltime"   ), 3), 4.639, 'SubProcRecalcCoolDownRanks - Test 1 - After CD1 - Calc Cool Down - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown1, "Overall"   ), 3), 3.716, 'SubProcRecalcCoolDownRanks - Test 1 - After CD1 - Calc Cool Down - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown1, "Discipline"), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 1 - After CD1 - Calc Cool Down - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown1, "Overall"   ), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 1 - After CD1 - Calc Cool Down - Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown1, "Discipline"), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 1 - After CD1 - Calc Cool Down - Team Rank Discipline ok');

-- Recalculate the cool down and make sure that the results have not changed
CALL _SubProcRecalcCoolDownRanks(@timeCoolDown1);

-- Now check the rankins after game 2 and cool down. things haven't changed thus results ahve to stay the same
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame3, "Alltime"   ), 3), 4.883, 'SubProcRecalcCoolDownRanks - Test 1 - After Game 2 - Re-Calc Cool Down - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame3, "Overall"   ), 3), 3.912, 'SubProcRecalcCoolDownRanks - Test 1 - After Game 2 - Re-Calc Cool Down - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame3, "Discipline"), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 1 - After Game 2 - Re-Calc Cool Down - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame3, "Overall"   ), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 1 - After Game 2 - Re-Calc Cool Down -Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame3, "Discipline"), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 1 - After Game 2 - Re-Calc Cool Down -Team Rank Discipline ok');

SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown1, "Alltime"   ), 3), 4.639, 'SubProcRecalcCoolDownRanks - Test 1 - After CD1 - Re-Calc Cool Down - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown1, "Overall"   ), 3), 3.716, 'SubProcRecalcCoolDownRanks - Test 1 - After CD1 - Re-Calc Cool Down - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown1, "Discipline"), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 1 - After CD1 - Re-Calc Cool Down - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown1, "Overall"   ), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 1 - After CD1 - Re-Calc Cool Down - Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown1, "Discipline"), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 1 - After CD1 - Re-Calc Cool Down - Team Rank Discipline ok');

CALL tap.finish();

-- ****************************************************
-- TEST 2
-- Now manually manipulating the results of the games
-- the player games get a result of 4 and the team
-- games of 3. When calculating the cool down
-- the reslts of the cool down have to be based on the
-- 3s and the 4s. A second cool down is also calculated
-- when recalculating the first cool down, the second
-- has to be updated as well.
-- ****************************************************
SELECT tap.plan(40);

-- Manipulate the rank points for player 1
SET @teamId = _TeamIdForUsers(1, 6);
UPDATE GamePlayerRankPoint
SET points = 4
WHERE userId = 1 AND matchId = @tempGameId2;

SET @teamId = _TeamIdForUsers(1, 6);
UPDATE GamePlayerRankPoint
SET points = 4
WHERE userId = 1 AND matchId = @tempGameId3;

UPDATE GameTeamRankPoint
SET points = 3
WHERE teamId = @teamId AND matchId = @tempGameId1;

-- The results before the cool down are based on the player and team ranks
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame3, "Alltime"   ), 3), 4    , 'SubProcRecalcCoolDownRanks - Test 2 - After Game 2 - Manipulated - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame3, "Overall"   ), 3), 4    , 'SubProcRecalcCoolDownRanks - Test 2 - After Game 2 - Manipulated - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame3, "Discipline"), 3), 4    , 'SubProcRecalcCoolDownRanks - Test 2 - After Game 2 - Manipulated - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame3, "Overall"   ), 3), 3    , 'SubProcRecalcCoolDownRanks - Test 2 - After Game 2 - Manipulated -Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame3, "Discipline"), 3), 3    , 'SubProcRecalcCoolDownRanks - Test 2 - After Game 2 - Manipulated -Team Rank Discipline ok');

-- the results after the cool down are based on the overrides which
-- have not been manipulated accordingly there should be no change.
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown1, "Alltime"   ), 3), 4.639, 'SubProcRecalcCoolDownRanks - Test 2 - After CD1 - Manipulated - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown1, "Overall"   ), 3), 3.716, 'SubProcRecalcCoolDownRanks - Test 2 - After CD1 - Manipulated - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown1, "Discipline"), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 2 - After CD1 - Manipulated - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown1, "Overall"   ), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 2 - After CD1 - Manipulated - Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown1, "Discipline"), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 2 - After CD1 - Manipulated - Team Rank Discipline ok');

-- cool down 2 has not yet been calculated and results have to be the same as CD1
-- CD 2 is planned after CD 2 and no games have been in between yet.
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown2, "Alltime"   ), 3), 4.639, 'SubProcRecalcCoolDownRanks - Test 2 - After CD2 - Manipulated - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown2, "Overall"   ), 3), 3.716, 'SubProcRecalcCoolDownRanks - Test 2 - After CD2 - Manipulated - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown2, "Discipline"), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 2 - After CD2 - Manipulated - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown2, "Overall"   ), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 2 - After CD2 - Manipulated - Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown2, "Discipline"), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 2 - After CD2 - Manipulated - Team Rank Discipline ok');

-- Calculate 2nd a cool down the cool down will be calculated on the results of game 
CALL _SubProcCoolDownRanks(@timeCoolDown2);

-- The results before the cool down are based on the player and team ranks
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame3, "Alltime"   ), 3), 4    , 'SubProcRecalcCoolDownRanks - Test 2 - After Game 2 - Insert CD2 - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame3, "Overall"   ), 3), 4    , 'SubProcRecalcCoolDownRanks - Test 2 - After Game 2 - Insert CD2 - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame3, "Discipline"), 3), 4    , 'SubProcRecalcCoolDownRanks - Test 2 - After Game 2 - Insert CD2 - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame3, "Overall"   ), 3), 3    , 'SubProcRecalcCoolDownRanks - Test 2 - After Game 2 - Insert CD2 -Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame3, "Discipline"), 3), 3    , 'SubProcRecalcCoolDownRanks - Test 2 - After Game 2 - Insert CD2 -Team Rank Discipline ok');

-- the results of CD 1 have not yet been re calculated
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown1, "Alltime"   ), 3), 4.639, 'SubProcRecalcCoolDownRanks - Test 2 - After CD1 - Insert CD2 - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown1, "Overall"   ), 3), 3.716, 'SubProcRecalcCoolDownRanks - Test 2 - After CD1 - Insert CD2 - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown1, "Discipline"), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 2 - After CD1 - Insert CD2 - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown1, "Overall"   ), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 2 - After CD1 - Insert CD2 - Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown1, "Discipline"), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 2 - After CD1 - Insert CD2 - Team Rank Discipline ok');

-- Cool down 2 is nowactually reclculated
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown2, "Alltime"   ), 3), 4.407, 'SubProcRecalcCoolDownRanks - Test 2 - After CD2 - Insert CD2 - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown2, "Overall"   ), 3), 3.53 , 'SubProcRecalcCoolDownRanks - Test 2 - After CD2 - Insert CD2 - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown2, "Discipline"), 3), 1.805, 'SubProcRecalcCoolDownRanks - Test 2 - After CD2 - Insert CD2 - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown2, "Overall"   ), 3), 1.805, 'SubProcRecalcCoolDownRanks - Test 2 - After CD2 - Insert CD2 - Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown2, "Discipline"), 3), 1.805, 'SubProcRecalcCoolDownRanks - Test 2 - After CD2 - Insert CD2 - Team Rank Discipline ok');

-- Now reclculate the first cool down whoich should also reclculate the second cool down
CALL _SubProcRecalcCoolDownRanks(@timeCoolDown1);

-- the results of CD are now based on the manipualted values
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown1, "Alltime"   ), 3), 3.8  , 'SubProcRecalcCoolDownRanks - Test 2 - After CD1 - Recalc CD1 - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown1, "Overall"   ), 3), 3.8  , 'SubProcRecalcCoolDownRanks - Test 2 - After CD1 - Recalc CD1 - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown1, "Discipline"), 3), 3.8  , 'SubProcRecalcCoolDownRanks - Test 2 - After CD1 - Recalc CD1 - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown1, "Overall"   ), 3), 2.85 , 'SubProcRecalcCoolDownRanks - Test 2 - After CD1 - Recalc CD1 - Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown1, "Discipline"), 3), 2.85 , 'SubProcRecalcCoolDownRanks - Test 2 - After CD1 - Recalc CD1 - Team Rank Discipline ok');

-- Cool down 2 is based on results of CD 1 since no game is in between
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown2, "Alltime"   ), 3), 3.61 , 'SubProcRecalcCoolDownRanks - Test 2 - After CD2 - Recalc CD1 - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown2, "Overall"   ), 3), 3.61 , 'SubProcRecalcCoolDownRanks - Test 2 - After CD2 - Recalc CD1 - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown2, "Discipline"), 3), 3.61 , 'SubProcRecalcCoolDownRanks - Test 2 - After CD2 - Recalc CD1 - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown2, "Overall"   ), 3), 2.708, 'SubProcRecalcCoolDownRanks - Test 2 - After CD2 - Recalc CD1 - Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown2, "Discipline"), 3), 2.708, 'SubProcRecalcCoolDownRanks - Test 2 - After CD2 - Recalc CD1 - Team Rank Discipline ok');

CALL tap.finish();

-- ****************************************************
-- TEST 3
-- Adding a further game after cool down 1. thus cool
-- down 2 will be based on game 4 rather than on CD 1
-- as before. After the game has been added the CD2
-- should have been automatically recalculated.
-- Triggering the recalculate of cool downs from CD1
-- should not change CD2 since it is based on Game 4
-- ****************************************************
SELECT tap.plan(35);

-- The results before the cool down are based on the still manipualted values
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame3, "Alltime"   ), 3), 4    , 'SubProcRecalcCoolDownRanks - Test 3 - After Game 3 - Before Insert - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame3, "Overall"   ), 3), 4    , 'SubProcRecalcCoolDownRanks - Test 3 - After Game 3 - Before Insert - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame3, "Discipline"), 3), 4    , 'SubProcRecalcCoolDownRanks - Test 3 - After Game 3 - Before Insert - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame3, "Overall"   ), 3), 3    , 'SubProcRecalcCoolDownRanks - Test 3 - After Game 3 - Before Insert -Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame3, "Discipline"), 3), 3    , 'SubProcRecalcCoolDownRanks - Test 3 - After Game 3 - Before Insert -Team Rank Discipline ok');

-- CoolDown 1 results are based on cool down 2
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown1, "Alltime"   ), 3), 3.8  , 'SubProcRecalcCoolDownRanks - Test 3 - After CD1 - Before Insert - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown1, "Overall"   ), 3), 3.8  , 'SubProcRecalcCoolDownRanks - Test 3 - After CD1 - Before Insert - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown1, "Discipline"), 3), 3.8  , 'SubProcRecalcCoolDownRanks - Test 3 - After CD1 - Before Insert - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown1, "Overall"   ), 3), 2.85 , 'SubProcRecalcCoolDownRanks - Test 3 - After CD1 - Before Insert - Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown1, "Discipline"), 3), 2.85 , 'SubProcRecalcCoolDownRanks - Test 3 - After CD1 - Before Insert - Team Rank Discipline ok');

-- Game 4 does not yet exist and results have to be the same as for CD1 which happened before
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame4, "Alltime"   ), 3), 3.8  , 'SubProcRecalcCoolDownRanks - Test 3 - After Game 4 - Before Insert - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame4, "Overall"   ), 3), 3.8  , 'SubProcRecalcCoolDownRanks - Test 3 - After Game 4 - Before Insert - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame4, "Discipline"), 3), 3.8  , 'SubProcRecalcCoolDownRanks - Test 3 - After Game 4 - Before Insert - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame4, "Overall"   ), 3), 2.85 , 'SubProcRecalcCoolDownRanks - Test 3 - After Game 4 - Before Insert - Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame4, "Discipline"), 3), 2.85 , 'SubProcRecalcCoolDownRanks - Test 3 - After Game 4 - Before Insert - Team Rank Discipline ok');

-- Game 4 does not yet exists. Therefore CD2 results are still based on CD1 results
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown2, "Alltime"   ), 3), 3.61 , 'SubProcRecalcCoolDownRanks - Test 3 - After CD2 - Before Insert - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown2, "Overall"   ), 3), 3.61 , 'SubProcRecalcCoolDownRanks - Test 3 - After CD2 - Before Insert - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown2, "Discipline"), 3), 3.61 , 'SubProcRecalcCoolDownRanks - Test 3 - After CD2 - Before Insert - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown2, "Overall"   ), 3), 2.708, 'SubProcRecalcCoolDownRanks - Test 3 - After CD2 - Before Insert - Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown2, "Discipline"), 3), 2.708, 'SubProcRecalcCoolDownRanks - Test 3 - After CD2 - Before Insert - Team Rank Discipline ok');

-- now add game number 4, with player 1 loosing, thus rnak points for player 1 should be dropped compared to
-- the cool down 1. cool down 1 will be used as inputs for the ranks. cool down 2 should be automatically
-- recalculated and results should be around 2% less as of the results after game 4
SET @tempGameId4 = 0; CALL InsertGame(@tempGameId4, @timeGame4, 1,2,0,0   ,  16,21 , 12,21 ,  0,0   ,  "Side B");

-- CD1 results should not have been touched
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown1, "Alltime"   ), 3), 3.8  , 'SubProcRecalcCoolDownRanks - Test 3 - After CD1 - After Insert - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown1, "Overall"   ), 3), 3.8  , 'SubProcRecalcCoolDownRanks - Test 3 - After CD1 - After Insert - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown1, "Discipline"), 3), 3.8  , 'SubProcRecalcCoolDownRanks - Test 3 - After CD1 - After Insert - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown1, "Overall"   ), 3), 2.85 , 'SubProcRecalcCoolDownRanks - Test 3 - After CD1 - After Insert - Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown1, "Discipline"), 3), 2.85 , 'SubProcRecalcCoolDownRanks - Test 3 - After CD1 - After Insert - Team Rank Discipline ok');

-- Game 4 now exists with results based on CD1. Player 1 lost, thus Game results are expected to be lower than CD1 results
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame4, "Alltime"   ), 3), 2.572, 'SubProcRecalcCoolDownRanks - Test 3 - After Game 4 - After Insert - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame4, "Overall"   ), 3), 2.572, 'SubProcRecalcCoolDownRanks - Test 3 - After Game 4 - After Insert - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame4, "Discipline"), 3), 3.8  , 'SubProcRecalcCoolDownRanks - Test 3 - After Game 4 - After Insert - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame4, "Overall"   ), 3), 2.85 , 'SubProcRecalcCoolDownRanks - Test 3 - After Game 4 - After Insert - Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame4, "Discipline"), 3), 2.85 , 'SubProcRecalcCoolDownRanks - Test 3 - After Game 4 - After Insert - Team Rank Discipline ok');

-- CD2 results should have been calculated based on Game 4
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown2, "Alltime"   ), 3), 2.443, 'SubProcRecalcCoolDownRanks - Test 3 - After CD2 - After Insert - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown2, "Overall"   ), 3), 2.443, 'SubProcRecalcCoolDownRanks - Test 3 - After CD2 - After Insert - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown2, "Discipline"), 3), 3.61 , 'SubProcRecalcCoolDownRanks - Test 3 - After CD2 - After Insert - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown2, "Overall"   ), 3), 2.708, 'SubProcRecalcCoolDownRanks - Test 3 - After CD2 - After Insert - Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown2, "Discipline"), 3), 2.708, 'SubProcRecalcCoolDownRanks - Test 3 - After CD2 - After Insert - Team Rank Discipline ok');

CALL tap.finish();

-- ****************************************************
-- TEST 4
-- recalculate all games starting from game 1
-- this should updated the manipualted ranks back to
-- real ranks and should also update all Cool Downs
-- ****************************************************
SELECT tap.plan(31);

DELETE FROM DebugUpdateRankPoint;
CALL _SubProcUpdateGameRankPointStartingFrom(@timeGame1);

-- Check what the debug tables sais about hte cursor
SELECT tap.eq(COUNT(*), 6    , 'SubProcRecalcCoolDownRanks - Processed correct amount of games') FROM DebugUpdateRankPoint;

-- Results should be completly recalculated
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame1, "Alltime"   ), 3), 1    , 'SubProcRecalcCoolDownRanks - Test 4 - After Game 1 - Re-Calc all Games - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame1, "Overall"   ), 3), 0    , 'SubProcRecalcCoolDownRanks - Test 4 - After Game 1 - Re-Calc all Games - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame1, "Discipline"), 3), 0    , 'SubProcRecalcCoolDownRanks - Test 4 - After Game 1 - Re-Calc all Games - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame1, "Overall"   ), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 4 - After Game 1 - Re-Calc all Games -Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame1, "Discipline"), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 4 - After Game 1 - Re-Calc all Games -Team Rank Discipline ok');

-- Results should be completly recalculated
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame2, "Alltime"   ), 3), 2.957, 'SubProcRecalcCoolDownRanks - Test 4 - After Game 2 - Re-Calc all Games - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame2, "Overall"   ), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 4 - After Game 2 - Re-Calc all Games - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame2, "Discipline"), 3), 0    , 'SubProcRecalcCoolDownRanks - Test 4 - After Game 2 - Re-Calc all Games - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame2, "Overall"   ), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 4 - After Game 2 - Re-Calc all Games -Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame2, "Discipline"), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 4 - After Game 2 - Re-Calc all Games -Team Rank Discipline ok');

-- Results should be completly recalculated
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame3, "Alltime"   ), 3), 4.883, 'SubProcRecalcCoolDownRanks - Test 4 - After Game 3 - Re-Calc all Games - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame3, "Overall"   ), 3), 3.912, 'SubProcRecalcCoolDownRanks - Test 4 - After Game 3 - Re-Calc all Games - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame3, "Discipline"), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 4 - After Game 3 - Re-Calc all Games - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame3, "Overall"   ), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 4 - After Game 3 - Re-Calc all Games -Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame3, "Discipline"), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 4 - After Game 3 - Re-Calc all Games -Team Rank Discipline ok');

-- Results should be completly recalculated
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown1, "Alltime"   ), 3), 4.639, 'SubProcRecalcCoolDownRanks - Test 4 - After CD1 - Re-Calc all Games - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown1, "Overall"   ), 3), 3.716, 'SubProcRecalcCoolDownRanks - Test 4 - After CD1 - Re-Calc all Games - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown1, "Discipline"), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 4 - After CD1 - Re-Calc all Games - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown1, "Overall"   ), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 4 - After CD1 - Re-Calc all Games - Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown1, "Discipline"), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 4 - After CD1 - Re-Calc all Games - Team Rank Discipline ok');

-- Results should be completly recalculated
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame4, "Alltime"   ), 3), 3.361, 'SubProcRecalcCoolDownRanks - Test 4 - After Game 4 - Re-Calc all Games - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame4, "Overall"   ), 3), 2.493, 'SubProcRecalcCoolDownRanks - Test 4 - After Game 4 - Re-Calc all Games - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame4, "Discipline"), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 4 - After Game 4 - Re-Calc all Games - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame4, "Overall"   ), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 4 - After Game 4 - Re-Calc all Games - Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame4, "Discipline"), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 4 - After Game 4 - Re-Calc all Games - Team Rank Discipline ok');

-- Results should be completly recalculated
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown2, "Alltime"   ), 3), 3.193 , 'SubProcRecalcCoolDownRanks - Test 4 - After CD2 - Re-Calc all Games - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown2, "Overall"   ), 3), 2.368 , 'SubProcRecalcCoolDownRanks - Test 4 - After CD2 - Re-Calc all Games - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown2, "Discipline"), 3), 1.805, 'SubProcRecalcCoolDownRanks - Test 4 - After CD2 - Re-Calc all Games - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown2, "Overall"   ), 3), 1.805, 'SubProcRecalcCoolDownRanks - Test 4 - After CD2 - Re-Calc all Games - Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown2, "Discipline"), 3), 1.805, 'SubProcRecalcCoolDownRanks - Test 4 - After CD2 - Re-Calc all Games - Team Rank Discipline ok');

CALL tap.finish();

-- ****************************************************
-- TEST 5
-- Shift game 2 into the front to make it be the first
-- game. all other games have to be calculated
-- accordingly. after that the game will
--  be shifted back
-- ****************************************************
SELECT tap.plan(62);

SET @timeGame0              = "2017-05-11 10:30:00";
SET @timeAfterGame0         = "2017-05-11 10:30:32";

-- now update game2 with a new time which is in front of game 1
CALL InsertGame(@tempGameId2, @timeGame0, 1,2,0,0   ,  21,14 , 21,18 ,  0,0   ,  "Side A");

-- Check what the debug tables sais about hte cursor
SELECT tap.eq(COUNT(*), 6    , 'SubProcRecalcCoolDownRanks - Processed correct amount of games') FROM DebugUpdateRankPoint;

-- Game 2 is now the new first game
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame0, "Alltime"   ), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 5 - After Game 2 - Moved 2 before 1 - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame0, "Overall"   ), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 5 - After Game 2 - Moved 2 before 1 - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame0, "Discipline"), 3), 0    , 'SubProcRecalcCoolDownRanks - Test 5 - After Game 2 - Moved 2 before 1 - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame0, "Overall"   ), 3), 0    , 'SubProcRecalcCoolDownRanks - Test 5 - After Game 2 - Moved 2 before 1 -Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame0, "Discipline"), 3), 0    , 'SubProcRecalcCoolDownRanks - Test 4 - After Game 2 - Moved 2 before 1 -Team Rank Discipline ok');

-- results of game one are now based on game2
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame1, "Alltime"   ), 3), 2.952, 'SubProcRecalcCoolDownRanks - Test 5 - After Game 1 - Moved 2 before 1 - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame1, "Overall"   ), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 5 - After Game 1 - Moved 2 before 1 - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame1, "Discipline"), 3), 0    , 'SubProcRecalcCoolDownRanks - Test 5 - After Game 1 - Moved 2 before 1 - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame1, "Overall"   ), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 5 - After Game 1 - Moved 2 before 1 -Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame1, "Discipline"), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 5 - After Game 1 - Moved 2 before 1 -Team Rank Discipline ok');

-- Results of game 3 are now based on game 1
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame3, "Alltime"   ), 3), 4.875, 'SubProcRecalcCoolDownRanks - Test 5 - After Game 3 - Moved 2 before 1 - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame3, "Overall"   ), 3), 3.912, 'SubProcRecalcCoolDownRanks - Test 5 - After Game 3 - Moved 2 before 1 - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame3, "Discipline"), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 5 - After Game 3 - Moved 2 before 1 - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame3, "Overall"   ), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 5 - After Game 3 - Moved 2 before 1 -Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame3, "Discipline"), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 5 - After Game 3 - Moved 2 before 1 -Team Rank Discipline ok');

-- All other results follow as usual
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown1, "Alltime"   ), 3), 4.631, 'SubProcRecalcCoolDownRanks - Test 5 - After CD1 - Moved 2 before 1 - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown1, "Overall"   ), 3), 3.716, 'SubProcRecalcCoolDownRanks - Test 5 - After CD1 - Moved 2 before 1 - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown1, "Discipline"), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 5 - After CD1 - Moved 2 before 1 - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown1, "Overall"   ), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 5 - After CD1 - Moved 2 before 1 - Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown1, "Discipline"), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 5 - After CD1 - Moved 2 before 1 - Team Rank Discipline ok');

SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame4, "Alltime"   ), 3), 3.353, 'SubProcRecalcCoolDownRanks - Test 5 - After Game 4 - Moved 2 before 1 - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame4, "Overall"   ), 3), 2.493, 'SubProcRecalcCoolDownRanks - Test 5 - After Game 4 - Moved 2 before 1 - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame4, "Discipline"), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 5 - After Game 4 - Moved 2 before 1 - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame4, "Overall"   ), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 5 - After Game 4 - Moved 2 before 1 - Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame4, "Discipline"), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 5 - After Game 4 - Moved 2 before 1 - Team Rank Discipline ok');

SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown2, "Alltime"   ), 3), 3.185, 'SubProcRecalcCoolDownRanks - Test 5 - After CD2 - Moved 2 before 1 - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown2, "Overall"   ), 3), 2.368, 'SubProcRecalcCoolDownRanks - Test 5 - After CD2 - Moved 2 before 1 - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown2, "Discipline"), 3), 1.805, 'SubProcRecalcCoolDownRanks - Test 5 - After CD2 - Moved 2 before 1 - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown2, "Overall"   ), 3), 1.805, 'SubProcRecalcCoolDownRanks - Test 5 - After CD2 - Moved 2 before 1 - Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown2, "Discipline"), 3), 1.805, 'SubProcRecalcCoolDownRanks - Test 5 - After CD2 - Moved 2 before 1 - Team Rank Discipline ok');

-- and move game2 back in second position, so place its old time which was after game 1
CALL InsertGame(@tempGameId2, @timeGame2, 1,2,0,0   ,  21,14 , 21,18 ,  0,0   ,  "Side A");

-- Check what the debug tables sais about hte cursor
SELECT tap.eq(COUNT(*), 6    , 'SubProcRecalcCoolDownRanks - Processed correct amount of games') FROM DebugUpdateRankPoint;

-- Go back to old order
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame1, "Alltime"   ), 3), 1    , 'SubProcRecalcCoolDownRanks - Test 5 - After Game 1 - Moved 2 behind 1 - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame1, "Overall"   ), 3), 0    , 'SubProcRecalcCoolDownRanks - Test 5 - After Game 1 - Moved 2 behind 1 - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame1, "Discipline"), 3), 0    , 'SubProcRecalcCoolDownRanks - Test 5 - After Game 1 - Moved 2 behind 1 - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame1, "Overall"   ), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 5 - After Game 1 - Moved 2 behind 1 -Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame1, "Discipline"), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 5 - After Game 1 - Moved 2 behind 1 -Team Rank Discipline ok');

-- Game 2 is back to its old place
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame2, "Alltime"   ), 3), 2.957, 'SubProcRecalcCoolDownRanks - Test 5 - After Game 2 - Moved 2 behind 1 - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame2, "Overall"   ), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 5 - After Game 2 - Moved 2 behind 1 - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame2, "Discipline"), 3), 0    , 'SubProcRecalcCoolDownRanks - Test 5 - After Game 2 - Moved 2 behind 1 - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame2, "Overall"   ), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 5 - After Game 2 - Moved 2 behind 1 -Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame2, "Discipline"), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 4 - After Game 2 - Moved 2 behind 1 -Team Rank Discipline ok');

SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame3, "Alltime"   ), 3), 4.883, 'SubProcRecalcCoolDownRanks - Test 5 - After Game 3 - Moved 2 behind 1 - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame3, "Overall"   ), 3), 3.912, 'SubProcRecalcCoolDownRanks - Test 5 - After Game 3 - Moved 2 behind 1 - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame3, "Discipline"), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 5 - After Game 3 - Moved 2 behind 1 - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame3, "Overall"   ), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 5 - After Game 3 - Moved 2 behind 1 -Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame3, "Discipline"), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 5 - After Game 3 - Moved 2 behind 1 -Team Rank Discipline ok');

SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown1, "Alltime"   ), 3), 4.639, 'SubProcRecalcCoolDownRanks - Test 5 - After CD1 - Moved 2 behind 1 - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown1, "Overall"   ), 3), 3.716, 'SubProcRecalcCoolDownRanks - Test 5 - After CD1 - Moved 2 behind 1 - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown1, "Discipline"), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 5 - After CD1 - Moved 2 behind 1 - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown1, "Overall"   ), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 5 - After CD1 - Moved 2 behind 1 - Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown1, "Discipline"), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 5 - After CD1 - Moved 2 behind 1 - Team Rank Discipline ok');

SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame4, "Alltime"   ), 3), 3.361, 'SubProcRecalcCoolDownRanks - Test 5 - After Game 4 - Moved 2 behind 1 - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame4, "Overall"   ), 3), 2.493, 'SubProcRecalcCoolDownRanks - Test 5 - After Game 4 - Moved 2 behind 1 - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame4, "Discipline"), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 5 - After Game 4 - Moved 2 behind 1 - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame4, "Overall"   ), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 5 - After Game 4 - Moved 2 behind 1 - Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame4, "Discipline"), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 5 - After Game 4 - Moved 2 behind 1 - Team Rank Discipline ok');

SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown2, "Alltime"   ), 3), 3.193, 'SubProcRecalcCoolDownRanks - Test 5 - After CD2 - Moved 2 behind 1 - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown2, "Overall"   ), 3), 2.368, 'SubProcRecalcCoolDownRanks - Test 5 - After CD2 - Moved 2 behind 1 - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown2, "Discipline"), 3), 1.805, 'SubProcRecalcCoolDownRanks - Test 5 - After CD2 - Moved 2 behind 1 - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown2, "Overall"   ), 3), 1.805, 'SubProcRecalcCoolDownRanks - Test 5 - After CD2 - Moved 2 behind 1 - Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown2, "Discipline"), 3), 1.805, 'SubProcRecalcCoolDownRanks - Test 5 - After CD2 - Moved 2 behind 1 - Team Rank Discipline ok');

CALL tap.finish();

-- ****************************************************
-- TEST 6
-- remove Game 2 all following games need
-- to be recalculated
-- ****************************************************
SELECT tap.plan(26);

-- Now delete game one and see if the whole calculation machinery will update the rankins correctly
CALL DeleteGame(@tempGameId2);

-- Check what the debug tables sais about hte cursor
SELECT tap.eq(COUNT(*), 4    , 'SubProcRecalcCoolDownRanks - Processed correct amount of games') FROM DebugUpdateRankPoint;

-- game one should not be recalculated
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame1, "Alltime"   ), 3), 1    , 'SubProcRecalcCoolDownRanks - Test 6 - After Game 1 - Deleted Game 2 - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame1, "Overall"   ), 3), 0    , 'SubProcRecalcCoolDownRanks - Test 6 - After Game 1 - Deleted Game 2 - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame1, "Discipline"), 3), 0    , 'SubProcRecalcCoolDownRanks - Test 6 - After Game 1 - Deleted Game 2 - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame1, "Overall"   ), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 6 - After Game 1 - Deleted Game 2 -Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame1, "Discipline"), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 6 - After Game 1 - Deleted Game 2 -Team Rank Discipline ok');

-- from game 3 results have changed
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame3, "Alltime"   ), 3), 3.012, 'SubProcRecalcCoolDownRanks - Test 6 - After Game 3 - Deleted Game 2 - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame3, "Overall"   ), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 6 - After Game 3 - Deleted Game 2 - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame3, "Discipline"), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 6 - After Game 3 - Deleted Game 2 - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame3, "Overall"   ), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 6 - After Game 3 - Deleted Game 2 -Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame3, "Discipline"), 3), 2    , 'SubProcRecalcCoolDownRanks - Test 6 - After Game 3 - Deleted Game 2 -Team Rank Discipline ok');

SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown1, "Alltime"   ), 3), 2.861, 'SubProcRecalcCoolDownRanks - Test 6 - After CD1 - Deleted Game 2 - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown1, "Overall"   ), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 6 - After CD1 - Deleted Game 2 - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown1, "Discipline"), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 6 - After CD1 - Deleted Game 2 - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown1, "Overall"   ), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 6 - After CD1 - Deleted Game 2 - Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown1, "Discipline"), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 6 - After CD1 - Deleted Game 2 - Team Rank Discipline ok');

SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame4, "Alltime"   ), 3), 1.689, 'SubProcRecalcCoolDownRanks - Test 6 - After Game 4 - Deleted Game 2 - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame4, "Overall"   ), 3), 0.786, 'SubProcRecalcCoolDownRanks - Test 6 - After Game 4 - Deleted Game 2 - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterGame4, "Discipline"), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 6 - After Game 4 - Deleted Game 2 - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame4, "Overall"   ), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 6 - After Game 4 - Deleted Game 2 - Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterGame4, "Discipline"), 3), 1.9  , 'SubProcRecalcCoolDownRanks - Test 6 - After Game 4 - Deleted Game 2 - Team Rank Discipline ok');

SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown2, "Alltime"   ), 3), 1.605, 'SubProcRecalcCoolDownRanks - Test 6 - After CD2 - Deleted Game 2 - Player 1 Rank Alltime    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown2, "Overall"   ), 3), 0.747, 'SubProcRecalcCoolDownRanks - Test 6 - After CD2 - Deleted Game 2 - Player 1 Rank Overall    ok');
SELECT tap.eq(Round(_RankPlayerPointUntil(1,     @timeAfterCoolDown2, "Discipline"), 3), 1.805, 'SubProcRecalcCoolDownRanks - Test 6 - After CD2 - Deleted Game 2 - Player 1 Rank Discipline ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown2, "Overall"   ), 3), 1.805, 'SubProcRecalcCoolDownRanks - Test 6 - After CD2 - Deleted Game 2 - Team Rank Overall    ok');
SELECT tap.eq(Round(_RankTeamPointUntil(@teamId, @timeAfterCoolDown2, "Discipline"), 3), 1.805, 'SubProcRecalcCoolDownRanks - Test 6 - After CD2 - Deleted Game 2 - Team Rank Discipline ok');

CALL tap.finish();

COMMIT;

-- Insert a bunch of extra games
SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:01:00", 1,9,6,10  ,  16,21 , 21,16 , 18,21  ,  "Side B");
SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:02:00", 1,9,6,10  ,  18,21 , 16,21 ,  0,0   ,  "Side B");
SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:03:00", 9,1,6,10  ,  21,14 , 21,18 ,  0,0   ,  "Side A");
SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:04:00", 9,6,0,0   ,  21,14 , 21,18 ,  0,0   ,  "Side A");
SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:05:00", 1,6,0,0   ,  16,21 , 14,21 ,  0,0   ,  "Side B");
SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:06:00", 8,2,0,0   ,  16,21 , 14,21 ,  0,0   ,  "Side B");
SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:07:00", 7,2,0,0   ,  16,21 , 14,21 ,  0,0   ,  "Side B");
SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:08:00", 7,1,2,6   ,  16,21 , 14,21 ,  0,0   ,  "Side B");
SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:09:00", 7,4,2,8   ,  21,14 , 16,21 , 21,18  ,  "Side A");

SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:10:00", 6,9,1,10  ,  16,21 , 21,16 , 18,21  ,  "Side B");
SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:11:00", 6,1,9,10  ,  18,21 , 16,21 ,  0,0   ,  "Side B");
SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:12:00", 10,6,9,1  ,  21,14 , 21,18 ,  0,0   ,  "Side A");
SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:13:00", 6,9,0,0   ,  21,14 , 21,18 ,  0,0   ,  "Side A");
SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:14:00", 6,1,0,0   ,  16,21 , 14,21 ,  0,0   ,  "Side B");
SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:15:00", 2,8,0,0   ,  16,21 , 14,21 ,  0,0   ,  "Side B");
SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:16:00", 2,7,0,0   ,  16,21 , 14,21 ,  0,0   ,  "Side B");
SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:17:00", 7,2,1,6   ,  16,21 , 14,21 ,  0,0   ,  "Side B");
SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:18:00", 8,2,4,7   ,  21,14 , 16,21 , 21,18  ,  "Side A");

SET @tempGameId3 = 0; CALL InsertGame(@tempGameId3, "2017-01-02 01:19:00", 6,9,1,10  ,  16,21 , 21,16 , 18,21  ,  "Side B");

COMMIT;

SET GLOBAL event_scheduler = 1; -- Reactivate Scheduler

-- ROLLBACK;
