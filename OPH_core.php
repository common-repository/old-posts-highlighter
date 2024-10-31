<?php
/*  
	Old Posts Highlighter by MKH : http://www.mkh.fr/old-posts-highlighter/
	This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
 
function OPH_Old_Posts_Highlighter ()  
	{
		if (OPH_update_time()) 
		{
			update_option('OPH_last_update', time());
			OPH_promote_old_posts();
		}
	} 

function OPH_promote_old_posts () 
	{
		global $wpdb;
		$omitCats = get_option('OPH_omit_cats');
		$forcedposts = get_option('OPH_FORCED_POSTS'); 
		$ageLimit = get_option('OPH_age_limit');
		
		if (!isset($omitCats)) 
		{
			$omitCats = OPH_OMIT_CATS;
		}
		
		if (!isset($forcedposts)) 
		{
			$forcedposts = OPH_FORCED_POSTS;
		}
		
		if (!isset($ageLimit)) 
		{
			$ageLimit = OPH_AGE_LIMIT;
		}
		
		$sql = "
				SELECT ID
				FROM $wpdb->posts
				WHERE post_type = 'post'
				AND post_status = 'publish'
				AND post_date < curdate( ) - INTERVAL ".$ageLimit." DAY
				";

					  
		if (($omitCats!='') && ( $forcedposts!=''))
		{
			$sql = $sql."
						AND NOT(ID IN (SELECT tr.object_id 
						FROM $wpdb->terms  t 
						inner join $wpdb->term_taxonomy tax on t.term_id=tax.term_id and tax.taxonomy='category' 
						inner join $wpdb->term_relationships tr on tr.term_taxonomy_id=tax.term_taxonomy_id 
						WHERE t.term_id IN (".$omitCats.")))
						UNION SELECT ID FROM wplol_posts WHERE ID IN (".$forcedposts.")
						";
		}
		else if ($omitCats!='')
		{
			$sql = $sql."
						AND NOT(ID IN (SELECT tr.object_id 
						FROM $wpdb->terms  t 
						inner join $wpdb->term_taxonomy tax on t.term_id=tax.term_id and tax.taxonomy='category' 
						inner join $wpdb->term_relationships tr on tr.term_taxonomy_id=tax.term_taxonomy_id 
						WHERE t.term_id IN (".$omitCats.")))						
						"; 
		}
				
		$sql = $sql."            
					ORDER BY RAND() 
					LIMIT 1 
					";
		$oldest_post = $wpdb->get_var($sql);  
		
		if (isset($oldest_post)) 
		{
			OPH_update_old_post($oldest_post);
		}
	}
/*
$omitCats = get_option('OPH_omit_cats');
$forcedposts = get_option('OPH_FORCED_POSTS'); 
global $wpdb;			  
	$sql1 = "SELECT ID
            FROM $wpdb->posts
            WHERE post_type = 'post'
                  AND post_status = 'publish'                  
                  ";
$sql1 = $sql1."AND NOT(ID IN (SELECT tr.object_id 
                                    FROM $wpdb->terms  t 
                                          inner join $wpdb->term_taxonomy tax on t.term_id=tax.term_id and tax.taxonomy='category' 
                                          inner join $wpdb->term_relationships tr on tr.term_taxonomy_id=tax.term_taxonomy_id 
                                    WHERE t.term_id IN (".$omitCats.")))									
				UNION SELECT ID FROM wplol_posts WHERE ID IN (".$forcedposts.")";
//echo $forcedposts;	 //echo "test";					
//$sql2 = mysql_query($sql1);
//while ($row = mysql_fetch_array($sql2, MYSQL_ASSOC)) {
//print_r($row);echo "<br/>";
//}	
$forcedposts = get_option('OPH_FORCED_POSTS'); 	
if (eregi("wp-admin/", $_SERVER['REQUEST_URI'])){
echo $sql1;
}*/

