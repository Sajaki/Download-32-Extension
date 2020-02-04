<?php

/**
*
* @package phpBB Extension - Oxpus Downloads
* @copyright (c) 2002-2020 OXPUS - www.oxpus.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace oxpus\dlext\controller\mcp;

use Symfony\Component\DependencyInjection\Container;

class mcp_approve
{
	protected $u_action;

	/* @var string phpBB root path */
	protected $root_path;

	/* @var string phpEx */
	protected $php_ext;

	/* @var Container */
	protected $phpbb_container;

	/* @var \phpbb\extension\manager */
	protected $phpbb_extension_manager;

	/* @var \phpbb\path_helper */
	protected $phpbb_path_helper;

	/* @var \phpbb\db\driver\driver_interface */
	protected $db;

	/* @var \phpbb\config\config */
	protected $config;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\auth\auth */
	protected $auth;

	/* @var \phpbb\request\request_interface */
	protected $request;

	/* @var \phpbb\template\template */
	protected $template;

	/* @var \phpbb\user */
	protected $user;

	/* @var \phpbb\language\language */
	protected $language;

	/** @var extension owned objects */
	protected $ext_path;
	protected $ext_path_web;
	protected $ext_path_ajax;

	protected $dlext_auth;
	protected $dlext_main;
	protected $dlext_topic;

	/**
	* Constructor
	*
	* @param string									$root_path
	* @param string									$php_ext
	* @param Container 								$phpbb_container
	* @param \phpbb\extension\manager				$phpbb_extension_manager
	* @param \phpbb\path_helper						$phpbb_path_helper
	* @param \phpbb\db\driver\driver_interfacer		$db
	* @param \phpbb\config\config					$config
	* @param \phpbb\controller\helper				$helper
	* @param \phpbb\auth\auth						$auth
	* @param \phpbb\request\request_interface 		$request
	* @param \phpbb\template\template				$template
	* @param \phpbb\user							$user
	* @param \phpbb\language\language				$language
	*/
	public function __construct(
		$root_path,
		$php_ext,
		Container $phpbb_container,
		\phpbb\extension\manager $phpbb_extension_manager,
		\phpbb\path_helper $phpbb_path_helper,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\config\config $config,
		\phpbb\controller\helper $helper,
		\phpbb\auth\auth $auth,
		\phpbb\request\request_interface $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\language\language $language,
		$dlext_auth,
		$dlext_main,
		$dlext_topic
	)
	{
		$this->root_path				= $root_path;
		$this->php_ext 					= $php_ext;
		$this->phpbb_container 			= $phpbb_container;
		$this->phpbb_extension_manager 	= $phpbb_extension_manager;
		$this->phpbb_path_helper		= $phpbb_path_helper;
		$this->db 						= $db;
		$this->config 					= $config;
		$this->helper 					= $helper;
		$this->auth						= $auth;
		$this->request					= $request;
		$this->template 				= $template;
		$this->user 					= $user;
		$this->language					= $language;

		$this->ext_path					= $this->phpbb_extension_manager->get_extension_path('oxpus/dlext', true);
		$this->ext_path_web				= $this->phpbb_path_helper->update_web_root_path($this->ext_path);
		$this->ext_path_ajax			= $this->ext_path_web . 'assets/javascript/';

		$this->dlext_auth				= $dlext_auth;
		$this->dlext_main				= $dlext_main;
		$this->dlext_topic				= $dlext_topic;
	}

	public function set_action($u_action)
	{
		$this->u_action = $u_action;
	}

	public function handle()
	{
		$nav_view = 'modcp';

		// Include the default base init script
		include_once($this->ext_path . 'phpbb/includes/base_init.' . $this->php_ext);

		$deny_modcp = true;
		
		$access_cat = array();
		$access_cat = $this->dlext_main->full_index(0, 0, 0, 2);
		
		$cat_auth = array();
		$cat_auth = $this->dlext_auth->dl_cat_auth($cat_id);
		
		if (sizeof($access_cat) || $this->auth->acl_get('a_'))
		{
			$deny_modcp = false;
		}
		
		if (isset($index[$cat_id]['auth_mod']) && $index[$cat_id]['auth_mod'])
		{
			$deny_modcp = false;
		}
		
		if ($cat_auth['auth_mod'])
		{
			$deny_modcp = false;
		}
		
		if ($deny_modcp)
		{
			trigger_error($this->language->lang('DL_NO_PERMISSION'));
		}

		add_form_key('dl_modcp');

		$this->template->assign_var('S_DL_MCP', true);

		if (!empty($dlo_id))
		{
			if (!check_form_key('dl_modcp'))
			{
				trigger_error('FORM_INVALID');
			}

			$sql = 'UPDATE ' . DOWNLOADS_TABLE . ' SET ' . $this->db->sql_build_array('UPDATE', array(
				'approve' => true)) . ' WHERE ' . $this->db->sql_in_set('id', $dlo_id);
			$this->db->sql_query($sql);

			if ($this->config['dl_enable_dl_topic'])
			{
				for ($i = 0; $i < sizeof($dlo_id); $i++)
				{
					$this->dlext_topic->gen_dl_topic(intval($dlo_id[$i]));
				}
			}
		}

		$sql_access_cats = ($this->auth->acl_get('a_') && $this->user->data['is_registered']) ? '' : ' AND ' . $this->db->sql_in_set('cat', $access_cat);

		$sql = 'SELECT COUNT(id) AS total FROM ' . DOWNLOADS_TABLE . "
			WHERE approve = 0
				$sql_access_cats";
		$result = $this->db->sql_query($sql);
		$total_approve = $this->db->sql_fetchfield('total');
		$this->db->sql_freeresult($result);

		$sql = 'SELECT cat, id, description, desc_uid, desc_bitfield, desc_flags FROM ' . DOWNLOADS_TABLE . "
			WHERE approve = 0
				$sql_access_cats
			ORDER BY cat, description";
		$result = $this->db->sql_query_limit($sql, $this->config['dl_links_per_page'], $start);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$cat_id		= $row['cat'];
			$cat_name	= $index[$cat_id]['cat_name'];
			$cat_name	= str_replace('&nbsp;&nbsp;|', '', $cat_name);
			$cat_name	= str_replace('___&nbsp;', '', $cat_name);
			$cat_view	= $index[$cat_id]['nav_path'];

			$description	= $row['description'];
			$desc_uid		= $row['desc_uid'];
			$desc_bitfield	= $row['desc_bitfield'];
			$desc_flags		= $row['desc_flags'];
			$description	= generate_text_for_display($description, $desc_uid, $desc_bitfield, $desc_flags);

			$file_id = $row['id'];

			$this->template->assign_block_vars('approve_row', array(
				'CAT_NAME'		=> $cat_name,
				'FILE_ID'		=> $file_id,
				'DESCRIPTION'	=> $description,

				'U_CAT_VIEW'	=> $cat_view,
				'U_EDIT'		=> $this->u_action . '&amp;mode=mcp_edit&amp;df_id=' . $file_id . '&amp;cat_id=' . $cat_id . '&amp;modcp=1',
				'U_DELETE'		=> $this->u_action . '&amp;mode=mcp_manage&amp;delete=1&amp;dlo_id[0]=' . $file_id . '&amp;cat_id=' . $cat_id . '&amp;modcp=99',
				'U_DOWNLOAD'	=> $this->helper->route('oxpus_dlext_details', array('df_id' => $file_id, 'dl_id' => $file_id, 'modcp' => 1, 'cat_id' => $cat_id)),
			));
		}

		$this->db->sql_freeresult($result);

		$s_hidden_fields = array(
			'cat_id'	=> $cat_id,
			'start'		=> $start
		);

		if ($total_approve > $this->config['dl_links_per_page'])
		{
			$pagination = $this->phpbb_container->get('pagination');
			$pagination->generate_template_pagination(
				$this->u_action . '&amp;mode=mcp_approve&amp;cat_id=' . $cat_id,
				'pagination',
				'start',
				$total_approve,
				$this->config['dl_links_per_page'],
				$page_start
			);

			$this->template->assign_vars(array(
				'PAGE_NUMBER'	=> $pagination->on_page($total_approve, $this->config['dl_links_per_page'], $page_start),
				'TOTAL_DL'		=> $this->language->lang('VIEW_DOWNLOADS', $total_approve),
			));
		}

		$this->template->assign_vars(array(
			'S_DL_MODCP_ACTION'	=> $this->u_action . '&amp;mode=mcp_approve',
			'S_HIDDEN_FIELDS'	=> build_hidden_fields($s_hidden_fields),
		));
	}
}
