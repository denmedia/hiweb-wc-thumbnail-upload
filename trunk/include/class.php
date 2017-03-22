<?php

	/**
	 * @return hw_wc_thumbnail_upload
	 */
	function hw_wc_thumbnail_upload(){
		static $class;
		if( !$class instanceof hw_wc_thumbnail_upload )
			$class = new hw_wc_thumbnail_upload();
		return $class;
	}


	class hw_wc_thumbnail_upload{

		public function upload( $_file ){
			if( !isset( $_file['tmp_name'] ) ){
				return 0;
			}
			///
			ini_set( 'upload_max_filesize', '128M' );
			ini_set( 'post_max_size', '128M' );
			ini_set( 'max_input_time', 300 );
			ini_set( 'max_execution_time', 300 );
			///
			$tmp_name = $_file['tmp_name'];
			$fileName = $_file['name'];
			if( !is_readable( $tmp_name ) ){
				return - 1;
			}
			///File Upload
			$wp_filetype = wp_check_filetype( $fileName, null );
			$wp_upload_dir = wp_upload_dir();
			$newPath = $wp_upload_dir['path'] . '/' . sanitize_file_name( $fileName );
			if( !copy( $tmp_name, $newPath ) ){
				return - 2;
			}
			$attachment = array(
				'guid' => $wp_upload_dir['url'] . '/' . $fileName,
				'post_mime_type' => $wp_filetype['type'],
				'post_title' => preg_replace( '/\.[^.]+$/', '', $fileName ),
				'post_content' => '',
				'post_status' => 'inherit'
			);
			$attachment_id = wp_insert_attachment( $attachment, $newPath );
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			$attachment_data = wp_generate_attachment_metadata( $attachment_id, $newPath );
			wp_update_attachment_metadata( $attachment_id, $attachment_data );
			return $attachment_id;
		}
	}