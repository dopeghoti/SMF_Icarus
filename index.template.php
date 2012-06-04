<?php
/**
 * Simple Machines Forum (SMF)
 *
 * @package SMF
 * @author Simple Machines
 * @copyright 2011 Simple Machines
 * @license http://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 2.0
 */

/*	This template is, perhaps, the most important template in the theme. It
	contains the main template layer that displays the header and footer of
	the forum, namely with main_above and main_below. It also contains the
	menu sub template, which appropriately displays the menu; the init sub
	template, which is there to set the theme up; (init can be missing.) and
	the linktree sub template, which sorts out the link tree.

	The init sub template should load any data and set any hardcoded options.

	The main_above sub template is what is shown above the main content, and
	should contain anything that should be shown up there.

	The main_below sub template, conversely, is shown after the main content.
	It should probably contain the copyright statement and some other things.

	The linktree sub template should display the link tree, using the data
	in the $context['linktree'] variable.

	The menu sub template should display all the relevant buttons the user
	wants and or needs.

	For more information on the templating system, please see the site at:
	http://www.simplemachines.org/
*/

// Initialize the template... mainly little settings.
function template_init()
{
	global $context, $settings, $options, $txt, $boarddir;

	/* Use images from default theme when using templates from the default theme?
		if this is 'always', images from the default theme will be used.
		if this is 'defaults', images from the default theme will only be used with default templates.
		if this is 'never' or isn't set at all, images from the default theme will not be used. */
	$settings['use_default_images'] = 'never';

	/* What document type definition is being used? (for font size and other issues.)
		'xhtml' for an XHTML 1.0 document type definition.
		'html' for an HTML 4.01 document type definition. */
	$settings['doctype'] = 'xhtml';

	/* The version this template/theme is for.
		This should probably be the version of SMF it was created for. */
	$settings['theme_version'] = '2.0.2';

	/* Set a setting that tells the theme that it can render the tabs. */
	$settings['use_tabs'] = true;

	/* Use plain buttons - as opposed to text buttons? */
	$settings['use_buttons'] = true;

	/* Show sticky and lock status separate from topic icons? */
	$settings['separate_sticky_lock'] = true;

	/* Does this theme use the strict doctype? */
	$settings['strict_doctype'] = false;

	/* Does this theme use post previews on the message index? */
	$settings['message_index_preview'] = false;

	/* Set the following variable to true if this theme requires the optional theme strings file to be loaded. */
	$settings['require_theme_strings'] = true;

	/*SSI require*/
	require_once($boarddir.'/SSI.php');
}

