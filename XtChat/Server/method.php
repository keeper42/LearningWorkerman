<?php

function logs($log){
    return iconv('UTF-8', 'GB2312', $log);
}
function filter($str,$isSingle = true,$isHtml=true,$isTags=false){
    if(is_empty($str)){
        return $str;
    }
    if($isTags){
        $str = strip_tags(filter_decode($str));
    }else{
        if($isHtml){
            $str = htmlspecialchars($str);
        }
        $str = addslashes($str);
        if($isSingle){
            $str = preg_replace("/'/","''",$str);
        }else{
            $str = str_replace("'","&single;",$str);
        }
    }
    return $str;
}
function is_empty($string){
    if(is_null($string)){
        return true;
    }
    if(is_string($string)){
        if(strlen($string)==0){
            return true;
        }
    }

    if($string===0||$string==0){
        return false;
    }

    if($string===true||$string===false){
        return false;
    }
    if(is_string($string)){
        if(strlen(trim($string))==0){
            return true;
        }
        if(trim($string)==''){
            return true;
        }
    }
    if(empty($string)){
        return true;
    }
    return false;
}
