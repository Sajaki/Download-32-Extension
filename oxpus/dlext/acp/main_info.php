<?php

/**
*
* @package phpBB Extension - Oxpus Downloads
* @copyright (c) 2015-2020 OXPUS - www.oxpus.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace oxpus\dlext\acp;

/**
* @package acp
*/
class main_info
{
	function module()
	{
		return array(
			'filename'	=> '\oxpus\dlext\acp\main_module',
			'title'		=> 'ACP_DOWNLOADS',
			'modes'		=> array(
				'overview'		=> array(
					'title' => 'DL_ACP_OVERVIEW',				'auth' => 'ext_oxpus/dlext && acl_a_dl_overview',		'cat' => array('ACP_DOWNLOADS')
				),
				'config'		=> array(
					'title' => 'DL_ACP_CONFIG_MANAGEMENT',		'auth' => 'ext_oxpus/dlext && acl_a_dl_config',			'cat' => array('ACP_DOWNLOADS')
				),
				'traffic'		=> array(
					'title' => 'DL_ACP_TRAFFIC_MANAGEMENT',		'auth' => 'ext_oxpus/dlext && acl_a_dl_traffic',		'cat' => array('ACP_DOWNLOADS')
				),
				'categories'	=> array(
					'title' => 'DL_ACP_CATEGORIES_MANAGEMENT',	'auth' => 'ext_oxpus/dlext && acl_a_dl_categories',		'cat' => array('ACP_DOWNLOADS')
				),
				'files'			=> array(
					'title' => 'DL_ACP_FILES_MANAGEMENT',		'auth' => 'ext_oxpus/dlext && acl_a_dl_files',			'cat' => array('ACP_DOWNLOADS')
				),
				'permissions'	=> array(
					'title' => 'DL_ACP_PERMISSIONS',			'auth' => 'ext_oxpus/dlext && acl_a_dl_permissions',	'cat' => array('ACP_DOWNLOADS')
				),
				'stats'			=> array(
					'title' => 'DL_ACP_STATS_MANAGEMENT',		'auth' => 'ext_oxpus/dlext && acl_a_dl_stats',			'cat' => array('ACP_DOWNLOADS')
				),
				'banlist'		=> array(
					'title' => 'DL_ACP_BANLIST',				'auth' => 'ext_oxpus/dlext && acl_a_dl_banlist',		'cat' => array('ACP_DOWNLOADS')
				),
				'ext_blacklist'	=> array(
					'title' => 'DL_EXT_BLACKLIST',				'auth' => 'ext_oxpus/dlext && acl_a_dl_blacklist',		'cat' => array('ACP_DOWNLOADS')
				),
				'toolbox'		=> array(
					'title' => 'DL_MANAGE',						'auth' => 'ext_oxpus/dlext && acl_a_dl_toolbox',		'cat' => array('ACP_DOWNLOADS')
				),
				'fields'		=> array(
					'title' => 'DL_ACP_FIELDS',					'auth' => 'ext_oxpus/dlext && acl_a_dl_fields',			'cat' => array('ACP_DOWNLOADS')
				),
				'browser'		=> array(
					'title' => 'DL_ACP_BROWSER',				'auth' => 'ext_oxpus/dlext && acl_a_dl_browser',		'cat' => array('ACP_DOWNLOADS')
				),
				'perm_check'	=> array(
					'title' => 'DL_ACP_PERM_CHECK',				'auth' => 'ext_oxpus/dlext && acl_a_dl_perm_check',		'cat' => array('ACP_DOWNLOADS')
				),
			),
		);
	}

	function install()
	{
	}

	function uninstall()
	{
	}
}