function OPH_update_old_post($oldest_post) 
	{
		global $wpdb;
		$post = get_post($oldest_post);
		$origPubDate = get_post_meta($oldest_post, 'OPH_original_pub_date', true); 
		
		if (!(isset($origPubDate) && $origPubDate!='')) 
		{
			$sql = "SELECT post_date from ".$wpdb->posts." WHERE ID = '$oldest_post'";
			$origPubDate=$wpdb->get_var($sql);
			add_post_meta($oldest_post, 'OPH_original_pub_date', $origPubDate);
			$origPubDate = get_post_meta($oldest_post, 'OPH_original_pub_date', true); 
		}
		
		$OPH_pos = get_option('OPH_pos');
		
		if (!isset($OPH_pos)) 
		{
			$OPH_pos = 0;
		}
		
		if ($OPH_pos==1) 
		{
			$new_time = date('Y-m-d H:i:s');
			$gmt_time = get_gmt_from_date($new_time);
		} 
		
		else 
		{
			$lastposts = get_posts('numberposts=1&offset=1');
			foreach ($lastposts as $lastpost) 
			{
				$post_date = strtotime($lastpost->post_date);
				$new_time = date('Y-m-d H:i:s',mktime(date("H",$post_date),date("i",$post_date),date("s",$post_date)+1,date("m",$post_date),date("d",$post_date),date("Y",$post_date)));
				$gmt_time = get_gmt_from_date($new_time);
			}
		}
		
		$sql = "UPDATE $wpdb->posts SET post_date = '$new_time',post_date_gmt = '$gmt_time',post_modified = '$new_time',post_modified_gmt = '$gmt_time' WHERE ID = '$oldest_post'";		
		
		$wpdb->query($sql);
		
		if (function_exists('wp_cache_flush')) 
		{
			wp_cache_flush();
		}		
			
		
		
		do_action( 'old_post_highlighted', $post );	
	}

function OPH_the_content($content) 
	{
		global $post;
		$showPub = get_option('OPH_show_original_pubdate');
		
		if (!isset($showPub)) 
		{
			$showPub = 1;
		}
		
		$givecredit = get_option('OPH_give_credit');
		
		if (!isset($givecredit)) 
		{
			$givecredit = 1;
		}
		
		$origPubDate = get_post_meta($post->ID, 'OPH_original_pub_date', true);
		$dateline = '';
		
		if (isset($origPubDate) && $origPubDate!='') 
		{
			if ($showPub || $givecredit) 
			{
				$dateline.='<p id="OPH">';
				if ($showPub) 
				{
					setlocale (LC_ALL, "fr_FR");
					$origPubDate = date("d/m/Y", strtotime($origPubDate));
					$dateline.=__("Article publié pour la première fois le $origPubDate", 'Old_Posts_Highlighter');
				}
				if ($givecredit) 
				{
						$dateline.=__('Mis en avant grâce à <a rel="nofollow" href="http://www.mkh.fr/old-posts-highlighter/">Old Posts Highlighter</a>', 'Old_Posts_Highlighter');
				}
				$dateline.='</p>';
			}
		}
		$atTop = get_option('OPH_at_top');
		
		if (isset($atTop) && $atTop) 
		{
			$content = $dateline.$content;
		} 
		
		else 
		{
			$content = $content.$dateline;
		}
		return $content;
	}

function OPH_update_time () 
	{
		$last = get_option('OPH_last_update');		
		$interval = get_option('OPH_interval');		
		
		if (!(isset($interval) && is_numeric($interval))) 
		{
			$interval = OPH_INTERVAL;
		}
		
		$aleatoire = get_option('OPH_interval_aleatoire');	
		
		if (!(isset($aleatoire) && is_numeric($aleatoire))) 
		{
			$aleatoire = OPH_INTERVAL_aleatoire;
		}
		
		if (false === $last) 
		{
			$ret = 1;
		} 
		
		else if (is_numeric($last)) 
		{ 
			$ret = ( (time() - $last) > ($interval+rand(0,$aleatoire)));
		}
		
		return $ret;
	}
	
?>