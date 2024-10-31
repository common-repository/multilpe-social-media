<?php
/*
Plugin Name: Multiple Social Media
Description: Adds a button which allows you to share on social networks : Facebook, Twitter, Sharethis. 
Version: 1.0.0
Author: Ajay Kumar, Harpinder Singh
Author URI: NA
Author Email: singhharpinder@hotmail.com
Translation: NA
*/

/*
    This file is part of ICanLocalize Translator.

    ICanLocalize Translator is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    ICanLocalize Translator is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with ICanLocalize Translator.  If not, see <http://www.gnu.org/licenses/>.
*/

$parser ='';
$write=0;
		define('MEDIA_URLPATH', trailingslashit( plugins_url( '', __FILE__ ) ) );
// only if twitter is enabled
wp_enqueue_script('social_media_twitter', 'http://platform.twitter.com/widgets.js');
wp_enqueue_script('social_media_st',"http://w.sharethis.com/widget/jquery.carousel.min.js");

$fb_type_options = array(
	'activity', 'sport',
	'bar', 'company', 'cafe', 'hotel', 'restaurant',
	'cause', 'sports_league', 'sports_team',
	'band', 'government', 'non_profit', 'school', 'university',
	'actor', 'athlete', 'author', 'director', 'musician', 'politician', 'public_figure',
	'city', 'country', 'landmark', 'state_province',
	'album', 'book', 'drink', 'food', 'game', 'movie', 'product', 'song', 'tv_show',
	'article', 'blog', 'website'
);

if ( !defined('WP_CONTENT_URL') ) {
	define('FBSHARE_URL',get_option('siteurl').'/wp-content/plugins/'.plugin_basename(dirname(__FILE__)).'/');
} else {
	define('FBSHARE_URL',WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__)).'/');
}

/**
 * Register options for the plugin menu
 * 
 * @access internal
 * @return void
 */
function fb_options() {
	add_menu_page('Facebook Share', 'Social Media','Social Media', basename(__FILE__), 'gb_options_page');
  add_submenu_page(basename(__FILE__), 'Global Settings', 'Global Setting','Social Media', basename(__FILE__), 'gb_options_page');
	add_submenu_page(basename(__FILE__), 'Facebook Settings', 'Facebook','Social Media', basename(__FILE__).'fb', 'fb_options_page');
  add_submenu_page(basename(__FILE__), 'Twitter Settings', 'Twitter','Social Media', basename(__FILE__).'tw', 'tw_options_page');
  add_submenu_page(basename(__FILE__), 'ShareThis Settings', 'ShareThis','Social Media', basename(__FILE__).'sh', 'st_options_page');
}

// Manual output
/* // manual option removed
function fbshare_manual() {
    if (get_option('fb_where') == 'manual') {
        return fb_generate_button();
    } else {
        return false;
    }
}
*/

function xh_get_featured_image($pageID)
{
	if ($pageID != '' && function_exists('has_post_thumbnail')){
				if (has_post_thumbnail( $pageID )){
					$image = wp_get_attachment_image_src( get_post_thumbnail_id( $pageID ), 'single-post-thumbnail' ); 
					if ($image !='')			
						return $image[0];
					else
						return false;}
				else 
					//echo('No Thumbnail Image');
					return false; }
	else 
			return false;
}

