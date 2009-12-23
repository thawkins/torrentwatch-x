<?php
if($torInfo) { 
  $clientId = 'clientId_' . $torInfo['id'];
  $sizeDone = $torInfo['sizeDone'];
  $totalSize = $torInfo['totalSize'];
  $percentage = $torInfo['percentage'];
  $ratio = $torInfo['ratio'];
  $status = $torInfo['status'];
  $peersSendingToUs = $torInfo['peersSendingToUs'];
  $peersGettingFromUs = $torInfo['peersGettingFromUs'];
  $peersConnected = $torInfo['peersConnected'];
}
if($item['description']) $description = $item['description'];
if($item['pubDate']) $pubDate = $item['pubDate'];
if($matched == "downloading" || $matched == "downloaded" || $matched == "cachehit" ) { $hidden = ""; } else { $hidden = "hidden"; } 
if(!($torHash)) $torHash = '###torHash###';
if($status == 1) {
  $stats = "Waiting for peers";
} else if($status == 2) {
  $stats = "Verifying files ($percentage%)";
} else if($status == 4) {
  $stats = "Downloading from $peersSendingToUs of $peersConnected peers: $sizeDone of $totalSize ($percentage%)  -  Ratio: $ratio";
} else if($status == 8) {
  $stats = "Seeding to $peersGettingFromUs of $peersConnected peers  -  Ratio: $ratio";
} else if($status == 16) {
  $stats = "Paused";
}
if($torInfo) $infoDiv = "<div id='tor_$id' class='torInfo tor_$torHash $clientId'>$stats</div>";

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
