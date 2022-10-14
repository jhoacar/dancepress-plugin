<?php
namespace DancePressTRWA\Models;

/**
 * BillingCustomPayments Model.
 *
 * Handles database functions connected to billing custom payments.
 *
 * @since 2.2.0
 */
final class BillingCustomPayments extends Model
{
    
    /**
     *
     * @var integer
     */
    const STATUS_UNPAID = 0;
    
    /**
     *
     * @var integer
     */
    const STATUS_PAID = 1;
    
    /**
     *
     * @var integer
     */
    const STATUS_REFUNDED = 2;
    
    public function getTableName()
    {
        return "{$this->p}ds_billing_custom_payments";
    }
    
    /**
     * Finds a set of billing custom payments by a provided billing id
     *
     * @param int $billingId The billing id
     * @return array
     */
    public function findByBillingId($billingId)
    {
        $customPayments = array();
        
        if (!$billingId) {
            return $customPayments;
        }
        
        $statement = "SELECT id
                        FROM {$this->p}ds_billing_custom_payments 
                        WHERE billing_id = %d";
        
        $statement = $this->db->prepare($statement, $billingId);
        
        $this->db->query($statement);
        
        $customPayments = $this->db->last_result;

        if ($customPayments) {
            foreach ($customPayments as $key => $customPayment) {
                $customPayments[$key] = $this->findById($customPayment->id);
            }
        }
        return $customPayments;
    }
    
    /**
     *
     * @param array $data
     * @return integer|null
     */
    public function create($data)
    {
        if (!isset($data['datetime_created'])) {
            $data['datetime_created'] = date("Y-m-d H:i:s");
        }
        
        return parent::create($data);
    }
    
    public function findById($id)
    {
        $option = new Option();
        
        $customPayment = parent::findById($id);
        $taxPercent = $option->getTaxPercent();
        $customPayment->tax_amount = 0;
        
        if ($customPayment && $customPayment->datetime_paid) {
            $customPayment->tax_amount = ($customPayment->amount * ($taxPercent/100));
        }
        
        return $customPayment;
    }
}
