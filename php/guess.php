<?php
function guess_match($title, $normalize = FALSE) { 
    // Episode
    $epi ='/(S\d+[. ]?E\d+(?:-E\d+)?'.'|';  // S12E1 or S12E1-E2
    $epi.='\d{1,2}x\d+(?:-\d+)?' .'|';  // 1x23 or 1x23-24
    $epi.='\d+[. ]?of[. ]?\d+'.'|';  // 03of18
    $epi.='Season[. ]?\d+,?[. ]?Episode[. ]?\d+'.'|'; // Season 4, episode 15
    $epi.='[. ]E\d+'.'|'; // E137
    $epi.='[\d -.]{10})/i';   // 2008-03-23 or 07.23.2008 or .20082306. etc

    // Quality
    $quality ='/(DVB'  .'|';
    $quality.='DSRIP'  .'|';
    $quality.='DVBRip' .'|';
    $quality.='DVDR'   .'|';
    $quality.='DVDRip' .'|';
    $quality.='DVDScr' .'|';
    $quality.='HR.HDTV'.'|';
    $quality.='HDTV'   .'|';
    $quality.='HR.PDTV'.'|';
    $quality.='PDTV'   .'|';
    $quality.='SatRip' .'|';
    $quality.='SVCD'   .'|';
    $quality.='TVRip'  .'|';
    $quality.='TVCap'  .'|';
    $quality.='WebRip' .'|';
    $quality.='720p'   .'|';
    $quality.='1080i'  .'|';
    $quality.='1080p)/i';

     if(preg_match($epi, $title, $match)) {
      $episode_guess = $match[0];
      $key_guess = preg_replace('/[-\(\/]/', '', preg_replace("/(^.+)$episode_guess(.*$)/", '\1', $title));
      if(preg_match($quality, $title, $qregs))
        $data_guess = str_replace("'", "&#39;", trim($qregs[1]));
      else
        $data_guess = '';
    } else {
      return False;
    }
    if($normalize == TRUE) {
    // Convert . and _ to spaces, and trim result
    $from = "._";
    $to = "  ";
    $key_guess = trim(strtr($key_guess, $from, $to));
    $data_guess = trim(strtr($data_guess, $from, $to));
    $episode_guess = trim(strtr($episode_guess, $from, $to));
    // Standardize episode output to SSxEE, strip leading 0
    // This is (b|c|d) from earlier.  If it is style e there will be no replacement, only strip leading 0
    $episode_guess = preg_replace('/0*(\d+)x0*(\d+)/', '\1x\2', 
        preg_replace('/(S(\d+)[. ]?E(\d+)|(\d+)x(\d+)|(\d+)[. ?]of[. ]?(\d+))|season[. ]?(\d+),?[. ]?episode[. ]?(\d+)/i',
            '\2\4\6\8x\3\5\7\9', $episode_guess));
            
  }  
  return array("key" => $key_guess, "data" => $data_guess, "episode" => $episode_guess);
}

function guess_feedtype($feedurl) {
  global $config_values;
  $response = check_for_cookies($feedurl);
  if($response) $feedurl = $response['url'];
  $get = curl_init();
  $getOptions[CURLOPT_URL] = $feedurl;
  get_curl_defaults(&$getOptions);
  curl_setopt_array($get, $getOptions);
  $content = explode('\n', curl_exec($get));
  curl_close($get);
  
  // Should be on the second line, but test the first 5 incase
  // of doctype etc.
  for($i = 0;$i < 5;$i++) {
    if(preg_match('/<feed xml/', $content[$i], $regs))
      return 'Atom';
    else if (preg_match('/<rss/', $content[$i], $regs))
      return 'RSS';
  }
  return "Unknown";
}


function guess_atom_torrent($summary) {
  $wc = '[\/\:\w\.\+\?\&\=\%\;]+';
  // Detects: A HREF=\"http://someplace/with/torrent/in/the/name\"
  if(preg_match('/A HREF=\\\"(http'.$wc.'torrent'.$wc.')\\\"/', $summary, $regs)) {
    _debug("guess_atom_torrent: $regs[1]\n",2);
    return $regs[1];
  } else {
    _debug("guess_atom_torrent: failed\n",2);
  }
  return FALSE;
}
?>
