<?php
// disable any kind of caching
header( "Expires: Mon, 20 Dec 2000 01:00:00 GMT" );
header( "Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
header( "Cache-Control: no-cache, must-revalidate" );
header( "Pragma: no-cache" );

ini_set('include_path', '.:./php');
$test_run = 0;
$firstrun = 0;
$verbosity = 0;

if (get_magic_quotes_gpc()) {
    $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
    while (list($key, $val) = each($process)) {
        foreach ($val as $k => $v) {
            unset($process[$key][$k]);
            if (is_array($v)) {
                $process[$key][stripslashes($k)] = $v;
                $process[] = &$process[$key][stripslashes($k)];
            } else {
                $process[$key][stripslashes($k)] = stripslashes($v);
            }
        }
    }
    unset($process);
}

if(!(file_exists('php/config.php'))) {
	$config = getcwd() . '/php/config.php';
        echo "<div id=\"checkFiles\" class=\"dialog_window\" style=\"display: block\">Please copy $config.dist to $config and edit it to match your environment. Then click your browsers refresh button.</div>";
	return;
}

require_once('rss_dl_utils.php');

// This function parses commands sent from a PC browser
function parse_options() {
	global $html_out, $config_values;
	$filler = "<br>";

	array_keys($_GET);
	$commands = array_keys($_GET);
	if(empty($commands))
		return FALSE;

	if(preg_match("/^\//", $commands[0])) {
		$commands[0] = preg_replace("/^\//", '', $commands[0]);
	}
	switch($commands[0]) {
		case 'firstRun':
            foreach($_GET['link'] as $link) {
				add_feed($link);
			}
			update_global_config();
			$config_values['Settings']['FirstRun'] = FALSE;
			write_config_file();
			break;
		case 'getClientData':
			if($_REQUEST['recent']) {
				$response = getClientData(1);
			} else {
				$response = getClientData(0);
			}
			echo $response;
			exit;
		case 'getHash':
			$response = torInfo($_REQUEST['getHash']);
			echo json_encode($response);
			exit;
		case 'delTorrent':
		        $response = delTorrent($_REQUEST['delTorrent'], $_REQUEST['trash']);
			echo "$response";
			exit;
		case 'stopTorrent':
		        $response = stopTorrent($_REQUEST['stopTorrent']);
			echo "$response";
			exit;
		case 'startTorrent':
		        $response = startTorrent($_REQUEST['startTorrent']);
			echo "$response";
			exit;
		case 'moveTo':
		        $response = moveTorrent($_REQUEST['moveTo'], $_REQUEST['torHash']);
			echo "$response";
			exit;
		case 'updateFavorite':
			update_favorite();
			break;
		case 'updateFeed':
			update_feed();
			break;
		case 'clearCache':
			clear_cache();
			break;
		case 'setGlobals':
			update_global_config();
			$config_values['Settings']['FirstRun'] = FALSE;
			write_config_file();
			break;
		case 'matchTitle':
		    $feedLink = $_GET['rss'];
		    foreach($config_values['Feeds'] as $key => $feed) {
		        if($feed['Link'] == "$feedLink") $idx = $key;
		    }
    		if($config_values['Feeds'][$idx]['seedRatio']) {
                $seedRatio = $config_values['Feeds'][$idx]['seedRatio'];
            } else {
                $seedRatio = $config_values['Settings']['Default Seed Ratio'];
            }
		
		    if(!($seedRatio)) $seedRatio = -1;
			if(($tmp = guess_match(html_entity_decode($_GET['title'])))) {
				$_GET['name'] = trim(strtr($tmp['key'], "._", "  "));
				$_GET['filter'] = trim($tmp['key']);
				if($config_values['Settings']['MatchStyle'] == "glob")
					$_GET['filter'] .= '*';
				$_GET['quality'] = $tmp['data'];
				$_GET['feed'] = $_GET['rss'];
				$_GET['button'] = 'Add';
				$_GET['savein'] = 'Default';
				$_GET['seedratio'] = $seedRatio;
			} else {
				$_GET['name'] = $_GET['title'];
				$_GET['filter'] = $_GET['title'];
				$_GET['quality'] = 'All';
				$_GET['feed'] = $_GET['rss'];
				$_GET['button'] = 'Add';
				$_GET['savein'] = 'Default';
				$_GET['seedratio'] = $seedRatio;
			}
			update_favorite();
			break;
		case 'dlTorrent':
			// Loaded via ajax
			$r = client_add_torrent(trim($_GET['link']),
				$config_values['Settings']['Download Dir'],
			    $_GET['title'], $_GET['feed']);

                if($r) { $torHash = get_torHash(add_cache($_GET['title'])); }
			echo $torHash;
			exit(0);
			break;
		case 'clearHistory':
			// Loaded via ajax
			if(file_exists($config_values['Settings']['History']))
				unlink($config_values['Settings']['History']);
			display_history();
			close_html();
			exit(0);
			break;
	    case 'get_tr_location':
            global $config_values;
            echo $config_values['Settings']['Transmission Host'] . ':' . $config_values['Settings']['Transmission Port'];
            exit;
        case 'get_client':
            global $config_values;
            echo $config_values['Settings']['Client'];
            exit;
		default:
			$output = "<script type='text/javascript'>alert('Bad Paramaters passed to ".$_SERVER['PHP_SELF'].":  ".$_SERVER['REQUEST_URI']."');</script>";
	}

	if(isset($exec))
		exec($exec, $output);
	if (isset($output)) {
		if(is_array($output))
			$output = implode($filler, $output);
		$html_out .= str_replace("\n", "<br>", "<div class='execoutput'>$output</div>");
		echo $html_out;
		$html_out = "";
	}
	return;
}

function torInfo($torHash) {
	global $config_values;

	switch($config_values['Settings']['Client']) {
		case 'Transmission':
			$request = array('arguments' => array('fields' => array('id', 'leftUntilDone', 'hashString',
		      		'totalSize', 'uploadedEver', 'downloadedEver', 'status', 'peersSendingToUs', 'peersGettingFromUs', 'peersConnected'), 'ids' => $torHash), 'method' => 'torrent-get');
                $response = transmission_rpc($request);
                $totalSize = $response['arguments']['torrents']['0']['totalSize'];
                $leftUntilDone = $response['arguments']['torrents']['0']['leftUntilDone'];
                $Uploaded = $response['arguments']['torrents']['0']['uploadedEver'];
                $Downloaded = $response['arguments']['torrents']['0']['downloadedEver'];
                if($totalSize) { 
                  $percentage = round((($totalSize-$leftUntilDone)/$totalSize)*100,2);
                }
                if($percentage < 100) { $dlStatus = "downloading"; }
                if(!($totalSize)) {
                  return array( 'dlStatus' => 'old_download' );
                } else {
                  if(!($Downloaded) || !($Uploaded)) {
                    $ratio = 0;
                } else {
                    $ratio = $Uploaded/$Downloaded;
                    $ratio = round($ratio, 2);
                }
                $bytesDone = $totalSize-$leftUntilDone;
                $sizeDone = human_readable($totalSize-$leftUntilDone);
                $totalSize = human_readable($totalSize);
                $clientId = $response['arguments']['torrents']['0']['id'];
                $status = $response['arguments']['torrents']['0']['status'];
                $peersSendingToUs = $response['arguments']['torrents']['0']['peersSendingToUs'];
                $peersGettingFromUs = $response['arguments']['torrents']['0']['peersGettingFromUs'];
                $peersConnected = $response['arguments']['torrents']['0']['peersConnected'];
                if($status == 1) {
                    $stats = "Waiting for peers";
                } else if($status == 2) {
                    $stats = "Verifying files ($percentage%)";
                } else if($status == 4) {
                    $stats = "Downloading from $peersSendingToUs of $peersConnected peers:
                      $sizeDone of $totalSize ($percentage%)  -  Ratio: $ratio";
                } else if($status == 8) {
                    $stats = "Seeding to $peersGettingFromUs of $peersConnected peers  -  Ratio: $ratio";
                } else if($status == 16) {
                    $stats = "Paused";
                }
                return array( 
                    'stats' => $stats,
                    'clientId' => $clientId,
                    'status' => $status,
                    'bytesDone' => $bytesDone
                );
            }
			exit;
	}
}

function getClientData($recent) {
	global $config_values;

	switch($config_values['Settings']['Client']) {	
		case 'Transmission':
			if($recent) {
			  $request = array('arguments' => array('fields' => array('id', 'name', 'status', 'errorString', 'hashString', 'leftUntilDone', 'downloadDir', 'totalSize', 'uploadedEver', 'downloadedEver', 'addedDate', 'status', 'peersSendingToUs', 'peersGettingFromUs', 'peersConnected'), 'ids' => 'recently-active'), 'method' => 'torrent-get');
			} else {
			  $request = array('arguments' => array('fields' => array('id', 'name', 'status', 'errorString', 'hashString', 'leftUntilDone', 'downloadDir',
		          'totalSize', 'uploadedEver', 'downloadedEver', 'addedDate', 'status', 'peersSendingToUs', 'peersGettingFromUs', 'peersConnected')), 'method' => 'torrent-get');
			}
			$response = transmission_rpc($request);
			return json_encode($response);
		break;
	}
}

function delTorrent($torHash, $trash) {
	global $config_values;

	switch($config_values['Settings']['Client']) {	
		case 'Transmission':
			$request = array('arguments' => array('delete-local-data' => $trash, 'ids' => $torHash), 'method' => 'torrent-remove');
			$response = transmission_rpc($request);
			return json_encode($response);
		break;
	}
}

function stopTorrent($torHash) {
	global $config_values;

	switch($config_values['Settings']['Client']) {	
		case 'Transmission':
			$request = array('arguments' => array('ids' => $torHash), 'method' => 'torrent-stop');
			$response = transmission_rpc($request);
			return json_encode($response);
		break;
	}
}

function startTorrent($torHash) {
	global $config_values;

	switch($config_values['Settings']['Client']) {	
		case 'Transmission':
			$request = array('arguments' => array('ids' => $torHash), 'method' => 'torrent-start');
			$response = transmission_rpc($request);
			return json_encode($response);
		break;
	}
}

function moveTorrent($location, $torHash) {
	global $config_values;

	switch($config_values['Settings']['Client']) {	
		case 'Transmission':
		    $torInfo = torInfo($torHash);
		    if($torInfo['bytesDone'] > 0) {
		        $move = true;
	        } else {
	            $move = false;
            }
        	$request = array('arguments' => array('location' => $location, 'move' => $move, 'ids' => $torHash), 'method' => 'torrent-set-location');
			$response = transmission_rpc($request);
			return json_encode($response);
		break;
	}
}

function display_global_config() {
	global $config_values, $html_out;

	$savetorrent=$transmission="";
	$deepfull=$deeptitle=$deepoff=$verifyepisode="";
	$matchregexp=$matchglob=$matchsimple="";
	$onlynewer=$folderclient=$combinefeeds="";

	switch($config_values['Settings']['Client']) {
		case 'Transmission':
			$transmission = 'selected="selected"';
			break;
                case 'folder':
                        $folderclient = 'selected="selected"';
                        break;
		default:
			// Shouldn't happen
			break;
	}
	if($config_values['Settings']['Combine Feeds'] == 1)
	    $combinefeeds = 'checked=1';

	if($config_values['Settings']['Save Torrents'] == 1)
		$savetorrent = 'checked=1';

	switch($config_values['Settings']['Deep Directories']) {
		case 'Full': $deepfull = 'selected="selected"';break;
		case 'Title': $deeptitle = 'selected="selected"'; break;
		default:$deepoff = 'selected="selected"';break;
	}

	if($config_values['Settings']['Verify Episode'] == 1)
		$verifyepisode = 'checked=1';
	if($config_values['Settings']['Only Newer'] == 1)
		$onlynewer = 'checked=1';

	switch($config_values['Settings']['MatchStyle']) {
		case 'glob': $matchglob="selected='selected'";break;
		case 'simple': $matchsimple="selected='selected'";break;
		case 'regexp': 
		default: $matchregexp="selected='selected'";break;
	}

	if(!($config_values['Settings']['FirstRun'])) {
		// Include the templates and append the results to html_out
		ob_start();
		require('templates/global_config.tpl');
		require('templates/feeds.tpl');
		$html_out .= ob_get_contents();
		ob_end_clean();
	}
}


function display_favorites_info($item, $key) {
	global $config_values, $html_out;
	$feed_options = '<option value="all">All</option>';
	if(isset($config_values['Feeds'])) {
		foreach($config_values['Feeds'] as $feed) {
			$feed_options .= '<option value="'.urlencode($feed['Link']).'"';
			if($feed['Link'] == $item['Feed'])
				$feed_options .= ' selected="selected"';
			$feed_options .= '>'.$feed['Name'].'</option>';
		}
	}

  // Dont handle with object buffer, is called inside display_favorites ob_start
  require('templates/favorites_info.tpl');
}

function display_favorites() {
	global $config_values, $html_out;

  ob_start();
  require('templates/favorites.tpl');
  $html_out .= ob_get_contents();
  ob_end_clean();
}

function display_history() {
	global $html_out, $config_values;

	if(file_exists($config_values['Settings']['History'])) {
		$history = array_reverse(unserialize(file_get_contents($config_values['Settings']['History'])));
  } else {
    $history = array();
  }

  ob_start();
  require('templates/history.tpl');
  $html_out .= ob_get_contents();
  ob_end_clean();
}

function display_legend() {
	global $html_out;

	ob_start();
	require('templates/legend.tpl');
	$html_out .= ob_get_contents();
	ob_end_clean();
}

function display_clearCache() {
	global $html_out;

	ob_start();
	require('templates/clear_cache.tpl');
	$html_out .= ob_get_contents();
	ob_end_clean();
}

function close_html() {
	global $html_out, $debug_output, $main_timer;
	echo $html_out;
	$html_out = "";
}

function check_files() {
    global $config_values;

    $myuid = posix_getuid();
    $configDir = platform_getConfigDir() . '/';
    if(!is_writable($configDir)) {
	echo "<div id=\"checkFiles\" class=\"dialog_window\" style=\"display: block\">Please create the directory $configDir and make sure it's readable and writeable for the user running the webserver (uid: $myuid). </div>";
    }
    $cwd = getcwd();
    if(!(get_base_dir() == $cwd)) {
        echo "<div id=\"checkFiles\" class=\"dialog_window\" style=\"display: block\">Please edit the config.php file and set the basedir to:<br /> \"$cwd\".<br />Then click your browsers refresh button.</div>";
	return;
    }
	

    if($config_values['Settings']['FirstRun']) return 0;

    $toCheck = array('cache_dir' => $config_values['Settings']['Cache Dir'],
                    'download_dir' => $config_values['Settings']['Download Dir'],
                    );
                    
    $deepDir = $config_values['Settings']['Deep Directories'];
    
    $error = false;
    foreach ($toCheck as $key => $file) {
        if(!(file_exists($file))) $error .= "$key:&nbsp;<i>\"$file\"</i>&nbsp;&nbsp;does not exist <br />";
        if(!($deepDir) && $key == 'download_dir') break;
        if(!(is_writable($file))) $error .= "$key:&nbsp;<i>\"$file\"</i>&nbsp;&nbsp;is not writable for uid: $myuid <br />";
        if(!(is_readable($file))) $error .= "$key:&nbsp;<i>\"$file\"</i>&nbsp;&nbsp;is not readable for uid: $myuid <br />";
    }
    
    if($error) {
        echo "<div id=\"checkFiles\" class=\"dialog_window\" style=\"display: block\">$error</div>";
    }
}


//
//
// MAIN Function
//
//
$main_timer = timer_init();
platform_initialize();

setup_default_config();
if(file_exists(platform_getConfigFile()))
	read_config_file();

$config_values['Global']['HTMLOutput']= 1;
$html_out = "";
$debug_output = "Torrentwatch Debug:";
$verbosity = 0;

parse_options();
display_global_config();
check_files();
display_favorites();
display_history();
display_clearCache();
display_legend();

echo $html_out;
$html_out = "";
ob_flush();flush();

// Feeds
if(isset($config_values['Feeds'])) {
	load_feeds($config_values['Feeds']);
	feeds_perform_matching($config_values['Feeds']);
}

close_html();
unlink_temp_files();

exit(0);

?>

