<?php
$host = "www.your-target-site.com";
$port = 80;
$data_file = "data.txt";

set_time_limit(0);

$files = load_data($data_file);
get_all($files);

function get_all($files = array())
{
   if (is_array($files) && count($files) > 0)
   {
       foreach ($files as $id => $file)
       {
          print "fetching $id: $file\n";
          if (get_single($file)==true) sleep(2);
          print "\n";
       }
   }
}

function get_single($file)
{
   global $host,$port;

   $state = false;

   if (file_exists("data/$file"))
   {
       if (filesize("data/$file") > 0) {
           return $state;
       }
   }
   $fs = fsockopen($host,$port,$errno,$errstr,60);
   fputs($fs,"GET /files/videos/videos/$file HTTP/1.1\r\nHost: www.your-target-site.com\r\n\r\n");
   if (!feof($fs))
   {
      $outfp = fopen("data/$file","w+b");
      if ($outfp == null) { die ("unable to create new file\n"); }
      $size = 0;
      while (!feof($fs))
      {
          $data = fgets($fs,1024*4);
          $size += strlen($data);
          fputs($outfp,$data);
          print number_format($size) . " bytes written\r";
      }
      fclose ($outfp);

      $state = true;
   }
   fclose ($fs);

   return $state;
}

function load_data($data_file = "data.txt")
{
   $files = array();
   $fp = fopen($data_file,"r+t");
   if ($fp == null) {
       die("unable to open $data_file\n");
   }
   while (!feof($fp))
   {
       $line = trim(fgets($fp,1024));
       if (preg_match("/^\w+\.\w+\s/sm",$line,$map)) {
           $files[] = $map[0];
       }
   }
   fclose ($fp);
   return $files;
}
?>
