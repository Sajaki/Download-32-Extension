<?php

/**
*
* @package phpBB Extension - Oxpus Downloads
* @copyright (c) 2002-2020 OXPUS - www.oxpus.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace oxpus\dlext\controller;

use Symfony\Component\DependencyInjection\Container;

class ajax
{
	/* @var \phpbb\db\driver\driver_interface */
	protected $db;

	/* @var \phpbb\user */
	protected $user;

	/* @var \phpbb\config\config */
	protected $config;

	/* @var \phpbb\language\language */
	protected $language;

	/* @var \phpbb\template\template */
	protected $template;

	/* @var \phpbb\request\request_interface */
	protected $request;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interfacer		$db
	* @param \phpbb\user							$user
	* @param \phpbb\config\config					$config
	* @param \phpbb\language\language 				$language
	* @param \phpbb\template\template				$template
	* @param \phpbb\request\request_interface 		$request
	* @param Container 								$phpbb_container
	*/
	public function __construct(
		\phpbb\db\driver\driver_interface $db,
		\phpbb\user $user,
		\phpbb\config\config $config,
		\phpbb\language\language $language,
		\phpbb\template\template $template,
		\phpbb\request\request_interface $request,
		Container $phpbb_container
	)
	{
		$this->db 			= $db;
		$this->user 		= $user;
		$this->config 		= $config;
		$this->language		= $language;
		$this->template 	= $template;
		$this->request		= $request;
	}

	public function handle()
	{
		$dl_id		= $this->request->variable('dl_id', 0);
		$rate_point	= $this->request->variable('rate_point', 0);

		if ($dl_id && $rate_point)
		{
			$sql = 'INSERT INTO ' . DL_RATING_TABLE . ' ' . $this->db->sql_build_array('INSERT', array(
				'rate_point'	=> $rate_point,
				'user_id'		=> $this->user->data['user_id'],
				'dl_id'			=> $dl_id));
			$this->db->sql_query($sql);
		
			$sql = 'SELECT AVG(rate_point) AS rating, COUNT(rate_point) AS total FROM ' . DL_RATING_TABLE . '
				WHERE dl_id = ' . (int) $dl_id . '
				GROUP BY dl_id';
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$new_rating = ceil($row['rating']);
			$total_ratings = $row['total'];
			$this->db->sql_freeresult($result);
		
			$sql = 'UPDATE ' . DOWNLOADS_TABLE . ' SET ' . $this->db->sql_build_array('UPDATE', array(
				'rating' => $new_rating)) . ' WHERE id = ' . (int) $dl_id;
			$this->db->sql_query($sql);
		
			$rate_img = '';
			$rate_yes = '<i class="icon fa-star fa-fw dl-green" title=""></i>';
			$rate_no = '<i class="icon fa-star-o fa-fw dl-yellow" title=""></i>';
		
			for ($i = 0; $i < $this->config['dl_rate_points']; $i++)
			{
				$j = $i + 1;
		
				$rate_img .= ($j <= $new_rating ) ? $rate_yes : $rate_no;
			}
		
			if ($total_ratings == 1)
			{
				$rate_img .= '<br />' . $this->language->lang('DL_RATING_ONE');
			}
			else
			{
				$rate_img .= '<br />' . $this->language->lang('DL_RATING_MORE', $total_ratings);
			}
		
			$json_out = json_encode(array('rate_img' => $rate_img, 'dl_id' => $dl_id));
		
			$http_headers = array(
				'Content-type' => 'text/html; charset=UTF-8',
				'Cache-Control' => 'private, no-cache="set-cookie"',
				'Expires' => gmdate('D, d M Y H:i:s', time()) . ' GMT',
			);
		
			foreach ($http_headers as $hname => $hval)
			{
				header((string) $hname . ': ' . (string) $hval);
			}
		
			$this->template->set_filenames(array(
				'body' => 'dl_json.html')
			);
			$this->template->assign_var('JSON_OUTPUT', $json_out);
			$this->template->display('body');
		}

		garbage_collection();
		exit_handler();
	}
}
