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

CREATE VIEW GameOverviewList AS
SELECT 
    gm.matchId, gm.datetime,
    _UserFullName(uA1.firstName, uA1.lastName)                      AS playerA1,
    _UserFullName(uB1.firstName, uB1.lastName)                      AS playerB1,
    IFNULL(_UserFullName(uA2.firstName, uA2.lastName), "")          AS playerA2,
    IFNULL(_UserFullName(uB2.firstName, uB2.lastName), "")          AS playerB2,
    _GameSideTypeGenderDirect(uA1.gender, uA2.gender)               AS teamGenderA,
    _GameSideTypeGenderDirect(uB1.gender, uB2.gender)               AS teamGenderB,
    IFNULL(gsA1.setPoints, "") as setA1, IFNULL(gsB1.setPoints, "") AS setB1,
    IFNULL(gsA2.setPoints, "") as setA2, IFNULL(gsB2.setPoints, "") AS setB2,
    IFNULL(gsA3.setPoints, "") as setA3, IFNULL(gsB3.setPoints, "") AS setB3,
    gw.side,
    _GameTypeDirect(uA1.gender, uB1.gender, uA2.gender, uB2.gender) AS gameType,
    _GameRankType(uA1.gender, uB1.gender, uA2.gender, uB2.gender)   AS rankType
FROM 
    GameMatch gm
LEFT JOIN
    GamePlayer gpA1
    ON (gm.matchId = gpA1.matchId AND gpA1.side = "Side A" AND gpA1.position = "Pos 1" )
LEFT JOIN
    GamePlayer gpA2
    ON (gm.matchId = gpA2.matchId AND gpA2.side = "Side A" AND gpA2.position = "Pos 2" )
LEFT JOIN
    GamePlayer gpB1
    ON (gm.matchId = gpB1.matchId AND gpB1.side = "Side B" AND gpB1.position = "Pos 1" )
LEFT JOIN
    GamePlayer gpB2
    ON (gm.matchId = gpB2.matchId AND gpB2.side = "Side B" AND gpB2.position = "Pos 2" )
LEFT JOIN
    User uA1
    ON (gpA1.userId = uA1.userId)
LEFT JOIN
    User uA2
    ON (gpA2.userId = uA2.userId)
LEFT JOIN
    User uB1
    ON (gpB1.userId = uB1.userId)
LEFT JOIN
    User uB2
    ON (gpB2.userId = uB2.userId)
LEFT JOIN
    GameSetPoint gsA1
    ON (gm.matchId = gsA1.matchId AND gsA1.side = "Side A" AND gsA1.setNr = 1)
LEFT JOIN
    GameSetPoint gsB1
    ON (gm.matchId = gsB1.matchId AND gsB1.side = "Side B" AND gsB1.setNr = 1)
LEFT JOIN
    GameSetPoint gsA2
    ON (gm.matchId = gsA2.matchId AND gsA2.side = "Side A" AND gsA2.setNr = 2)
LEFT JOIN
    GameSetPoint gsB2
    ON (gm.matchId = gsB2.matchId AND gsB2.side = "Side B" AND gsB2.setNr = 2)
LEFT JOIN
    GameSetPoint gsA3
    ON (gm.matchId = gsA3.matchId AND gsA3.side = "Side A" AND gsA3.setNr = 3)
LEFT JOIN
    GameSetPoint gsB3
    ON (gm.matchId = gsB3.matchId AND gsB3.side = "Side B" AND gsB3.setNr = 3)    
LEFT JOIN
    GameWinner gw
    ON (gm.matchId = gw.matchId)    
    ;
    
CREATE VIEW UserStatsPlayerAlltime AS
SELECT
    u.userId, u.firstName, u.lastName,
    _RankPlayerPointUntil(u.userId, NOW(), "Alltime")               AS rankPoints,
    _GameRankType(uA1.gender, uB1.gender, uA2.gender, uB2.gender)   AS rankType,
    COUNT(DISTINCT gp.matchId)                                      AS games,
    COUNT(DISTINCT IF(gp.side = gw.side, gp.matchId, NULL))         AS gamesWon,
    COUNT(DISTINCT IF(gp.side = gw.side, NULL, gp.matchId))         AS gamesLost,
    ROUND(
        IFNULL(
            COUNT(DISTINCT IF(gp.side = gw.side, gp.matchId, NULL)) / 
            COUNT(DISTINCT gp.matchId)
        , 1)
    ,3)                                                     AS gamesRatio,
    COUNT(*)                                                AS sets,
    SUM(IF(gsp_our.setPoints > gsp_their.setPoints, 1, 0))  AS setsWon,
    SUM(IF(gsp_our.setPoints < gsp_their.setPoints, 1, 0))  AS setsLost,
    ROUND(
        IFNULL(
            SUM(IF(gsp_our.setPoints > gsp_their.setPoints, 1, 0)) / 
            COUNT(*)
        , 1)
    ,3)                                                     AS setsRatio,
    SUM(gsp_our.setPoints + gsp_their.setPoints)            AS points,
    SUM(gsp_our.setPoints)                                  AS pointsWon,
    SUM(gsp_their.setPoints)                                AS pointsLost,
    ROUND(
        IFNULL(
            SUM(gsp_our.setPoints) / 
            SUM(gsp_our.setPoints + gsp_their.setPoints)
        , 1)
    ,3)                                                     AS pointsRatio
