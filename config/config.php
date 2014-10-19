<?php
  define('DB_ADAPTER', 'mysql'); 
  define('DB_HOST', '127.0.0.1');
  define('DB_USER', 'root'); 
  define('DB_PASS', '689812');
  define('DB_NAME', 'lygoffice');
  define('DB_PERSIST', false);
  define('TABLE_PREFIX', 'og_'); 
  define('DB_ENGINE', 'InnoDB');
  $url='http://'.$_SERVER['HTTP_HOST'].substr($PHP_SELF,0,strrpos($PHP_SELF,'/'));

  define('ROOT_URL',$url. '/fengoffice');
  define('DEFAULT_LOCALIZATION', 'en_us'); 
  define('COOKIE_PATH', '/');
  define('DEBUG', false);
  define('SEED', 'a070692dceabf26c15e263d74d0acf52');
  define('DB_CHARSET', 'utf8'); 
  return true;
?>