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
    wp_register_script( 'ntz-sortable', $this->settings['dir'] . 'js/sortable.js', array( 'jquery-ui-sortable' ), $this->settings['version'] );
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
      "items" => array(

      ),
      "tpl" => array(
        "item" => file_get_contents( $this->settings['path'] . '/views/partials/sortable-item.mustache' )
      )
    );

    echo $this->tpl_helper( $field_template, $content );
  }


  /*
  *  create_options()
  *
  *  Create extra options for your field. This is rendered when editing a field.
  *  The value of $field['name'] can be used (like bellow) to save extra data to the $field
  *
  *  @type  action
  *  @since 3.6
  *  @date  23/01/13
  *
  *  @param $field  - an array holding all the field's data
  */

  function create_options( $field )
  {
    // vars
    $key = $field['name'];
    // echo $this->tpl_helper( 'create-options', array(

    // ) );
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

  function format_value( $value, $post_id, $field )
  {
    $new_value = array();


    // empty?
    if( empty($value) )
    {
      return $value;
    }


    // find attachments (DISTINCT POSTS)
    $attachments = get_posts(array(
      'post_type' => 'attachment',
      'numberposts' => -1,
      'post_status' => null,
      'post__in' => $value,
    ));

    $ordered_attachments = array();
    foreach( $attachments as $attachment)
    {
      // create array to hold value data
      $ordered_attachments[ $attachment->ID ] = array(
        'id'      =>  $attachment->ID,
        'alt'     =>  get_post_meta($attachment->ID, '_wp_attachment_image_alt', true),
        'title'     =>  $attachment->post_title,
        'caption'   =>  $attachment->post_excerpt,
        'description' =>  $attachment->post_content,
        'mime_type'   =>  $attachment->post_mime_type,
      );

    }


    // override value array with attachments
    foreach( $value as $v)
    {
      if( isset($ordered_attachments[ $v ]) )
      {
        $new_value[] = $ordered_attachments[ $v ];
      }
    }


    // return value
    return $new_value;
  }


  /*
  *  format_value_for_api()
  *
  *  This filter is appied to the $value after it is loaded from the db and before it is passed back to the api functions such as the_field
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

  function format_value_for_api( $value, $post_id, $field )
  {
    $value = $this->format_value( $value, $post_id, $field );

    // find all image sizes
    $image_sizes = get_intermediate_image_sizes();


    if( $value )
    {
      foreach( $value as $k => $v )
      {
        if( strpos($v['mime_type'], 'image') !== false )
        {
          // is image
          $src = wp_get_attachment_image_src( $v['id'], 'full' );

          $value[ $k ]['url'] = $src[0];
          $value[ $k ]['width'] = $src[1];
          $value[ $k ]['height'] = $src[2];


          // sizes
          if( $image_sizes )
          {
            $value[$k]['sizes'] = array();

            foreach( $image_sizes as $image_size )
            {
              // find src
              $src = wp_get_attachment_image_src( $v['id'], $image_size );

              // add src
              $value[ $k ]['sizes'][ $image_size ] = $src[0];
              $value[ $k ]['sizes'][ $image_size . '-width' ] = $src[1];
              $value[ $k ]['sizes'][ $image_size . '-height' ] = $src[2];
            }
            // foreach( $image_sizes as $image_size )
          }
          // if( $image_sizes )
        }
        else
        {
          // is file
          $src = wp_get_attachment_url( $v['id'] );

          $value[ $k ]['url'] = $src;
        }
      }
      // foreach( $value as $k => $v )
    }
    // if( $value )


    // return value
    return $value;
  }

}

new ntz_acf_sortable();