FROM
    UserActivePlayer        u
LEFT JOIN GamePlayer        gp          ON (u.userId = gp.userId)
LEFT JOIN GameWinner        gw          ON (gp.matchId = gw.matchId)
LEFT JOIN GameMatch         gm          ON (gp.matchId = gm.matchId)
LEFT JOIN GameSetPoint      gsp_our     ON (gp.matchId = gsp_our.matchId AND gsp_our.side = gp.side)
LEFT JOIN GameSetPoint      gsp_their   ON (gp.matchId = gsp_their.matchId AND gsp_their.side != gp.side AND gsp_their.setNr = gsp_our.setNr)
LEFT JOIN GamePlayer        gpA1        ON (gm.matchId = gpA1.matchId AND gpA1.side = "Side A" AND gpA1.position = "Pos 1" )
LEFT JOIN GamePlayer        gpA2        ON (gm.matchId = gpA2.matchId AND gpA2.side = "Side A" AND gpA2.position = "Pos 2" )
LEFT JOIN GamePlayer        gpB1        ON (gm.matchId = gpB1.matchId AND gpB1.side = "Side B" AND gpB1.position = "Pos 1" )
LEFT JOIN GamePlayer        gpB2        ON (gm.matchId = gpB2.matchId AND gpB2.side = "Side B" AND gpB2.position = "Pos 2" )
LEFT JOIN User              uA1         ON (gpA1.userId = uA1.userId)
LEFT JOIN User              uA2         ON (gpA2.userId = uA2.userId)
LEFT JOIN User              uB1         ON (gpB1.userId = uB1.userId)
LEFT JOIN User              uB2         ON (gpB2.userId = uB2.userId)
GROUP BY u.userId
ORDER BY
    rankPoints DESC;               

CREATE VIEW UserStatsPlayerAlltimePos AS
SELECT 
    _CountSession() as position,
    stats.*
FROM UserStatsPlayerAlltime as stats;
    
CREATE VIEW UserStatsPlayerOverall AS
SELECT 
    u.userId, u.firstName, u.lastName,
    _RankPlayerPointUntil(u.userId, NOW(), "Overall")               AS rankPoints,
    _GameRankType(uA1.gender, uB1.gender, uA2.gender, uB2.gender)   AS rankType,
    COUNT(DISTINCT gp.matchId)                                      AS games,
    COUNT(DISTINCT IF(gp.side = gw.side, gp.matchId, NULL))         AS gamesWon,
    COUNT(DISTINCT IF(gp.side = gw.side, NULL, gp.matchId))         AS gamesLost,
    ROUND(
        IFNULL(
            COUNT(DISTINCT IF(gp.side = gw.side, gp.matchId, NULL)) / 
            COUNT(DISTINCT gp.matchId)
        , 1)
    ,3)                                                     AS gamesRatio,
    COUNT(*)                                                AS sets,
    SUM(IF(gsp_our.setPoints > gsp_their.setPoints, 1, 0))  AS setsWon,
    SUM(IF(gsp_our.setPoints < gsp_their.setPoints, 1, 0))  AS setsLost,
    ROUND(
        IFNULL(
            SUM(IF(gsp_our.setPoints > gsp_their.setPoints, 1, 0)) / 
            COUNT(*)
        , 1)
    ,3)                                                     AS setsRatio,
    SUM(gsp_our.setPoints + gsp_their.setPoints)            AS points,
    SUM(gsp_our.setPoints)                                  AS pointsWon,
    SUM(gsp_their.setPoints)                                AS pointsLost,
    ROUND(
        IFNULL(
            SUM(gsp_our.setPoints) / 
            SUM(gsp_our.setPoints + gsp_their.setPoints)
        , 1)
    ,3)                                                     AS pointsRatio
FROM
    UserActivePlayer        u
