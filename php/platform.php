<?php

if(is_dir("/Library/WebServer"))
  $platform = "OSX";
else
  $platform = "Linux";

function platform_initialize() {
  global $platform;
}

function platform_getGunzip() {
  // not really a valid reponce, but browserEmulator willtry the gzinflate function first
  if(function_exists('gzinflate'))
    return 'gzinflate';

  global $platform;
  switch($platform) {
    case 'NMT':
      if(file_exists('/bin/gunzip'))
        return "/bin/gunzip";
      else if(file_exists('/bin/busybox')) {
        exec('/bin/busybox gunzip 2>&1', $output);
        if($output[0] == 'busybox: applet not found')
          return FALSE;
        else
          return "/bin/busybox gunzip";
      }
      return FALSE;
    case 'Linux':
    default:
      if(file_exists('/bin/gunzip'))
        return "/bin/gunzip";
      return FALSE;
  }
}

function platform_getDownloadDir() {
  global $platform;
  switch($platform) {
    case 'Linux':
    default:
      return "~/Download";
      break;
  }
}