// The main sub template above the content.
function template_html_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// Show right to left and the character set for ease of translating.
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"', $context['right_to_left'] ? ' dir="rtl"' : '', '>
<head>';

	// The ?fin20 part of this link is just here to make sure browsers don't cache it wrongly.
	echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/index', $context['theme_variant'], '.css?fin20" />';

	// Some browsers need an extra stylesheet due to bugs/compatibility issues.
	foreach (array('ie7', 'ie6', 'webkit') as $cssfix)
		if ($context['browser']['is_' . $cssfix])
			echo '
	<link rel="stylesheet" type="text/css" href="', $settings['default_theme_url'], '/css/', $cssfix, '.css" />';

	// RTL languages require an additional stylesheet.
	if ($context['right_to_left'])
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/rtl.css" />';

	// Here comes the JavaScript bits!
	echo '
	<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/script.js?fin20"></script>
	<script type="text/javascript" src="', $settings['theme_url'], '/scripts/theme.js?fin20"></script>
	<script type="text/javascript"><!-- // --><![CDATA[
		var smf_theme_url = "', $settings['theme_url'], '";
		var smf_default_theme_url = "', $settings['default_theme_url'], '";
		var smf_images_url = "', $settings['images_url'], '";
		var smf_scripturl = "', $scripturl, '";
		var smf_iso_case_folding = ', $context['server']['iso_case_folding'] ? 'true' : 'false', ';
		var smf_charset = "', $context['character_set'], '";', $context['show_pm_popup'] ? '
		var fPmPopup = function ()
		{
			if (confirm("' . $txt['show_personal_messages'] . '"))
				window.open(smf_prepareScriptUrl(smf_scripturl) + "action=pm");
		}
		addLoadEvent(fPmPopup);' : '', '
		var ajax_notification_text = "', $txt['ajax_in_progress'], '";
		var ajax_notification_cancel_text = "', $txt['modify_cancel'], '";
	// ]]></script>';

	echo '
	<meta http-equiv="Content-Type" content="text/html; charset=', $context['character_set'], '" />
	<meta name="description" content="', $context['page_title_html_safe'], '" />', !empty($context['meta_keywords']) ? '
	<meta name="keywords" content="' . $context['meta_keywords'] . '" />' : '', '
	<title>', $context['page_title_html_safe'], '</title>';

	// Please don't index these Mr Robot.
	if (!empty($context['robot_no_index']))
		echo '
	<meta name="robots" content="noindex" />';

	// Present a canonical url for search engines to prevent duplicate content in their indices.
	if (!empty($context['canonical_url']))
		echo '
	<link rel="canonical" href="', $context['canonical_url'], '" />';

	// Show all the relative links, such as help, search, contents, and the like.
	echo '
	<link rel="help" href="', $scripturl, '?action=help" />
	<link rel="search" href="', $scripturl, '?action=search" />
	<link rel="contents" href="', $scripturl, '" />';

	// If RSS feeds are enabled, advertise the presence of one.
	if (!empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged']))
		echo '
	<link rel="alternate" type="application/rss+xml" title="', $context['forum_name_html_safe'], ' - ', $txt['rss'], '" href="', $scripturl, '?type=rss;action=.xml" />';

	// If we're viewing a topic, these should be the previous and next topics, respectively.
	if (!empty($context['current_topic']))
		echo '
	<link rel="prev" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=prev" />
	<link rel="next" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=next" />';

	// If we're in a board, or a topic for that matter, the index will be the board's index.
	if (!empty($context['current_board']))
		echo '
	<link rel="index" href="', $scripturl, '?board=', $context['current_board'], '.0" />';

	// Output any remaining HTML headers. (from mods, maybe?)
	echo $context['html_headers'];

	echo '
</head>
<body>';
}

function template_body_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings, $boardurl;
	topbar();
	echo '

<div id="header">
	<div id="logo">';
	if ((!empty($settings['header_title1'])) && (!empty($settings['header_title2']))) {
		echo '
			<h1>
				<a href="'.$boardurl.'">
					<span>'.$settings['header_title1'].'</span>'.$settings['header_title2'].'
				</a>
			</h1>
		';
	}
	else {
		echo '
			<h1><a href="'.$boardurl.'">'.$txt['Text_0'].'</a></h1>';
	}
	if (!empty($settings['header_slogan'])) {
		echo '
			<p>'.$settings['header_slogan'].'</p>';
	}
	else {
		echo '
			<p>'.$txt['Text_00'].'</p>';
	}
echo '
	</div>
</div>
',menu(),'
<div id="wrapper">';
	blockIzq();
	// Define the upper_section toggle in JavaScript.
	echo '
		<script type="text/javascript"><!-- // --><![CDATA[
			var oMainHeaderToggle = new smc_Toggle({
				bToggleEnabled: true,
				bCurrentlyCollapsed: ', empty($options['collapse_header']) ? 'false' : 'true', ',
				aSwappableContainers: [
					\'upper_section\'
				],
				aSwapImages: [
					{
						sId: \'upshrink\',
						srcExpanded: smf_images_url + \'/upshrink.png\',
						altExpanded: ', JavaScriptEscape($txt['upshrink_description']), ',
						srcCollapsed: smf_images_url + \'/upshrink2.png\',
						altCollapsed: ', JavaScriptEscape($txt['upshrink_description']), '
					}
				],
				oThemeOptions: {
					bUseThemeSettings: ', $context['user']['is_guest'] ? 'false' : 'true', ',
					sOptionName: \'collapse_header\',
					sSessionVar: ', JavaScriptEscape($context['session_var']), ',
					sSessionId: ', JavaScriptEscape($context['session_id']), '
				},
				oCookieOptions: {
					bUseCookie: ', $context['user']['is_guest'] ? 'true' : 'false', ',
					sCookieName: \'upshrink\'
				}
			});
		// ]]></script>';

	// Show the menu here, according to the menu sub template.
