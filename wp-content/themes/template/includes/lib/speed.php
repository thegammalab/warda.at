<?php
if ( ! class_exists('TDF_Speed') ){

  class TDF_Speed {

    public function __construct($cont) {
      //$cont = $this->process_scripts($cont);
      //$cont = $this->process_styles($cont);




      echo $cont;
    }

    function process_styles($cont){

      $pattern = "/href=['\"](?P<css>([^'\"]+?css)[^'\"]*)/";
      preg_match_all($pattern, $cont, $matches);
      foreach($matches[0] as $item){
        $cont = str_replace($item,"",$cont);
      }

      $styles = $matches[0];
      $local_styles = array();
      $include_styles = array();


      foreach($styles as $f=>$v){
        if(strpos("aaa".$v,get_bloginfo("url"))){
          $local_styles[$f]=substr($v,6);
        }else{
          $include_styles[$f]=substr($v,6);
        }
      }

      $pattern = "/<style[^>]*>(.*)<\/style>/Uis";
      preg_match_all($pattern, $cont, $matches);
      foreach($matches[0] as $item){
        $cont = str_replace($item,"",$cont);
      }
      $inline_styles = ($matches[1]);


      $the_styles ="";

      $cont = str_replace('</head>','</head>',$cont);

      $the_styles = '<style>';
      foreach($inline_styles as $css){
        $the_styles.=$this->minify_css(($css));
      }
      foreach($local_styles as $css){
        $the_styles.=$this->minify_css(file_get_contents($css));
      }
      $the_styles .= '</style>';

      $cont = str_replace('</head>',$the_styles.'</head>',$cont);

      $the_styles = "";
      foreach($include_styles as $css){
        $the_styles.='<link rel="stylesheet" async defer crossorigin="anonymous" href="'.$css.'" type="text/css" />';
      }
      $cont = str_replace('</body>',$the_styles.'</body>',$cont);

      return $cont;
    }

    function process_scripts($cont){
      $pattern = "/<script[^>]*>(.*)<\/script>/Uis";
      preg_match_all($pattern, $cont, $matches);

      $scripts = array();
      $inline_scripts = array();

      foreach($matches[0] as $f=>$item){
        $item_clean = str_replace(" ","",$item);
        if($pos = strpos($item_clean,"src='")){
          $nd = strpos($item_clean,"'",$pos+6);
          if($nd){
            $cont = str_replace($item,"",$cont);

            $scripts[]=substr($item_clean,$pos+5,$nd-$pos-5);
          }
        }elseif($pos = strpos($item_clean,'src="')){
          $nd = strpos($item_clean,'"',$pos+6);
          if($nd){
            $cont = str_replace($item,"",$cont);

            $scripts[]=substr($item_clean,$pos+5,$nd-$pos-5);
          }
        }else{
          if(!strpos($item_clean,"type") || strpos($item_clean,"type")>100 || (strpos($item_clean,"type") && strpos($item_clean,"javascript"))){
            $cont = str_replace($item,"",$cont);
            $inline_scripts[]=$matches[1][$f];
          }
        }
      }

      $cont = str_replace("\r", " ", $cont);
      $cont = str_replace("\n", " ", $cont);

      foreach($scripts as $f=>$v){
        if(strpos("aaa".$v,get_bloginfo("url"))){
          $local_scripts[$f]=$v;
        }else{
          $include_scripts[$f]=$v;
        }
      }

      $the_scripts = "";
      foreach($include_scripts as $script){
        if(strpos($script,"jquery.min.js")){
          $the_scripts.='<script type="text/javascript" src="'.$script.'"></script>';
        }else{
          $the_scripts.='<script style="display:none;" type="text/javascript" src="'.$script.'"></script>';
        }
      }
      $the_scripts .='<script style="display:none;" async defer>';
      foreach($include_scripts as $script){
        //$the_scripts.=$this->minify_js(file_get_contents($script));
      }
      //print_r($local_scripts);
      foreach($local_scripts as $script){
        if(strpos($script,".min")){
          $the_scripts.=(file_get_contents($script)).";";
        }else{
          $the_scripts.=$this->minify_js(file_get_contents($script)).";";
        }
      }
      //print_r($inline_scripts);
      foreach($inline_scripts as $script){
        $the_scripts.=$this->minify_js(($script)).";";
      }
      $the_scripts .= '</script>';

      $cont = str_replace('</body>',$the_scripts.'</body>',$cont);

      return $cont;
    }

    function minify_css($text){
      $text = str_replace("../images/",get_template_directory_uri().'/assets/images/',$text);
      $text = str_replace("../fonts/",get_template_directory_uri().'/assets/fonts/',$text);
      $text = str_replace('"images/','"'.get_template_directory_uri().'/assets/images/',$text);
      $text = str_replace("'images/","'".get_template_directory_uri().'/assets/images/',$text);
      $text = str_replace('\t','',$text);
      while(strpos($text,"  ")){
        $text = str_replace('  ',' ',$text);
      }
      $text = str_replace("\n"," ",$text);
      $text = preg_replace('/\/\*((?!\*\/).)*\*\//', '', $text); // negative look ahead
      $text = preg_replace('/\s{2,}/', ' ', $text);
      $text = preg_replace('/\s*([:;{}])\s*/', '$1', $text);
        $text = preg_replace('/;}/', '}', $text);
        return $text;
      }

      function minify_js($javascript){
        $javascript = str_replace('\t','',$javascript);
        while(strpos($javascript,"  ")){
          $javascript = str_replace('  ',' ',$javascript);
        }
        while($pos = strpos($javascript,"/*")){
          $nd = strpos($javascript,"*/",$pos);
          $javascript = substr($javascript,0,$pos).substr($javascript,$nd+2);
        }
        $lines = explode("\n",$javascript);
        foreach($lines as $f=>$v){
          $lines[$f] = trim($v);
          if(substr($lines[$f],0,2)=="//"){
            unset($lines[$f]);
          }elseif(!$lines[$f]){
            unset($lines[$f]);
          }elseif(substr($lines[$f],strlen($lines[$f])-2)=="})"){
            $lines[$f] = $lines[$f].";";
          }
        }
        $javascript = implode(" ",$lines);
        return $javascript;

      }
    }
  }
