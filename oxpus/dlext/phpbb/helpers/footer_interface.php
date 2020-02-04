<?php

/**
*
* @package phpBB Extension - Oxpus Downloads
* @copyright (c) 2002-2020 OXPUS - www.oxpus.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace oxpus\dlext\phpbb\helpers;

/**
 * Interface for footer
 *
 */
interface footer_interface
{
	/**
	 * Set the module parameters for footer handlings
	 *
	 * @param string $nav_view current page on breadcrumps
	 * @param int $cat_id current category
	 * @param int $df_id current download
	 * @param array $index the complete downloads category index 
	 * @return void
	 * @access public
	 */
	public function set_parameter($nav_view = '', $cat_id = 0, $df_id = 0, $index = array());

	/**
	 * Module main part
	 *
	 * @return void
	 * @access public
	 */
	public function handle();
}
