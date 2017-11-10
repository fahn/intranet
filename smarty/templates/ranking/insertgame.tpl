<datalist id="allPlayerList">
  <option value="<?php echo $fullName; ?>" />
</datalist>

<div id="formReportInsertGame">
  <h3>Report a Game for Badminton Ranking</h3>
  <p>Tell the date, players, set points and winner of the played game.</p>
  <hr/>
  <form>
    <label for = "<?php echo $variableNameDate;?>">Date and Time:</label>
      <div class = "radioGroup">
      <div class = "radioRow">
      <div class = "radioCell">
    <input
        type		= "date"
        id			= "<?php echo $variableNameDate;?>"
        name		= "<?php echo $variableNameDate;?>"
        placeholder	= "2017-05-01"
        value		= "<?php echo $variableNameDateValue;?>"
      />
      </div>
      <div class = "radioCell">
      <input
        type		= "time"
        id			= "<?php echo $variableNameDate;?>"
        name		= "<?php echo $variableNameTime;?>"
        placeholder	= "20:00:00"
        value		= "<?php echo $variableNameTimeValue;?>"
      />
      </div>
      </div>
      </div>
      <div class = "radioGroup">
      <div class = "radioRow">
      <div class = "radioCell">
      <label for = "<?php echo $variableNamePlayerA1;?>">Team A Player 1:</label>
        <input
          type		= "text"
          id			= "<?php echo $variableNamePlayerA1;?>"
          name		= "<?php echo $variableNamePlayerA1;?>"
          placeholder	= "Philipp Fischer"
          value		= "<?php echo $variableNamePlayerA1Value;?>"
          list		= "allPlayerList"
        />
        <?php $this->getAllPlayerDataList(); ?>
      <label for = "<?php echo $variableNamePlayerA2;?>">Team A Player 2:</label>
        <input
          type		= "text"
          id			= "<?php echo $variableNamePlayerA2;?>"
          name		= "<?php echo $variableNamePlayerA2;?>"
          placeholder	= "Carsten Borchert"
          value		= "<?php echo $variableNamePlayerA2Value;?>"
          list		= "allPlayerList"
        />
        <?php $this->getAllPlayerDataList(); ?>
      <label for = "<?php echo $variableNameSetA1;?>">Team A Set 1 Points:</label>
        <input
          type		= "text"
          id			= "<?php echo $variableNameSetA1;?>"
          name		= "<?php echo $variableNameSetA1;?>"
          placeholder	= "21"
          value		= "<?php echo $variableNameSetA1Value;?>"
        />
      <label for = "<?php echo $variableNameSetA2;?>">Team A Set 2 Points:</label>
        <input
          type		= "text"
          id			= "<?php echo $variableNameSetA2;?>"
          name		= "<?php echo $variableNameSetA2;?>"
          placeholder	= "21"
          value		= "<?php echo $variableNameSetA2Value;?>"
        />
      <label for = "<?php echo $variableNameSetA3;?>">Team A Set 3 Points:</label>
        <input
          type		= "text"
          id			= "<?php echo $variableNameSetA3;?>"
          name		= "<?php echo $variableNameSetA3;?>"
          placeholder	= "0"
          value		= "<?php echo $variableNameSetA3Value;?>"
        />
      </div>
      <div class = "radioCell">
      <label for = "<?php echo $variableNamePlayerB1;?>">Team B Player 1:</label>
        <input
          type		= "text"
          id			= "<?php echo $variableNamePlayerB1;?>"
          name		= "<?php echo $variableNamePlayerB1;?>"
          placeholder	= "Jan Sippli"
          value		= "<?php echo $variableNamePlayerB1Value;?>"
          list		= "allPlayerList"
        />
        <?php $this->getAllPlayerDataList(); ?>
      <label for = "<?php echo $variableNamePlayerB2;?>">Team B Player 2:</label>
        <input
          type		= "text"
          id			= "<?php echo $variableNamePlayerB2;?>"
          name		= "<?php echo $variableNamePlayerB2;?>"
          placeholder	= "Isabel Adam"
          value		= "<?php echo $variableNamePlayerB2Value;?>"
          list		= "allPlayerList"
        />
        <?php $this->getAllPlayerDataList(); ?>
      <label for = "<?php echo $variableNameSetB1;?>">Team B Set 1 Points:</label>
        <input
          type		= "text"
          id			= "<?php echo $variableNameSetB1;?>"
          name		= "<?php echo $variableNameSetB1;?>"
          placeholder	= "16"
          value		= "<?php echo $variableNameSetB1Value;?>"
        />
      <label for = "<?php echo $variableNameSetB2;?>">Team B Set 2 Points:</label>
        <input
          type		= "text"
          id			= "<?php echo $variableNameSetB2;?>"
          name		= "<?php echo $variableNameSetB2;?>"
          placeholder	= "18"
          value		= "<?php echo $variableNameSetB2Value;?>"
        />
      <label for = "<?php echo $variableNameSetB3;?>">Team B Set 3 Points:</label>
        <input
          type		= "text"
          id			= "<?php echo $variableNameSetB3;?>"
          name		= "<?php echo $variableNameSetB3;?>"
          placeholder	= "0"
          value		= "<?php echo $variableNameSetB3Value;?>"
        />
      </div>
      </div>
      </div>
      <label>Winner:</label>
      <div class = "radioGroup">
      <div class = "radioRow">
      <div class = "radioCell">
      <input
        type		= "radio"
        id			= "<?php echo $variableNameWinnerSideA;?>"
        name		= "<?php echo $variableNameWinner;?>"
        value		= "<?php echo $variableNameWinnerSideA;?>"
      <?php echo $checkedAttributeIsWinnerSideA . rn;  ?>
      />
      <label class = "radio" for = "<?php echo $variableNameWinnerSideA;?>">Winner A</label>
      </div>
      <div class = "radioCell">
      <input
        type		= "radio"
        id			= "<?php echo $variableNameWinnerSideB;?>"
        name		= "<?php echo $variableNameWinner;?>"
        value		= "<?php echo $variableNameWinnerSideB;?>"
        <?php echo $checkedAttributeIsWinnerSideB. rn;  ?>
      />
      <label class = "radio" for = "<?php echo $variableNameWinnerSideB;?>">Winner B</label>
      </div>
      </div>
      </div>
      <input
      type		= "submit"
      name		= "<?php echo $variableNameAction;?>"
      value		= "<?php echo $variableNameActionInsertGame;?>"
      formaction	= "<?php echo BrdbHtmlPage::PAGE_REPORT_INSERT_GAME;?>"
      formmethod	= "post"
    />
  </form>
</div>
