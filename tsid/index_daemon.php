<?php
if ( @$_GET['tsid'] < (time() - 10) ) {
    //no match - redirect
    include('index2.php');
    die();
};
    $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    switch ($lang){
      case "fr":
        //echo "PAGE FR";
        include("index_fr.php");
        break;
      case "it":
        //echo "PAGE IT";
        include("index_it.php");
        break;
      case "en":
        //echo "PAGE EN";
        include("index_en.php");
        break;        
      case "ru":
        //echo "PAGE RU";
        include("index_ru.php");
        break;
      default:
        //echo "PAGE EN - Setting Default";
        include("index_en.php");
        break;
}
?>