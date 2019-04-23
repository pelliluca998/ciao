<?php

/*
Please read this if you want to encode your blade templates with SourceGuardian.

You may use this script to pre-compile all of your blade templates and generate .php scripts from them.
Compiled templates may be encoded as normal php scripts along with your PHP scripts from app/ and routes/ folders.

Original .blade.php files will be moved to a backup folder after compilation. This is necessary to force Laravel
to use encoded templates instead of original blade files as Laravel always looks for .blade.php at first and only then .php
files in resources/views. Also you probably don't want to keep original .blade.php files in resources/views anyway, as otherwise
there is no reason for encoding them.

MAKE SURE YOU HAVE A BACKUP COPY OF YOUR PROJECT BEFORE RUNNING THE SCRIPT AND BEFORE ENCODING IT WITH SOURCEGUARDIAN


THE SCRIPT IS LICENSED FREE OF CHARGE AND ACCORDINGLY IS PROVIDED ON AN "AS IS" BASIS. THE END USER AGREES AND ACKNOWLEDGES THAT
SOURCEGUARDIAN HAS NO LIABILITY TO THE END USER, WHETHER IN CONTRACT, TORT (INCLUDING NEGLIGENCE) OR OTHERWISE ARISING FROM ANY
DEFECTS IN THE SCRIPT AND ALL WARRANTIES IMPLIED BY THE LAWS OF ANY JURISDICTION IN WHICH THE END USER USES THE SCRIPT ARE EXPRESSLY
EXCUDED TO THE FULLEST EXTENT PERMITTED BY THE LAWS OF SUCH JURISDICTION.

Copyright (C) SourceGuardian, 2017
*/

use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\Filesystem\Filesystem;

/*
Configure source and backup folders for your blade templates
*/

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$modules = ['Contabilita', 'Diocesi', 'Email', 'Famiglia'];


foreach($modules as $module){
  $viewspath = __DIR__.'/Modules/'.$module.'/Resources/views';
  $viewsbackup = __DIR__.'/Modules/'.$module.'/Resources.bak/views';

  $bladeCompiler = new BladeCompiler(new Filesystem(), $viewspath);

  $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewspath), RecursiveIteratorIterator::SELF_FIRST);
  $total = $ok = 0;
  foreach($objects as $bladename => $object){
    if (strstr($bladename, '.blade.php')) {
      $total++;
      echo "$bladename - ";
      try {
        $bladecontents = file_get_contents($bladename);
        $phpcontents = $bladeCompiler->compileString($bladecontents);
        $phpname = str_replace('.blade.php', '.php', $bladename);
        file_put_contents($phpname, $phpcontents);
        $bladebackup = str_replace($viewspath, $viewsbackup, $bladename);
        if (!is_dir(dirname($bladebackup))) mkdir(dirname($bladebackup), 0755, true);
        rename($bladename, $bladebackup);
        echo "OK";
        $ok++;
      }
      catch (Exception $e){
        echo $e->getMessage();
      }
      echo "\n";
    }
  }

  //restore views to original folder
  rename(__DIR__.'/Modules/'.$module.'/Resources/', __DIR__.'/Modules/'.$module.'/Resources.compiled');
  rename(__DIR__.'/Modules/'.$module.'/Resources.bak/', __DIR__.'/Modules/'.$module.'/Resources');

  echo "Total:$total, compiled:$ok, errors:".($total - $ok)."\n";
}
