<?php declare(strict_types=1);
/**
 * frontend link
 *
 * @package ls_search
 */

/** @global \JTL\Smarty\JTLSmarty $smarty */
/** @global JTL\Plugin\PluginInterface $plugin */

// $smarty->assign('ls_search_var', 'Hallo Welt!')
//     ->assign('exampleConfigVars', $plugin->getConfig());
session_start();
$_SESSION['tempids'] = "";
$_SESSION['empty'] = false;
if($_POST['ids']){
    $_SESSION['cat_ids'] = $_POST['ids'];

    if($_SESSION['tempids'] != $_POST['ids']){
        $_SESSION['empty'] = true;
        $_SESSION['tempids'] = $_SESSION['cat_ids'];
    }else{
        $_SESSION['empty'] = false;
    }
}

if($_POST['key']){
    $_SESSION['key'] = $_POST['key']; 
}else{
    if($_SESSION['empty'] ){
        $_SESSION['key'] = "" ;
    }
}
if($_POST['ptype']){
    $_SESSION['type'] = $_POST['ptype']; 
}else{
    if($_SESSION['empty'] ){
        $_SESSION['type'] = "" ;
    }
}


if($_POST['headerDesc']){
    $_SESSION['headerDesc'] = base64_decode($_POST['headerDesc']);  
}else{
    if($_SESSION['empty'] ){
        $_SESSION['headerDesc'] = "";  
    }
}

if($_POST['hinweis']){
    $_SESSION['hinweis'] = base64_decode($_POST['hinweis']);  
}else{
    if($_SESSION['empty'] ){
        $_SESSION['hinweis'] = "";  
    }
}

if($_POST['img']){
    $_SESSION['img'] = $_POST['img'];  
}else{
    if($_SESSION['empty'] ){
        $_SESSION['img'] = "";  
    }
}
if($_POST['parentCat']){
    $_SESSION['parentCat'] = $_POST['parentCat'];  
}else{
    if($_SESSION['empty'] ){
        $_SESSION['parentCat'] = "";  
    }
}


/*--------------from url */
// if(empty($_SESSION['cat_ids'])){
    if(isset($_GET['sqs']) && !empty(trim($_GET['sqs']))){
        $_SESSION['cat_ids'] = base64_decode($_GET['sqs']);
        $_SESSION['key'] = base64_decode($_GET['k']);
        $_SESSION['type'] = $_GET['p'];
        $_SESSION['ids'] = base64_decode($_GET['sqs']);
    }
// }





