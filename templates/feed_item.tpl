<?php
if($torInfo['id']) $clientId = 'clientId_' . $torInfo['id'];
if($item['description']) $description = $item['description'];
if($item['pubDate']) $pubDate = $item['pubDate'];
if($matched == "downloading" || $matched == "downloaded" || $matched == "cachehit" ) { $hidden = ""; } else { $hidden = "hidden"; } 
if($torInfo['torInfo']) $infoDiv = "<div id='tor_$id' class='torInfo tor_$torHash $clientId'>" . $torInfo['torInfo'] . "</div>";
if(!($torHash)) $torHash = '###torHash###';

print <<< EOH

<li id=$id name=$id class="torrent match_$matched $alt $torHash $clientId" title="$description">
<table width="100%" cellspacing="0"><tr><td class="buttons left match_$matched">
<p class='start'><a href="#" title="Download this torrent" onclick='javascript:$.dlTorrent("torrentwatch.php?dlTorrent=1&title=$utitle&link=$ulink","$id")'>
<img height=10 src="images/tor_start.png">
</a></p>

<p class="activeTorrent delete $hidden">
<a href="#" title="Delete torrent but keep data" onclick='javascript:$.delTorrent("torrentwatch.php?delTorrent=$torHash&trash=false")'>
<img height=10 src="images/tor_stop.png"></p>

</td><td class="buttons right match_$matched ">
<p class='favorite'><a href="#" title="Add this show to favorites" onclick='javascript:$.addFavorite("torrentwatch.php?matchTitle=1&rss=$feed&title=$utitle")'>
<img height=10 src="images/tor_fav.png">
</a></p>

<p class="activeTorrent trash $hidden">
<a href="#" title="Delete torrent and its data" onclick='javascript:$.delTorrent("torrentwatch.php?delTorrent=$torHash&trash=true")'>
<img height=10 src="images/tor_trash.png"></a></p>


</td><td class="torrent_name">
<span class='torrent_pubDate'>$pubDate</span>
<span class='torrent_name'>$title</span>

$infoDiv
</td></tr></table></li>

EOH;
?>
