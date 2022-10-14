<?php
namespace DancePressTRWA\Models;

class Countries extends Model
{
    public function __construct($sessionCondition = '')
    {
        parent::__construct($sessionCondition);
    }

    /**
     *
     * @return array
     */
    public function getAll()
    {
        return $this->db->get_results("SELECT * FROM {$this->p}ds_countries ORDER BY name = 'Canada' DESC, name = 'United States' DESC, name = 'Australia' DESC, name = 'United Kingdom' DESC");
    }

    /**
     *
     * @param int $id
     */
    public function findById($id)
    {
        $id = (int) $id;
        $results =  $this->db->get_results("SELECT * FROM {$this->p}ds_countries WHERE id = {$id}");
        $resObj = (object) $results[0];
        return $resObj;
    }
}
