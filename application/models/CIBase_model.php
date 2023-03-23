<?php

interface MedocInterface {
    public function setTable($table);
}

abstract class CIBase_model extends CI_Model implements MedocInterface
{
    protected $table;

    function __construct()
    {
        parent::__construct();
    }

    /**
     * @param string $table nom de la table lié au model
     */
    abstract function setTable($table);

    /**
     * @param string $field nom du champ de la table
     * @param string $value la valeur correpondant au champ de la table
     * @param string|null $table nom de la table
     * @return mixed|null l'objet correspondant ou null si inexistant
     */
    public function getByFieldValue($field, $value, $table = null)
    {
        $table = is_null($table) ? $this->table : $table;
        $result = $this->db->where($field, $value)
                           ->get($table)
                           ->result();
        return count($result) > 0 ? $result[0] : null;
    }

    /**
     * @param string $field nom du champ de la table
     * @param string $value la valeur correpondant au champ de la table
     * @param string|null $table nom de la table
     * @return int le nombre de résultats trouvés
     */
    public function countByFieldValue($field, $value, $table = null)
    {
        $table = is_null($table) ? $this->table : $table;
        return $this->db->where($field, $value)
                        ->from($table)
                        ->count_all_results();
    }

    /**
     * @param string $field nom du champ de la table
     * @param string $value la valeur correpondant au champ de la table
     * @param string|null $table nom de la table
     * @return bool true si au moins un résultat est trouvé, false sinon
     */
    public function existByFieldValue($field, $value, $table = null)
    {
        $table = is_null($table) ? $this->table : $table;
        return $this->countByFieldValue($field, $value, $table) > 0;
    }

    /**
     * @param int $id l'ID à vérifier
     * @param string|null $table nom de la table
     * @return bool true si l'ID existe dans la table, false sinon
     */
    public function idExist($id, $table = null)
    {
        $table = is_null($table) ? $this->table : $table;
        return $this->db->where('id', $id)
                        ->from($table)
                        ->count_all_results() > 0;
    }

    /**
     * @param array $data contenant les données à insérer
     * @param string|null $table nom de la table
     * @param array $to_now contient la liste des champs à mettre en now
     * @return int l'ID de la ligne insérée
     */
    public function create($data, $table = null, $to_now = [])
    {
        $table = is_null($table) ? $this->table : $table;
        foreach ($to_now as $field_name) {
            $data[$field_name] = date('Y-m-d H:i:s');
        }
        $this->db->insert($table, $data);
        return $this->db->insert_id();
    }

    /**
     * @param string $select='*'
     * @param array $where=[]
     * @param string|null $order_by=null
     * @param string $direction='desc'
     * @param string|null $limit=null
     * @param string|null $offset=null
     * @param string|null $table=null
     */
    public function read($select = '*', $where = array(), $order_by = null, $direction = 'desc', $limit = null, $offset = null, $table = null) {
        $table = is_null($table)?$this->table:$table;
        $this->db->select($select)->from($table);
        if (!empty($where)) {
            $this->db->where($where);
        }
        if (!empty($order_by)) {
            $this->db->order_by($order_by, $direction);
        }
        if (!empty($limit)) {
            $this->db->limit($limit, $offset);
        }
        $query = $this->db->get();
        return $query->result();
    }


    /**
     * @param string $field
     * @param string $keywords='' la valeur en chaine de caractère à rechercher
     * @param string $select='*' les champs selectionnés
     * @param array $where=[]
     * @param string|null $order_by=null
     * @param string $direction='desc'
     * @param string|null $limit=null
     * @param string|null $offset=null
     * @param string $wildcard='after' (after, before, both)
     * @param string|null $table nom de la table 
     */
    function search($field, $keywords = '', $select="*", $where = array(), $order_by = null, $direction = 'asc', $limit = null, $offset = null, $wildcard='after', $table = null) {
        $table = is_null($table)?$this->table:$table;
        $this->db->select($select)->from($table);
        if (!empty($keywords)) {
            $wildcard = in_array($wildcard, ['before', 'after', 'both'])?$wildcard:'after';
            $this->db->like($field, $keywords, $wildcard);
        }
        if (!empty($where)) {
            $this->db->where($where);
        }
        if (!empty($order_by)) {
            $this->db->order_by($order_by, $direction);
        }
        if (!empty($limit)) {
            $this->db->limit($limit, $offset);
        }
        $query = $this->db->get();
        return $query->result();
    }
}