/*
	global $user_info;
	if ($user_info['is_admin']){
	template_menu();
	}
*/

	// The main content should go here.
	echo '
	<div id="content_section"><div class="frame">
		<div id="main_content_section">';

	// Custom banners and shoutboxes should be placed here, before the linktree.
	unread();
	// Show the navigation tree.
	theme_linktree();
}

function template_body_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
		</div>
	</div></div>';

	echo '
</div>';
}

function template_html_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	footer();
	echo '
</body></html>';
}

// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree($force_show = false)
{
	global $context, $settings, $options, $shown_linktree;

	// If linktree is empty, just return - also allow an override.
	if (empty($context['linktree']) || (!empty($context['dont_default_linktree']) && !$force_show))
		return;

	echo '
	<div class="navigate_section">
		<ul>';

	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		echo '
			<li', ($link_num == count($context['linktree']) - 1) ? ' class="last"' : '', '>';

		// Show something before the link?
		if (isset($tree['extra_before']))
			echo $tree['extra_before'];

		// Show the link, including a URL if it should have one.
		echo $settings['linktree_link'] && isset($tree['url']) ? '
				<a href="' . $tree['url'] . '"><span>' . $tree['name'] . '</span></a>' : '<span>' . $tree['name'] . '</span>';

		// Show something after the link...?
		if (isset($tree['extra_after']))
			echo $tree['extra_after'];

		// Don't show a separator for the last one.
		if ($link_num != count($context['linktree']) - 1)
			echo ' &#187;';

		echo '
			</li>';
	}
	echo '
		</ul>
	</div>';

	$shown_linktree = true;
}

// Show the menu up top. Something like [home] [help] [profile] [logout]...
function template_menu()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '
		<div id="main_menu">
			<ul class="dropmenu" id="menu_nav">';

	foreach ($context['menu_buttons'] as $act => $button)
	{
		echo '
				<li id="button_', $act, '">
					<a class="', $button['active_button'] ? 'active ' : '', 'firstlevel" href="', $button['href'], '"', isset($button['target']) ? ' target="' . $button['target'] . '"' : '', '>
						<span class="', isset($button['is_last']) ? 'last ' : '', 'firstlevel">', $button['title'], '</span>
					</a>';
		if (!empty($button['sub_buttons']))
		{
			echo '
					<ul>';

			foreach ($button['sub_buttons'] as $childbutton)
			{
				echo '
						<li>
							<a href="', $childbutton['href'], '"', isset($childbutton['target']) ? ' target="' . $childbutton['target'] . '"' : '', '>
								<span', isset($childbutton['is_last']) ? ' class="last"' : '', '>', $childbutton['title'], !empty($childbutton['sub_buttons']) ? '...' : '', '</span>
							</a>';
				// 3rd level menus :)
				if (!empty($childbutton['sub_buttons']))
				{
					echo '
							<ul>';

					foreach ($childbutton['sub_buttons'] as $grandchildbutton)
						echo '
								<li>
									<a href="', $grandchildbutton['href'], '"', isset($grandchildbutton['target']) ? ' target="' . $grandchildbutton['target'] . '"' : '', '>
										<span', isset($grandchildbutton['is_last']) ? ' class="last"' : '', '>', $grandchildbutton['title'], '</span>
									</a>
								</li>';

					echo '
							</ul>';
				}

				echo '
						</li>';
			}
				echo '
					</ul>';
		}
		echo '
				</li>';
	}

	echo '
			</ul>
		</div>';
}

