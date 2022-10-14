<?php
namespace DancePressTRWA\Models;

use Exception as PHPException;
use \DancePressTRWA\Util\FinancialCalculations as FinancialCalculations;

/**
 * Billing Model.
 *
 * Handles database functions connected to billing.
 * Correctly SHOULD only connect to Billing table.
 *
 * @since 1.0
 */
final class Billing extends Model
{
    public $totalFees;
    public $transaction_id;
    private $classDetails;
    private $payment_method;
    private $discount = 0;
    public $errors;

    public function __construct($sessionCondition = '')
    {
        parent::__construct($sessionCondition);
    }

    public function getTableName()
    {
        return "{$this->p}ds_billing";
    }

    /**
     * Initiate unpaid billing.
     *
     * @param array $data Data to have Fee data added.
     * @param array  $classes Classes to be initiated
     * @return array [$data, $totalFees]
     */
    public function initiateUnpaid(array $data, array $classes)
    {
        if (!$pricing = $this->getClassPrice($classes)) {
            return false;
        }

        $fees = new \DancePressTRWA\Util\FinancialCalculations;
        $this->setTotalFees($fees->calculateTotalFees($pricing));
        $this->saveUnverifiedTransaction($data, $pricing);

        return array($data, 'totalFees' => $this->getTotalFees());
    }


    /**
     * Recalculate all fees for a particular parent by using a student as a
     * refernce point.
     *
     * @see \DancePressTRWA\Models\Billing::updateBillingAgreementByParent()
     * @param int $studentId
     */
    public function updateBillingAgreementByStudent($studentId)
    {
        $objParents = new Parents($this->sessionCondition);

        $parent = $objParents->findParentsByStudentId($studentId);

        $this->updateBillingAgreementByParent($parent[0]->id);
    }

    /**
     * Recalculate all fees for a particular parent
     *
     * @param int $parentId The id of the parent
     */
    public function updateBillingAgreementByParent($parentId)
    {
        $objStudents = new ClassStudents($this->sessionCondition);
        $objFees = new FinancialCalculations;

        $billing = $this->getBillingHistoryByParentId($parentId);
        $students = $objStudents->getStudentsByParentId($parentId);

        $this->payment_method = $billing->payment_method;
        $this->discount = $billing->discount;

        foreach ($students as $sk => $s) {
            $students[$sk]->classes = $objStudents->getStudentClasses($s->id);
        }

        if (!$pricing = $this->getClassPriceBackend($students)) {
            return false;
        }

        $this->setTotalFees($objFees->calculateTotalFees($pricing));

        if ($this->payment_method == 'cheques') {
            $this->updateChequeTransaction($parentId);
        } else {
            $this->updateVerifiedTransaction($parentId);
        }
    }

