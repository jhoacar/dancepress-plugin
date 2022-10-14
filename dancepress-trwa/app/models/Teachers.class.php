<?php
namespace DancePressTRWA\Models;

/**
 * BillingInstallments Model.
 *
 * Handles database functions connected to teachers installments.
 * Returns data from ds_class_teachers table
 *
 * @since 1.0
 */

class Teachers extends Model
{
    public function __construct($sessionCondition = '')
    {
        parent::__construct($sessionCondition);
    }

    public function getClassTeachers()
    {
        $res = $this->db->get_results("select id, firstname, lastname FROM  wp_ds_class_teachers WHERE " . $this->sessionCondition);

        return $res;
    }
}