function st_options_page()
{
  
  $st_current_type=get_option('st_current_type');
  $isActive = (get_option('st_plug_active') == 'on') ? 'checked="checked"' : '';
  $publisher_id = get_option('st_pubid');
  if(empty($publisher_id)){
		$toShow="";
	}
	else{
		$toShow=get_option('st_widget');
	}
  
  ?>
<div class="wrap" style="font-size:13px;">
<div class="icon32" id="icon-options-general"><br/></div><h2><?php _e('Settings for ShareThis Integration') ?></h2>
<div id="fb_canvas" style="width:800px;float:left">
<form method="post" action="options.php">
  <?php
			// New way of setting the fields, for WP 2.7 and newer
				if(function_exists('settings_fields')){
					settings_fields('st-options');
				} else {
					wp_nonce_field('update-options');
    ?>  
    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="page_options" value="st_plug_active,st_version,st_services,st_current_type,st_plug_seq" />
     <?php
        }
     ?>
			<table class='form-table'>
			<tr class='light'><th scope="row">
            <?php _e('ShareThis'); ?>
            </th>
			<td>
<?	echo'	<input   type="checkbox" name="st_plug_active"  '.$isActive.'><BR/>';
	$plugin_location=WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	$opt_js_location=$plugin_location."wp_st_opt.js"; 
  ?>
   </td></tr>	
    <tr class='light'>
          <th scope=row>
                 <?php _e('Display Order'); ?>
            </th>
      <td >
      <input type='text' size=2 maxlength=2 value='<?=get_option('st_plug_seq')?>' name='st_plug_seq' />
 <?  print("<script type=\"text/javascript\" src=\"$opt_js_location\"></script>");
  print('</td></tr></table><p class="submit">
						<input type="submit" onclick="st_log();" name="submit_button" value="'.__('Update ShareThis Options', 'sharethis').'" />
					</p>
					<input type="hidden" name="st_action" value="st_update_settings" />
			</div>
	');
?>

      
  </form>
  </div>
  </div>
<?}

function tw_options_page()
{
  
  $twitter_count = (get_option('twitter_count')) ? 'checked="checked"' : '';
  $twitter_follow = (get_option('twitter_follow_active')) ? 'checked="checked"' : '';
  $twitter_follow_style = ($twitter_follow != '')?'inline':'none';
  $twitter_id = (get_option('twitter_id')) ? 'checked="checked"' : '';
?>
<div class="wrap" style="font-size:13px;">
<div class="icon32" id="icon-options-general"><br/></div><h2><?php _e('Settings for Twitter Integration') ?></h2>
  <div id="fb_canvas" style="width:800px;float:left">
  <form method="post" action="options.php">
  <?php
			// New way of setting the fields, for WP 2.7 and newer
				if(function_exists('settings_fields')){
					settings_fields('tw-options');
				} else {
					wp_nonce_field('update-options');
    ?>  
    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="page_options" value="twitter_follow_active,twitter_width,twitter_text,twitter_count,twitter_follow_txt,twitter_follow_id,twitter_seq,twitter_follow_seq" />
     <?php
        }
     ?>
			<table class='form-table'>
			<tr class='dark'><th scope="row">
                <strong><?php _e('Follow US')?>:</strong>
            </th>
			<td>
				<input type="checkbox" name="twitter_follow_active" <?=$twitter_follow?> />&nbsp;<?php _e('Activate to display automatically')?><BR/>
        <span class="setting-description"><?php _e('Provide below information for Twitter FollowUS button. you can integrate button manually by calling following function "echo  display_social($options); where $options =TWFOLLOW; "') ?></span><BR/>
			  <span id='twitter_follow_dv' style="display:<?=$twitter_follow_style?>">
        </td></tr>
        <tr class='dark'>
          <th scope="row">
                <?php _e('Display Order')?>
            </th>
            <td colspan=2>
              <input type="text" size=2 maxlength=2 value="<?php echo get_option('twitter_follow_seq')?>" name="twitter_follow_seq" />
            </td>
        </tr>
    <tr class='dark'><th scope="row">
            </th>
			<td>
        <input name="twitter_follow_id" type="text" id="twitter_follow_id" value="<?php echo get_option('twitter_follow_id'); ?>" size="50" />
        <INPUT TYPE="button" value='Test Link' onclick="javascript:window.open('http://twitter.com/#!/'+document.getElementById('twitter_follow_id').value)"></div><BR/>
        <span class="setting-description"><?php _e('Add profile Name like "http://twitter.com/#!/{PROFILENAME}"')?></span><BR/><BR/>
        <input type="text" id="twitter_follow_txt" name="twitter_follow_txt" value="<?=stripslashes(get_option('twitter_follow_txt'))?>" size="10">
        <!-- Preview Button -->
        <INPUT TYPE="button" value='Preview' onclick="javascript:document.getElementById('txtTWString').innerHTML=document.getElementById('twitter_follow_txt').value;">
         <span style='display:inline;vertical-align:text-bottom;margin-left:80px;'>
         <img src="<?php echo plugins_url('images/twitter.jpg' , __FILE__ )?>" style='vertical-align:text-bottom;'>&nbsp;<span id='txtTWString'><?php echo get_option('twitter_follow_txt')?></span>
         <BR/><span class="setting-description"><?php _e('Add text to display next to image') ?></span>
        </span>
      </td></tr>
      		<tr class='light'><th scope="row">
                <strong><?php _e('Twitter Share')?>:</strong>
            </th>
            <td >
            <input type="checkbox" name="twitter_id" <?=$twitter_id?>>&nbsp;<?php _e('Activate to display automatically') ?><BR/>
        <span class="setting-description"><?php _e('Provide below information for Twitter Share button. you can integrate button manually by calling following function "echo  display_social($options); where $options =TWSHARE; "') ?></span><BR/>
            </td>
            </tr>
        <tr class='light'>
          <th scope="row">
                <?php _e('Display Order')?>
            </th>
            <td colspan=2>
              <input type="text" size=2 maxlength=2 value="<?php echo get_option('twitter_seq')?>" name="twitter_seq" />
            </td>
        </tr>
      <tr class='light'><th scope="row">
                <?php _e('Button width')?>:
            </th>
			<td>
				<input type="text" name="twitter_width" value="<?=stripslashes(get_option('twitter_width'))?>" size="10"> px<br />
			</td></tr>
			<tr class='light'>
      <th scope="row">
                <?php _e('Additional text')?>:
      </th>
			<td>
				<input type="text" name="twitter_text" value="<?=stripslashes(get_option('twitter_text'))?>" size="25"><br />
				<span class="setting-description"><?php _e('optional text added at the end of every tweet, e.g. " (via @authorofblogentry)".
				If you use it, insert an initial space or puntuation mark') ?></span>
			</td></tr>
			<tr class='light'><td><?php _e('Show counter') ?>:</td>
			<td>
				<input type="checkbox" name="twitter_count" <?=$twitter_count?> />
			</td></tr>
      </div>
			</table>
          <p class="submit">
            <input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
        </p>
  </form>
<?}

function gb_options_page()
{
?>
  <div class="wrap" style="font-size:13px;">
  <div class="icon32" id="icon-options-general"><br/></div><h2><?php _e('Global Settings for Social Media')?></h2>
  <p><?php _e('This plugin will install the Facebook Share,Facepile and send widget for each of your blog posts in both the content of your posts.')?>
</p>
<form method="post" action="options.php">
<?php
  // New way of setting the fields, for WP 2.7 and newer
  if(function_exists('settings_fields')){
    settings_fields('gb-options');
  } else {
    wp_nonce_field('update-options');
?>   <input type="hidden" name="action" value="update" />
      <input type="hidden" name="page_options" value="fb_display_page,fb_display_front,fb_where,fb_style,fb_admin_ids" />
      <?php
  }
?>
<table class="form-table">
            <tr class='light'>
              <p><strong><?php _e('Global setting for all buttons in this plug-in')?></strong></p>
	                <th scope="row">
	                    <?php _e('Display');?>
	                </th>
	                <td colspan=2>
	                    <p>
	                        <input type="checkbox" value="1" <?php if (get_option('fb_display_page') == '1') echo 'checked="checked"'; ?> name="fb_display_page" id="fb_display_page" group="fb_display"/>
	                        <label for="fb_display_page"><?php _e('Display buttons on all pages')?></label><br />
                          <input type="checkbox" value="1" <?php if (get_option('fb_display_front') == '1') echo 'checked="checked"'; ?> name="fb_display_front" id="fb_display_front" group="fb_display"/>
	                        <label for="fb_display_front"><?php _e('Display buttons on home page only')?></label>
	                        <br />
                    </p>
                  </td> 
	            </tr>
              <tr class='dark'>
              <th scope="row">
                    Position
                </th>
                <td colspan=2>
                		<select name="fb_where" > 
                			<option <?php if (get_option('fb_where') == 'before') echo 'selected="selected"'; ?> value="before"><?php _e('Before Page')?></option>
                			<option <?php if (get_option('fb_where') == 'after') echo 'selected="selected"'; ?> value="after"><?php _e('After  Page')?></option>
                			<option <?php if (get_option('fb_where') == 'beforeandafter') echo 'selected="selected"'; ?> value="beforeandafter"><?php _e('Before and After  Page')?></option> 
                    </select> 
               </td>
            </tr>
            <tr class='light'>
                <th scope="row"><label for="fb_style"><?php _e('Styling')?></label></th>
              <td colspan=2>
                  <input name="fb_style" type="text" id="fb_style" value="<?php echo htmlspecialchars(get_option('fb_style')); ?>" size="50" />
                  <span class="setting-description"><br />
                  <?php _e('Add style to the div that surrounds the button')?> E.g. <code>float: left; margin-right: 10px;</code></span>
            </tr>	
</table>
<p class="submit">
  <input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
</p>
<p class="setting-description">
<strong><?php _e('How to use:');?></strong><BR/>
<ol>
<li><?php _e('Activate options');?></li>
<li><?php _e('Fill all fields (Optional fields are marked)');?></li>
<li><?php _e('Save changes'); ?></li>
</ol>
<strong><?php _e('How to add manually:');?></strong><BR/>
<?php _e('There is simple steps to use manually'); ?>
<ol>
<li><?php _e('You need to add code with parameters:');?><BR/>display_social(params);</li>
<li><?php _e('There are different parameters given below (not case sensitive):');?><BR/><BR/>
      <ul>
          <li>Facebook Like:FBLIKE</li>
          <li>Facebook FollowUS:FBFOLLOW</li>
          <li>Twitter Share:TWSHARE</li>
          <li>Twitter FollowUS:TWFOLLOW</li>
          <li>Share this:SHARETHIS</li>
          <li>Facebook FacePile:FBFACEPILE</li>
      </ul>
</li>
</ol>
<strong>Example1 :</strong>To diaplay Facebook Like and Twitter FollowUS use like:display_social('FBLIKE,TWFOLLOW');<br/>
<strong>Example2 :</strong>To diaplay Twitter FollowUS and Share this use like:display_social('TWFOLLOW,SHARETHIS');<br/>
<strong>Note:</strong>Buttons will appear in which sequence it is passes. In example2 'Twitter FollowUS' button will appear before Share this.<img src="<?php echo plugins_url('images/exm2-1.JPG' , __FILE__ )?>"> <BR/>To display 'Share this' before 'Twitter FollowUS' pass parameters like: display_social('SHARETHIS,TWFOLLOW');<img src="<?php echo plugins_url('images/exm2-2.JPG' , __FILE__ )?>">
</p>

</form>
  </div>
  </div>
<?
}

function fb_options_page()
{
  //global $fb_type_options;
  $facebook_fpile = (get_option('fb_facepile_id') == 'on') ? 'checked="checked"' : '';
  $facebook_fpile_style = ($facebook_fpile != '')?'inline':'none';
  $facebook_follow_active = (get_option('fb_follow_active') == 'on')? 'checked="checked"' : '';
?>
  <div class="wrap" style="font-size:13px;">
  <div class="icon32" id="icon-options-general"><br/></div><h2><?php _e('Settings for Facebook Share Integration')?></h2>
  <div id="fb_canvas" style="width:800px;float:left">
			<form method="post" action="options.php">
			<?php
				// New way of setting the fields, for WP 2.7 and newer
				if(function_exists('settings_fields')){
					settings_fields('fb-options');
				} else {
					wp_nonce_field('update-options');
      ?>   <input type="hidden" name="action" value="update" />
            <input type="hidden" name="page_options" value="fb_like_active,fb_version,fb_like_seq,fb_follow_seq" />
            <?php
        }
    ?>
    <table class="form-table">
        <tr class='dark'>
          <th scope="row">
                <strong><?php _e('Facebook Like')?></strong>
            </th>
            <td colspan=2>
              <input type="checkbox" <?php if (get_option('fb_like_active') == 'on') echo 'checked="checked"'; ?> name="fb_like_active" id="fb_like_active" />&nbsp;<?php _e('Activate to display automatically')?>
            </td>
        </tr>
        <tr class='dark'>
          <th scope="row">
                <?php _e('Display Order')?>
            </th>
            <td colspan=2>
              <input type="text" size=2 maxlength=2 value="<?php echo get_option('fb_like_seq')?>" name="fb_like_seq" />
            </td>
        </tr>
       <tr class='dark'>
        <th scope="row"><?php _e('Show Send')?></th>
        <td><input type="checkbox" name="fb_share_show_send" id="fb_share_show_send" value="1" <?php  if (get_option('fb_share_show_send') == '1') {echo 'checked="checked"'; } ?> />
        <br />
        <?php _e('By default, LIKE button will be shown. Select this option to include a SEND button.
        SEND button will allow visitors to send links to their friends in private.')?><br />
        </td>
        <td>&nbsp;</td>
      </tr> 
            <tr class='dark'>
                <th scope="row"><div class="ogcontainer"><?php _e('Facebook Admin IDs')?></div></th>
                <td><div class="ogcontainer"><p>
                  <input name="fb_admin_ids" type="text" id="fb_admin_ids" value="<?php echo (get_option('fb_admin_ids')); ?>" size="50" />
                </p>
                  <div class="setting-description"><?php _e('Separate multiple Admin IDs with &quot;,&quot;. For each post, a page would be created on Facebook automatically, under the supplied Facebook ID. You can keep track of people who liked it, from your Facebook Account. Use only ID and not name. E.g. <code>12345671,12346567</code></span>. Don\'t know your Facebook ID?')?> <a href="javascript:void(0)" onclick="document.getElementById('facebookIDHelp').style.display = 'block';"><?php _e('Click here')?></a></div>
                  <div id="facebookIDHelp" style="display:none;"><br />
                    1. 
                  <?php _e('Call  URL https://graph.facebook.com/{username}(Replace {username} with your username on Facebook)')?> <br />
                  2. <?php _e('You will get data from Facebook with your ID')?><br />
                  </div>
                </div></td>
                <td>&nbsp;</td>
            </tr>
            <tr class='dark'>
              <th scope="row"><div class="ogcontainer"><?php _e('Facebook App ID')?></div></th>
              <td><div class="ogcontainer"><input name="fb_app_id" type="text" id="fb_app_id" value="<?php echo (get_option('fb_app_id')); ?>" size="50" />
              (Optional)</div><BR/>
              <div class="setting-description"><?php _e('Don\'t know your Facebook App ID?')?> <a href="javascript:void(0)" onclick="document.getElementById('facebookAPPIDHelp').style.display = 'block';"><?php _e('Click here')?></a></div>
              <div id="facebookAPPIDHelp" style="display:none;"><br />
                1. <?php _e('You can find the app ID in the URL. https://developers.facebook.com/apps/{APPLICATIONID}/basic-info')?>
                  <br />
                2. <?php _e('To create new Application')?><a href='javascript:;' onclick="window.open('http://developers.facebook.com/setup/')"><?php _e('click here.')?></a><br />
              </div>
              </td>
              <td>&nbsp;</td>
              </tr>
              <tr class='dark'>
              <th scope="row"><?php _e('Facebook Page ID')?></th>
              <td colspan=2><input name="fb_page_id" type="text" id="fb_page_id" value="<?php echo (get_option('fb_page_id')); ?>" size="50" />
              (Optional)
              </td>
            </tr>
              <tr class='light'>
	                <th scope="row">
	                    <strong><?php _e('Follow US')?></strong>
	                </th>
                  <td colspan=2>
                  <div class="ogcontainer">
                  <input   type="checkbox" name="fb_follow_active" <?php echo $facebook_follow_active;?>>&nbsp;<?php _e('Activate to display automatically')?><BR/>
                </td></tr>
        <tr class='light'>
          <th scope="row">
                <?php _e('Display Order')?>
            </th>
            <td colspan=2>
              <input type="text" size=2 maxlength=2 value="<?php echo get_option('fb_follow_seq')?>" name="fb_follow_seq" />
            </td>
        </tr>
        <tr class='light'>
          <th scope="row">
            </th>
            <td colspan=2>
                  <span class="setting-description"><?php _e('Provide below information for Facebook FollowUS button. you can integrate button manually by calling following function "echo  display_social($options); where $options =FBFOLLOW; "')?></span><BR/>
                  <input name="fb_follow_id" type="text" id="fb_follow_id" value="<?php echo get_option('fb_follow_id'); ?>" size="50" /><INPUT TYPE="button" value='Test Link' onclick="javascript:window.open('http://www.facebook.com/'+document.getElementById('fb_follow_id').value)">
                  </div>
                  <span class="setting-description">
                  <?php _e('Add Name or Profile ID like "profile.php?id=1234567"')?></span><br/>
                  <input name="fb_follow_txt" type="text" id="fb_follow_txt" value="<?php echo get_option('fb_follow_txt'); ?>" size="25" /><INPUT TYPE="button" value='Preview' onclick="javascript:document.getElementById('txtString').innerHTML=document.getElementById('fb_follow_txt').value;">
                  &nbsp;&nbsp;<span style='display:inline;vertical-align:text-bottom;margin-left:80px;'>
                      <img src="<?php echo plugins_url('images/fbfollow.jpg' , __FILE__ )?>" style='vertical-align:text-bottom;'>&nbsp;<span id='txtString'><?php echo get_option('fb_follow_txt')?></span>
                  </span>
                  <BR/><span class="setting-description">
                  <?php _e('Add text to display next to image')?></span>
                  </td>
	            </tr>
                  <tr class='dark'>
                  <TH>
                  <strong><?php _e('Facebook Facepile')?></strong>&nbsp;(<?php _e('Manual Only')?>)
                  </TH>
                  <td valign=top colspan=2>
                  <input   type="checkbox" name="fb_facepile_id" <?php echo $facebook_fpile;?> onchange="if (this.checked == true) { getElementById('facebook_facepile_dv').style.display = 'inline';} else {getElementById('facebook_facepile_dv').style.display = 'none';}"/>&nbsp;<?php _e('Activate to display on function call<BR/><strong>Note:To use this option put following function call in template like "&lt;? display_social(\'fbfacepile\'); ?&gt;"')?></strong>
            </td>
        </tr>
        <tr class='dark'>
        <td colspan=3>
        <table id='facebook_facepile_dv' style='display:<?php echo $facebook_fpile_style;?>'>
 <!--        <tr class='dark'>
          <th scope="row">
                <?php _e('Display Order')?>
            </th>
            <td colspan=2>
                  <input type="text" size=2 maxlength=2 value="<?php echo get_option('fb_facepile_seq')?>" name="fb_facepile_seq" />
            </td></tr>
          <tr> -->
          <th scope="row">
                <?php _e('URL')?>
            </th>
            <td >
             <input name="fb_facepile_url" type="text" id="fb_facepile_url" value="<?php echo get_option('fb_facepile_url'); ?>" size="45" /><BR/>
                  <span class="setting-description">
                  <?php _e('If you want the Facepile to display friends who have liked your page, specify the URL of the page here.')?></span>
            </td><td rowspan=3>
                  <div style='float:left'>
                  <iframe id='fbfacepile' src="" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:200px;" allowTransparency="true"></iframe>
                  </div>
            </td></tr>
          <tr>
          <th scope="row">
                <?php _e('Width')?>
            </th>
            <td colspan=2>
             <input name="fb_facepile_wd" type="text" id="fb_facepile_wd" value="<?php echo get_option('fb_facepile_wd'); ?>" size="4" /><BR/>
                  <span class="setting-description">
                  <?php _e('The width of the plugin in pixels.')?></span>
              </td></tr>
          <tr>
          <th scope="row">
                <?php _e('Width')?>
            </th>
            <td colspan=2>
                  <?php _e('Count')?>:<input name="fb_facepile_count" type="text" id="fb_facepile_count" value="<?php echo get_option('fb_facepile_count'); ?>" size="2" /><INPUT TYPE="button" value='Preview' onclick="javascript:document.getElementById('fbfacepile').src='http://www.facebook.com/plugins/facepile.php?href='+document.getElementById('fb_facepile_url').value+'&amp;size=small&amp;colorscheme=light&amp;width='+document.getElementById('fb_facepile_wd').value+'&amp;max_rows='+document.getElementById('fb_facepile_count').value;"><BR/>
                  <span class="setting-description">
                  <?php _e('The maximum number of rows of profile pictures to show.')?>
                  </span>
            </td></tr>
          <tr>              
                  </table>
                  </td>
                  </tr>
                  </p>
                  </td>
                  </tr>
			</table>
        <p class="submit">
            <input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
        </p>

    </form>

		</div> <!--End of fb_canvas-->
<?php
}

/**
 * Function to display buttons
 * 
 * @access internal
 * @return String buttons HTML
 */

function display_social($params='')
{
  global $post;
  $button = '';

  if($params !=''){
    $aButtons = explode(',',$params);
    }

foreach($aButtons as $name)
  {
switch(strtoupper($name))
    {
    case 'FBLIKE':
        $isActive = (get_option('fb_like_active') == 'on')?true:false;
        $showsend = (get_option('fb_share_show_send') == '1')?'true':'';
        $fbappid = '125029517579627';
        if (get_option('fb_app_id') != '')
          $fbappid = get_option('fb_app_id');
          if (get_post_status($post->ID) == 'publish') {
              $url = get_permalink();
          }
//          $url = 'http://facebook.com/fastbooking';
         if($isActive && $url != '')
          $button .= '<div id="fb_share_1" style="'.get_option('fb_style').';" name="fb_share"><div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js#appId='.$fbappid.'&amp;xfbml=1"></script><fb:like href="'.$url.'" send="'.$showsend.'" layout="'. get_fb_type() .'" show_faces="false" font="arial"></fb:like></div>';	
      break;
    case 'FBFACEPILE':
          $isActive = (get_option('fb_facepile_id') == 'on')?true:false;
          $sURL = get_option('fb_facepile_url');
          $iNumRows = get_option('fb_facepile_count');
          $iWidth = get_option('fb_facepile_wd');
          if($isActive && $sURL !='' && $iNumRows > 0 && $iWidth >0)
            {
              $button .= '<div id="fb_share_1" style="'.get_option('fb_style').';" name="fb_share"><div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:facepile href="'.$sURL.'" size="large" width="'.$iWidth.'" max_rows="'.$iNumRows.'" colorscheme="dark"></fb:facepile></div>';
            }
      break;
    case 'FBFOLLOW':
          $sFBID = get_option('fb_follow_id');
          $exText = get_option('fb_follow_txt');
          $sFBFText = (trim($exText) !='')?$exText:'Follow Us';
          if ($sFBID !='') 
          {
            $button .= '<div id="fb_share_1" style="'.get_option('fb_style').';" name="fb_share"><a href="javascript:;" onclick=window.open("http://facebook.com/'.$sFBID.'")><img src="'.plugins_url('images/fbfollow.jpg' , __FILE__ ).'" title="'.$sFBFText.'" border="0" />&nbsp;'.get_option('fb_follow_txt').'</a></div>';
          }
      break;
    case 'TWSHARE':
      $data_count = (get_option('twitter_count')) ? 'horizontal' : 'none';
      $link = get_permalink();
      // add here some check and twitter_text value
    		$button .= '<div id="fb_share_1" style="'.get_option('fb_style').';"> 
				<a href="javascript:;" onclick=window.open("http://twitter.com/share") class="twitter-share-button" data-count="'.$data_count.'" data-text="'.get_the_title().stripslashes(get_option('twitter_text')).'" data-url="'.$link.'">Tweet</a> 
			</div>';

      $button .= '';
      break;
    case 'TWFOLLOW':
      $exText = get_option('twitter_follow_txt');
      $sTwFText = (trim($exText) !='')?$exText:'Follow Us';
      if (get_option('twitter_follow_id') !='') 
        {
          $button .='<div id="fb_share_1" style="'.get_option('fb_style').';" name="fb_share"><a href="javascript:;" onclick=window.open("http://twitter.com/#!/'.get_option('twitter_follow_id').'")><img src="'.plugins_url('images/twitter.jpg' , __FILE__ ).'" title="'.$sTwFText.'" alt="'.$sTwFText.'" />&nbsp;'.get_option('twitter_follow_txt').'</a></div>';
        }
      break;
    case 'SHARETHIS':
      $button .=	'<div id="fb_share_1" style="'.get_option('fb_style').';" name="fb_share">'.ts_generate_button().'</div>';
      break;
    }

  }
echo $button;
}

/**
* Function to get active option with options
 * @access internal
 * @return String button Options
*/

function getOptions()
{
  $sExt = 'ex';
  $sDisplayOptions ='';
  $aDisplayOptions = array();

  $aOptions = array('twitter_follow_active' => array('twitter_follow_seq','TWFOLLOW'),
                         'fb_follow_active'  => array('fb_follow_seq','FBFOLLOW'),
                         'st_plug_active' => array('st_plug_seq','SHARETHIS'),
                         'fb_api_type' => array('fb_like_seq','FBLIKE'),
                         'twitter_id' =>array('twitter_seq','TWSHARE')
    );

  foreach($aOptions as $sActive => $aData)
  {

        if(get_option($sActive) == 'on')
          {
          $iSeq = get_option($aData[0]);
          if($iSeq > 0 && !isset($aDisplayOptions[$iSeq]))
               $aDisplayOptions[$iSeq] =$aData[1];
          else if($iSeq > 0 && isset($aDisplayOptions[$iSeq]))
               $aDisplayOptions[++$iSeq] =$aData[1];
          else
              ${aDisplayOptions.$sExt}[] =$aData[1];
          }


  }

$aFree = ${aDisplayOptions.$sExt};
if(!empty($aFree))
foreach($aFree as $val)
  {
  $aDisplayOptions[] = $val; 
  }


  if(!empty($aDisplayOptions))
  {
  ksort($aDisplayOptions);
  $sDisplayOptions = implode(',',$aDisplayOptions);
  }
return $sDisplayOptions;
}

/**
 * function add different active options to display buttons on pages automatically
 * 
 * @access internal
 * @return String buttons HTML
 */
function fb_generate_button()
{
	global $post;
  $url = '';
	
	if ($showsend) { $showsend = 'true'; }
	else {$showsend = 'false'; }
  $sDisplayOptions = '';

  $button = '<div style="height:33px; padding-top:2px; padding-bottom:2px; clear:both;">';
  $sDisplayOptions = getOptions();

  if(!empty($aDisplayOptions))
  {
  $sDisplayOptions = implode(',',$aDisplayOptions);
  }
  if($sDisplayOptions != '')
    $button .= display_social(trim($sDisplayOptions,','));

    $button .= '</div><div style="clear:both;"></div>';

  return $button . $content; 

}

/**
 * function to build Twitter butons
 * 
 * @access internal
 * @return String button script
 */

function ts_generate_button(){
	global $post;
	$out="";
 $widget=get_option('st_widget');
  $tags=get_option('st_tags');
	if(!empty($widget)){

    if(preg_match('/buttons.js/',$widget)){

			if(!empty($tags)){
         $tags=preg_replace("/\\\'/","'", $tags);
				$tags=preg_replace("/<\?php the_permalink\(\); \?>/",get_permalink($post->ID), $tags);
				$tags=preg_replace("/<\?php the_title\(\); \?>/",strip_tags(get_the_title()), $tags);
				$tags=preg_replace("/{URL}/",get_permalink($post->ID), $tags);
				$tags=preg_replace("/{TITLE}/",strip_tags(get_the_title()), $tags);
			}else{
				$tags="<span class='st_sharethis' st_title='".strip_tags(get_the_title())."' st_url='".get_permalink($post->ID)."' displayText='ShareThis'></span>";
				$tags="<span class='st_facebook_large' st_title='<?php the_title(); ?>' st_url='<?php the_permalink(); ?>' displayText='share'></span><span class='st_twitter_large' st_title='<?php the_title(); ?>' st_url='<?php the_permalink(); ?>' displayText='share'></span><span class='st_email_large' st_title='<?php the_title(); ?>' st_url='<?php the_permalink(); ?>' displayText='share'></span><span class='st_sharethis_large' st_title='<?php the_title(); ?>' st_url='<?php the_permalink(); ?>' displayText='share'></span>";	
				$tags=preg_replace("/<\?php the_permalink\(\); \?>/",get_permalink($post->ID), $tags);
				$tags=preg_replace("/<\?php the_title\(\); \?>/",strip_tags(get_the_title()), $tags);		
			}
			$out=$tags;	
		}else{
			$out = '<script type="text/javascript">SHARETHIS.addEntry({ title: "'.strip_tags(get_the_title()).'", url: "'.get_permalink($post->ID).'" });</script>';
		}
	}

	return $out;
}


/**
 * function to return facebook link type
 * 
 * @access internal
 * @return String button type
 */

function get_fb_type() 
{
	$fb_type = 'button_count';//'icon_link';
	if (get_option('fb_version') == "button") 
	{
			if (!get_option('fb_include_counter') == "1") 
				 $fb_type = 'button';
				else $fb_type = get_option('fb_count_type');
	}
	return $fb_type;

}




// check if its required
function fb_generate_static_button()
{
	if (get_post_status($post->ID) == 'publish') {
        $url = get_permalink();
    }
	
	if (get_option('fb_api_type') == "sh") 
		{$button = '<div id="fb_share_1" style="'.get_option('fb_style').'"><a name="fb_share" type="'. get_fb_type() .'" share_url="'.$url.'" href="http://www.facebook.com/sharer.php">Share</a></div><div><script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script></div>';}
	else
		{$button .= '<div id="fb_share_1" style="'.get_option('fb_style').';" name="fb_share"><iframe src="http://www.facebook.com/plugins/like.php?href='.$url.'&amp;layout='. get_fb_type() .'&amp;width=55" scrolling="no" frameborder="0" allowTransparency="true"></iframe></div>';}
}

function fb_share($content)
{
	global $post;
    // add the manual option, code added by kovshenin
    /* // global manual option disabled
    if (get_option('fb_where') == 'manual') {
        return $content;
    }*/
    if (get_option('fb_display_page') == null && is_page()) {
        return $content;
    }
    if (get_option('fb_display_front') == null && is_home()) {
        return $content;
    }

    $button = fb_generate_button();
		$where = 'fb_where';
/* // short code option disabled
	if (get_option($where) == 'shortcode') {
		return str_replace('[fbshare]', $button, $content);
	}
	else*/
	{
		// if we have switched the button off

		if (get_post_meta($post->ID, 'fbsharenew') == null) {
			if (get_option($where) == 'beforeandafter') {
				return $button . $content . $button;
			} else if (get_option($where) == 'before') {
				return $button . $content;
			} else {
				return $content . $button;
			}
		} else {
			return $content;
		}
	}
}

/**
 * function to set different meta's for Sharethis option
 * 
 * @access internal
 * @return void
 */
function fb_share_meta_header()
{
	$fbTypeOf = get_option('fb_type_of');
	$fbAdminIDs = get_option('fb_admin_ids');
	$fbAppID = get_option('fb_app_id');	
	$fbPageID = get_option('fb_page_id');
	$fbShareImage = get_option('fb_share_image');
	
	if ($fbTypeOf == '')
	{
		$fbTypeOf = 'website';	
	}
	
	if ($fbAdminIDs != '')
	{
		echo '<meta property="fb:admins" content="'.$fbAdminIDs.'" />'."\n";
	}
	if ($fbAppID != '') {
	echo '<meta property="fb:app_id" content="'.$fbAppID.'" />'."\n";
    }
    if ($fbPageID != '') {
	echo '<meta property="fb:page_id" content="'.$fbPageID.'" />'."\n";
    }
	echo '<meta property="og:site_name" content="'.htmlspecialchars(get_bloginfo('name')).'" />'."\n";
	echo '<meta property="og:type" content="'. $fbTypeOf .'" />'."\n";
	if(is_single() || is_page()) {
	
	//Reset query to double sure that it gives ID
	wp_reset_query();
	$currentPage = get_the_ID();
	$image = xh_get_featured_image($currentPage);
	if ($image != '')
	{
		echo '<meta property="og:image" content="'. $image .'" />'."\n";	}
	else {
		echo '<meta property="og:image" content="'. $fbShareImage .'" />'."\n";
	}
		
	$title = the_title('', '', false);
	$php_version = explode('.', phpversion());
	if(count($php_version) && $php_version[0]>=5)
		$title = html_entity_decode($title,ENT_QUOTES,'UTF-8');
	else
		$title = html_entity_decode($title,ENT_QUOTES);
    	echo '<meta property="og:title" content="'.htmlspecialchars($title).'" />'."\n";
    	echo '<meta property="og:url" content="'.get_permalink().'" />'."\n";
	if($tt_like_settings['use_excerpt_as_description']=='true') {
    		$description = trim(get_the_excerpt());
		if($description!='')
		    	echo '<meta property="og:description" content="'.htmlspecialchars($description).'" />'."\n";
	}
    } else {
    	echo '<meta property="og:title" content="'.get_bloginfo('name').'" />';
    	echo '<meta property="og:url" content="'.get_bloginfo('url').'" />';
    	echo '<meta property="og:description" content="'.get_bloginfo('description').'" />';
    }
    st_widget_on_head();
}

/**
 * function to print Sharethis widget
 * 
 * @access internal
 * @return void
 */
function st_widget_on_head() {
	$widget = get_option('st_widget');
	if ($widget == '') {
	}
	else{
		$widget = st_widget_fix_domain($widget);
		$widget = preg_replace("/\&/", "&amp;", $widget);
	}
	print($widget);
}

function st_widget_fix_domain($widget) {
	return preg_replace(
		"/\<script\s([^\>]*)src\=\"http\:\/\/sharethis/"
		, "<script $1src=\"http://w.sharethis"
		, $widget
		);
}

/**
 * Register options for the plugin
 * 
 * @access internal
 * @return void
 */
//fb_display_page,fb_display_front,fb_where,fb_style
function fb_init(){

    if(function_exists('register_setting')){
		register_setting('fb-options', 'fb_share_image');
		register_setting('fb-options', 'fb_type_of');
		register_setting('fb-options', 'fb_app_id');	
		register_setting('fb-options', 'fb_page_id');		
		register_setting('fb-options', 'fb_admin_ids');
    register_setting('fb-options', 'fb_version');
    register_setting('fb-options', 'fb_admin_ids');
    register_setting('fb-options','fb_like_seq');
    register_setting('fb-options','fb_follow_seq');
#    register_setting('fb-options','fb_facepile_seq');
#    register_setting('fb-options', 'fb_rss_where');
		register_setting('fb-options', 'fb_count_type');
		register_setting('fb-options', 'fb_share_show_send');
    register_setting('fb-options','fb_follow_active');
    register_setting('fb-options', 'fb_follow_id');
    register_setting('fb-options','fb_follow_txt');
    register_setting('fb-options','fb_facepile_id');
    register_setting('fb-options','fb_facepile_url');
    register_setting('fb-options','fb_facepile_count');
    register_setting('fb-options','fb_facepile_wd');
    register_setting('fb-options','fb_like_active');

    register_setting('tw-options','twitter_follow_active');
    register_setting('tw-options','twitter_width');
    register_setting('tw-options','twitter_text');
    register_setting('tw-options','twitter_count');
    register_setting('tw-options','twitter_follow_txt');
    register_setting('tw-options','twitter_id');
    register_setting('tw-options','twitter_follow_id');
    register_setting('tw-options','twitter_follow_seq');
    register_setting('tw-options','twitter_seq');


    register_setting('st-options','st_plug_active');
    register_setting('gb-options', 'fb_display_page');
    register_setting('gb-options', 'fb_display_front');
    register_setting('gb-options', 'fb_style');
    register_setting('gb-options', 'fb_where');
    register_setting('st-options', 'st_plug_seq');
    }
}

/**
 * Fetch public key in widget
 * 
 * @access internal
 * @return Mixed Public Key Or False
 */
function getKeyFromTag(){
	$widget = get_option('st_widget');
	$pattern = "/publisher\=([^\&\"]*)/";
	preg_match($pattern, $widget, $matches);
	$pkey = $matches[1];
	if(empty($pkey)){
		return false;
	}
	else{
		return $pkey;
	}
}

/**
 * Generates public key for Sharethis
 * 
 * @access internal
 * @return String Public Key
 */
function makePkey(){
	return "wp.".sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),mt_rand( 0, 0x0fff ) | 0x4000,mt_rand( 0, 0x3fff ) | 0x8000,mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ) );
}

