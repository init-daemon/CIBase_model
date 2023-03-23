# CIBase_model
```php application\models\<Model_name>_model.php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . 'models/CIBase_model.php');

class Model_name_model extends CIBase_model
{

	function __construct()
	{
		parent::__construct();
        $this->setTable('nom_de_la_table');
	}

    public function setTable($table)
    {
        $this->table = $table;
    }
}

```