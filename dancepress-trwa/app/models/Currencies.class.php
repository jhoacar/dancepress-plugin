<?php

namespace DancePressTRWA\Models;

final class Currencies extends Model
{
    public function __construct( $sessionCondition = '' )
    {
        parent::__construct( $sessionCondition );
    }
    
    /**
     *
     * @return array
     */
    public function getAll()
    {
    }
    
    /**
     *
     * @param int $id
     */
    public function findById( $id )
    {
        $id = (int) $id;
        $results = $this->db->get_results( "SELECT *, CONCAT(name, ' - ', code) as qualified_name FROM {$this->p}ds_currency WHERE id = {$id}" );
        $resObj = (object) $results[0];
        return $resObj;
    }

}