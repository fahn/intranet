### Badminton Ranking - The About
------

Badminton Ranking is a ranking system for your badminton players. It is a web based 
tool that allows players to compare their performance with other players.

#### Registration

You have to ask an Admin to register for Badminton Ranking. You have to provide an 
email and a password. The email is used for identification only. A User can have 3 
different states:

1. *Admin*: An Admin can add new players and add and change games.
2. *Reporter*: A Reporter can add games and change them.
3. *Player*: A Player can score and participate in games.

#### The scoring systems

Badminton ranking provides various different types of scores to account for individual 
and team performance. As part of these measure Badminton Ranking provides the serious 
comparisons for official style matches such as a mens single or girls double. On top 
of that it provides additional scores to compare teams in general or the overall individual 
performance in all games ever played for example. 

The results and current performance is recorded in different lists. All these lists 
compare the players and teams by their achieved points. The lowest amount of points 
a player can have is 0 points. Every game played will add or withdraw some points from 
a players or team record in one or several of these scores. Once in a while Badminton 
Ranking will decide to cool down all players. This means that depending on settings 
all player records will be reduced by 10% of their current score around every month.

The scores recorded are generally clustered by individual player and team, as well as 
discipline and overall ratings. The lists recorded are:

1. **Single Alltime Score** - This is the all-time record of a players performance. 
  Every game played, single matches as well as double matches are accounted in this 
  score. This list allows to compare the performance of all players women and men with 
  each other.
2. **Single Overall Score** - This is the overall record of a player performance. 
  Other than the alltime score, this score only counts the wins and losses of single 
  player games such as man vs. man, woman vs. woman but also woman vs. man. Hence this 
  list allows to compare the single game performance of all players.
3. **Single Discipline Man Score** - This is the single man record of all male player performances. 
  Different than the Single Overall Score, this score only counts the man vs. man player 
  performance. Accordingly this list allows to compare the single game performance 
  of all male players.
4. **Single Discipline Woman Score** - This is the single woman record of all female player performances. 
  Different than the Single Overall Score, this score only counts the woman vs. woman player 
  performance. Accordingly this list allows to compare the single game performance 
  of all female players. 
1. **Double Overall Score** - This is the double overall record of all team performances.
  This score counts the wins and losses of all team based games such as men vs. men, 
  men vs. woman as well as mixed vs. woman. This list allows to compare the performance 
  of all teams against each other.
1. **Double Discipline Men Score** - This is the double men record of all male team performances.
  This score counts the wins and losses of all male double team games. This list allows to compare the performance 
  of all male teams with each other.
1. **Single Discipline Women Score** - This is the double women record of all female team performances.
  This score counts the wins and losses of all female double team games. This list allows to compare the performance 
  of all female teams with each other.
1. **Single Discipline Mixed Score** - This is the double mixed record of all mixed team performances.
  This score counts the wins and losses of all mixed double team games. This list allows to compare the performance 
  of all mixed teams with each other.  

#### Which scores are affected by my games

Now when you are a player that is registered to Badminton Ranking someone will record 
your games and report them to this program. But which score is actually affected by 
your games. The following examples may help to understand the scoring:

- **A men vs. men double game** - This game will affect the double men score as well 
  as the double overall score and also the single alltime score for all four players.
- **A woman vs. woman single game** - This game will affect the single woman score 
  as well as the single overall score and finally the alltime score for both players.
- **A men double vs. women single game** - Strange combination but this game will only 
  affect the single alltime score of all players.

#### How is my score calculated

Depending on the type of game you played all relevant score lists will be updated with 
the new points that have either be won or lost. Accordingly the first step is to decide 
which score lists are affected, from here the latest scores of all players and teams 
until the time of the new game are extracted.

#### Extracting the player and team scores

E.g. playing a women double game means that it accounts for the single overall scores 
of all four players as well as the team scores of overall double games and the double 
women games. Therefore the following scores may be extracted in case we have *Team 2* 
with *Player 6* and *Player 8* and *Team 5* with *Player 2* and *Player 3* the initial 
results for the calculation may look like this:

- *Single Alltime Scores of Team 2*: Player 6 with 89 - Player 8 with 2 
- *Single Alltime Scores of Team 5*: Player 2 with 60 - Player 3 with 45
- *Double Overall Scores*: Team 2 with 6 - Team 5 with 40
- *Double Discipline Scores*: Team 2 with 4 - Team 5 with 45

Now where the current results are extracted, the actual calculation of the new result 
can start. Therefore the Alltime score of the game sides need to be calculated by summing 
up the individual results.

- *Double Alltime Scores*: Team 2 with 91 - Team 5 with 105

#### Start calculating the team win and losses

Once all individual scores and team scores are extracted it is time to calculate how 
much points each team is winning or loosing. The results are calculated 
individually for each game type i.e. alltime, overall and discipline. The winning team 
earns 2 points while the loosing team looses 1 point in general. Additional to that 
both teams put 5% of their points into a pot which is redistributed to the teams depending 
on the game standings and the points played. Imagine the following game result

- *Set 1*: Team 2 vs. Team 5 - 21:18
- *Set 2*: Team 2 vs. Team 5 - 21:10

E.g. Team 2 is the winner of the game. The result will be calculated as such:

1. *Team 2*: throws (5%) 4.55 points into the pot and remains with 86.45 points.
2. *Team 5*: throws (5%) 5.25 points into the pot and remains with 99.75 points.
3. The pot now contains in total 9.80 points that are distributed depending on the 
   game standings.
4. The game consists of (21 + 18 + 21 + 10) = 70 totally played points.
5. *Team 2*: owned 42 of the points winning now winning 60% 5.88 points of the pot.
6. *Team 5*: owned 28 of the points winning now winning 40% 3.92 points of the pot.
7. *Team 2*: as the winning team now has a new score of (86.45 + 2.00 + 5.88) = 94.33 
  points.   
8. *Team 5*: as the winning team now has a new score of (99.75 - 1.00 + 3.92) = 102.67 
  points.   

#### Assigning the new standings

Following the above example the overall scores or discipline scores are getting calculated. 
Depending on the game type, the according scores will be overwritten with the new result. 
In case of a single player game, a Team will consist of only one player. Badminton 
Ranking will detect that case and overwrite the Single Player score instead of the 
team score.

#### Reassigning the alltime scores to the actual players

In case of alltime games the points earned or lost have to be redistributed on both 
players. Accordingly the points won or lost are devided by two and assigned to the 
players. 

- *Team 2*: started with 91 and earned to 94.33, which is a win of 3.33 points.
- *Team 5*: started with 105 and dropped to 102.67, summarizes a loss of 2.33 points. 
- *Alltime Scores of Team 2*: Player 6 now with 90.665 - Player 8 now with 3.665 
- *Alltime Scores of Team 5*: Player 2 now with 58.835 - Player 3 now with 43.835

### The Technology

It is developed as a web based application using PHP 5, HTML5 and CSS3 and MySQL. It is intended 
to run on an Apache Web Server and has been tested on current Firefox and Chrome based 
web-browsers.

#### Release Notes

Release 0.9.2 ALPHA

- The first test release of Badminton Ranking Brandet for BC Comet.

-----

	