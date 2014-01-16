<?php
/*
Plugin Name: Advanced Custom Fields: Sortable
Description: Custom Sortable boxes
Version: 1.0.0
Author: Ionut Staicu
Author URI: http://www.iamntz.com/
License: GPL
Copyright: Ionut Staicu
*/


add_action('acf/register_fields', function(){
  include_once('sortable.php');
});
