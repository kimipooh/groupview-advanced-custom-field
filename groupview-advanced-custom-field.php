<?php
/*
Plugin Name: Advanced Custom Field: Shortcode for Group Field View
Plugin URI: 
Description: The plugin is for adding the group view for Advanced Custom Field to Wordpress.
Version: 0.21
Author: Kimiya Kitani
Author URI: 
*/

$default_var = array(
	'groupview_custom_field_ver'		=>	'0.3',
	'groupview_custom_field_modified'	=>	'2013-07-23',
);

/* hogehoge_01〜hogehoge_20までのうちデータとしてあるものを表示
 hogehoge_01はヘッダ情報、hogehoge_最後の数字はフッターとして<div>を分ける

５項目のデータがあるなら
 <div id="gcustom_header">
    <p>hogehoge_01データ</p>
 </div>
 
 <div id="gcustom_contents">
    <p>hogehoge_02データ</p>
    <p>hogehoge_03データ</p>
    <p>hogehoge_04データ</p>
 </div>     

 <div id="gcustom_footer">
    <p>hogehoge_05データ</p>
 </div>
*/

function shortcode($atts){
  // gnome = フィールド名の接頭語（hogehoge_01 の「hogehoge_」部分）
  // limit = フィールド数（最大99項目まで、デフォルトは20）
  extract(shortcode_atts(
	array('gname' => '', 'limit'=>20 ), $atts));

  $gname = esc_attr(strip_tags(html_entity_decode($gname,ENT_QUOTES)));
  $limit = esc_attr(strip_tags(html_entity_decode($limit,ENT_QUOTES)));
  $html_data = "";

  // $gnameがなければ処理を終了
  if(empty($gname)) return;

  // 数値データ以外はデフォルトの20を設定
  if(!is_numeric($limit)) $limit = 20;
  // 数値であり得ない数値なら20を設定
  else if($limit <= 0 || $limit >99) $limit = 20;

  $data = ""; $j = 0;
  for ($i=1; $i < $limit ; $i++){
    $ci = sprintf("%02d", $i); 
    $tmp = get_field($gname . $ci, $post->ID); 
    // $tmp に、必要ならセキュリティ対策を  
    // $tmp = esc_attr(strip_tags(html_entity_decode($tmp,ENT_QUOTES))); 等
    if(!empty($tmp)) $data[$j++] = $tmp;
  }

  if(!empty($data[0])){
      $html_data .= "<div id='gcustom_header'>\n";

      $html_data .= "<p>" . $data[0] . "</p>\n";

      $html_data .= "</div>\n";
  }
  
  $html_data .= "<div id='gcustom_body'>\n"; 

  $html_data .= "<ul>\n";

  $num = count($data);
  for ($i=1; $i < $num - 1 ; $i++){
      $html_data .=  "  <li>" . $data[$i] . "</li>\n";
  }

  $html_data .= "</ul>\n";

  $html_data .=  "</div>\n";

  $html_data .=  "<div id='gcustom_footer'>\n";
  $html_data .=  "<p class='text-right'>". $data[$i] . "</p>\n";
  $html_data .=  "</div>\n";
  
  echo apply_filters('the_content', $html_data);
}

//ショートコード [groupview-custom-field gname=""]
add_shortcode('groupview-advanced-custom-field', 'shortcode');

//add_action('save_post', 'push_gdata');
// 記事更新時のみ
//add_filter( 'wp_insert_post_data' , 'push_gdata' , '99', 2 );
// 記事新規追加＆更新
// wp_insert_postを使うか、save_postなら優先順位をデフォルトから下げないといけない（新規追加時対策）
// http://www.sandalot.com/wordpress%E3%81%A7%E6%96%B0%E8%A6%8F%E6%8A%95%E7%A8%BF%E6%99%82%E3%80%81%E5%88%A5%E3%81%AA%E6%8A%95%E7%A8%BF%E3%82%82%E8%BF%BD%E5%8A%A0%E3%81%99%E3%82%8B%E6%96%B9%E6%B3%95/
add_action('wp_insert_post','push_gdata2');

// 記事更新時のみ
function push_gdata($data, $postarr){
  if(in_category('vrf-research')){
    $data['post_content'] = html_data_output('vrf-notice_');
  }

  return $data;
}

// 記事新規追加＆更新
function push_gdata2(){
  global $post;

  remove_action('wp_insert_post','push_gdata2');

  $data['ID'] = $post->ID;
  if(in_category('vrf-research')){
    $data['post_content'] = html_data_output('vrf-notice_');
    wp_update_post($data);
  }
  add_action('wp_insert_post','push_gdata2');
}


function html_data_output($gname, $limit=20){
  // $gnameがなければ処理を終了
  if(empty($gname)) return;

  // 数値データ以外はデフォルトの20を設定
  if(!is_numeric($limit)) $limit = 20;
  // 数値であり得ない数値なら20を設定
  else if($limit <= 0 || $limit >99) $limit = 20;

  $data = ""; $j = 0;
  for ($i=1; $i < $limit ; $i++){
    $ci = sprintf("%02d", $i); 
    $tmp = get_field($gname . $ci, $post->ID); 
    // $tmp に、必要ならセキュリティ対策を  
    // $tmp = esc_attr(strip_tags(html_entity_decode($tmp,ENT_QUOTES))); 等
    if(!empty($tmp)) $data[$j++] = $tmp;
  }

  if(!empty($data[0])){
      $html_data .= "<div id='gcustom_header'>\n";

      $html_data .= "<p>" . $data[0] . "</p>\n";

      $html_data .= "</div>\n";
  }
  
  $html_data .= "<div id='gcustom_body'>\n"; 

  $html_data .= "<ul>\n";

  $num = count($data);
  for ($i=1; $i < $num - 1 ; $i++){
      $html_data .=  "  <li>" . $data[$i] . "</li>\n";
  }

  $html_data .= "</ul>\n";

  $html_data .=  "</div>\n";

  $html_data .=  "<div id='gcustom_footer'>\n";
  $html_data .=  "<p class='text-right'>". $data[$i] . "</p>\n";
  $html_data .=  "</div>\n";
  
  return $html_data;
}



?>