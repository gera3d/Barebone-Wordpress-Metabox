<?php
/*
Plugin Name: Wordpress Metabox Bare Bone
Plugin URI: https://github.com/gera3d
Description: I am going to be adding a Meta Box with the name of the song I am listening to. The idea here is a bare bones metabox for anyone to use.
Version: .1
Author: Gera Yeremin
Author URI: http://Yerem.in
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/**
 * I am going to explain how this code works and what its doing.  I am going to break it down in 3 parts.
 * 1. I am going to create a meta box that you will use to input data.
 * 2. I am going to save the date out of the box.
 * 3. I am going to display the data on the post.
 */

/**
 * 1 Adds a meta box to the post editing screen AKA jamming
 */
function the_jam() {
    add_meta_box( 'example_meta', 'Do you like to Jam?', 'the_jam_callback', 'post' );
} // end the_jam()
add_action( 'add_meta_boxes', 'the_jam' );


//1.2 Outputs the content of the metabox

function the_jam_callback( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'song_nonce' );
    $example_stored_meta = get_post_meta( $post->ID );
    ?>
 
    <p>
        <label for="listening" class="thesong">Here Is What I am Jamming:</label>
        <input type="text" name="listening" id="listening" value="<?php echo ( !empty( $example_stored_meta['listening'][0] ) ) ? esc_attr( $example_stored_meta['listening'][0] ) : ''; ?>" />
    </p>
 
    <?php
} // 1.3 end the_jam_callback()

/**
 * 2 Saves the Song I am listening To
 */
function the_jam_save( $post_id ) {
 
    // 2.2 Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'song_nonce' ] ) && wp_verify_nonce( $_POST[ 'song_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
 
    // 2.3 Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }
 
    // 2.4 Checks for input and sanitizes/saves if needed
    if( isset( $_POST[ 'listening' ] ) ) {
        update_post_meta( $post_id, 'listening', sanitize_text_field( $_POST[ 'listening' ] ) );
    }
 
} // 2.5 end the_jam_save()
add_action( 'save_post', 'the_jam_save' );

/**
 * 3 Display the song on the post
 */
function insertsong( $content ) {
    if(!is_feed() ) {

        // 3.2 Retrieves the stored Song from the database
        $meta_value = get_post_meta( get_the_ID(), 'listening', true );
     
        // 3.3 Checks and displays the song in the post
        if( !empty( $meta_value ) ) {
            $meta = "I am Listening to: ";
            $meta .= esc_html( $meta_value );
            $content = $meta . $content;
        }

    }
    return $content;
}
add_filter ('the_content', 'insertsong');
// We are all done