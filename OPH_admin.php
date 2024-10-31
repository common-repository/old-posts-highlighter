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

require_once('Old_Posts_Highlighter.php');
require_once('OPH_core.php');

function OPH_head_admin() 
{
	wp_enqueue_script('jquery-ui-tabs');
	$home = get_settings('siteurl');
	$base = '/'.end(explode('/', str_replace(array('\\','/OPH_admin.php'),array('/',''),__FILE__)));
	$stylesheet = $home.'/wp-content/plugins' . $base . '/css/old_posts_highlighter.css';
	echo('<link rel="stylesheet" href="' . $stylesheet . '" type="text/css" media="screen" />');

}

function OPH_options() 
{	 	
	$message = null;
	$message_updated = __("Options du module Old Posts Highlighter mises à jour", 'Old_Posts_Highlighter');
	
	if (!empty($_POST['OPH_action'])) 
	{
		$message = $message_updated;	
		
		if (isset($_POST['OPH_interval'])) 
		{
			update_option('OPH_interval',$_POST['OPH_interval']);
		}
		
		if (isset($_POST['OPH_interval_aleatoire'])) 
		{
			update_option('OPH_interval_aleatoire',$_POST['OPH_interval_aleatoire']);
		}
		
		if (isset($_POST['OPH_age_limit'])) 
		{ 
			update_option('OPH_age_limit',$_POST['OPH_age_limit']);
		}
		
		if (isset($_POST['OPH_show_original_pubdate'])) 
		{
			update_option('OPH_show_original_pubdate',$_POST['OPH_show_original_pubdate']);
		}
		
		if (isset($_POST['OPH_give_credit'])) 
		{
			update_option('OPH_give_credit',$_POST['OPH_give_credit']);
		}
		
		if (isset($_POST['OPH_pos'])) 
		{
			update_option('OPH_pos',$_POST['OPH_pos']);
		}
		
		if (isset($_POST['OPH_at_top'])) 
		{
			update_option('OPH_at_top',$_POST['OPH_at_top']);
		}
		
		if (isset($_POST['post_category'])) 
		{
			update_option('OPH_omit_cats',implode(',',$_POST['post_category']));
		}	
		
		else {
			update_option('OPH_omit_cats','');			
		}
		
		if (isset($_POST['OPH_FORCED_POSTS'])) 
		{
			update_option('OPH_FORCED_POSTS',$_POST['OPH_FORCED_POSTS']);
		}
		
		print('
			<div id="message" class="updated fade">
				<p>'.__('Options du module OPH mises à jour.', 'Old_Posts_Highlighter').'</p>
			</div>');
	}
	
	$omitCats = get_option('OPH_omit_cats');
	$forcedposts = get_option('OPH_FORCED_POSTS');
	
	if (!isset($omitCats)) 
	{
		$omitCats = OPH_OMIT_CATS;		
	}
	
	if (!isset($forcedposts)) 
	{
		$forcedposts = OPH_FORCED_POSTS;		
	}
	
	$ageLimit = get_option('OPH_age_limit');
	
	if (!isset($ageLimit)) 
	{
		$ageLimit = OPH_AGE_LIMIT;
	}
	
	$showPub = get_option('OPH_show_original_pubdate');
	
	if (!isset($showPub))
	{
		$showPub = 1;
	}
	
	$atTop = get_option('OPH_at_top');
	
	if (!isset($atTop)) 
	{
		$atTop = 0;
	}
	
	$OPH_give_credit = get_option('OPH_give_credit');
	
	if (!isset($OPH_give_credit)) 
	{
		$OPH_give_credit = 1;
	}
	
	$OPH_pos = get_option('OPH_pos');
	
	if (!isset($OPH_pos)) 
	{
		$OPH_pos = 1;
	}
	
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
	
	print('
			<div class="wrap">
			
				<h2>'.__('Old Posts Highlighter : Mise en avant d\'anciens articles', 'Old_Posts_Highlighter').'</h2>
				
				<form id="OPH" name="OPH_Old_Posts_Highlighter" action="'.get_bloginfo('wpurl').'/wp-admin/options-general.php?page=OPH_admin.php" method="post">
				
					<input type="hidden" name="OPH_action" value="OPH_update_settings" />
					
					<p>'.__('Si vous voulez automatiser la (re)publication de vos articles sur les réseaux sociaux, voici les <b>outils gratuits</b> que nous vous conseillons (ils utilisent le <a href="/feed" target="_blank">RSS</a> de votre site) :', 'Old_Posts_Highlighter').'<br/>
					Twitter :  <a href="http://www.twitterfeed.com" target="_blank">Twitterfeed</a><br/>
					Facebook : <a href="http://app.rssgraffiti.com/" target="_blank">RSS Graffiti</a>
					</p>
					
					<fieldset class="options">	
					
						<div class="option">
							<label for="OPH_interval">'.__('Intervalle minimum entre les publications ', 'Old_Posts_Highlighter').'</label>
							<select name="OPH_interval" id="OPH_interval">
									<option value="'.OPH_15_MINUTES.'" '.OPH_optionselected(OPH_15_MINUTES,$interval).'>'.__('15 Minutes', 'Old_Posts_Highlighter').'</option>
									<option value="'.OPH_30_MINUTES.'" '.OPH_optionselected(OPH_30_MINUTES,$interval).'>'.__('30 Minutes', 'Old_Posts_Highlighter').'</option>
									<option value="'.OPH_1_HOUR.'" '.OPH_optionselected(OPH_1_HOUR,$interval).'>'.__('1 Heure', 'Old_Posts_Highlighter').'</option>																		<option value="'.OPH_2_HOURS.'" '.OPH_optionselected(OPH_2_HOURS,$interval).'>'.__('2 Heures', 'Old_Posts_Highlighter').'</option>
									<option value="'.OPH_4_HOURS.'" '.OPH_optionselected(OPH_4_HOURS,$interval).'>'.__('4 Heures', 'Old_Posts_Highlighter').'</option>
									<option value="'.OPH_6_HOURS.'" '.OPH_optionselected(OPH_6_HOURS,$interval).'>'.__('6 Heures', 'Old_Posts_Highlighter').'</option>
									<option value="'.OPH_12_HOURS.'" '.OPH_optionselected(OPH_12_HOURS,$interval).'>'.__('12 Heures', 'Old_Posts_Highlighter').'</option>
									<option value="'.OPH_24_HOURS.'" '.OPH_optionselected(OPH_24_HOURS,$interval).'>'.__('24 Heures (1 jour)', 'Old_Posts_Highlighter').'</option>
									<option value="'.OPH_48_HOURS.'" '.OPH_optionselected(OPH_48_HOURS,$interval).'>'.__('48 Heures (2 jours)', 'Old_Posts_Highlighter').'</option>
									<option value="'.OPH_72_HOURS.'" '.OPH_optionselected(OPH_72_HOURS,$interval).'>'.__('72 Heures (3 jours)', 'Old_Posts_Highlighter').'</option>
									<option value="'.OPH_168_HOURS.'" '.OPH_optionselected(OPH_168_HOURS,$interval).'>'.__('168 Heures (7 jours)', 'Old_Posts_Highlighter').'</option>
							</select>
						</div>
						
						<div class="option">
							<label for="OPH_interval_aleatoire">'.__('Intervalle aléatoire (ajouté à l\'intervalle minimum) : ', 'Old_Posts_Highlighter').'</label>
							<select name="OPH_interval_aleatoire" id="OPH_interval_aleatoire">
									<option value="'.OPH_1_HOUR.'" '.OPH_optionselected(OPH_1_HOUR,$aleatoire).'>'.__('Jusqu\'à 1 Heure', 'Old_Posts_Highlighter').'</option>																		<option value="'.OPH_2_HOURS.'" '.OPH_optionselected(OPH_2_HOURS,$aleatoire).'>'.__('Jusqu\'à 2 Heures', 'Old_Posts_Highlighter').'</option>
									<option value="'.OPH_4_HOURS.'" '.OPH_optionselected(OPH_4_HOURS,$aleatoire).'>'.__('Jusqu\'à 4 Heures', 'Old_Posts_Highlighter').'</option>
									<option value="'.OPH_6_HOURS.'" '.OPH_optionselected(OPH_6_HOURS,$aleatoire).'>'.__('Jusqu\'à 6 Heures', 'Old_Posts_Highlighter').'</option>
									<option value="'.OPH_12_HOURS.'" '.OPH_optionselected(OPH_12_HOURS,$aleatoire).'>'.__('Jusqu\'à 12 Heures', 'Old_Posts_Highlighter').'</option>
									<option value="'.OPH_24_HOURS.'" '.OPH_optionselected(OPH_24_HOURS,$aleatoire).'>'.__('Jusqu\'à 24 Heures (1 jour)', 'Old_Posts_Highlighter').'</option>
							</select>
						</div>
						<div class="option">
							<label for="OPH_age_limit">'.__('Age minimum d\'un article pour pouvoir être mis en avant : ', 'Old_Posts_Highlighter').'</label>
							<select name="OPH_age_limit" id="OPH_age_limit">									<option value="7" '.OPH_optionselected(7,$ageLimit).'>'.__('7 Jours', 'Old_Posts_Highlighter').'</option>
									<option value="30" '.OPH_optionselected(30,$ageLimit).'>'.__('30 Jours', 'Old_Posts_Highlighter').'</option>
									<option value="60" '.OPH_optionselected(60,$ageLimit).'>'.__('60 Jours', 'Old_Posts_Highlighter').'</option>
									<option value="90" '.OPH_optionselected(90,$ageLimit).'>'.__('90 Jours', 'Old_Posts_Highlighter').'</option>
									<option value="120" '.OPH_optionselected(120,$ageLimit).'>'.__('120 Jours', 'Old_Posts_Highlighter').'</option>
									<option value="240" '.OPH_optionselected(240,$ageLimit).'>'.__('240 Jours', 'Old_Posts_Highlighter').'</option>
									<option value="365" '.OPH_optionselected(365,$ageLimit).'>'.__('365 Jours', 'Old_Posts_Highlighter').'</option>
									<option value="730" '.OPH_optionselected(730,$ageLimit).'>'.__('730 Jours', 'Old_Posts_Highlighter').'</option>
									</select>
						</div>
						<div class="option">
							<label for="OPH_pos">'.__('Mettre en avant à la position (En position 2, l\'article le plus récent reste le premier) : ', 'Old_Posts_Highlighter').'</label>
							<select name="OPH_pos" id="OPH_pos">
									<option value="1" '.OPH_optionselected(1,$OPH_pos).'>'.__('1', 'Old_Posts_Highlighter').'</option>
									<option value="2" '.OPH_optionselected(2,$OPH_pos).'>'.__('2', 'Old_Posts_Highlighter').'</option>
							</select>
						</div>
						
						<div class="option">
							<label for="OPH_show_original_pubdate">'.__('Afficher la date de première publication en fin d\'article ? ', 'Old_Posts_Highlighter').'</label>
							<select name="OPH_show_original_pubdate" id="OPH_show_original_pubdate">
									<option value="1" '.OPH_optionselected(1,$showPub).'>'.__('Oui', 'Old_Posts_Highlighter').'</option>
									<option value="0" '.OPH_optionselected(0,$showPub).'>'.__('Non', 'Old_Posts_Highlighter').'</option>
							</select>
						</div>
						
						<div class="option">
							<label for="OPH_at_top">'.__('Afficher la date de première publication en début d\'article ', 'Old_Posts_Highlighter').'</label>
							<select name="OPH_at_top" id="OPH_at_top">
									<option value="1" '.OPH_optionselected(1,$atTop).'>'.__('Oui', 'Old_Posts_Highlighter').'</option>
									<option value="0" '.OPH_optionselected(0,$atTop).'>'.__('Non', 'Old_Posts_Highlighter').'</option>
							</select>
						</div>			

						<div class="option">
							<label for="OPH_give_credit">'.__('Envie de nous remercier en nous faisant un lien ?', 'Old_Posts_Highlighter').'</label>
							<select name="OPH_give_credit" id="OPH_give_credit">
									<option value="1" '.OPH_optionselected(1,$OPH_give_credit).'>'.__('Oui', 'Old_Posts_Highlighter').'</option>
									<option value="0" '.OPH_optionselected(0,$OPH_give_credit).'>'.__('Non', 'Old_Posts_Highlighter').'</option>
							</select>
						</div>
						
						<div class="option">
							<label>'.__('Catégories à ne pas prendre en compte pour la mise en avant', 'Old_Posts_Highlighter').'</label>
							 <ul id="categories" class="list:category categorychecklist form-no-clear">
								');
								wp_category_checklist(0, 0, explode(',',$omitCats));
								print('
							</ul>
						</div>
						
						<div class="option" id="forced-posts">
								<label for="OPH_FORCED_POSTS">'.__('Prendre en compte les articles suivants de ces catégories<br/>Format ID,ID,ID, ... (ex : 53,109,257)', 'Old_Posts_Highlighter').'</label>
								
								<textarea name="OPH_FORCED_POSTS" type="textarea" rows="2" cols="70">'.$forcedposts.'</textarea>
						</div>
						
					</fieldset>
					
						<p class="submit">
						<input type="submit" class="button-primary"name="submit" value="'.__('Mettre à jour les options', 'Old_Posts_Highlighter').'" />
					</p>
				</form>' );
}

function OPH_optionselected($opValue, $value) 
{
	if($opValue==$value) 
	{
		return 'selected="selected"';
	}
	return '';
}

function OPH_options_setup() 
{	
	add_menu_page('Old Posts Highlighter', 'Old Posts Highlighter', 10, basename(__FILE__), 'OPH_options', 'dashicons-backup', 26);
}

?>