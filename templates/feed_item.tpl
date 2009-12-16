<li id='li_<?php echo $torHash; ?>' class='torrent <?php echo "match_$matched $alt"; ?>' title='<?php echo _isset($item, 'description'); ?>'>
<a class='context_link_fav' 
   href='<?php echo "torrentwatch.php?matchTitle=1&rss=$feed&title=$utitle"; ?>'>
</a>
<a class='context_link_start' 
   href='<?php echo "torrentwatch.php?dlTorrent=1&title=$utitle&link=$ulink"; ?>'>
</a>
<span class='torrent_pubDate'><?php echo _isset($item, 'pubDate'); ?></span>
<span class='torrent_name'><?php echo $title; ?></span>
</li>
