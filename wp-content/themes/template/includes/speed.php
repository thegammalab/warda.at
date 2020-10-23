<?php
if ( ! class_exists('TDF_Speed') ){

  class TDF_Speed {

    public function __construct($cont) {
      $cont = trim(preg_replace('/\s\s+/', ' ', $cont));
      $cont = str_replace("\r", "", $cont);
      $cont = str_replace("\n", "", $cont);

      $cont = $this->process_styles($cont);
      $cont = $this->process_scripts($cont);

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


      $the_styles = '<style>';
      foreach($inline_styles as $css){
        $the_styles.=$this->minify_css(($css));
      }
      foreach($local_styles as $css){
        $the_styles.=$this->minify_css(str_replace("../",get_template_directory_uri().'/assets/',file_get_contents($css)));
      }
      $the_styles .= '</style>';

      $cont = str_replace('<head>',$the_styles.'</head>',$cont);

      $the_styles = "";
      foreach($include_styles as $css){
        $the_styles.='<link href="'.$css.'" type="text/css" />';
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
        $cont = str_replace($item,"",$cont);
        $item = str_replace(" ","",$item);
        if($pos = strpos($item,"src='")){
          $nd = strpos($item,"'",$pos+6);
          if($nd){
            $scripts[]=substr($item,$pos+5,$nd-$pos-5);
          }
        }elseif($pos = strpos($item,'src="')){
          $nd = strpos($item,'"',$pos+6);
          if($nd){
            $scripts[]=substr($item,$pos+5,$nd-$pos-5);
          }
        }else{
          $inline_scripts[]=$matches[1][$f];
        }
      }

      foreach($scripts as $f=>$v){
        if(strpos("aaa".$v,get_bloginfo("url"))){
          $local_scripts[$f]=$v;
        }else{
          $include_scripts[$f]=$v;
        }
      }

      $the_scripts = "";
      foreach($include_scripts as $script){
        $the_scripts.='<script type="text/javascript" src="'.$script.'"></script>';
      }
      $the_scripts .="<script>";
      foreach($include_scripts as $script){
        //$the_scripts.=$this->minify_js(file_get_contents($script));
      }
      //print_r($local_scripts);
      foreach($local_scripts as $script){
        $the_scripts.=$this->minify_js(file_get_contents($script))."\n";
      }

      foreach($inline_scripts as $script){
        $the_scripts.=$this->minify_js(($script));
      }
      $the_scripts .= '</script>';

      $cont = str_replace('</body>',$the_scripts.'</body>',$cont);

      return $cont;
    }

    function minify_css($text){
      $text = preg_replace('/\/\*((?!\*\/).)*\*\//', '', $text); // negative look ahead
      $text = preg_replace('/\s{2,}/', ' ', $text);
      $text = preg_replace('/\s*([:;{}])\s*/', '$1', $text);
        $text = preg_replace('/;}/', '}', $text);
        return $text;
      }

      function minify_js($javascript){
        $output = preg_replace(array("/\s+\n/", "/\n\s+/", "/ +/"), array("\n", "\n ", " "), $javascript);
        $pattern = '/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\')\/\/.*))/';
        $output = preg_replace($pattern, '', $output);
        return $output;

      }



    }
  }
