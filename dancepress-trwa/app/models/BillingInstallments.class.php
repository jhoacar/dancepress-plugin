<?php
namespace DancePressTRWA\Models;

/**
 * BillingInstallments Model.
 *
 * Handles database functions connected to billing installments.
 * Correctly SHOULD only connect to billing_installments table.
 *
 * @since 1.0
 */
final class BillingInstallments extends Model
{
    
    /**
     * Find Billing INstallments by id.
     *
     * @param int $id The id of the required installment.
     * @return object Containing a billing installment.
     */
    public function findById($id)
    {
        $id = (int) $id;

        $results =  $this->db->get_results("SELECT * FROM {$this->p}ds_billing_installments WHERE id = {$id}");

        $resObj = (object) $results[0];

        return $resObj;
    }

    /**
    *
    * Get max number of installments this client needs to pay
    *
    * @param object $parents
    * @return int number of installments
    */

    public function getMaxParentBillingInstallments($parents)
    {
        $maxParentInstallments = 0;

        $objBilling = new Billing($this->sessionCondition);

        //var_dump($parents);

        if (empty($parents)) {
            return $maxParentInstallments;
        }

        foreach ($parents as $parent) {
            //get billingId from parentId
            $billingHistory = $objBilling->getBillingHistoryByParentId($parent->id);

            if ($billingHistory) {
                $billingId = (int) $billingHistory->billing_id;
                $billingItem = $objBilling->getTransactionWithParentById($billingId, 1);

                if ($billingItem) {
                    $installments = $this->getInstallments($billingId);

                    if (count($installments) > $maxParentInstallments) {
                        $maxParentInstallments = count($installments);
                    }
                }
            }
        }

        return $maxParentInstallments;
    }

    /**
     * Gets a list of installments for a particular billing record
     * @param $billingId
     * @deprecated
     * @see Billing::getInstallments()
     */
    public function getBillingInstallments($billingId)
    {
        return (array) $this->getInstallments((int)$billingId);
    }

    /**
     * Gets a list of installmants for a particular billing record
     *
     * @param int $billingId The id of the billing record to retrieve the installments for
     * @return array An array of billing installments if any exist for the billing record, and empty array  otherwise
     */
    public function getInstallments($billingId)
    {
        $billingId = (int)$billingId;
        if ($billingId < 1) {
            return [];
        }

        $statement = "SELECT * FROM {$this->p}ds_billing_installments WHERE billing_id = {$billingId} ORDER BY payment_date ASC";

        $this->query($statement);

        $result = $this->db->last_result;

        if (!$result) {
            $result = [];
        }

        return $result;
    }

    public function advanceByBillingId($billingId)
    {
        $billingId = (int) $billingId;
        return $this->query("UPDATE {$this->p}ds_billing_installments SET paid = TRUE, datetime_paid = NOW() WHERE billing_id = $billingId");
    }
}