// Generate a strip of buttons.
function template_button_strip($button_strip, $direction = 'top', $strip_options = array())
{
	global $settings, $context, $txt, $scripturl;

	if (!is_array($strip_options))
		$strip_options = array();

	// List the buttons in reverse order for RTL languages.
	if ($context['right_to_left'])
		$button_strip = array_reverse($button_strip, true);

	// Create the buttons...
	$buttons = array();
	foreach ($button_strip as $key => $value)
	{
		if (!isset($value['test']) || !empty($context[$value['test']]))
			$buttons[] = '
				<li><a' . (isset($value['id']) ? ' id="button_strip_' . $value['id'] . '"' : '') . ' class="button_strip_' . $key . (isset($value['active']) ? ' active' : '') . '" href="' . $value['url'] . '"' . (isset($value['custom']) ? ' ' . $value['custom'] : '') . '><span>' . $txt[$value['text']] . '</span></a></li>';
	}

	// No buttons? No button strip either.
	if (empty($buttons))
		return;

	// Make the last one, as easy as possible.
	$buttons[count($buttons) - 1] = str_replace('<span>', '<span class="last">', $buttons[count($buttons) - 1]);

	echo '
		<div class="buttonlist', !empty($direction) ? ' float' . $direction : '', '"', (empty($buttons) ? ' style="display: none;"' : ''), (!empty($strip_options['id']) ? ' id="' . $strip_options['id'] . '"': ''), '>
			<ul>',
				implode('', $buttons), '
			</ul>
		</div>';
}

function topbar()
{
	global $scripturl, $settings, $txt, $context, $boardurl, $modSettings, $user_info;
	echo'
	<div id="wpadminbar">
		<div class="quicklinks">
			<ul style="overflow: visible;" class="menutops">';
				if(!$user_info['is_guest']){
					echo'
					<li id="wp-admin-bar-my-account-with-avatar" class="menupop">';
						echo'
						<a href="',$scripturl,'?action=profile;u='.$user_info['id'].'">
							<span class="avatar_lu">
							',!empty($context['user']['avatar']['image']) ? '
								'.$context['user']['avatar']['image'].'' : '<img src="'.$settings['images_url'].'/SinAvatar.png" alt="" />' , $user_info['name'] ,'
							</span>
						</a>';
					echo'
						<ul>
							',$context['allow_edit_profile'] ? '
								<li id="wp-admin-bar-editar-mi-perfil" class="">
									<a href="'.$scripturl.'?action=profile">'.$txt['profile'].'</a>
									<ul>
										<li><a href="'.$scripturl.'?action=profile">'.$txt['summary'].'</a></li>
										<li><a href="'.$scripturl.'?action=profile;area=account">'.$txt['account'].'</a></li>
										<li><a href="'.$scripturl.'?action=profile;area=forumprofile">'.$txt['forumprofile'].'</a></li>
									</ul>
								</li>
							' : '' ,'
							<li id="wp-admin-bar-escritorio" class="">
								<a href="'.$scripturl.'?action=pm">'.$txt['pm_short'].'</a>
								<ul>
									<li><a href="'.$scripturl.'?action=pm">'.$txt['pm_menu_read'].' ( '.$context['user']['unread_messages'].' )</a></li>
									<li><a href="'.$scripturl.'?action=pm;sa=send">'.$txt['pm_menu_send'].'</a></li>
								</ul>
							</li>											
							<li id="wp-admin-bar-cerrar-sesion" class=" ">
								<a href="',sprintf($scripturl . '?action=logout;%1$s=%2$s', $context['session_var'], $context['session_id']),'">'.$txt['logout'].'</a>
							</li>
						</ul>
					</li>';
				if($user_info['is_admin']){
				echo'
					<li id="wp-admin-bar-new-admin" class="menupop">
						<a href="'.$scripturl.'?action=admin"><span>'.$txt['admin'].'</span></a>
						<ul>
							<li><a href="', $scripturl, '?action=admin;area=featuresettings">'.$txt['modSettings_title'].'</a></li>
							<li><a href="', $scripturl, '?action=admin;area=packages">'.$txt['package'].'</a></li>
							<li><a href="', $scripturl, '?action=admin;area=logs;sa=errorlog;desc">'.$txt['errlog'].'</a></li>
							<li><a href="', $scripturl, '?action=admin;area=permissions">'.$txt['edit_permissions'].'</a></li>
						</ul>
					</li>';
				}
				echo'
					<li id="wp-admin-bar-new-content" class="menupop">
						<a href="'.$scripturl.'?action=unread"><span>'.$txt['Text_01'].'</span></a>
						<ul>
							<li><a href="', $scripturl, '?action=unread">', $txt['Text_02'], '</a></li>
							<li><a href="', $scripturl, '?action=unread;all;start=0">', $txt['Text_03'], '</a></li>
							<li><a href="', $scripturl, '?action=unreadreplies">', $txt['Text_04'], '</a></li>
						</ul>
					</li>';
				}
				else{
					echo'
					<li id="wp-admin-bar-my-account-with-avatar" class="menupop">
						<a href="'.$scripturl.'">
							<span class="avatar_lu">
								<img src="'.$settings['images_url'].'/SinAvatar.png" alt="*" />
								'.$txt['Text_05'].'</span>
						</a>
						<ul>
							<li><a href="', $scripturl, '?action=login">', $txt['Text_06'], '</a></li>
							<li><a href="', $scripturl, '?action=register">', $txt['Text_07'], '</a></li>
						</ul>
					</li>
					';
				}
	echo'
			</ul>
		</div>
	</div>';

}