LEFT JOIN GamePlayer        gp          ON (u.userId = gp.userId)
LEFT JOIN GameWinner        gw          ON (gp.matchId = gw.matchId)
LEFT JOIN GameMatch         gm          ON (gp.matchId = gm.matchId)
LEFT JOIN GameSetPoint      gsp_our     ON (gp.matchId = gsp_our.matchId AND gsp_our.side = gp.side)
LEFT JOIN GameSetPoint      gsp_their   ON (gp.matchId = gsp_their.matchId AND gsp_their.side != gp.side AND gsp_their.setNr = gsp_our.setNr)
LEFT JOIN GamePlayer        gpA1        ON (gm.matchId = gpA1.matchId AND gpA1.side = "Side A" AND gpA1.position = "Pos 1" )
LEFT JOIN GamePlayer        gpA2        ON (gm.matchId = gpA2.matchId AND gpA2.side = "Side A" AND gpA2.position = "Pos 2" )
LEFT JOIN GamePlayer        gpB1        ON (gm.matchId = gpB1.matchId AND gpB1.side = "Side B" AND gpB1.position = "Pos 1" )
LEFT JOIN GamePlayer        gpB2        ON (gm.matchId = gpB2.matchId AND gpB2.side = "Side B" AND gpB2.position = "Pos 2" )
LEFT JOIN User              uA1         ON (gpA1.userId = uA1.userId)
LEFT JOIN User              uA2         ON (gpA2.userId = uA2.userId)
LEFT JOIN User              uB1         ON (gpB1.userId = uB1.userId)
LEFT JOIN User              uB2         ON (gpB2.userId = uB2.userId)
WHERE
    (
        _GameTypeDirect(uA1.gender, uB1.gender, uA2.gender, uB2.gender) = "Single" 
    ) AND (
        _GameRankType(uA1.gender, uB1.gender, uA2.gender, uB2.gender) = "Overall" OR 
        _GameRankType(uA1.gender, uB1.gender, uA2.gender, uB2.gender) = "Discipline"
    )
GROUP BY u.userId
ORDER BY
    rankPoints DESC;  
    
CREATE VIEW UserStatsPlayerOverallPos AS
SELECT 
    _CountSession() as position,
    stats.*
FROM UserStatsPlayerOverall as stats;    
    
CREATE VIEW UserStatsPlayerDisciplineSingleMen AS
SELECT 
    u.userId, u.firstName, u.lastName,
    _RankPlayerPointUntil(u.userId, NOW(), "Discipline")               AS rankPoints,
    _GameRankType(uA1.gender, uB1.gender, uA2.gender, uB2.gender)   AS rankType,
    COUNT(DISTINCT gp.matchId)                                      AS games,
    COUNT(DISTINCT IF(gp.side = gw.side, gp.matchId, NULL))         AS gamesWon,
    COUNT(DISTINCT IF(gp.side = gw.side, NULL, gp.matchId))         AS gamesLost,
    ROUND(
        IFNULL(
            COUNT(DISTINCT IF(gp.side = gw.side, gp.matchId, NULL)) / 
            COUNT(DISTINCT gp.matchId)
        , 1)
    ,3)                                                     AS gamesRatio,
    COUNT(*)                                                AS sets,
    SUM(IF(gsp_our.setPoints > gsp_their.setPoints, 1, 0))  AS setsWon,
    SUM(IF(gsp_our.setPoints < gsp_their.setPoints, 1, 0))  AS setsLost,
    ROUND(
        IFNULL(
            SUM(IF(gsp_our.setPoints > gsp_their.setPoints, 1, 0)) / 
            COUNT(*)
        , 1)
    ,3)                                                     AS setsRatio,
    SUM(gsp_our.setPoints + gsp_their.setPoints)            AS points,
    SUM(gsp_our.setPoints)                                  AS pointsWon,
    SUM(gsp_their.setPoints)                                AS pointsLost,
    ROUND(
        IFNULL(
            SUM(gsp_our.setPoints) / 
            SUM(gsp_our.setPoints + gsp_their.setPoints)
        , 1)
    ,3)                                                     AS pointsRatio
FROM
    UserActivePlayer        u
