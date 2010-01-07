#!/usr/bin/php-cgi -q
<?php
// rss_dl.php
// By Erik Bernhardson
//
// This program is a command line interface to torrentwatch
// 

ini_set('include_path', '.:'.dirname(realpath($_SERVER['argv'][0])).'/php');
ini_set("precision", 4);
   
// These are our extra functions
require_once('rss_dl_utils.php');

$config_values;
$test_run = 0;
$verbosity = 0;
$func_timer = 0;

function usage() {
	__debug( $_SERVER['argv'][0] . "<options> - CLI Interface to Torrent Watch\n",0);
	__debug( "           -c <dir> : Enable Cache\n",0);
	__debug( "           -C : Disable Cache\n",0);
	__debug( "           -d : skip watch folder\n",0);
	__debug( "           -D : Start torrents in watch folder\n",0);
	__debug( "           -h : show this help\n",0);
	__debug( "           -nv: not verbose (default)\n",0);
	__debug( "           -q : quiet (no output)\n",0);
	__debug( "           -v : verbose output\n",0);
	__debug( "           -vv: verbose output(even more)\n",0);
	__debug( "    Note: This interface only writes to the config file when using the -i option\n",0);
}

function __debug($string, $lvl = 1) {
  global $config_values, $verbosity, $debug_output;
  file_put_contents('/tmp/tw-rss_dl.log', $string, FILE_APPEND);

  if($verbosity >= $lvl) {
      echo($string);
  }
}

function parse_args() {
	global $config_values, $argc, $argv, $test_run, $verbosity;
	for($i=1;$i<$argc;$i++) {
		switch( $_SERVER['argv'][$i]) {
			case '-c':
				$i++;
				$config_values['Settings']['Cache Dir'] =  $_SERVER['argv'][$i];
				break;
			case '-C':
				unset($config_values['Settings']['Cache Dir']);
				break;
			case '-d':
				$config_values['Settings']['Run Torrentwatch'] = 0;
				break;
			case '-D':
				$config_values['Settings']['Run Torrentwatch'] = 1;
				break;
			case '-h':
				usage();
				exit(1);
			case '-nv':
				$verbosity = 0;
				break;
			case '-q':
				$verbosity = -1;
				break;
			case '-t':
				$test_run = 1;
				break;
			case '-v':
				$verbosity = 1;
				break;
			case '-vv':
				$verbosity = 2;
				break;
			default:
				__debug("Unknown command line argument:  " . $_SERVER['argv'][$i] . "\n",0);
				break;
		}
	}
}

//
// Begin Main Function
//
//

	$main_timer = timer_init();
	if(file_exists(platform_getConfigFile()))
		read_config_file();
	else
		setup_default_config();

	if(isset($config_values['Settings']['Verbose']))
		$verbosity = $config_values['Settings']['Verbose'];
	parse_args();
	__debug(date("F j, Y, g:i a")."\n",0);

	if(isset($config_values['Feeds'])) {
		load_feeds($config_values['Feeds']);
		feeds_perform_matching($config_values['Feeds']);
	}

	if(_isset($config_values['Settings'], 'Run Torrentwatch', FALSE) and !$test_run) {
		global $hit;
		$hit = 0;
		check_for_torrents($config_values['Settings']['Watch Dir'], $config_values['Settings']['Download Dir']);
		if(!$hit)
			__debug("No New Torrents to add from watch folder\n", 0);
	} else {
		__debug("Skipping Watch Folder\n");
	}

	unlink_temp_files();

	__debug($func_timer."s\n",0);

	__debug(timer_get_time($main_timer)."s\n",0);
?>
