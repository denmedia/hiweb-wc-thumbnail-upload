<?php

	add_action( 'current_screen', function(){
		if( get_current_screen()->post_type == "product" ){
			wp_register_script( 'hw_wc_upload_zone', plugins_url( basename( dirname( dirname( __FILE__ ) ) ) ) . '/include/hw_upload_zone.js', array( 'jquery' ), true );
			wp_register_script( 'hw_wc_upload', plugins_url( basename( dirname( dirname( __FILE__ ) ) ) ) . '/include/hw_upload.js', array( 'jquery', 'hw_wc_upload_zone' ), true );
			wp_register_style( 'hw_wc_thumbnail_upload', plugins_url( basename( dirname( dirname( __FILE__ ) ) ) ) . '/include/hw_wc_thumbnail_upload.css' );
			wp_enqueue_script( 'hw_wc_upload_zone' );
			wp_enqueue_script( 'hw_wc_upload' );
			wp_enqueue_style( 'hw_wc_thumbnail_upload' );
		}
	} );

	///AJAX
	add_action( 'wp_ajax_takao_upload', function(){
		$post_id = intval( $_SERVER['HTTP_POSTID'] );
		$file = $_FILES['file'];
		if( $post_id > 0 ){
			///Upload File
			$attachment_id = hw_wc_thumbnail_upload()->upload( $file );
			if( $attachment_id <= 0 ){
				wp_die( json_encode( [ false, 'не удалось загрузит  файл' ] ) );
			} else {
				if( $_SERVER['HTTP_POSTTYPE'] == 'taxonomy' ){
					$R = update_term_meta( $post_id, 'thumbnail_id', $attachment_id );
				} else {
					$R = set_post_thumbnail( $post_id, $attachment_id );
				}
				if( $R == false ){
					wp_die( json_encode( [ false, 'не удалось установить миниатюру для товара' ] ) );
				} else {
					$img_src = wp_get_attachment_image( $attachment_id );
					wp_die( json_encode( [ true, $img_src ] ) );
				}
			}
		}
		wp_die( json_encode( [ false, 'Не верный ID товара' ] ) );
	} );