LEFT JOIN GamePlayer        gp          ON (u.userId = gp.userId)
LEFT JOIN GameWinner        gw          ON (gp.matchId = gw.matchId)
LEFT JOIN GameMatch         gm          ON (gp.matchId = gm.matchId)
LEFT JOIN GameSetPoint      gsp_our     ON (gp.matchId = gsp_our.matchId AND gsp_our.side = gp.side)
LEFT JOIN GameSetPoint      gsp_their   ON (gp.matchId = gsp_their.matchId AND gsp_their.side != gp.side AND gsp_their.setNr = gsp_our.setNr)
LEFT JOIN GamePlayer        gpA1        ON (gm.matchId = gpA1.matchId AND gpA1.side = "Side A" AND gpA1.position = "Pos 1" )
LEFT JOIN GamePlayer        gpA2        ON (gm.matchId = gpA2.matchId AND gpA2.side = "Side A" AND gpA2.position = "Pos 2" )
LEFT JOIN GamePlayer        gpB1        ON (gm.matchId = gpB1.matchId AND gpB1.side = "Side B" AND gpB1.position = "Pos 1" )
LEFT JOIN GamePlayer        gpB2        ON (gm.matchId = gpB2.matchId AND gpB2.side = "Side B" AND gpB2.position = "Pos 2" )
LEFT JOIN User              uA1         ON (gpA1.userId = uA1.userId)
LEFT JOIN User              uA2         ON (gpA2.userId = uA2.userId)
LEFT JOIN User              uB1         ON (gpB1.userId = uB1.userId)
LEFT JOIN User              uB2         ON (gpB2.userId = uB2.userId)
WHERE
    (
        _GameTypeDirect(uA1.gender, uB1.gender, uA2.gender, uB2.gender) = "Single" 
    ) AND (
        _GameRankType(uA1.gender, uB1.gender, uA2.gender, uB2.gender) = "Discipline"
    ) AND (
        u.gender = "Male"
    )
GROUP BY u.userId
ORDER BY
    rankPoints DESC;   
    
CREATE VIEW UserStatsPlayerDisciplineSingleMenPos AS
SELECT 
    _CountSession() as position,
    stats.*
FROM UserStatsPlayerDisciplineSingleMen as stats;    

CREATE VIEW UserStatsPlayerDisciplineSingleWomen AS
SELECT 
    u.userId, u.firstName, u.lastName,
    _RankPlayerPointUntil(u.userId, NOW(), "Discipline")               AS rankPoints,
    _GameRankType(uA1.gender, uB1.gender, uA2.gender, uB2.gender)   AS rankType,
    COUNT(DISTINCT gp.matchId)                                      AS games,
    COUNT(DISTINCT IF(gp.side = gw.side, gp.matchId, NULL))         AS gamesWon,
    COUNT(DISTINCT IF(gp.side = gw.side, NULL, gp.matchId))         AS gamesLost,
    ROUND(
        IFNULL(
            COUNT(DISTINCT IF(gp.side = gw.side, gp.matchId, NULL)) / 
            COUNT(DISTINCT gp.matchId)
        , 1)
    ,3)                                                     AS gamesRatio,
    COUNT(*)                                                AS sets,
    SUM(IF(gsp_our.setPoints > gsp_their.setPoints, 1, 0))  AS setsWon,
    SUM(IF(gsp_our.setPoints < gsp_their.setPoints, 1, 0))  AS setsLost,
    ROUND(
        IFNULL(
            SUM(IF(gsp_our.setPoints > gsp_their.setPoints, 1, 0)) / 
            COUNT(*)
        , 1)
    ,3)                                                     AS setsRatio,
    SUM(gsp_our.setPoints + gsp_their.setPoints)            AS points,
    SUM(gsp_our.setPoints)                                  AS pointsWon,
    SUM(gsp_their.setPoints)                                AS pointsLost,
    ROUND(
        IFNULL(
            SUM(gsp_our.setPoints) / 
            SUM(gsp_our.setPoints + gsp_their.setPoints)
        , 1)
    ,3)                                                     AS pointsRatio
FROM
    UserActivePlayer        u
LEFT JOIN GamePlayer        gp          ON (u.userId = gp.userId)
LEFT JOIN GameWinner        gw          ON (gp.matchId = gw.matchId)
LEFT JOIN GameMatch         gm          ON (gp.matchId = gm.matchId)
LEFT JOIN GameSetPoint      gsp_our     ON (gp.matchId = gsp_our.matchId AND gsp_our.side = gp.side)
LEFT JOIN GameSetPoint      gsp_their   ON (gp.matchId = gsp_their.matchId AND gsp_their.side != gp.side AND gsp_their.setNr = gsp_our.setNr)
LEFT JOIN GamePlayer        gpA1        ON (gm.matchId = gpA1.matchId AND gpA1.side = "Side A" AND gpA1.position = "Pos 1" )
LEFT JOIN GamePlayer        gpA2        ON (gm.matchId = gpA2.matchId AND gpA2.side = "Side A" AND gpA2.position = "Pos 2" )
LEFT JOIN GamePlayer        gpB1        ON (gm.matchId = gpB1.matchId AND gpB1.side = "Side B" AND gpB1.position = "Pos 1" )
LEFT JOIN GamePlayer        gpB2        ON (gm.matchId = gpB2.matchId AND gpB2.side = "Side B" AND gpB2.position = "Pos 2" )
LEFT JOIN User              uA1         ON (gpA1.userId = uA1.userId)
LEFT JOIN User              uA2         ON (gpA2.userId = uA2.userId)
LEFT JOIN User              uB1         ON (gpB1.userId = uB1.userId)
LEFT JOIN User              uB2         ON (gpB2.userId = uB2.userId)
WHERE
    (
        _GameTypeDirect(uA1.gender, uB1.gender, uA2.gender, uB2.gender) = "Single" 
    ) AND (
        _GameRankType(uA1.gender, uB1.gender, uA2.gender, uB2.gender) = "Discipline"
    ) AND (
        u.gender = "Female"
    )
