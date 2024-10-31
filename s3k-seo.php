<?php 
    /*
    Plugin Name: s3k Wordpress Custom SEO
    Plugin URI: #
    Description:  s3k Wordpress Custom SEO Meta for Posts, Pages and Categories.
    Author: s3k.biz
    Version: 1.0
    Author URI: http://s3k.biz/
    */
	
	
/*
*  s3k SEO Meta Tags , (Categories)
*/


//add extra fields to category edit form hook
add_action ( 'edit_category_form_fields', 's3k_seo_category_fields');
//add extra fields to category edit form callback function
function s3k_seo_category_fields( $tag ) {    //check for existing featured ID
    $t_id = $tag->term_id;
    $cat_meta = get_option( "s3k_category_meta_$t_id");
?>
<tr><td colspan="2"style="padding:0px; margin:0px;"><h3 style="padding:0px; margin:5px 0;">s3k SEO Meta</h3></td></tr>
<tr class="form-field">
	<th scope="row" valign="top"><label for="s3k_seo_title"><?php _e('SEO Title'); ?></label></th>
	<td>
		<input type="text" name="Cat_meta[s3k_seo_title]" id="Cat_meta[s3k_seo_title]" size="3" style="width:60%;" value="<?php echo $cat_meta['s3k_seo_title'] ? $cat_meta['s3k_seo_title'] : ''; ?>"><br />
		<span class="description"><?php _e('The SEO title is used on the archive page for this term..'); ?></span>
    </td>
</tr>
<tr class="form-field">
	<th scope="row" valign="top"><label for="s3k_seo_keywords"><?php _e('SEO Keywords'); ?></label></th>
	<td>
		<input type="text" name="Cat_meta[s3k_seo_keywords]" id="Cat_meta[s3k_seo_keywords]" size="25" style="width:60%;" value="<?php echo $cat_meta['s3k_seo_keywords'] ? $cat_meta['s3k_seo_keywords'] : ''; ?>"><br />
		<span class="description"><?php _e('Meta keywords used on the archive page for this term.'); ?></span>
	</td>
</tr>
<tr class="form-field">
	<th scope="row" valign="top"><label for="s3k_seo_description"><?php _e('SEO Description'); ?></label></th>
	<td>
		<input type="text" name="Cat_meta[s3k_seo_description]" id="Cat_meta[s3k_seo_description]" size="25" style="width:60%;" value="<?php echo $cat_meta['s3k_seo_description'] ? $cat_meta['s3k_seo_description'] : ''; ?>"><br />
        <span class="description"><?php _e('The SEO description is used for the meta description on the archive page for this term.'); ?></span>
    </td>
</tr>
<?php
}

// save extra category extra fields hook
add_action ( 'edited_category', 'save_s3k_category_fileds');
   // save extra category extra fields callback function
function save_s3k_category_fileds( $term_id ) {
    if ( isset( $_POST['Cat_meta'] ) ) {
        $t_id = $term_id;
        $cat_meta = get_option( "s3k_category_meta_$t_id");
        $cat_keys = array_keys($_POST['Cat_meta']);
            foreach ($cat_keys as $key){
            if (isset($_POST['Cat_meta'][$key])){
                $cat_meta[$key] = sanitize_text_field($_POST['Cat_meta'][$key]);
            }
        }
        //save the option array
        update_option( "s3k_category_meta_$t_id", $cat_meta );
    }
}


/*
*  s3k SEO Meta Tags   (Posts, Pages)
*/

add_action("add_meta_boxes", "add_s3k_meta_box_seo");
function add_s3k_meta_box_seo() {
	$post_types = array ( 'post', 'page' );
    foreach( $post_types as $post_type )
    {
		add_meta_box("s3k_seo_meta", "s3k SEO Meta", "s3k_seo_meta_markup", $post_type, "normal", "default", null);
	}
}
function s3k_seo_meta_markup($object) {
    wp_nonce_field(basename(__FILE__), "meta-box-nonce");
    ?>
			<div class="misc-pub-section">
				<label>SEO Title <br/> <input type="text" value="<?php echo get_post_meta($object->ID, "s3k_seo_title", true); ?>" name="s3k_seo_title"  style="width:400px !important; font-weight:normal"/></label>
			</div>
			<div class="misc-pub-section">
				<label>SEO Keywords <br/> <input type="text" value="<?php echo get_post_meta($object->ID, "s3k_seo_keywords", true); ?>" name="s3k_seo_keywords" style="width:400px !important; font-weight:normal" /></label>
			</div>
			<div class="misc-pub-section">
				<label>SEO Description <br/> <input type="text" value="<?php echo get_post_meta($object->ID, "s3k_seo_description", true); ?>" name="s3k_seo_description" style="width:400px !important; font-weight:normal" /></label>
			</div>
    <?php  
}


function save_s3k_meta_box($post_id, $post, $update)
{
     if (!isset($_POST["meta-box-nonce"]) || !wp_verify_nonce($_POST["meta-box-nonce"], basename(__FILE__)))
        return $post_id;

    if(!current_user_can("edit_post", $post_id))
        return $post_id;

    if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
        return $post_id;

    if('post' == $post->post_type || 'page' == $post->post_type)
	{
		if(isset($_POST["s3k_seo_title"]))    {
			$s3k_seo_title = sanitize_text_field(esc_html($_POST["s3k_seo_title"]));
		}      
		 if(isset($_POST["s3k_seo_keywords"]))  {
			$s3k_seo_keywords =  sanitize_text_field(esc_html($_POST["s3k_seo_keywords"]));
		}   
		 if(isset($_POST["s3k_seo_description"]))    {
			$s3k_seo_description =  sanitize_text_field(esc_html($_POST["s3k_seo_description"]));
		}
		update_post_meta($post_id, "s3k_seo_title", $s3k_seo_title);
		update_post_meta($post_id, "s3k_seo_keywords", $s3k_seo_keywords);
		update_post_meta($post_id, "s3k_seo_description", $s3k_seo_description);
	} else {
		return $post_id;
	}
}
add_action("save_post", "save_s3k_meta_box", 10, 3);

function display_s3k_seo_meta_callback()
{
	if(is_category()) { 
		$cat_id = get_query_var('cat');
		$cat_meta = get_option( "s3k_category_meta_$cat_id");
		$customTitle = $cat_meta['seo_title'];
		?>
		<title><?php echo $cat_meta['s3k_seo_title'];?></title>
		<meta name="keywords" content="<?php echo $cat_meta['s3k_seo_keywords'];?>" />
		<meta name="description" content="<?php echo $cat_meta['s3k_seo_description'];?>" />
		<?php
	 } else if(is_single()) {
		 $post_meta = get_post_meta(get_the_ID ());
		  ?>
		  <title><?php echo $post_meta['s3k_seo_title'][0];?></title>
		  <meta name="keywords" content="<?php echo $post_meta['s3k_seo_keywords'][0];?>" />
		  <meta name="description" content="<?php echo $post_meta['s3k_seo_description'][0];?>" />
		  <?php
	 }
}
//add_action('wp_head', 'display_s3k_seo_meta_callback', 5);	
add_shortcode( 'display_s3k_seo_meta', 'display_s3k_seo_meta_callback' );

?>