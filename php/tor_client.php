<?php
/*
 * client.php
 * Client specific functions
 */


function transmission_sessionId() {
  global $config_values;
  $sessionIdFile = '/tmp/.Transmission-Session-Id';

  if(file_exists($sessionIdFile)) {
    $handle = fopen($sessionIdFile, r);
    $sessionId = fread($handle, filesize($sessionIdFile));
  } else {
    $tr_user = $config_values['Settings']['Transmission Login'];
    $tr_pass = base64_decode($config_values['Settings']['Transmission Password']);
    $tr_host = $config_values['Settings']['Transmission Host'];
    $tr_port = $config_values['Settings']['Transmission Port'];
    $tr_uri = $config_values['Settings']['Transmission URI'];


    $sid = curl_init();
    $sid_options = array(CURLOPT_RETURNTRANSFER => true,
                   CURLOPT_URL => "http://$tr_host:$tr_port$tr_uri",
                   CURLOPT_HEADER => true,
                   CURLOPT_NOBODY => true,
                   CURLOPT_USERPWD => "$tr_user:$tr_pass"
                  );

    curl_setopt_array($sid, $sid_options);

    $header = curl_exec($sid);
    curl_close($sid);
  
    preg_match("/X-Transmission-Session-Id:\s(\w+)/",$header,$ID);

    $handle = fopen($sessionIdFile, "w");
    fwrite($handle, $ID[1]);
    fclose($handle);
  
    $sessionId = $ID[1];
  }

  return $sessionId;
}

function transmission_rpc($request) {
  global $config_values;
  $sessionIdFile = '/tmp/.Transmission-Session-Id';

  $tr_user = $config_values['Settings']['Transmission Login'];
  $tr_pass = base64_decode($config_values['Settings']['Transmission Password']);
  $tr_uri = $config_values['Settings']['Transmission URI'];
  $tr_host = $config_values['Settings']['Transmission Host'];
  $tr_port = $config_values['Settings']['Transmission Port'];

  $request = json_encode($request);
  $reqLen = strlen("$request");
  
  $run = 1; 
  while($run) { 
    $SessionId = transmission_sessionId();

    $post = curl_init();
    $post_options = array(CURLOPT_RETURNTRANSFER => true,
                          CURLOPT_URL => "http://$tr_host:$tr_port$tr_uri",
                       	  CURLOPT_USERPWD => "$tr_user:$tr_pass",
                          CURLOPT_HTTPHEADER => array (
                                                "POST $tr_uri HTTP/1.1",
                                                "Host: $tr_host",
                                                "X-Transmission-Session-Id: $SessionId",
                                                'Connection: Close',
                                                "Content-Length: $reqLen",
                                                'Content-Type: application/json'
                                               ),
                          CURLOPT_POSTFIELDS => "$request"
                   );
    curl_setopt_array($post, $post_options);

    $raw = curl_exec($post);
    curl_close($post);

    if(preg_match('/409: Conflict/', $raw)) {
      unlink($sessionIdFile);
    } else {
      $run = 0;
    }
  }
  return json_decode($raw, TRUE);
}

function get_deep_dir($dest, $tor_name) {
    global $config_values;
    switch($config_values['Settings']['Deep Directories']) {
      case '0':
        break;
      case 'Title':
        $guess = guess_match($tor_name, TRUE);
        if(isset($guess['key'])) {
          $dest = $dest."/".$guess['key'];
          break;
        }
        _debug("Deep Directories: Couldn't match $tor_name Reverting to Full\n", 1);
      case 'Full':
      default:
        $dest = $dest."/".$tor_name;
        break;
    }
    return $dest;
}

function folder_add_torrent($tor, $dest, $title) {
  global $config_values;
  // remove invalid chars
  $title = strtr($title, '/', '_');
  // add the directory and extension
  $dest = "$dest/$title.".$config_values['Settings']['Extension'];
  // save it
  file_put_contents($dest, $tor);
  return 0;
}

