<div class="dialog_window" id="feeds">
  <h2 class="dialog_heading">Feeds</h2>
  <?php if(isset($config_values['Feeds'])): ?>
    <?php foreach($config_values['Feeds'] as $key => $feed): ?>
      <div class="feeditem">
        <form action="torrentwatch.php?updateFeed=1" class="feedform">
          <a class="submitForm button" id="Delete" href="#">Delete</a>
          <input type="hidden" name="idx" value="<?php echo $key; ?>">
          <br><label class="item"><a href=<?php echo $feed['Link']; ?>target="_blank"><?php echo $feed['Name']; ?></a></label>
        </form>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
  <br>
  <div class="feeditem">
    <form action="torrentwatch.php?updateFeed=1" class="feedform">
      <a class="submitForm button" id="Add" href="#">Add</a>
      <label class="item">New Feed:</label>
      <input type="text" name="link">
    </form>
  </div>
  <div class="buttonContainer">
    <a class="toggleDialog button" href="#">Close</a>
    <a class="toggleDialog button" href="#configuration">Back</a>
  </div>
</div>
