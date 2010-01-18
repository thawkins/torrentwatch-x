<div class="dialog_window" id="hidelist">
        <form action="torrentwatch.php?delHidden=1" id="hidelist_form" name="hidelist_form">
            <ul class="hidelist">
            <?php if($config_values['Hidden']): ?>
            <?php foreach($config_values['Hidden'] as $key => $item): ?>
                <li>
                    <label class="item checkbox">
                    <input type="checkbox" name="unhide[]" value="<?=$key?>"/>
                    <?php echo $item['Name']; ?></label>
                </li>
            <?php endforeach; ?>
            <?php else: ?>
                <li><h2 style='color: red; text-align: center'>You did not hide any shows.</h2></li>
            <?php endif; ?>
        </ul>
        <a class="submitForm button" id="Delete" href="#">Unhide</a>
        <a class='toggleDialog button' href='#'>Close</a> 
        </form>
</div>