function menu()
{
	global $context, $settings, $options, $scripturl, $txt;

	//Not allowed actions
	$is_allowed = array('home','help','search','register','login','logout',);
	echo'
		<div id="menu">
			<ul id="main">';
	foreach ($context['menu_buttons'] as $act => $button){
		if(in_array($act,$is_allowed))
			echo '
				<li ',$button['active_button'] ? ' class="current_page_item"' : '' ,'>
					<a href="', $button['href'], '">
						', $button['title'], '
					</a>
				</li>';
	}

	$current_action = '';
	if(!empty($_REQUEST['action']) || !empty($_REQUEST['topic']) || !empty($_REQUEST['board']) || !empty($_REQUEST['page']) || !empty($_REQUEST['blog']))
		$current_action = 'other';
	if(!empty($current_action)){
		if (!empty($context['menu_buttons']['admin']['show'])) {
			echo '
				<li ',$context['menu_buttons']['admin']['active_button'] ? ' class="current_page_item"' : '' ,'>
					<a href="'.$scripturl.'?action=admin">
						'.$txt['Text_08'].'
					</a>
				</li>
			';
			}
		if (!empty($context['menu_buttons']['profile']['show'])) {
			echo '
				<li ',$context['menu_buttons']['profile']['active_button'] ? ' class="current_page_item"' : '' ,'>
					<a href="'.$scripturl.'?action=profile">
						'.$txt['Text_09'].'
					</a>
				</li>
			';
		}
	}
	echo'
			</ul>
		</div>
	';
}

