<div class="dialog_window" id="feeds">
  <h2 class="dialog_heading">Feeds</h2>
  <div class="feeditem add">
      <form action="torrentwatch.php?updateFeed=1" class="feedform">
      <a class="submitForm button" id="Add" href="#">Add</a>
      <label class="item">Add feed:</label>
      <input type="text" class="feed_link" name="link">
      </form>
  </div>
  <div id="feedName"><label class="item">Name</label></div>
  <label class="item">Ratio</label>
  <?php if(isset($config_values['Feeds'])): ?>
    <?php foreach($config_values['Feeds'] as $key => $feed): ?>
      <div class="feeditem">
          <form action="torrentwatch.php?updateFeed=1" class="feedform">  
          <input type="hidden" name="idx" value="<?php echo $key; ?>">
          <input class="feed_name" type="text" name="feed_name" title="<?php echo $feed['Link']; ?>"
                 value="<?php echo $feed['Name']; ?>"</input>
         <input class="seed_ratio" type="text" name="seed_ratio" title="Set default seed ratio for this feed."
                value="<?php echo $feed['seedRatio']; ?>"</input>
          <a class="submitForm button" id="Delete" href="#">Delete</a>
          <a class="submitForm button" id="Update" href="#">Update</a>
          </form>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
  <div class="buttonContainer">
    <a class="toggleDialog button" href="#">Close</a>
    <a class="toggleDialog button" href="#configuration">Back</a>
  </div>
</div>
