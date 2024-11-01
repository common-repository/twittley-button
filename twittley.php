<?php

/*
Plugin Name: Twittley Retweet Button
Plugin URI: http://twittley.com/twittley-button.php
Description: Adds the Twittley Retweet Button to your posts, pages, and rss feeds. Making is incredibly easy for your users to retweet without leaving your site!
Version: 2.0
Author: Twittley.com
Author URI: http://twittley.com
*/


function getdesc($num,$content) 
{  
$cnt = strip_tags($content);
$cnt =  ereg_replace("(https?)://", "", $cnt);
$cnt = str_replace("\n","",$cnt);
$cnt = str_replace('"',"",$cnt);
$cnt = html_entity_decode($cnt, ENT_QUOTES, "utf-8");

$limit = $num+1;  
$cnt = explode(' ', $cnt, $limit);  
array_pop($cnt);  

$cnt = implode(" ",$cnt)."...";  

return $cnt;  
}

function twittley_add_button($pagecontent) {
	global $post;
	$url = ''; // blank
	if (get_post_status($post->ID) == 'publish')
		$url = get_permalink(); // get url if possible

  if (!get_option('twittley_pages') && is_page()) //do not display button
      return $pagecontent;
 
  elseif (!get_option('twittley_feed') && is_feed()) //do not display button
      return $pagecontent;
  
  elseif (!get_option('twittley_posts') && is_single()) //do not display button
      return $pagecontent;
   
  elseif (!get_option('twittley_index') && is_home()) 
      return $pagecontent;
  else
  {
   
     $title=the_title("","",false);
     $description=get_post_meta($post->ID, 'description', true);

     if (!$description)
     {
     $description=getdesc(50,$pagecontent);
     $description= str_replace("http://", "", $description);
     }

     $getkeywords = get_the_tags(); if ($getkeywords) { foreach($getkeywords as $tag) { $keywords=$keywords.$tag->name.','; } }
     $keywords=substr($keywords, 0, count($keywords)-2);

     

     $twittleystyle=get_option('twittley_design');
     if ($twittleystyle=="7")
     $twittleystyle=rand(0,6);

     foreach((get_the_category()) as $category) { 
     if ($category->cat_name!="Uncategorized")
     $cat=$cat.$category->cat_name.','; 
        } 
     
     $cat=substr($cat, 0, count($cat)-2);
     
     if ($keywords=="")
     $keywords=$cat;

     if ($keywords=="")
     $keywords=get_option('twittley_tags');

     $keywords = str_replace('"',"",$keywords);
     
     $button = '<div id="twittley_button" style="'.get_option('twittley_css').'">
	       <script>
               var twittleyurl="'.$url.'";
               var twittleytitle="'.$title.'";
               var twittleykeywords="'.$keywords.'";
	       var twittleydescription="'.$description.'";
               var twittleystyle="'.$twittleystyle.'";
               </script> 
	       <script src="http://twittley.com/button/button.js"></script><noscript><a href="http://twittley.com/" title="'.$title.'">'.$title.'</a></noscript></div>';

  switch (get_option('twittley_position'))
  {

  case "0": //before
  return $button.$pagecontent;
  break;

  case "1": //after
  return $pagecontent.$button;
  break;

  case "2": //before and after
  return $button.$pagecontent.$button;
  break;
      }
   }
}

function twittley_options_menu() // adds menu
{
    add_options_page('Twittley Button Settings', 'Twittley Button Settings', 8, __FILE__, 'twittley_options_page');
}

