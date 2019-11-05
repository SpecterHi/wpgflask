<?php

GFForms::include_addon_framework();

class GFDataExportAddOn extends GFAddOn {

	protected $_version = GF_Data_Export_ADDON_VERSION;
	protected $_min_gravityforms_version = '1.9';
	protected $_slug = 'data_export_addon';
	protected $_path = 'data_export/data_export_addon.php';
	protected $_full_path = __FILE__;
	protected $_title = 'Gravity Forms data_export_addon Add-On';
	protected $_short_title = 'data_export_addon Add-On';

	private static $_instance = null;

	/**
	 * Get an instance of this class.
	 *
	 * @return GFDataExportAddOn
	 */
	public static function get_instance() {
		if ( self::$_instance == null ) {
			self::$_instance = new GFDataExportAddOn();
		}

		return self::$_instance;
	}

	/**
	 * Handles hooks and loading of language files.
	 */
	public function init() {
		parent::init();
		//cccc数据导出表单提交到lx服务器
		add_action( 'gform_after_submission_1', array( $this, 'data_export' ) , 10, 2 );
		//cccc慧科上传表单提交到lx服务器
		add_action( 'gform_after_submission_3',  array( $this, 'huike2tsv' ), 10, 2 );
	}




	// # FRONTEND FUNCTIONS --------------------------------------------------------------------------------------------

	

	// # ADMIN FUNCTIONS -----------------------------------------------------------------------------------------------

	/**
	 * Creates a custom page for this add-on.
	 */
	public function plugin_page() {
		echo 'This page appears in the Forms menu';
	}

	public function data_export( $entry, $form ) {
		$is_fresh = false;
		$data_source = '';
		$field='';
		if (rgar( $entry, '5' ) == 'weibo') {
			$data_source = 'weibo';
		} else if (rgar( $entry, '5' ) == 'wechat') {
			//30天之外
			$data_source = 'wechat';
			$is_fresh = false;
		} else {
			//30天之内
			$data_source = 'wechat';
			$is_fresh = true;
		}
	
		if(rgar( $entry, '6' )){
			$field=rgar( $entry, '6' );
		}
		if(rgar( $entry, '8' )){
			$field=rgar( $entry, '8' );
		}
		if(rgar( $entry, '9' )){
			$field=rgar( $entry, '9' );
		}
		$user = '';
		$role = '';
		if(is_user_logged_in()) {
		   $user = wp_get_current_user();
		   $role = ((array) $user->roles)[0];
	   } 
		$endpoint_url = 'http://api.jnu.rocks:5001/export';
		$body = array(
			'start_time' => rgar( $entry, '10' ).':00',
			'end_time' => rgar( $entry, '11' ).':00',
			'is_fresh' => (bool)$is_fresh,
			'data_source' => $data_source,
			'field' => $field,
			'dsl_query' => rgar( $entry, '7' ),
			'user_id' => get_current_user_id(),
			'user_role' => $role,
			'email' => rgar( $entry, '12' )
			);
		GFCommon::log_debug( 'gform_after_submission: body => ' . print_r( $body, true ) );
	
	 
		$response = wp_remote_post( $endpoint_url, array(
		'httpversion' => '1.0',
			'sslverify' => false,
		'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
		'body' => json_encode($body),
		'method'      => 'POST',
		'data_format' => 'body'
		) );
	
		GFCommon::log_debug( 'gform_after_submission: response => ' . print_r( $response, true ) );

		print_r($response); 
		
		if (is_wp_error($response)) { 
		$error_message = $response->get_error_message(); 
		//echo "Something went wrong: $error_message"; 
	} else { 
		/*echo 'Response:<pre>'; 
		print_r($response); 
		echo '</pre>'; */
	} 
	}

	public	function huike2tsv( $entry, $form ) {
		$endpoint_url = 'http://api.jnu.rocks:5001/huike2tsv';
	   $body = array(
		   'url' => rgar( $entry, '1' ),
		   'email' => rgar( $entry, '2' )
		   );
	   GFCommon::log_debug( 'gform_after_submission: body => ' . print_r( $body, true ) );
   //echo rgar( $entry, '1' );
	
	   $response = wp_remote_post( $endpoint_url, array(
	   'httpversion' => '1.0',
		   'sslverify' => false,
	   'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
	   'body' => json_encode($body),
	   'method'      => 'POST',
	   'data_format' => 'body'
	   ) );
   
	   GFCommon::log_debug( 'gform_after_submission: response => ' . print_r( $response, true ) );
   
	   if (is_wp_error($response)) { 
		   $error_message = $response->get_error_message(); 
		   // echo "Something went wrong: $error_message"; 
	   } else { 
		   /*echo 'Response:<pre>'; 
		   print_r($response); 
		   echo '</pre>'; */
	   } 
   }
}