/**
 * Install sharethis options on plugin installation
 * 
 * @access internal
 * @return void
 */
function install_ShareThis(){
	$publisher_id = get_option('st_pubid'); //pub key value
	$widget = get_option('st_widget'); //entire script tag
	$newUser=false;

	if (get_option('st_version') == '') {
		update_option('st_version', '4x');
	}
	
	if(empty($publisher_id)){
		if(!empty($widget)){
			$newPkey=getKeyFromTag();
			if($newPkey==false){
				$newUser=true;
				update_option('st_pubid',trim(makePkey()));
			}else{
				update_option('st_pubid',$newPkey); //pkey found set old key
			}
		}else{
			$newUser=true;
			update_option('st_pubid',trim(makePkey()));
		}
	}
	
	if($widget==false || !preg_match('/stLight.options/',$widget)){
		$pkey2=get_option('st_pubid'); 
		$widget ="<script charset=\"utf-8\" type=\"text/javascript\">var switchTo5x=true;</script>";
		$widget.="<script charset=\"utf-8\" type=\"text/javascript\" src=\"http://w.sharethis.com/button/buttons.js\"></script>";
		$widget.="<script type=\"text/javascript\">stLight.options({publisher:'$pkey2'});var st_type='wordpress".trim(get_bloginfo('version'))."';</script>";
		update_option('st_widget',$widget);
	}
 

}