function blockIzq()
{
	global $settings, $context, $txt, $scripturl;
	foreach (array('ie8', 'ie7', 'ie6', 'webkit') as $cssfix)
		if ($context['browser']['is_' . $cssfix])
			echo '
				<style type="text/css">
					.class_popup {
						background: #808080;
					}
				</style>
			';

	if (!empty($settings['Disable_Col'])) {
		echo'
			<div id="page">
				<div id="sidebar1" class="sidebar">
					<ul>
						<li>
							<form id="searchform" action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '">
								<div>
									<h2>'.$txt['Text_10'].'</h2>
									<input type="text" name="search" id="search" size="15" value=" Search..." onblur="if(this.value==\'\') this.value=\' Search...\';"  onfocus="if(this.value==\' Search...\') this.value=\'\';" />
									<input type="hidden" name="advanced" value="0" />
								</div>
							</form>
						</li>
					</ul>
				</div>
				<div id="content">
		';	
	}
	else {
		$current_action = '';
		if(!empty($_REQUEST['action']) || !empty($_REQUEST['topic']) || !empty($_REQUEST['board']) || !empty($_REQUEST['page']) || !empty($_REQUEST['blog']))
			$current_action = 'other';
		if(!empty($current_action)){
			echo'
				<div id="page">
					<div id="sidebar1" class="sidebar">
						<ul>
							<li>
								<form id="searchform" action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '">
									<div>
										<h2>'.$txt['Text_10'].'</h2>
										<input type="text" name="search" id="search" size="15" value=" Search..." onblur="if(this.value==\'\') this.value=\' Search...\';"  onfocus="if(this.value==\' Search...\') this.value=\'\';" />
										<input type="hidden" name="advanced" value="0" />
									</div>
								</form>
							</li>
						</ul>
					</div>
					<div id="content">
			';	
		}
		else {
			echo'
				<div id="page">
					<div id="sidebar1" class="sidebar">
						<ul>
							<li>
								<form id="searchform" action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '">
									<div>
										<h2>'.$txt['Text_10'].'</h2>
										<input type="text" name="search" id="search" size="15" value=" Search..." onblur="if(this.value==\'\') this.value=\' Search...\';"  onfocus="if(this.value==\' Search...\') this.value=\'\';" />
										<input type="hidden" name="advanced" value="0" />
									</div>
								</form>
							</li>
							<li>
								<h2>'.$txt['Text_11'].'</h2>
									',menulateral(),'
							</li>
							<li>
								<h2>'.$txt['Text_12'].'</h2>
								<ul>
									',PostsRecent(),'
								</ul>
							</li>
						</ul>
					</div>
					<div id="content1">
			';
		}
	}
}

function menulateral()
{
	global $context, $settings, $options, $scripturl, $txt;

	if ($context['user']['is_logged']) {
		$is_allowed = array('admin','moderate','profile','pm','mlist','calendar',);
		foreach ($context['menu_buttons'] as $act => $button){
			if(in_array($act,$is_allowed))
				echo '
				<ul>
					<li>
						<img src="',$settings['images_url'],'/theme/marca.gif" alt="" /> <a href="', $button['href'], '">
							', $button['title'], '
						</a>
					</li>
				</ul>';
		}
	}
	elseif (!empty($context['show_login_bar']))
	{
		echo '
				<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/sha1.js"></script>
				<form id="guest_form" action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '" ', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>
					<div align="center">
						<b>'.$txt['Text_05'].'</b>
						<br /><br />
					</div>
					<div style="padding-right: 7px; text-align: right">
					<b>'.$txt['Text_13'].':</b> <input type="text" name="user" size="10" class="input_text" />
					<b>'.$txt['Text_14'].':</b> <input type="password" name="passwrd" size="10" class="input_password" />
					<b>'.$txt['Text_15'].':</b> <select name="cookielength">
						<option value="60">', $txt['one_hour'], '</option>
						<option value="1440">', $txt['one_day'], '</option>
						<option value="10080">', $txt['one_week'], '</option>
						<option value="43200">', $txt['one_month'], '</option>
						<option value="-1" selected="selected">', $txt['forever'], '</option>
					</select>
					<input type="submit" value="', $txt['login'], '" class="button_submit" /><br />
					</div>';

		if (!empty($modSettings['enableOpenID']))
			echo '
					<br /><input type="text" name="openid_identifier" id="openid_url" size="25" class="input_text openid_login" />';

		echo '
					<input type="hidden" name="hash_passwrd" value="" />
				</form>';
	}
}

