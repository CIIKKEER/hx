<?php
namespace appx\api\v100\model;

use appx;
use appx\model\c_model;

class m_test extends c_model
{

	public function about ()
	{
		/**
		 * 
		 * @var \hx\db\i_trans $i_t
		 */
		return [pathinfo(__FILE__),$this->db->mysqli->open_with_env_json(__DIR__.'/../../../../env/env.json')->get_db_information()->to_array()->get()];
	}
}