GROUP BY u.userId
ORDER BY
    rankPoints DESC;   

CREATE VIEW UserStatsPlayerDisciplineSingleWomenPos AS
SELECT 
    _CountSession() as position,
    stats.*
FROM UserStatsPlayerDisciplineSingleWomen as stats;        
    
CREATE VIEW UserStatsTeamOverall AS
SELECT 
    t.*,
    _RankTeamPointUntil(t.teamId, NOW(), "Overall")                 AS rankPoints,
    _GameRankType(uA1.gender, uB1.gender, uA2.gender, uB2.gender)   AS rankType,
    COUNT(DISTINCT gp1.matchId)                                     AS games,
    COUNT(DISTINCT IF(gp1.side = gw.side, gp1.matchId, NULL))       AS gamesWon,
    COUNT(DISTINCT IF(gp1.side = gw.side, NULL, gp1.matchId))       AS gamesLost,
    ROUND(
        IFNULL(
            COUNT(DISTINCT IF(gp1.side = gw.side, gp1.matchId, NULL)) / 
            COUNT(DISTINCT gp1.matchId)
        , 1)
    ,3)                                                     AS gamesRatio,
    COUNT(*)                                                AS sets,
    SUM(IF(gsp_our.setPoints > gsp_their.setPoints, 1, 0))  AS setsWon,
    SUM(IF(gsp_our.setPoints < gsp_their.setPoints, 1, 0))  AS setsLost,
    ROUND(
        IFNULL(
            SUM(IF(gsp_our.setPoints > gsp_their.setPoints, 1, 0)) / 
            COUNT(*)
        , 1)
    ,3)                                                     AS setsRatio,
    SUM(gsp_our.setPoints + gsp_their.setPoints)            AS points,
    SUM(gsp_our.setPoints)                                  AS pointsWon,
    SUM(gsp_their.setPoints)                                AS pointsLost,
    ROUND(
        IFNULL(
            SUM(gsp_our.setPoints) / 
            SUM(gsp_our.setPoints + gsp_their.setPoints)
        , 1)
    ,3)                                                     AS pointsRatio
FROM
    TeamActivePlayer        AS t
LEFT JOIN GameMatch         gm          ON (_TeamIdForGameSide(gm.matchId, "Side A") = t.teamId OR _TeamIdForGameSide(gm.matchId, "Side B") = t.teamId)
LEFT JOIN GameWinner        gw          ON (gm.matchId = gw.matchId)
LEFT JOIN GamePlayer        gp1         ON (gm.matchId = gp1.matchId AND t.user1Id = gp1.userId)
LEFT JOIN GamePlayer        gp2         ON (gm.matchId = gp2.matchId AND t.user2Id = gp2.userId)
LEFT JOIN GameSetPoint      gsp_our     ON (gm.matchId = gsp_our.matchId AND gsp_our.side = gp1.side)
LEFT JOIN GameSetPoint      gsp_their   ON (gm.matchId = gsp_their.matchId AND gsp_their.side != gp1.side AND gsp_their.setNr = gsp_our.setNr)
LEFT JOIN GamePlayer        gpA1        ON (gm.matchId = gpA1.matchId AND gpA1.side = "Side A" AND gpA1.position = "Pos 1" )
LEFT JOIN GamePlayer        gpA2        ON (gm.matchId = gpA2.matchId AND gpA2.side = "Side A" AND gpA2.position = "Pos 2" )
LEFT JOIN GamePlayer        gpB1        ON (gm.matchId = gpB1.matchId AND gpB1.side = "Side B" AND gpB1.position = "Pos 1" )
LEFT JOIN GamePlayer        gpB2        ON (gm.matchId = gpB2.matchId AND gpB2.side = "Side B" AND gpB2.position = "Pos 2" )
LEFT JOIN User              uA1         ON (gpA1.userId = uA1.userId)
LEFT JOIN User              uA2         ON (gpA2.userId = uA2.userId)
LEFT JOIN User              uB1         ON (gpB1.userId = uB1.userId)
LEFT JOIN User              uB2         ON (gpB2.userId = uB2.userId)
WHERE
    (
        _GameTypeDirect(uA1.gender, uB1.gender, uA2.gender, uB2.gender) = "Double" 
    ) 
