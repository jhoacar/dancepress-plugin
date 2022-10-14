<?php
namespace DancePressTRWA\Models;

class News extends Model
{
    public function __construct($sessionCondition = '')
    {
        parent::__construct($sessionCondition);
    }

    public function getNews($limit)
    {
        $sql = $this->db->prepare(
            "
			
			SELECT
				*
			FROM
				{$this->p}ds_news
			WHERE " . $this->sessionCondition . " 
			ORDER BY id DESC
			LIMIT %d",
            (int)$limit
        );
        $this->db->query($sql);
        return $this->db->last_result;
    }
    
    /*public function saveReferenceTransactionId($referenceTransactionId){
        $sql = $this->db->prepare("

            UPDATE
                {$this->p}ds_billing
            SET
                referenceid=%d'
            WHERE
                id = %d
            LIMIT 1
        ", $transaction_id, $referenceTransactionId);

        $this->db->query($sql);
    }

    public function saveUnverifiedTransaction($data, $pricing){

        $sql = $this->db->prepare("

            INSERT INTO
                {$this->p}ds_billing
            SET
                parent_id = %d,
                parent2_id = %d,
                payment = %s,
                payment_confirmed = 0,
                billing_details = %s,

                paymentamount1 = %f,
                paymentamount2 = %f,
                paymentamount3 = %f,
                paymentamount4 = %f
            ", $data['parents']['mother'], $data['parents']['father'], $pricing, json_encode($this->totalFees),
            $this->totalFees->period1total,	$this->totalFees->period2total,	$this->totalFees->period3total, $this->totalFees->period4total
        );
        $this->db->query($sql);
        $this->transaction_id = $this->db->insert_id;
    }*/
}
