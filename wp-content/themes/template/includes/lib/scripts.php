<?php
if ( ! class_exists('TDF_Scripts') ){

  class TDF_Scripts {

    public function __construct() {

      //add_action('template_include',array($this,"start_full_html"));
      //add_action('shutdown',array($this,"get_full_html"));
    }

    function start_full_html(){
      echo "1111";
      ob_start();
    }

    function get_full_html(){
      $cont = ob_get_contents();
      echo $cont;
      $this->merge_js($cont);
      ob_end_clean();
    }

    function merge_js($string){
      $pattern = "/<script[^>]*>(.*)<\/script>/Uis";
      preg_match_all($pattern, $string, $matches);
      print_r($matches);
    }
  }

  new TDF_Scripts;
}