/**
 * Add options to activate plugin
 * 
 * @access internal
 * @return void
 */

function fb_activate(){
	add_option('fb_share_image', '');	
  add_option('fb_like_active','on');
  add_option('fb_like_seq','0');
	add_option('fb_type_of', 'website');
	add_option('fb_app_id', '');
	add_option('fb_page_id', '');
	add_option('fb_admin_ids', '');
	add_option('fb_api_type', 'on');
  add_option('fb_where', 'before');
#  add_option('fb_rss_where', 'before');
  add_option('fb_style', 'float:left; margin-left: 10px;');
  add_option('fb_admin_ids','');
  add_option('fb_version', 'button_count');
  add_option('fb_display_page', '1');
  add_option('fb_display_front', '1');
  add_option('fb_count_type', 'box_count');
	add_option('fb_share_show_send', '0');
  add_option('fb_follow_active','0');
  add_option('fb_follow_seq','0');
  add_option('fb_follow_id','');
  add_option('fb_follow_txt','');
  add_option('fb_facepile_id','');
#  add_option('fb_facepile_seq','0');
  add_option('fb_facepile_url','');
  add_option('fb_facepile_count','');
  add_option('fb_facepile_wd','');

  add_option('twitter_follow_active','on');
  add_option('twitter_follow_seq','0');
  add_option('twitter_width','110');
  add_option('twitter_text','');
  add_option('twitter_count','on');
  add_option('twitter_follow_txt','');
  add_option('twitter_id','');
  add_option('twitter_seq','0');
  add_option('twitter_follow_id','');

add_option('st_version','4x');
add_option('st_sent','true');
add_option('st_upgrade_five','5x');
add_option('st_tags',"<span class='st_sharethis' st_title='<?php the_title(); ?>' st_url='<?php the_permalink(); ?>' displayText='ShareThis'></span>");
add_option('st_services','facebook,twitter,email,sharethis');
add_option('st_current_type','classic');
add_option('st_plug_active','');
add_option('st_plug_seq','0');

install_ShareThis();
enable_role();
}