    /**
     * Gets a list of the next scheduled payments for set of parents
     * @param array $ids The id(s) of the patents to retrieve the next scheduled payments for
     * @throws \Exception Throw an instance of \Exception if not parent was provided
     * @return array|NULL A list of the next scheduled payments for the parents if any found, null otherwise
     */
    public function getNextScheduledPayments(array $ids)
    {
        $objInstallments = new BillingInstallments;
        if (empty($ids)) {
            throw new \Exception("Parent ids not provided");
        }

        $sql = "SELECT
				parents.id as parent_id,
				parents.firstname,
				parents.lastname,
				parents.email,
				billing.id as billing_id,
				billing.*
			FROM
				{$this->p}ds_parents parents
			INNER JOIN
				{$this->p}ds_billing billing ON parents.id = billing.parent_id
			WHERE
				payment_method = 'online'
			AND
				billing.payment_confirmed = 1
			AND

		";
        $where = "( ";

        foreach ($ids as $id) {
            $where .= "billing.parent_id = " . (int)$id . " OR ";
        }
        $where = rtrim($where, 'OR ') . ') ';

        $sql .= $where;

        $sql .= ' AND parents.' . $this->sessionCondition;

        $this->db->query($sql);

        $result = $this->db->last_result;

        foreach ($result as &$parent) {
            if ($parent->registration_fee_paid == 0) {
                $parent->nextPaymentName = "Registration Fee";
                $parent->nextPaymentAmount = $parent->registration_fee;
                break;
            }

            foreach ($objInstallments->getInstallments($parent->billing_id) as $installment) {
                if (!$installment->paid) {
                    $parent->nextPaymentName = $installment->payment_name;
                    $parent->nextPaymentAmount = $installment->amount;
                    $parent->nextBillingInstallmentId = $installment->id;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Gets the total number of classes a set of students are currently enrolled in
     * @param array $studentIds
     * @return string|NULL
     */
    public function getStudentsClassCount(array $studentIds)
    {
        $idNum = count($studentIds);

        $where = '(';

        for ($i = 1; $i <= $idNum; $i++) {
            $where .= " student_id = " . $studentIds[$idNum - 1] . " OR";
        }

        $where = rtrim($where, ' OR') . ')';

        $where .= ' AND `students`.' . $this->sessionCondition;

        //Inner Join required to avoid miscounting when student was signed up for deleted classes.
        $sql = "SELECT count(*)
			FROM
				{$this->p}ds_class_students students
			INNER JOIN
				{$this->p}ds_classes class ON class.id = students.class_id
			WHERE
				$where";

        return $this->db->get_var($sql);
    }

    /**
     *
     * @param array $classes
     * @return array
     */
    private function classNumberCalcs(array $classes)
    {
        $options = new Option();
        //$global_installment_fees = $options->getInstallmentFees(true);

        $this->setClassDetails($classes);

        $scheduledPayments = [];
        $totalOneTimeFlatFee = 0;

        $globalInstallments = get_option('ds_installment_fees');

        $dstrwa_multi = get_option('dstrwa_multi');

        $classCount = count($classes);
        $studentClassCount = 0;

        foreach ($classes as $value) {
            $studentClassCount += $value->student_count;
        }

        $classCount = ($classCount * $studentClassCount);

        foreach ($classes as $class) {
            //1. If one time flat course fee - calculate price and skip rest
            if (!empty($class->class_fee) && $class->class_fee > 0.0) {
                $totalOneTimeFlatFee += $class->class_fee;
                continue;
            }

            $class->installments = $this->getClassInstallmentFees($class->id);

            //2. Check for and use course-level installments if they exist
            if ($class->installments) {
                foreach ($class->installments as $classInstallment) {
                    //$className = $class->name;
                    $installmentDate = $classInstallment->payment_date;

                    if (!isset($scheduledPayments[$installmentDate])) {
                        $scheduledPayments[$installmentDate]['payment_name'] = $classInstallment->name;
                        $scheduledPayments[$installmentDate]['amount'] = 0;
                    }

                    //Increase the scheduled payment installment amount by the
                    //amount of the installment by the number of students in the class
                    $scheduledPayments[$installmentDate]['amount'] += ($classInstallment->amount * $class->student_count);
                }
            } elseif ($class->use_global_installments && $globalInstallments) {
                //3. Else Global installments are set: calculate them with any bulk discounts.
                foreach ($dstrwa_multi as $installName => $instDeets) {
                    $bulkClassFee = 0;
                    if (isset($dstrwa_multi[$installName]['num_required']) && $dstrwa_multi[$installName]['num_required']) {
                        $last = 0;
                        foreach ($dstrwa_multi[$installName]['num_required'] as $idx => $min) {
                            if ($classCount >= $min && $min >= $last) {
                                $bulkClassFee = $dstrwa_multi[$installName]['fee'][$idx];
                                $last = $min;
                            }
                        }

                        foreach ($globalInstallments['date'] as $ik => $installDate) {
                            if ($globalInstallments['name'][$ik] == urldecode($installName)) {
                                //TODO Look into whether or not installments would not be overwritten on each iteration
                                $scheduledPayments[$installDate]['payment_name'] = $globalInstallments['name'][$ik];
                                $scheduledPayments[$installDate]['amount'] = ($bulkClassFee > 0 ? $bulkClassFee : ($globalInstallments['amount'][$ik]) * $studentClassCount);
                            }
                        }
                    }
                }
            } else {
                //4. Else, lets see if a global one-time flat fee has been define for classes
                $onTimeFlatFee = $options->getClassesOneTimeFlatFee();

                if (!empty($onTimeFlatFee) && $onTimeFlatFee > 0) {
                    $totalOneTimeFlatFee += $onTimeFlatFee;
                    continue;
                }

                throw new PHPException("Could not determine cost for class '{$class->name}'");
            }
        }

        return array(
            "classes" => $classes,
            "scheduled_payments" => $scheduledPayments,
            "total_classes_one_time_amount" => $totalOneTimeFlatFee,
        );
    }

    /**
     * Gets the installment fees that has been defined for a particular course by a provided id
     * @param int $class_id The id of the course/class
     * @throws \Exception Throws and instance of \Exception is the course id was not provided
     * @return array A list of installments if any have been defined for the course, and empty array otherwise
     */
    public function getClassInstallmentFees($class_id)
    {
        $class_id = (int)$class_id;
        if (empty($class_id)) {
            throw new \Exception("Class id not provided");
        }

        $this->query("SELECT * FROM {$this->p}ds_class_installment_fees where class_id = $class_id ORDER BY payment_date ASC");
        return  $this->db->last_result;
    }

    /**
     *
     * @param array $classes
     * @return array
     */
    public function getClassPrice(array $classes)
    {
        $cids = '';

        if (isset($classes['class'])) {
            foreach ($classes['class'] as $class) {
                foreach ($class as $classId => $weekDay) {
                    if ($weekDay['day'] != '') {
                        $cids .= " id = " . $classId . " OR";
                    }
                }
            }
        }

        if (isset($classes['recommended'])) {
            foreach ($classes['recommended'] as $class) {
                foreach ($class as $classId) {
                    $cids .= " id = " . $classId . " OR";
                }
            }
        }

        $cids = rtrim($cids, " OR");

        if (! $cids) {
            return false;
        }

        $sql = "
			SELECT
				*
			FROM
				{$this->p}ds_classes
			WHERE
				is_parent_event = 1
			AND
				(
					$cids
				) AND " . $this->sessionCondition;

        $this->db->query($sql);
        $res = $this->db->last_result;

        //How many children taking each class.
        foreach ($res as &$v) {
            if (isset($classes['class'])) {
                foreach ($classes['class'] as $child) {
                    if (array_key_exists($v->id, $child) && $child[$v->id]['day'] != '') {
                        if (!isset($v->student_count)) {
                            $v->student_count = 1;
                        } else {
                            $v->student_count = $v->student_count + 1;
                        }
                    }
                }
            }

            if (isset($classes['recommended'])) {
                foreach ($classes['recommended'] as $class) {
                    foreach ($class as $ccv) {
                        if ($v->id == $ccv) {
                            if (!isset($v->student_count)) {
                                $v->student_count = 1;
                            } else {
                                $v->student_count = $v->student_count + 1;
                            }
                        }
                    }
                }
            }
        }

        $this->setClassDetails($res);

        return $this->classNumberCalcs($res);
    }


    /**
     * Modified version of getClassPrice for recalculating fees in backend.
     *
     * @param array $students
     * @return array
     */
    private function getClassPriceBackend(array $students)
    {
        $objClasses = new ClassManager($this->sessionCondition);

        $classIds = array();

        foreach ($students as $student) {
            foreach ($student->classes as $class) {
                $classIds[] = (int)$class->id;
            }
        }

        $classes = $objClasses->findByIds($classIds);

        if ($classes) {
            foreach ($classes as  &$class) {
                foreach ($students as $student) {
                    foreach ($student->classes as $studentClass) {
                        //If student in this class
                        if ($class->id == $studentClass->id) {
                            if (!isset($class->student_count)) {
                                $class->student_count = 1;
                            } else {
                                $class->student_count ++;
                            }
                        }
                    }
                }
            }
        }

        return $this->classNumberCalcs($classes);
    }

    public function saveReferenceTransactionId($referenceTransactionId)
    {
        $sql = $this->db->prepare("

			UPDATE
				{$this->p}ds_billing
			SET
				referenceid='%s'
			WHERE
				id = %d
			AND " . $this->sessionCondition . "
			LIMIT 1
		", $referenceTransactionId, $this->transaction_id);

        $this->db->query($sql);
    }

    /**
     *
     * @param array $data
     * @param array $pricing
     * @throws \Exception
     */
    public function saveUnverifiedTransaction(array $data, $pricing)
    {
        if (empty($data['parents']['parent'])) {
            throw new \Exception('Parent Id is required');
        }

        //Check to see if billing entry already exists
        $sql = $this->db->prepare("SELECT id
			FROM
				{$this->p}ds_billing
			WHERE
				parent_id = %d
			AND " . $this->sessionCondition, $data['parents']['parent']);

        $billingId = $this->db->get_var($sql);

        if (!$billingId) {
            $sql = $this->db->prepare(
                "INSERT INTO
					{$this->p}ds_billing
				SET
					parent_id = %d,
					payment = %s,
					payment_confirmed = 1,
					billing_details = %s,
					registration_fee = %f,
					total_classes_one_time_amount = %f,
					date_added = now(),
				" . $this->sessionCondition,
                $data['parents']['parent'],
                $this->totalFees->grandTotal,
                json_encode($data),
                $this->totalFees->payableNowIncTaxRegFeeOnly,
                $this->totalFees->totalClassesOneTimeAmount
            );

            $this->db->query($sql);
            $this->transaction_id = $this->db->insert_id;

            foreach ($this->totalFees->periods as $installment) {
                $statement = "INSERT INTO {$this->p}ds_billing_installments
					(billing_id, payment_name, payment_date, amount)
					VALUES({$this->transaction_id}, '{$installment['payment_name']}', '{$installment['payment_date']}', {$installment['incTax']})";

                $this->query($statement);
            }
        } else {
            $sql = $this->db->prepare(
                "

				UPDATE
					{$this->p}ds_billing
				SET
					parent_id = %d,
					payment = %s,
					payment_confirmed = 1,
					billing_details = %s,
					registration_fee = %f,
					total_classes_one_time_amount = %f,
					date_added = now(),
				" . $this->sessionCondition . "
				WHERE
					id = %d
				LIMIT 1",
                $data['parents']['parent'],
                $this->totalFees->grandTotal,
                json_encode($data),
                $this->totalFees->payableNowIncTaxRegFeeOnly,
                $this->totalFees->totalClassesOneTimeAmount,
                $billingId
            );

            $this->db->query($sql);
            $this->transaction_id = $billingId;

            //Clear installments and re-insert
            //FIXME Bad CRUD. All SQL for billing_installments should be in correct model
            $sql = "DELETE FROM {$this->p}ds_billing_installments where billing_id = $billingId";
            $this->query($statement);

            foreach ($this->totalFees->periods as $installment) {
                $statement = "INSERT INTO {$this->p}ds_billing_installments
					(billing_id, payment_name, payment_date, amount)
					VALUES({$this->transaction_id}, '{$installment['payment_name']}', '{$installment['payment_date']}', {$installment['incTax']})";

                $this->query($statement);
            }
        }
    }

    /**
     * Create an empty billing agreement for parents added in back end.
     * @param int $parentId
     */
    public function createParentBillingBlank($parentId)
    {
        $parentId = (int)$parentId;

        $sql = $this->db->prepare(
            "

			INSERT INTO
				{$this->p}ds_billing
			SET
				parent_id = %d,
				payment_confirmed = 1,
				date_added = now(),
				" . $this->sessionCondition,
            $parentId
        );

        $this->db->query($sql);
        $this->transaction_id = $this->db->insert_id;
    }

    /**
     * Saves all details of a verified transaction.
     * @param object $stripe Stripe details
     * @param array $session etails from client session data.
     * @param string $paymentType Valid values 'advance' or 'online'
     * @return void
     */
    public function saveVerifiedTransaction($stripeData, $session, $paymentType)
    {
        $installments = new BillingInstallments;
        if ($paymentType == 'advance') {
            $installments->advanceByBillingId($session['stage4']['transaction_id']);
        } else {
            $paymentType = "online";
        }

        $sql = $this->db->prepare(
            "UPDATE
				{$this->p}ds_billing
			SET
				payment_confirmed = 1,
				stripe_id = %s,
				stripe_default_card = %s,
				stripe_confirmation_data = %s,
				stripe_email = %s,
				registration_fee_paid = 1,
				registration_fee_date = now(),
				payment_method = %s
			WHERE
				id = %d
			AND
				" . $this->sessionCondition . "
			LIMIT 1",
            $stripeData->id,
            $stripeData->default_card,
            json_encode(array($stripeData->created, $stripeData->description, $stripeData->email, $stripeData->cards)),
            $stripeData->email,
            $paymentType,
            $session['stage4']['transaction_id']
        );

        $this->db->query($sql);
    }

    /**
     * Update the most recent billing agreement ( in the future all earlier billing agreements will be by definition expired).
     * @param int $parentId
     * @throws \Exception
     */
    private function updateVerifiedTransaction($parentId)
    {
        $sql = $this->db->prepare("SELECT
				id
			FROM
				{$this->p}ds_billing
			WHERE
				parent_id = %d
			AND
				payment_confirmed = 1
			AND " .
                $this->sessionCondition . "
			ORDER BY
				date_added DESC
			LIMIT 1
		", $parentId);

        $billingAgreementId = $this->db->get_var($sql);

        if (!$billingAgreementId) {
            $sql = $this->db->prepare("SELECT
					id
				FROM
					{$this->p}ds_billing
				WHERE
					parent_id = %d
				AND
					payment_confirmed = 0
				AND " .
                    $this->sessionCondition . "
				ORDER BY
					date_added DESC
				LIMIT 1
			", $parentId);

            $billingAgreementId = $this->db->get_var($sql);

            if (! $billingAgreementId) {
                throw new \Exception("Error: Billing agreement not found.");
            }
        }

        $totalFees = $this->getTotalFees();

        $sql = $this->db->prepare(
            "UPDATE
				{$this->p}ds_billing
			SET
				payment = %f,
				registration_fee = %f,
				total_classes_one_time_amount = %f,
				payment_confirmed = 1
			WHERE
				id = %d
			AND " .
                $this->sessionCondition,
            $totalFees->grandTotal,
            $totalFees->payableNowIncTaxRegFeeOnly,
            $totalFees->totalClassesOneTimeAmount,
            $billingAgreementId
        );

        //Update the installments for the billing record $billingAgreementId
        //where the payment_date column value matches with the calculated total
        //fees respective period payment_date key value

        foreach ($this->totalFees->periods as $installment) {
            if (!$this->db->get_var("SELECT id FROM {$this->p}ds_billing_installments WHERE payment_date = '{$installment['payment_date']}' and billing_id = {$billingAgreementId}")) {

                $this->query("
                    INSERT INTO
                        {$this->p}ds_billing_installments
                    SET
                        amount = {$installment['incTax']},
                        payment_date = '{$installment['payment_date']}',
                        billing_id = {$billingAgreementId},
                        payment_name = '{$installment['payment_name']}'");
            } else {
                $this->query("
                    UPDATE
                        {$this->p}ds_billing_installments
                    SET
                        amount = {$installment['incTax']},
                        payment_name = '{$installment['payment_name']}'
                    WHERE
                        payment_date = '{$installment['payment_date']}'
                    AND
                        billing_id = {$billingAgreementId}");
            }
        }

        $this->db->query($sql);
    }

    public function updateChequeTransaction(int $parentId)
    {
        $payment = 0;

        $totalFees = $this->getTotalFees();

        $sql = $this->db->prepare(
            "SELECT
                id
			FROM
				{$this->p}ds_billing
			WHERE
				parent_id = %d
			AND
				payment_confirmed = 1
			AND " .
                $this->sessionCondition, $parentId);

        $billingId = $this->db->get_var($sql);

        foreach ($totalFees->periods as $installment) {
            $payment += $installment['base'];

            $this->query(
                "UPDATE
                    {$this->p}ds_billing_installments
                SET
                    amount = {$installment['base']}
                WHERE
                    payment_date = '{$installment['payment_date']}'
                AND
                    billing_id = {$billingId}");
        }

        $sql = $this->db->prepare(
            "UPDATE
				{$this->p}ds_billing
			SET
				payment = %s,
				registration_fee = %f,
				total_classes_one_time_amount = %f
			WHERE
				id = %d
			AND " .
                $this->sessionCondition,
            $totalFees->grandTotal,
            $totalFees->registrationFee,
            $totalFees->totalClassesOneTimeAmount,
            $billingId
        );

        $this->db->query($sql);
    }


    /**
     * Verify, but record reg fee as not paid Update amounts to remove transaction fees.
     * @param \stdClass $totalFees
     * @param int $billingId
     */
    public function saveChequeTransaction($totalFees, $billingId)
    {
        $payment = $totalFees->registrationFee;
        $billingId = (int)$billingId;

        foreach ($totalFees->periods as $installment) {
            $payment +=  $installment['base'];
            //Update the billing installments with base amount, not incTax, where 'payment_date' field matches
            $this->query("UPDATE {$this->p}ds_billing_installments SET amount = {$installment['base']} WHERE 'payment_date' = '{$installment['payment_date']}'");
        }

        $sql = $this->db->prepare(
            "UPDATE
				{$this->p}ds_billing
			SET
				payment_confirmed = 1,
				payment = %f,
				registration_fee_paid = 0,
				payment_method = 'cheques',
				registration_fee = %f
			WHERE
				id = %d
			AND " .
                $this->sessionCondition
            . "
			LIMIT 1",
            $payment,
            $totalFees->registrationFee,
            $billingId
        );

        $this->query($sql);
    }

    /**
     * Verify, but record reg fee as not paid Update amounts to remove transaction fees.
     * @param \stdClass $totalFees
     * @param int $billingId
     */
    public function savenNoPayTransaction($totalFees, $billingId)
    {
        $payment = $totalFees->registrationFee;
        $billingId = (int)$billingId;

        foreach ($totalFees->periods as $installment) {
            $payment +=  $installment['base'];
            //Update the billing installments with base amount, not incTax, where 'payment_date' field matches
            $this->query("UPDATE {$this->p}ds_billing_installments SET amount = {$installment['base']} WHERE 'payment_date' = '{$installment['payment_date']}'");
        }

        $sql = $this->db->prepare(
            "UPDATE
				{$this->p}ds_billing
			SET
				payment_confirmed = 1,
				payment = %f,
				registration_fee_paid = 0,
				payment_method = 'cheques',
				registration_fee = %f
			WHERE
				id = %d
			AND " .
                $this->sessionCondition
            . "
			LIMIT 1",
            $payment,
            $totalFees->registrationFee,
            $billingId
        );

        $this->query($sql);
    }

    public function getUnverifiedTransactionById($id)
    {
        $sql = $this->db->prepare("
			SELECT
				*
			FROM
				{$this->p}ds_billing
			WHERE
				id = %d
			AND " .
                $this->sessionCondition, $id);

        return $this->db->get_row($sql);
    }

    /**
     *
     * @param int $id
     * @param int  $payment_confirmed
     * @return array|object|NULL|void
     */
    public function getTransactionById($id, $payment_confirmed = 1)
    {
        $sql = $this->db->prepare("
			SELECT
				*
			FROM
				{$this->p}ds_billing
			WHERE
				id = %d
			AND
				payment_confirmed = $payment_confirmed
			AND " .
                $this->sessionCondition, $id);


        $transaction =  $this->db->get_row($sql);

        $installments = new BillingInstallments;

        if ($transaction) {
            $transaction->installments = $installments->getInstallments($id);
        }

        return $transaction;
    }

    public function getTransactionWithParentById($id, $payment_confirmed = 1)
    {
        $sql = $this->db->prepare("
			SELECT
				b.*,
				b.id AS billing_id,
				p.id AS parent_id,
				p.*
			FROM
				{$this->p}ds_billing b
			INNER JOIN
				{$this->p}ds_parents p ON b.parent_id=p.id
			WHERE
				b.id = %d
			AND
				b.payment_confirmed = %d
			AND b." .
                $this->sessionCondition, $id, $payment_confirmed);

        return $this->db->get_row($sql);
    }

    public function getBillingDetails(int $billingId)
    {
        $sql = $this->db->prepare("SELECT
				billing_details
			FROM
				{$this->p}ds_billing
			WHERE
				id = %d
			AND " .
                $this->sessionCondition, $billingId);

        return json_decode($this->db->get_var($sql));
    }

    private function setTotalFees($totalFees)
    {
        $this->totalFees = $totalFees;
    }

    private function getTotalFees()
    {
        return $this->totalFees;
    }

    /**
     *
     * @param int $id
     * @param int $confirmed
     * @return array|NULL
     */
    public function getBillingHistoryByParentId($id, $confirmed = 1)
    {
        $objBillingCustomPayments = new BillingCustomPayments();
        $installments = new BillingInstallments;

        $id = (int)$id;
        $confirmed = (int)$confirmed;
        $billing = null;

        $sql = $this->db->prepare(
            "
				select
				b.*,
				b.id AS billing_id,
				p.id AS parent_id,
				p.*
				FROM
					{$this->p}ds_billing b
				INNER JOIN
					{$this->p}ds_parents p ON b.parent_id=p.id
				WHERE
					p.id=%d
				AND
					b.payment_confirmed = %d
				AND `b`." .
                    $this->sessionCondition,
            $id,
            $confirmed
        );

        $this->db->query($sql);

        $result = $this->db->last_result;

        if ($result) {
            $billing = $result[0];

            $billing->installments = $installments->getInstallments($billing->billing_id);
            $billing->customPayments = $objBillingCustomPayments->findByBillingId($billing->billing_id);
        }

        return $billing;
    }


    public function validateParentAccount($parent_id)
    {
        $parent_id = (int) $parent_id;

        $res = $this->db->update(
            "{$this->p}ds_billing",
            array(
                    'registration_fee_paid' => 1,
                    'registration_fee_date' => 'now()'
                ),
            array(
                    'parent_id' => $parent_id,
                    'sessions_id' => dance_getUserSessionId()
                )
        );

        if ($res === false) {
            return false;
        }

        return true;
    }

    public function getPendingPayments()
    {
        $sql = "
			SELECT
				b.*,
				p.firstname,
				p.lastname,
				date_format(timestampadd(DAY,5,b.date_added), '%Y-%m-%d %h:%i %p') AS due_date,
				date_format(p.date_added, '%Y-%m-%d %h:%i %p') AS date_added
			FROM
				{$this->p}ds_billing b
			INNER JOIN
				{$this->p}ds_parents p ON b.parent_id = p.id
			WHERE
				b.registration_fee_paid = 0
			AND
				b.payment_confirmed = 1
			AND
				p.is_confirmed = 1
			AND
				p.active = 1
			AND `b`." .
                $this->sessionCondition

            . " ORDER BY
				b.date_added ASC
		";
        $this->db->query($sql);
        return $this->db->last_result;
    }

    public function sendReminder($parent, $billingId)
    {
        $billingId = (int) $billingId;

        $option = new Option();
        $headers = [];
        $contactEmail = $option->getContactEmail();
        $contactTelephone = $option->getContactTelephone();
        $blogName = get_bloginfo('name');
        $toEmail = $parent->email;
        $parentName = $parent->firstname . ' ' . $parent->lastname;

        $headers = array("From: {$blogName} <{$contactEmail}>","Content-type:text/html");

        $email = "Dear {$parentName}<br/>
		This email is to remind you that your registration fee and scheduled payment cheques are due to be paid to {$blogName}.<br/><br/>
		Your dancer or dancers' class places are kept on hold for five days after you register, after which they may be assigned to other dancers.<br/><br/>
		Please call {$blogName} at {$contactTelephone} or email <a href='mailto:{$contactEmail}'>{$contactEmail}</a> if you have any questions.<br/><br/>
		Best regards,<br/><br/>{$blogName}<br/><br/>";


        wp_mail($toEmail, "Reminder: your {$blogName} registration payments are due", $email, $headers);

        $sql = $this->db->prepare("UPDATE
				{$this->p}ds_billing
			SET
				reminder_sent = now()
			WHERE
				id = %d
			AND " .
                $this->sessionCondition
            . " LIMIT 1
		", $billingId);

        $this->query($sql);

        return "Reminder sent";
    }

    public function deleteBillingAgreement($id)
    {
        $sql = $this->db->prepare("DELETE FROM
				{$this->p}ds_billing
			WHERE
				id = %d
			AND " .
                $this->sessionCondition
            . " LIMIT 1
		", $id);

        $this->query($sql);

        return "Billing agreement deleted";
    }

    /**
     * Gets a set of billing records by a provided year and month
     * @param int $year
     * @param int $month
     * @return array|NULL
     */
    public function getPaymentsByMonth($year, $month)
    {
        $year = (int) $year;
        $month = (int) $month;

        $sql = $this->db->prepare(
            "SELECT id,
				registration_fee,
				registration_fee_paid,
				registration_fee_date
			FROM {$this->p}ds_billing
			WHERE
				YEAR(registration_fee_date)=%d
			AND
				MONTH(registration_fee_date)=%d
			AND
				payment_confirmed = 1 AND " . $this->sessionCondition,
            $year,
            $month
        );

        $this->query($sql);

        $result = $this->db->last_result;

        foreach ($result as &$billingRecord) {
            $billingRecord->installments = $this->getInstallments($billingRecord->id);
        }

        return $result;
    }

    /**
     * Gets a set of billing records by a provided year
     * @param int $year
     * @return array|NULL
     */
    public function getPaymentsByYear($year)
    {
        $year = (int) $year;

        $sql = $this->db->prepare(
            "SELECT id,
			registration_fee,
			registration_fee_paid,
			registration_fee_date

			FROM {$this->p}ds_billing

			WHERE
				YEAR(registration_fee_date)=%d
			AND
				payment_confirmed=1 AND " . $this->sessionCondition,
            $year
        );

        $this->query($sql);

        $result = $this->db->last_result;

        foreach ($result as &$billingRecord) {
            $billingRecord->installments = $this->getInstallments($billingRecord->id);
        }

        return $result;
    }

    /**
     * Gets the total amount of received payments for a particular year and month
     * @param int $year
     * @param int $month
     * @return number
     */
    public function getReceivedPaymentsByMonth($year, $month)
    {
        $year = (int) $year;
        $month = (int) $month;

        $receivedTotal = 0;
        $billings = $this->getPaymentsByMonth($year, $month);

        foreach ($billings as $billing) {
            foreach ($billing->installments as $installment) {
                if ($installment->paid) {
                    $receivedTotal += $installment->amount;
                }
            }
        }

        return $receivedTotal;
    }

    /**
     * Gets the total amount of received payments for a particular year
     * @param int $year
     * @return number
     */
    public function getReceivedPaymentsByYear($year)
    {
        $year = (int) $year;

        $receivedTotal = 0;
        $billings = $this->getPaymentsByYear($year);


        foreach ($billings as $billing) {
            foreach ($billing->installments as $installment) {
                if ($installment->paid) {
                    $receivedTotal += $installment->amount;
                }
            }
        }

        return $receivedTotal;
    }

    /**
     * Gets the total amount of funds that are scheduled for payment for a provided year and month
     * @param int $year
     * @param int $month
     * @return number
     */
    public function getScheduledPaymentsByMonth($year, $month)
    {
        $year = (int) $year;
        $month = (int) $month;

        $scheduledTotal = 0;
        $billings = $this->getPaymentsByMonth($year, $month);


        foreach ($billings as $billing) {
            foreach ($billing->installments as $installment) {
                if (!$installment->paid) {
                    $scheduledTotal += $installment->amount;
                }
            }
        }

        return $scheduledTotal;
    }

    /**
     * Gets the total amount of funds that are scheduled for payment for a provided year
     * @param int $year
     * @return number
     */
    public function getScheduledPaymentsByYear($year)
    {
        $year = (int) $year;

        $scheduledTotal = 0;
        $billings = $this->getPaymentsByYear($year);

        foreach ($billings as $billing) {
            foreach ($billing->installments as $installment) {
                if (!$installment->paid) {
                    $scheduledTotal += $installment->amount;
                }
            }
        }

        return $scheduledTotal;
    }

    /**
     *
     * @param array $parentIds
     * @return array|NULL
     */
    public function doScheduledStripePayments($parentIds)
    {
        $option = new Option();

        Stripe::setApiKey($option->getStripeSecretKey());
        $currencyCode = strtolower($option->getStripeCurrency());

        if (empty($currencyCode)) {
            throw new PHPException("Currency not set in plugin");
        }

        $paymentDetails = $this->getNextScheduledPayments((array)$parentIds);

        foreach ($paymentDetails as &$pd) {
            if ($pd->nextPaymentAmount == 0) {
                $this->recordCustomerStripePayment($pd);
                continue;
            }

            $e = false;

            $paymentAmount = $pd->nextPaymentAmount * 100;

            try {
                $pd->charges = Stripe_Charge::create(
                    array(
                    "amount" => $paymentAmount,
                    "currency" => $currencyCode,
                    "customer" => $pd->stripe_id,
                    "description" => $pd->email . " " . get_bloginfo('name') . " Fees: " . $pd->nextPaymentName )
                );
            } catch (Stripe_CardError $e) {
                $this->errors[] = $e;
            } catch (Stripe_InvalidRequestError $e) {
                $this->errors[] = $e;
            } catch (Stripe_AuthenticationError $e) {
                $this->errors[] = $e; // Authentication with Stripe's API failed // (maybe you changed API keys recently)
            } catch (Stripe_ApiConnectionError $e) {
                $this->errors[] = $e; // Network communication with Stripe failed
            } catch (Stripe_Error $e) {
                $this->errors[] = $e; // Display a very generic error to the user, and maybe send // yourself an email
            } catch (Exception $e) {
                $this->errors[] = $e; // Something else happened, completely unrelated to Stripe
            }

            if ($e) {
                $pd->payment_declined = true;
                $this->errors['payment_details'][] = $pd;
                continue;
            } else {
                $pd->payment_declined = false;
                $this->recordCustomerStripePayment($pd);
            }
        }

        return $paymentDetails;
    }

    /**
     *
     * @param \stdClass $paymentDetails
     * @return boolean
     */
    private function recordCustomerStripePayment($paymentDetails)
    {
        if (!$paymentDetails->nextBillingInstallmentId) {
            throw new PHPException("Payment details does not feature the next billing installment id");
        }

        $sql = $this->db->prepare(
            "UPDATE
				{$this->p}ds_billing_installments
			SET
				`paid` = 1,
				`datetime_paid` = NOW()
			WHERE
				id = %d

			LIMIT 1",
            $paymentDetails->nextBillingInstallmentId
        );

        //Returns the number of records that were successfully updated
        $updated = $this->db->query($sql);

        if (!$updated) {
            throw new PHPException("Unable to update billing installment with id '{$paymentDetails->nextBillingInstallmentId}'");
        }

        return true;
    }

    /**
     * Change whether or not a billing installment has been paid
     * @param int $installmentId The installment id
     * @param int $status The status the installment should be changed to. 1 for paid, 0 for unpaid
     */
    public function changeInstallmentPaidStatus($installmentId, $status)
    {
        $installmentId = (int)$installmentId;
        $status = (int)$status;

        $date = 'CURRENT_TIMESTAMP';

        if (!$status) {
            $date = 'NULL';
        }

        $this->query("UPDATE {$this->p}ds_billing_installments SET paid = {$status}, datetime_paid = {$date} WHERE id = {$installmentId}");
    }

    /**
     * Change the paid status forthe registration fee for a billing record
     * @param int $billingId The id of the billing record to change the registration fee paid status for
     * @param int $status The status to change the billing record paid status to
     */
    public function changeRegistrationFeePaidStatus($billingId, $status)
    {
        $billingId = (int)$billingId;
        $status = (int)$status;
        $date = 'CURRENT_TIMESTAMP';

        if (!$status) {
            $date = 'NULL';
        }

        $this->query("UPDATE {$this->p}ds_billing SET registration_fee_paid = {$status}, registration_fee_date = {$date} where id = {$billingId}");
    }

    public function setClassDetails($classes)
    {
        $this->classDetails = $classes;
        return $this->classDetails;
    }

    public function getClassDetails()
    {
        return $this->classDetails;
    }

    /**
    * Backwards compatibility for BillingInstallments::getMaxParentBillingInstallments().
    * Deprecated. Will be removed in version 3
    * @param object $parents
    * @return int $maxbillingInstallments
    * @deprecated
    */
    public function getMaxParentBillingInstallments($parents)
    {
        $installments = new BillingInstallments;
        return $installments->getMaxParentBillingInstallments($parents);
    }
}