GROUP BY t.teamId
ORDER BY
    rankPoints DESC;    

CREATE VIEW UserStatsTeamOverallPos AS
SELECT 
    _CountSession() as position,
    stats.*
FROM UserStatsTeamOverall as stats;    

CREATE VIEW UserStatsTeamDisciplineDoubleMen AS
SELECT 
    t.*,
    _RankTeamPointUntil(t.teamId, NOW(), "Discipline")              AS rankPoints,
    _GameRankType(uA1.gender, uB1.gender, uA2.gender, uB2.gender)   AS rankType,
    COUNT(DISTINCT gp1.matchId)                                     AS games,
    COUNT(DISTINCT IF(gp1.side = gw.side, gp1.matchId, NULL))       AS gamesWon,
    COUNT(DISTINCT IF(gp1.side = gw.side, NULL, gp1.matchId))       AS gamesLost,
    ROUND(
        IFNULL(
            COUNT(DISTINCT IF(gp1.side = gw.side, gp1.matchId, NULL)) / 
            COUNT(DISTINCT gp1.matchId)
        , 1)
    ,3)                                                     AS gamesRatio,
    COUNT(*)                                                AS sets,
    SUM(IF(gsp_our.setPoints > gsp_their.setPoints, 1, 0))  AS setsWon,
    SUM(IF(gsp_our.setPoints < gsp_their.setPoints, 1, 0))  AS setsLost,
    ROUND(
        IFNULL(
            SUM(IF(gsp_our.setPoints > gsp_their.setPoints, 1, 0)) / 
            COUNT(*)
        , 1)
    ,3)                                                     AS setsRatio,
    SUM(gsp_our.setPoints + gsp_their.setPoints)            AS points,
    SUM(gsp_our.setPoints)                                  AS pointsWon,
    SUM(gsp_their.setPoints)                                AS pointsLost,
    ROUND(
        IFNULL(
            SUM(gsp_our.setPoints) / 
            SUM(gsp_our.setPoints + gsp_their.setPoints)
        , 1)
    ,3)                                                     AS pointsRatio
FROM
    TeamActivePlayer        AS t
LEFT JOIN GameMatch         gm          ON (_TeamIdForGameSide(gm.matchId, "Side A") = t.teamId OR _TeamIdForGameSide(gm.matchId, "Side B") = t.teamId)
LEFT JOIN GameWinner        gw          ON (gm.matchId = gw.matchId)
LEFT JOIN GamePlayer        gp1         ON (gm.matchId = gp1.matchId AND t.user1Id = gp1.userId)
LEFT JOIN GamePlayer        gp2         ON (gm.matchId = gp2.matchId AND t.user2Id = gp2.userId)
LEFT JOIN GameSetPoint      gsp_our     ON (gm.matchId = gsp_our.matchId AND gsp_our.side = gp1.side)
LEFT JOIN GameSetPoint      gsp_their   ON (gm.matchId = gsp_their.matchId AND gsp_their.side != gp1.side AND gsp_their.setNr = gsp_our.setNr)
LEFT JOIN GamePlayer        gpA1        ON (gm.matchId = gpA1.matchId AND gpA1.side = "Side A" AND gpA1.position = "Pos 1" )
LEFT JOIN GamePlayer        gpA2        ON (gm.matchId = gpA2.matchId AND gpA2.side = "Side A" AND gpA2.position = "Pos 2" )
LEFT JOIN GamePlayer        gpB1        ON (gm.matchId = gpB1.matchId AND gpB1.side = "Side B" AND gpB1.position = "Pos 1" )
LEFT JOIN GamePlayer        gpB2        ON (gm.matchId = gpB2.matchId AND gpB2.side = "Side B" AND gpB2.position = "Pos 2" )
LEFT JOIN User              uA1         ON (gpA1.userId = uA1.userId)
LEFT JOIN User              uA2         ON (gpA2.userId = uA2.userId)
LEFT JOIN User              uB1         ON (gpB1.userId = uB1.userId)
LEFT JOIN User              uB2         ON (gpB2.userId = uB2.userId)
WHERE
    (
        _GameSideTypeGenderDirect(uA1.gender, uA2.gender) = "Double Men" AND 
        _GameSideTypeGenderDirect(uB1.gender, uB2.gender) = "Double Men"
    )