function twittley_options_page() 
{
  //0 == before
  //1 == after
  //2 == before & after

  switch (get_option('twittley_position'))
  {
  case "0": //before
  $positionchecked[0]='checked'; 
  break;

  case "1": //after
  $positionchecked[1]='checked'; 
  break;

  case "2": //before and after
  $positionchecked[2]='checked'; 
  break;
}

   if (get_option('twittley_posts')=='1') // display it
  $postschecked='checked';

  if (get_option('twittley_pages')=='1') // display it
  $pageschecked='checked';
  
 

  if (get_option('twittley_feed')=='1') // display it
  $feedchecked='checked';

  if (get_option('twittley_index')=='1') // display it
  $indexchecked='checked';

  if (get_option('twittley_design')=='0') // button 1
  $btn01='checked';
  if (get_option('twittley_design')=='1') // button 2
  $btn02='checked';
  if (get_option('twittley_design')=='2') // button 3
  $btn03='checked';
  if (get_option('twittley_design')=='3') // button 4
  $btn04='checked';
  if (get_option('twittley_design')=='4') // button 5
  $btn05='checked';
  if (get_option('twittley_design')=='5') // button 6
  $btn06='checked';
  if (get_option('twittley_design')=='6') // button 7
  $btn07='checked';
  if (get_option('twittley_design')=='7') // button random
  $btn08='checked';
  
  echo '<div class="wrap">';
  echo '<div class="icon32" id="icon-options-general"><br/></div>';
  echo '<h2>Twittley Button Settings</h2>';

 

  echo '<div style="float:left;"><a href="http://twittley.com"><img src="../wp-content/plugins/twittley-button/twittley-logo-wp.gif"></a></div>';

  echo '<div style="float:right;width:40%;height:330px;border:2px solid #002132;">
<div style="height:15px;color:#fff;background-color:#002132;font-size:11px;padding:2px;line-height:110%;">Twittley News</div>
<iframe style="width:100%;height:100%;border:0;" src="http://twittley.com/buttonnews.php"></iframe></div>';

  echo '<div style="width:58%;overflow:hidden;float:left;"><p>This plugin will install the Twittley button for each of your blog posts in both the content of your posts and the RSS feed.</p>';
  echo ' <form method="post" action="options.php">';
  wp_nonce_field('update-options');

  echo '<p><b>Button Position</b></p>';
  echo '<p>
        <input type="radio" value="0" name="twittley_position" group="twittley_position" '.$positionchecked[0].'/> 
        <label for="twittley_position1">Before your post.</label>     
        </p>';

  echo '<p>
        <input type="radio" value="1" name="twittley_position" group="twittley_position" '.$positionchecked[1].'/> 
        <label for="twittley_position2">After your post.</label>     
        </p>';

  echo '<p>
        <input type="radio" value="2" name="twittley_position" group="twittley_position" '.$positionchecked[2].'/> 
        <label for="twittley_position3">Before and after your post.</label>     
        </p>';
  

  echo '<p><b>Display Button</b></p>';

  echo '<p><input type="checkbox" value="1" name="twittley_posts" '.$postschecked.'/> 
         <label for="twittley_posts">Display Twittley button on posts.</label></p>';

  echo '<p><input type="checkbox" value="1" name="twittley_feed"  '.$feedchecked.'/> 
         <label for="twittley_feed">Display Twittley button in RSS feeds.</label></p>';

  echo '<p><input type="checkbox" value="1" name="twittley_pages" '.$pageschecked.'/> 
         <label for="twittley_pages">Display Twittley button on pages.</label></p>';

  echo '<p><input type="checkbox" value="1" name="twittley_index" '.$indexchecked.'/> 
         <label for="twittley_index">Display Twittley button on index page.</label></p>';


  echo '<p><b>Choose A Color</b></p>';
  echo '<div style="width:100%;overflow:hidden;">

        <div style="width:150px;float:left;text-align:center;">
        <label for="twittley_design">Cyan</label>   
        <br><img src="http://twittley.com/img/sm/button/cyan/preview.jpg" style="margin-bottom:3px;"><br>
        <input type="radio" value="0" name="twittley_design" group="twittley_design" '.$btn01.'/> 
        </div>
        
        <div style="width:150px;float:left;text-align:center;">
        <label for="twittley_design">Lime</label>   
        <br><img src="http://twittley.com/img/sm/button/lime/preview.jpg" style="margin-bottom:3px;"><br>
        <input type="radio" value="1" name="twittley_design" group="twittley_design" '.$btn02.'/> 
        </div>
        
		<div style="width:150px;float:left;text-align:center;">
        <label for="twittley_design">Orange</label>   
        <br><img src="http://twittley.com/img/sm/button/orange/preview.jpg" style="margin-bottom:3px;"><br>
        <input type="radio" value="2" name="twittley_design" group="twittley_design" '.$btn03.'/> 
        </div>
        
        <div style="width:150px;float:left;text-align:center;">
        <label for="twittley_design">Pink</label>   
        <br><img src="http://twittley.com/img/sm/button/pink/preview.jpg" style="margin-bottom:3px;"><br>
        <input type="radio" value="3" name="twittley_design" group="twittley_design" '.$btn04.'/> 
        </div>
        
        <div style="width:150px;float:left;text-align:center;">
        <label for="twittley_design">Red</label>   
        <br><img src="http://twittley.com/img/sm/button/red/preview.jpg" style="margin-bottom:3px;"><br>
        <input type="radio" value="4" name="twittley_design" group="twittley_design" '.$btn05.'/> 
        </div>
        
        <div style="width:150px;float:left;text-align:center;">
        <label for="twittley_design">Purple</label>   
        <br><img src="http://twittley.com/img/sm/button/violet/preview.jpg" style="margin-bottom:3px;"><br>
        <input type="radio" value="5" name="twittley_design" group="twittley_design" '.$btn06.'/> 
        </div>
        
        <div style="width:150px;float:left;text-align:center;">
        <label for="twittley_design">Yellow</label>   
        <br><img src="http://twittley.com/img/sm/button/yellow/preview.jpg" style="margin-bottom:3px;"><br>
        <input type="radio" value="6" name="twittley_design" group="twittley_design" '.$btn07.'/> 
        </div>
		
         <div style="width:150px;float:left;text-align:center;">
        <label for="twittley_design">Shuffle Mode</label>
		<br><img src="http://twittley.com/img/sm/button/shuffle.jpg" style="margin-bottom:3px;"><br>
        <input type="radio" value="7" name="twittley_design" group="twittley_design" '.$btn08.'/> 
        </div> 

        </div>';
 
  echo '<p><b>Button Style</b></p>';
  echo ' <p><input type="text" style="width:300px;" value="'.get_option('twittley_css').'" name="twittley_css" /></p>
         <span class="setting-description">Default: <code>float: left; margin-right:8px;</code></span>';

  echo '<p><b>Default Tags</b></p>';
  echo ' <p><input type="text" style="width:300px;" value="'.get_option('twittley_tags').'" name="twittley_tags" /></p>
         <span class="setting-description">Example: <code>Cars, Nissan, 350Z (comma seperated)</code></span>';

  echo '<input type="hidden" name="action" value="update" />
        <input type="hidden" name="page_options" value="twittley_position,twittley_pages,twittley_index,twittley_posts,twittley_feed,twittley_css,twittley_design,twittley_tags" />
        <p class="submit">
        <input type="submit" name="Submit" value="';
       _e('Save Changes');
  echo '" /> </p>';   

  echo '</form>';
  echo '</div></div>';
}

function twittley_remove_filter($content) 
{
    remove_action('the_content', 'twittley_add_button');
    return $content;
}


add_action('admin_menu', 'twittley_options_menu');

//add button to page
add_filter('the_content', 'twittley_add_button');
add_filter('get_the_excerpt', 'twittley_remove_filter', 9); 


//add options
add_option('twittley_position',0);
add_option('twittley_design',0);
add_option('twittley_feed',1);
add_option('twittley_pages',1);
add_option('twittley_index',1);
add_option('twittley_posts',1);
add_option('twittley_tags',"");


add_option('twittley_css', 'float: left; margin-right: 8px;');
?>
