<?php

	add_action( 'wp_handle_upload_prefilter', 'upload_filter' );
	add_action( 'add_attachment', 'update_attachment_title' );


	/**
	 * Checks whether or not the current file should be cleaned.
	 *
	 * This function runs when files are being uploaded to the WordPress media 
	 * library. The function checks if the clean_image_filenames_mime_types filter 
	 * has been used and overrides other settings if it has. Otherwise, the plugin 
	 * settings are used. 
	 *
	 * If a file shall be cleaned or not is checked by comparing the current file's 
	 * mime type to the list of mime types to be cleaned.
	 *
	 * @since 1.1 Added more complex checks and moved the actual cleaning to clean_filename().
	 * @since 1.0
	 * @param array The file information including the filename in $file['name'].
	 * @return array The file information with the cleaned or original filename.
	 */
	function upload_filter( $file ) {
		$original_filename = pathinfo( $file[ 'name' ] );
		set_transient( '_clean_image_filenames_original_filename', $original_filename[ 'filename' ], 60 );
		$file = clean_filename( $file );
	    return $file;
	}

	/**
	 * Performs the filename cleaning.
	 *
	 * This function performs the actual cleaning of the filename. It takes an 
	 * array with the file information, cleans the filename and sends the file 
	 * information back to where the function was called from. 
	 *
	 * @since 1.1
	 * @param array File details including the filename in $file['name'].
	 * @return array The $file array with cleaned filename.
	 */
	function clean_filename( $file ) {
		
		$input = array(
			'ß', 
			'·', 
		);
		
		$output = array(
			'ss', 
			'.' 
		);
		
		$path = pathinfo( $file[ 'name' ] );
		$new_filename = preg_replace( '/.' . $path[ 'extension' ] . '$/', '', $file[ 'name' ] );
		$new_filename = str_replace( $input, $output, $new_filename );
		$file[ 'name' ] = sanitize_title( $new_filename ) . '.' . $path[ 'extension' ];
		
		return $file;
	}
	
	/**
	 * Set attachment title to original, un-cleaned filename
	 *
	 * The original, un-cleaned filename is saved as a transient called 
	 * _clean_image_filenames_original_filename just before the filename is cleaned 
	 * and saved. When WordPress adds the attachment to the database, this function 
	 * picks up the original filename from the transient and saves it as the 
	 * attachment title.
	 *
	 * @since 1.2
	 * @param int Attachment post ID.
	 */	
	function update_attachment_title( $attachment_id ) {
		$original_filename = get_transient( '_clean_image_filenames_original_filename' );
		if ( $original_filename ) {
			wp_update_post( array( 'ID' => $attachment_id, 'post_title' => $original_filename ) );
			delete_transient( '_clean_image_filenames_original_filename' );
		}
	}