GROUP BY t.teamId
ORDER BY
    rankPoints DESC; 
    
CREATE VIEW UserStatsTeamDisciplineDoubleMenPos AS
SELECT 
    _CountSession() as position,
    stats.*
FROM UserStatsTeamDisciplineDoubleMen as stats;           
    
CREATE VIEW UserStatsTeamDisciplineDoubleWomen AS
SELECT 
    t.*,
    _RankTeamPointUntil(t.teamId, NOW(), "Discipline")              AS rankPoints,
    _GameRankType(uA1.gender, uB1.gender, uA2.gender, uB2.gender)   AS rankType,
    COUNT(DISTINCT gp1.matchId)                                     AS games,
    COUNT(DISTINCT IF(gp1.side = gw.side, gp1.matchId, NULL))       AS gamesWon,
    COUNT(DISTINCT IF(gp1.side = gw.side, NULL, gp1.matchId))       AS gamesLost,
    ROUND(
        IFNULL(
            COUNT(DISTINCT IF(gp1.side = gw.side, gp1.matchId, NULL)) / 
            COUNT(DISTINCT gp1.matchId)
        , 1)
    ,3)                                                     AS gamesRatio,
    COUNT(*)                                                AS sets,
    SUM(IF(gsp_our.setPoints > gsp_their.setPoints, 1, 0))  AS setsWon,
    SUM(IF(gsp_our.setPoints < gsp_their.setPoints, 1, 0))  AS setsLost,
    ROUND(
        IFNULL(
            SUM(IF(gsp_our.setPoints > gsp_their.setPoints, 1, 0)) / 
            COUNT(*)
        , 1)
    ,3)                                                     AS setsRatio,
    SUM(gsp_our.setPoints + gsp_their.setPoints)            AS points,
    SUM(gsp_our.setPoints)                                  AS pointsWon,
    SUM(gsp_their.setPoints)                                AS pointsLost,
    ROUND(
        IFNULL(
            SUM(gsp_our.setPoints) / 
            SUM(gsp_our.setPoints + gsp_their.setPoints)
        , 1)
    ,3)                                                     AS pointsRatio
FROM
    TeamActivePlayer        AS t
LEFT JOIN GameMatch         gm          ON (_TeamIdForGameSide(gm.matchId, "Side A") = t.teamId OR _TeamIdForGameSide(gm.matchId, "Side B") = t.teamId)
LEFT JOIN GameWinner        gw          ON (gm.matchId = gw.matchId)
LEFT JOIN GamePlayer        gp1         ON (gm.matchId = gp1.matchId AND t.user1Id = gp1.userId)
LEFT JOIN GamePlayer        gp2         ON (gm.matchId = gp2.matchId AND t.user2Id = gp2.userId)
LEFT JOIN GameSetPoint      gsp_our     ON (gm.matchId = gsp_our.matchId AND gsp_our.side = gp1.side)
LEFT JOIN GameSetPoint      gsp_their   ON (gm.matchId = gsp_their.matchId AND gsp_their.side != gp1.side AND gsp_their.setNr = gsp_our.setNr)
LEFT JOIN GamePlayer        gpA1        ON (gm.matchId = gpA1.matchId AND gpA1.side = "Side A" AND gpA1.position = "Pos 1" )
LEFT JOIN GamePlayer        gpA2        ON (gm.matchId = gpA2.matchId AND gpA2.side = "Side A" AND gpA2.position = "Pos 2" )
LEFT JOIN GamePlayer        gpB1        ON (gm.matchId = gpB1.matchId AND gpB1.side = "Side B" AND gpB1.position = "Pos 1" )
LEFT JOIN GamePlayer        gpB2        ON (gm.matchId = gpB2.matchId AND gpB2.side = "Side B" AND gpB2.position = "Pos 2" )
LEFT JOIN User              uA1         ON (gpA1.userId = uA1.userId)
LEFT JOIN User              uA2         ON (gpA2.userId = uA2.userId)
LEFT JOIN User              uB1         ON (gpB1.userId = uB1.userId)
LEFT JOIN User              uB2         ON (gpB2.userId = uB2.userId)
WHERE
    (
        _GameSideTypeGenderDirect(uA1.gender, uA2.gender) = "Double Women" AND 
        _GameSideTypeGenderDirect(uB1.gender, uB2.gender) = "Double Women"
    )