/**
 * Add capability for the Administrator role
 * 
 * @access internal
 * @return void
 */
function enable_role()
{
#global $wpdb , $wp_roles, $wp_version;
	// Check for capability
	if ( !current_user_can('activate_plugins') ) 
		return;

	// Set the capabilities for the administrator
	$role = get_role('administrator');
	// We need this role, no other chance
	if ( empty($role) ) {
		update_option( "fb_init_check", __('Sorry, Social Media works only with a role called administrator',"social-media") );
		return;
	}

  $role->add_cap('Social Media');

}

/**
 * DeActivate the plugin options
 * 
 * @access internal
 * @return void
 */

function fb_deactivate()
{
  delete_option('fb_share_image');	
	delete_option('fb_type_of');
	delete_option('fb_app_id');
	delete_option('fb_page_id');
	delete_option('fb_admin_ids');
	delete_option('fb_api_type');
  delete_option('fb_where');
  delete_option('fb_admin_ids');
  delete_option('fb_style');
  delete_option('fb_version');
  delete_option('fb_display_page');
  delete_option('fb_display_front');
  delete_option('fb_count_type');
	delete_option('fb_share_show_send');
  delete_option('fb_follow_active');
  delete_option('fb_follow_id');
  delete_option('fb_follow_txt');
  delete_option('fb_facepile_id');
  delete_option('fb_facepile_url');
  delete_option('fb_facepile_count');
  delete_option('fb_facepile_wd');
  delete_option('twitter_follow_active');
  delete_option('twitter_width');
  delete_option('twitter_text');
  delete_option('twitter_count');
  delete_option('twitter_follow_txt');
  delete_option('twitter_id');
  delete_option('twitter_follow_id');
  delete_option('st_version');
  delete_option('st_sent');
  delete_option('st_upgrade_five');
  delete_option('st_tags');
  delete_option('st_services');
  delete_option('st_current_type');
  delete_option('st_plug_active');
  delete_option('st_pubid');
  delete_option('st_widget');
  delete_option('st_pubid');
  delete_option('fb_like_active');

// remove capability
  fb_social_remove_capability("Social Media");

}

/**
 * function to load css
 * 
 * @access internal
 * @return void
 */
function load_media_styles()
{
  wp_enqueue_style( 'css', MEDIA_URLPATH .'/css/media.css', array());
}

/**
 * Deregister a capability from all classic roles
 * 
 * @access internal
 * @param string $capability name of the capability which should be removed
 * @return void
 */
function fb_social_remove_capability($capability)
{
	// this function remove the $capability only from the classic roles
	$check_order = array("subscriber", "contributor", "author", "editor", "administrator");

	foreach ($check_order as $role) {

		$role = get_role($role);
		$role->remove_cap($capability) ;
	}

}

add_filter('the_content', 'fb_share');

if(is_admin()){
    add_action('admin_menu', 'fb_options');
    add_action('admin_init', 'fb_init');
    add_action('admin_print_styles','load_media_styles');
}

add_action('wp_head', 'fb_share_meta_header');
register_activation_hook( __FILE__, 'fb_activate'); // activate
register_uninstall_hook(__FILE__,'fb_deactivate');


?>