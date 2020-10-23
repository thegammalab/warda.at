<?php
if ( ! class_exists('TDF_Emails') ){

  class TDF_Emails {

    public function __construct() {
      add_filter( 'tdf_email_test', array( $this, 'email_test' ), 10, 2);
      add_filter( 'tdf_email_test_admin', array( $this, 'email_test_admin' ), 10, 2);
    }

    function email_test($txt,$args){
      return "custom: ".serialize($args).$txt;
    }
    function email_test_admin($txt,$args){
      return "admin custom: ".serialize($args).$txt;
    }
  }

  new TDF_Emails;
}