GROUP BY t.teamId
ORDER BY
    rankPoints DESC;        

CREATE VIEW UserStatsTeamDisciplineDoubleWomenPos AS
SELECT 
    _CountSession() as position,
    stats.*
FROM UserStatsTeamDisciplineDoubleWomen as stats;        
    
CREATE VIEW UserStatsTeamDisciplineDoubleMixed AS
SELECT 
    t.*,
    _RankTeamPointUntil(t.teamId, NOW(), "Discipline")              AS rankPoints,
    _GameRankType(uA1.gender, uB1.gender, uA2.gender, uB2.gender)   AS rankType,
    COUNT(DISTINCT gp1.matchId)                                     AS games,
    COUNT(DISTINCT IF(gp1.side = gw.side, gp1.matchId, NULL))       AS gamesWon,
    COUNT(DISTINCT IF(gp1.side = gw.side, NULL, gp1.matchId))       AS gamesLost,
    ROUND(
        IFNULL(
            COUNT(DISTINCT IF(gp1.side = gw.side, gp1.matchId, NULL)) / 
            COUNT(DISTINCT gp1.matchId)
        , 1)
    ,3)                                                     AS gamesRatio,
    COUNT(*)                                                AS sets,
    SUM(IF(gsp_our.setPoints > gsp_their.setPoints, 1, 0))  AS setsWon,
    SUM(IF(gsp_our.setPoints < gsp_their.setPoints, 1, 0))  AS setsLost,
    ROUND(
        IFNULL(
            SUM(IF(gsp_our.setPoints > gsp_their.setPoints, 1, 0)) / 
            COUNT(*)
        , 1)
    ,3)                                                     AS setsRatio,
    SUM(gsp_our.setPoints + gsp_their.setPoints)            AS points,
    SUM(gsp_our.setPoints)                                  AS pointsWon,
    SUM(gsp_their.setPoints)                                AS pointsLost,
    ROUND(
        IFNULL(
            SUM(gsp_our.setPoints) / 
            SUM(gsp_our.setPoints + gsp_their.setPoints)
        , 1)
    ,3)                                                     AS pointsRatio
FROM
    TeamActivePlayer        AS t
LEFT JOIN GameMatch         gm          ON (_TeamIdForGameSide(gm.matchId, "Side A") = t.teamId OR _TeamIdForGameSide(gm.matchId, "Side B") = t.teamId)
LEFT JOIN GameWinner        gw          ON (gm.matchId = gw.matchId)
LEFT JOIN GamePlayer        gp1         ON (gm.matchId = gp1.matchId AND t.user1Id = gp1.userId)
LEFT JOIN GamePlayer        gp2         ON (gm.matchId = gp2.matchId AND t.user2Id = gp2.userId)
LEFT JOIN GameSetPoint      gsp_our     ON (gm.matchId = gsp_our.matchId AND gsp_our.side = gp1.side)
LEFT JOIN GameSetPoint      gsp_their   ON (gm.matchId = gsp_their.matchId AND gsp_their.side != gp1.side AND gsp_their.setNr = gsp_our.setNr)
LEFT JOIN GamePlayer        gpA1        ON (gm.matchId = gpA1.matchId AND gpA1.side = "Side A" AND gpA1.position = "Pos 1" )
LEFT JOIN GamePlayer        gpA2        ON (gm.matchId = gpA2.matchId AND gpA2.side = "Side A" AND gpA2.position = "Pos 2" )
LEFT JOIN GamePlayer        gpB1        ON (gm.matchId = gpB1.matchId AND gpB1.side = "Side B" AND gpB1.position = "Pos 1" )
LEFT JOIN GamePlayer        gpB2        ON (gm.matchId = gpB2.matchId AND gpB2.side = "Side B" AND gpB2.position = "Pos 2" )
LEFT JOIN User              uA1         ON (gpA1.userId = uA1.userId)
LEFT JOIN User              uA2         ON (gpA2.userId = uA2.userId)
LEFT JOIN User              uB1         ON (gpB1.userId = uB1.userId)
LEFT JOIN User              uB2         ON (gpB2.userId = uB2.userId)
WHERE
    (
        _GameSideTypeGenderDirect(uA1.gender, uA2.gender) = "Double Mixed" AND 
        _GameSideTypeGenderDirect(uB1.gender, uB2.gender) = "Double Mixed"
    )
GROUP BY t.teamId
ORDER BY
    rankPoints DESC;        
    
CREATE VIEW UserStatsTeamDisciplineDoubleMixedPos AS
SELECT 
    _CountSession() as position,
    stats.*
FROM UserStatsTeamDisciplineDoubleMixed as stats;           