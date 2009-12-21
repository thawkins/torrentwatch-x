<li id=<?php echo $id; ?> class='torrent  <?php echo "match_$matched $alt $torHash tr_id_". $torInfo['id'] ; ?>' title='<?php echo _isset($item, 'description'); ?>'>
<table width="100%" cellspacing="0"><tr><td class="buttons left <?php echo "match_$matched tr_id_" . $torInfo['id']; ?>">
<p><a href="#" title="Download this torrent" onclick="javascript:$.dlTorrent('<?php echo "torrentwatch.php?dlTorrent=1&title=$utitle&link=$ulink"; ?>')">
<img height=10 src="images/tor_start.png">
</a></p>

<?php if($torInfo[id]) { echo '
  <p class=tr_id_' . $torInfo['id'] . '><img height=10 src="images/tor_stop.png"></p>'; } ?>

</td><td class="buttons right <?php echo "match_$matched tr_id_" . $torInfo['id'] ; ?>">
<p><a href="#" title="Add this show to favorites" onclick="javascript:$.addFavorite('<?php echo "torrentwatch.php?matchTitle=1&rss=$feed&title=$utitle"; ?>')">
<img height=10 src="images/tor_fav.png">
</a></p>

<?php if($torInfo[id]) { echo '
  <p class=tr_id_' . $torInfo['id'] . '><img height=10 src="images/tor_trash.png"></p>'; } ?>

</td><td class="torrent_name">
<span class='torrent_pubDate'><?php echo _isset($item, 'pubDate'); ?></span>
<span class='torrent_name'><?php echo $title; ?></span>

<?php if($torInfo) { echo '
  <div id="tor_' . $id . '" "class="torInfo tor_' . $torHash . ' tr_id_' . $torInfo['id'] . '">' . $torInfo['torInfo'] . '</div>'; } ?>

</td></tr></table></li>
