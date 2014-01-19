<?php
class ntz_acf_sortable extends acf_field {
  var $settings;

  function __construct(){
    Mustache_Autoloader::register();
    // vars
    $this->name     = 'ntz_sortable';
    $this->label    = __("Sortable Boxes",'ntz');
    $this->category = __("Content",'ntz');

    $this->defaults = array(
      'preview_size' => 'thumbnail',
      'library'      => 'all'
    );


    $this->l10n = array(
      'select'     => __("Add Image to Gallery",'ntz'),
      'edit'       => __("Edit Image",'ntz'),
      'update'     => __("Update Image",'ntz'),
      'uploadedTo' => __("uploaded to this post",'ntz'),
      'count_0'    => __("No images selected",'ntz'),
      'count_1'    => __("1 image selected",'ntz'),
      'count_2'    => __("%d images selected",'ntz'),
    );

    // lang
    load_textdomain('acf', dirname(__FILE__) . '/lang/acf-sortable-' . get_locale() . '.mo');

    parent::__construct();

    $this->settings = array(
      'path'    => apply_filters('acf/helpers/get_path', __FILE__),
      'dir'     => apply_filters('acf/helpers/get_dir', __FILE__),
      'version' => '1.1.0'
    );
  }


  protected function tpl_helper( $template, $content = array() ){
    $path = $this->settings['path'];
    $options = array(
      'template_class_prefix'  => '__cache',
      'cache'                  => $path . '/views/cache',
      'cache_file_mode'        => 0666,
      'cache_lambda_templates' => true,
      'loader'                 => new Mustache_Loader_FilesystemLoader( $path . '/views' ),
      'partials_loader'        => new Mustache_Loader_FilesystemLoader( $path . '/views/partials' ),
      'helpers' => array(),
      'escape'                 => function($value) {
        return htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
      },
      'charset'          => 'UTF-8',
      'logger'           => new Mustache_Logger_StreamLogger('php://stderr'),
      'strict_callables' => true,
    );

    $mustache = new Mustache_Engine( $options );

    $tpl = $mustache->loadTemplate( $template );
    return $tpl->render( $content );
  } // tpl_helper


  public function input_admin_enqueue_scripts(){
    wp_register_script( 'ntz-media', $this->settings['dir'] . 'js/ntz-media.js', array( "media-views" ), $this->settings['version'] );
    wp_register_script( 'ntz-sortable', $this->settings['dir'] . 'js/sortable.js', array( 'ntz-media', 'jquery-ui-sortable' ), $this->settings['version'] );
    wp_register_style( 'ntz-sortable', $this->settings['dir'] . 'css/sortable.css', array(), $this->settings['version'] );

    wp_enqueue_script(array(
      'ntz-sortable',
    ));

    wp_enqueue_style(array(
      'ntz-sortable',
    ));
  }


  /*
  *  create_field()
  *
  *  Create the HTML interface for your field
  *
  *  @param $field - an array holding all the field's data
  *
  *  @type  action
  *  @since 3.6
  *  @date  23/01/13
  */

  function create_field( $field ){

    $field_template = apply_filters( 'ntz-acf-template/sortable/create-field', 'create-field' );
    $content = array(
      "field_name" => $field['name'],
      "items" => $field['value'],
      "tpl" => array(
        "item" => file_get_contents( $this->settings['path'] . '/views/partials/sortable-item.mustache' )
      )
    );

    echo $this->tpl_helper( $field_template, $content );
  }


  /*
  *  format_value()
  *
  *  This filter is appied to the $value after it is loaded from the db and before it is passed to the create_field action
  *
  *  @type  filter
  *  @since 3.6
  *  @date  23/01/13
  *
  *  @param $value  - the value which was loaded from the database
  *  @param $post_id - the $post_id from which the value was loaded
  *  @param $field  - the field array holding all the field options
  *
  *  @return  $value  - the modified value
  */

  function format_value( $value, $post_id, $field ){
    $new_value = array();

    if( empty($value) ){
      return $value;
    }


    foreach( $value['image'] as $key => $image_id ){
      $image_size_1 = $image_size_2 = $image_size_3 = $image_size_4 = null;

      if( !empty( $image_id ) ){
        $image_size_1 = wp_get_attachment_image_src( $image_id, 'column-layout-1' );
        $image_size_1 = $image_size_1[0];

        $image_size_2 = wp_get_attachment_image_src( $image_id, 'column-layout-2' );
        $image_size_2 = $image_size_2[0];

        $image_size_3 = wp_get_attachment_image_src( $image_id, 'column-layout-3' );
        $image_size_3 = $image_size_3[0];

        $image_size_4 = wp_get_attachment_image_src( $image_id, 'column-layout-4' );
        $image_size_4 = $image_size_4[0];

      }

      $new_value[] = array(
        "image_size_1"       => $image_size_1,
        "image_size_2"       => $image_size_2,
        "image_size_3"       => $image_size_3,
        "image_size_4"       => $image_size_4,
        "colspan"            => $value['colspan'][$key],
        "url"                => $value['external_url'][$key],
        "image_id"           => $image_id,

        "open_in_new_window" => selected( $value['open_in_new_window'][$key], 1, false ),
        "colspan_1_selected" => selected( $value['colspan'][$key], 1, false ),
        "colspan_2_selected" => selected( $value['colspan'][$key], 2, false ),
        "colspan_3_selected" => selected( $value['colspan'][$key], 3, false ),
        "colspan_4_selected" => selected( $value['colspan'][$key], 4, false ),
      );
    }

    return $new_value;
  }
}

new ntz_acf_sortable();