function PostsRecent()
{
	global $scripturl, $txt, $settings;
	
	//Load SSI Function
	$array = ssi_recentTopics('5',null,null, 'array', true);
	
	$sql = array();
	
	foreach($array AS $t)

		$sql[] = '
			<img src="'.$settings['images_url'].'/theme/marca.gif" alt="" /> 
			<a href="'.$t['href'].'" target="_self" onmouseover="document.getElementById(\'b'.$t['topic'].'\').style.display = \'block\'" onmouseout="document.getElementById(\'b'.$t['topic'].'\').style.display = \'none\'">

				'.shorten_subject($t['subject'], 18).'
			</a>
			<div class="class_popup" id="b'.$t['topic'].'" style="display: none">
				<b>'.$txt['Text_16'].':</b> <b style="color: #000">&nbsp;'.$t['board']['name'].'</b>
				<br /><b>'.$txt['Text_17'].':</b> <b style="color: #000">&nbsp;'.$t['subject'].'</b>
				<br /><b>'.$txt['Text_18'].':</b> <b style="color: #000">&nbsp;'.$t['views'].'</b>
				<br /><b>'.$txt['Text_19'].':</b> <b style="color: #000">&nbsp;'.$t['replies'].'</b>
			</div>
		';

	if(!empty($sql))
		echo'<li>',implode('</li><li>',$sql),'</li>';
	else
		echo'<li>'.$txt['Text_20'].'</li>';

}

function footer()
{
	global $settings, $context, $txt, $scripturl;
	echo'
			<div style="clear: both;">&nbsp;</div>
			</div>
		</div>
		<div class="footers">
			<div id="footer">
				<p class="copyright">
					&copy;&nbsp;&nbsp;Adk Cool Black &nbsp;&bull;&nbsp; Design by 
					<a title="SMF Personal" href="http://www.smfpersonal.net/">
						^HeRaCLeS^
					</a>
				</p>
				<p class="link">
					',theme_copyright(),'
				</p>
			</div>
		</div>
	';
}

function unread()
{
	global $settings, $context, $txt, $scripturl;

	$current_action = '';
	if(!empty($_REQUEST['action']) || !empty($_REQUEST['topic']) || !empty($_REQUEST['board']) || !empty($_REQUEST['page']) || !empty($_REQUEST['blog']))
		$current_action = 'other';
	if(empty($current_action))
	if ($context['user']['is_logged'])
	{
		echo '
			<div align="center">
				<a href="', $scripturl, '?action=unread">'.$txt['Text_02'].'</a> | 
				<a href="', $scripturl, '?action=unread;all;start=0">'.$txt['Text_03'].'</a>
				<br /><a href="', $scripturl, '?action=unreadreplies">'.$txt['Text_04'].'</a>';
		if ($context['in_maintenance'] && $context['user']['is_admin'])
			echo '
					<br />'.$txt['maintain_mode_on'].'';

		if (!empty($context['unapproved_members']))
			echo '
					<br />', $context['unapproved_members'] == 1 ? $txt['approve_thereis'] : $txt['approve_thereare'], ' <a href="', $scripturl, '?action=admin;area=viewmembers;sa=browse;type=approve">', $context['unapproved_members'] == 1 ? $txt['approve_member'] : $context['unapproved_members'] . ' ' . $txt['approve_members'], '</a> ', $txt['approve_members_waiting'], '';

		if (!empty($context['open_mod_reports']) && $context['show_open_reports'])
			echo '
					<br /><a href="', $scripturl, '?action=moderate;area=reports">', sprintf($txt['mod_reports_waiting'], $context['open_mod_reports']), '</a>';
		echo '
			</div>';
	}
}

?>