function transmission_add_torrent($tor, $dest, $title, $seedRatio = -1) {
  global $config_values;
  // transmission dies with bad folder if it doesn't end in a /
  if(substr($dest, strlen($dest)-1, 1) != '/')
    $dest .= '/';
  $request = array('method' => 'torrent-add',
                   'arguments' => array('download-dir' => $dest,
                                        'metainfo' => base64_encode($tor)
                                       )
                               );
  $response = transmission_rpc($request);
  _debug(json_encode($response),0);
  _debug("\r\n",0);

  $torHash = $response['arguments']['torrent-added']['hashString'];

  if($seedRatio != "" && $seedRatio >= 0 && ($torHash)) {
    $request = array('method' => 'torrent-set',
		     'arguments' => array('hashString' => $torHash,
	 	     'seedRatioLimit' => $seedRatio,
		     'seedRatioMode' => 1)
		    );
    $response = transmission_rpc($request);
    _debug(json_encode($response),0);
    _debug("\r\n",0);
  } 


  if(isset($response['result']) AND ($response['result'] == 'success' or $response['result'] == 'duplicate torrent')) {
    $cache = $config_values['Settings']['Cache Dir'] . "rss_dl_" . filename_encode($title);
    if($torHash) {
      $handle = fopen("$cache", "w");
      fwrite($handle, $torHash);
      fclose($handle);
    }
    return 0;
  } else {
    if(!isset($response['result']))
      return "Failure connecting to Transmission";
    else
      return "Transmission RPC Error: ".print_r($response, TRUE);
  }
}

function client_add_torrent($filename, $dest, $title, &$fav = NULL, $feed = NULL) {
  global $config_values, $hit;
  $hit = 1;
  $filename = htmlspecialchars_decode($filename);

  // Detect and append cookies from the feed url
  $url = $filename;
  if($feed && $cookies = stristr($feed, ':COOKIE:')) {
    $url .= $cookies;
  }

  $be = new BrowserEmulator();
  $be->addHeaderLine("User-Agent", 'Python-urllib/1.17');
  if(!($tor = $be->file_get_contents($url))) {
  print '<pre>'.print_r($_GET, TRUE).'</pre>';
    _debug("Couldn't open torrent: $filename\n",-1);
    return FALSE;
  }
  $tor_info = new BDecode("", $tor);
  if(!($tor_name = $tor_info->{'result'}['info']['name'])) {
    $tor_name = $title;
  }

  if(!isset($dest)) {
    $dest = $config_values['Settings']['Download Dir'];
  }
  if(isset($fav) && $fav['Save In'] != 'Default') {
    $dest = $fav['Save In'];
  } else if($config_values['Settings']['Deep Directories']) {
    $dest = get_deep_dir($dest, $tor_name);
    _debug("Deep Directorys, change dest to $dest\n", 1);
  }
  if(!file_exists($dest) or !is_dir($dest)) {
    if(file_exists($dest))
      unlink($dest);
    mkdir($dest, 0777, TRUE);
  }
  switch($config_values['Settings']['Client']) {
    case 'Transmission':
      $return = transmission_add_torrent($tor, $dest, $title, _isset($fav, 'seedRatio', -1));
      break;
    case 'folder':
      $return = folder_add_torrent($tor, $dest, $tor_name);
      break;
    default:
      _debug("Invalid Torrent Client: ".$config_values['Settings']['Client']."\n",-1);
      exit(1);
  }
  if($return === 0) {
    add_history($tor_name);
    _debug("Started: $tor_name in $dest\n",0);
    if(isset($fav))
      updateFavoriteEpisode($fav, $tor_name);
      _debug("Updated Favorites");
    if($config_values['Settings']['Save Torrents'])
      file_put_contents("$dest/$tor_name.torrent", $tor);
  } else {
    _debug("Failed Starting: $tor_name  Error: $return\n",-1);
  }
  return ($return === 0);
}
?>
