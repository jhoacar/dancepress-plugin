<?php
namespace DancePressTRWA\Models;

class Registration extends Model
{
    public $studentIds;
    public $parentIds;

    public function __construct($sessionCondition = '')
    {
        parent::__construct($sessionCondition);
    }

    public function registerUser($data, $parentId = false)
    {
        //clean up data:
        $tmpStage1 = json_decode($data['stage1']);
        $tmpStage2 = json_decode($data['stage2']);

        if (!isset($tmpStage1->parent)) {
            die("Error 17: Something unexpected happened. Please <a href='/registration/'>restart the registration process</a> and provide any missing address data. We're sorry for the inconvenience.");
        }

        $tmpStage1->parent->address1 = ucwords($tmpStage1->parent->address1);
        $tmpStage1->parent->address2 = ucwords($tmpStage1->parent->address2);
        $tmpStage1->parent->city = ucwords($tmpStage1->parent->city);
        $tmpStage1->parent->firstname = ucfirst($tmpStage1->parent->firstname);
        $tmpStage1->parent->lastname = ucfirst($tmpStage1->parent->lastname);
        $tmpStage1->parent->postal_code = strtoupper($tmpStage1->parent->postal_code);
        $tmpStage1->parent->emergency_contact = ucwords($tmpStage1->parent->emergency_contact);

        $required = array('firstname', 'lastname','address1');

        foreach ($required as $req) {
            if ($tmpStage1->parent->{$req} == '') {
                die("Error 29: Something unexpected happened. Please <a href='/registration/'>restart the registration process</a> and provide any missing address data. We're sorry for the inconvenience.");
            }
        }

        if (isset($tmpStage2->child)) {
            foreach ($tmpStage2->child as &$student) {
                $student->address1 = @ucwords($student->address1);
                $student->address2 = @ucwords($student->address2);
                $student->city = @ucwords($student->city);
                $student->firstname = ucfirst($student->firstname);
                $student->lastname = ucfirst($student->lastname);
                $student->postal_code = @strtoupper($student->postal_code);
            }
        }
        if (isset($tmpStage2->returning_id)) {
            foreach ($tmpStage2->returning_id as &$student2) {
                $student2->address1 = @ucwords($student2->address1);
                $student2->address2 = @ucwords($student2->address2);
                $student2->city = @ucwords($student2->city);
                $student2->firstname = ucfirst($student2->firstname);
                $student2->lastname = ucfirst($student2->lastname);
                $student2->postal_code = @strtoupper($student2->postal_code);
            }
        }

        $data['stage1'] = json_encode($tmpStage1);
        $data['stage2'] = json_encode($tmpStage2);

        if (!is_user_logged_in()) {
            $parentIds = $this->registerParents($data['stage1'], $data['stage2']);
            if (!$students = $this->registerStudents($data['stage2'], $parentIds)) {
                return false;
            }
        } else {
            $parentIds = $this->updateParents($parentId, $data['stage1'], $data['stage2']);
            if (!$students = $this->updateStudents($parentId, $data['stage2'], $parentIds)) {
                return false;
            }
        }

        $this->registerClasses($data['stage3'], $students);

        return array('parents' => $parentIds, 'students' => $students);
    }

    private function registerParents($parents, $students)
    {
        $parents = json_decode($parents);
        $students = json_decode($students);

        foreach ($parents as $k => $v) {
            if ($k != "parent") {
                continue;
            }

            $address = json_encode(array('address1' => $v->address1, 'address2' => $v->address2, 'city' => $v->city, 'postal_code' => $v->postal_code, 'phone_primary' => $v->phone_primary, 'phone_secondary' => $v->phone_secondary));

            //Quivck hack in case people getting through somehow.
            if ($v->email == '' || $v->firstname == '' || $v->lastname == '') {
                die("Error 91: Something unexpected happened. Please <a href='/registration/'>restart the registration process</a> and provide any missing address data. We're sorry for the inconvenience.");
            }

            $sql = $this->db->prepare(
                "
					INSERT INTO
						{$this->p}ds_parents
					SET
						firstname = %s,
						lastname = %s,
						email = %s,
						address_data = %s,
						meta = %s, " .
                        $this->sessionCondition,
                $v->firstname,
                $v->lastname,
                $v->email,
                $address,
                json_encode($v)
            );
            $this->db->query($sql);
            $this->parentIds[$k] = $this->db->insert_id;
        }
        return $this->parentIds;
    }

    private function updateParents($parentId, $parents, $students)
    {
        $parentId = (int) $parentId;

        $parents = json_decode($parents);
        $students = json_decode($students);

        foreach ($parents as $k => $v) {
            if ($k != "parent") {
                continue;
            }

            $address = json_encode(array(
                 'address1' => $v->address1,
                 'address2' => $v->address2,
                 'city' => $v->city,
                 'postal_code' => $v->postal_code,
                 'phone_primary' => $v->phone_primary,
                 'phone_secondary' => $v->phone_secondary
            ));

            //Quick hack in case people getting through somehow.
            if ($v->email == '' || $v->firstname == '' || $v->lastname == '') {
                die("Error 131: Something unexpected happened. Please <a href='/registration/'>restart the registration process</a> and provide any missing address data. We're sorry for the inconvenience.");
            }

            $sql = $this->db->prepare(
                "
					UPDATE
						{$this->p}ds_parents
					SET
						firstname = %s,
						lastname = %s,
						email = %s,
						address_data = %s,
						meta = %s, " .
                        $this->sessionCondition . '
					WHERE
						id = %d
				',
                $v->firstname,
                $v->lastname,
                $v->email,
                $address,
                json_encode($v),
                $parentId
            );

            $this->db->query($sql);
            $this->parentIds[$k] = $parentId;
        }

        return $this->parentIds;
    }

    private function registerStudents($data, $parentIds)
    {
        $students = json_decode($data);

        $address = false;

        if (!isset($students->child)) {
            return false;
        }

        foreach ($students->child as $k => $v) {
            if (!isset($v->same_address) || !$v->same_address) {
                $address = json_encode(array(
                    'address1' => $v->address1,
                    'address2' => $v->address2,
                    'city' => $v->city,
                    'postal_code' => $v->postal_code,
                    'phone_primary' => $v->phone_primary,
                    'phone_secondary' => $v->phone_secondary
                ));
            }

            $sql = $this->db->prepare(
                "INSERT INTO
						{$this->p}ds_students
					SET
						firstname = %s,
						lastname = %s,
						parent_id = %d,
						parent2_id = %d,
						birthdate = %s,
						gender = %s,
						address_data = %s,
						meta = %s,
						date_added = NOW(), " .
                        $this->sessionCondition,
                $v->firstname,
                $v->lastname,
                (int)$parentIds['parent'],
                0,
                $v->dateofbirth->year . '-' . $v->dateofbirth->month . '-' . $v->dateofbirth->day,
                $v->gender,
                $address,
                json_encode($v)
            );

            $this->db->query($sql);
            $students->child->{$k}->student_id = $this->db->insert_id;
            $this->studentIds[] = $this->db->insert_id;
        }

        return $students->child;
    }

    private function updateStudents($parentId, $data, $parentIds)
    {
        $parentId = (int) $parentId;

        $students = json_decode($data);

        $address = false;

        //Process new students first, so data doesn't get merged too early.

        if (isset($students->child)) {
            foreach ($students->child as $k => $v) {
                if (!isset($v->same_address) || !$v->same_address) {
                    $address = json_encode(array(
                           'address1' => $v->address1,
                           'address2' => $v->address2,
                           'city' => $v->city,
                           'postal_code' => $v->postal_code,
                           'phone_primary' => $v->phone_primary,
                           'phone_secondary' => $v->phone_secondary
                    ));
                } else {
                    $address = '';
                }

                $sql = $this->db->prepare(
                    "
						INSERT INTO
							{$this->p}ds_students
						SET
							firstname = %s,
							lastname = %s,
							parent_id = %d,
							parent2_id = %d,
							birthdate = %s,
							gender = %s,
							address_data = %s,
							meta = %s,
							date_added = NOW(), " .
                            $this->sessionCondition,
                    $v->firstname,
                    $v->lastname,
                    (int)$parentIds['parent'],
                    0,
                    $v->dateofbirth->year . '-' . $v->dateofbirth->month . '-' . $v->dateofbirth->day,
                    $v->gender,
                    $address,
                    json_encode($v)
                );

                $this->db->query($sql);
                $students->child->{$k}->student_id = $this->db->insert_id;
                $students->child->{$k}->id = $students->child->{$k}->student_id; //stupid stupid stupid
                $this->studentIds[] = $this->db->insert_id;
            }
        }

        //Now process existing students, and merge the data.
        if (isset($students->returning_id)) {
            $i = count(get_object_vars($students->child)); //don't use count on StdClass objects. Convert to array.

            foreach ($students->returning_id as $sid => $v) {
                if (!isset($v->same_address) || !$v->same_address) {
                    $address = json_encode(array(
                           'address1' => $v->address1,
                           'address2' => $v->address2,
                           'city' => $v->city,
                           'postal_code' => $v->postal_code,
                           'phone_primary' => $v->phone_primary,
                           'phone_secondary' => $v->phone_secondary
                    ));
                } else {
                    $address = '';
                }

                $sql = $this->db->prepare(
                    "
						UPDATE
							{$this->p}ds_students
						SET
							firstname = %s,
							lastname = %s,
							parent_id = %d,
							parent2_id = %d,
							birthdate = %s,
							gender = %s,
							address_data = %s,
							meta = %s,
							date_added = NOW(), " .
                            $this->sessionCondition . "
						WHERE
							id = %d
					",
                    $v->firstname,
                    $v->lastname,
                    $parentId,
                    0,
                    $v->dateofbirth->year . '-' . $v->dateofbirth->month . '-' . $v->dateofbirth->day,
                    $v->gender,
                    $address,
                    json_encode($v),
                    $v->id
                );
                $this->db->query($sql);

                //merge returning students into same object as new students
                if (!isset($students->child)) {
                    $students->child = new stdClass();
                }

                $i++;
                $students->child->{$i} = $v;

                $this->studentIds[] = $sid;
            }
        }

        return $students->child;
    }

    private function registerClasses($data, $students)
    {
        $classes = json_decode($data);

        foreach ($students as $sk => $sv) {
            $studentId = (isset($sv->id) && $sv->id) ? $sv->id : $sv->student_id;

            //Remove any prior class registrations which might exist for this session.
            $sql = $this->db->prepare("
				DELETE FROM
					{$this->p}ds_class_students
				WHERE
					student_id = %d
				AND
				 " . $this->sessionCondition, $studentId);
            $this->db->query($sql);
        }

        if (isset($classes->class)) {
            foreach ($classes->class as $childNum => $class) {
                $childNum = ltrim($childNum, '_'); //Not sure how this got in, but ... whatever.

                foreach ($class as $classId => $weekDay) {
                    if (!$weekDay->day) {
                        continue;
                    }

                    foreach ($students as $sk => $sv) {
                        if ($childNum == $sk || (isset($sv->id) && $sv->id == $childNum)) {
                            $studentId = (isset($sv->id) && $sv->id) == $childNum ? $sv->id : $sv->student_id;

                            $sql = $this->db->prepare(
                                "
								INSERT IGNORE INTO
									{$this->p}ds_class_students
								SET
									class_id = %d,
									student_id = %d,
									week_day = %d, " .
                                    $this->sessionCondition,
                                $classId,
                                $studentId,
                                $weekDay->day
                            );

                            $this->db->query($sql);
                        }
                    }
                }
            }
        }

        if (isset($classes->recommended)) {
            foreach ($classes->recommended as $sid => $classIds) {
                foreach ($classIds as $cid) {
                    $sql = $this->db->prepare(
                        "
						INSERT IGNORE INTO
							{$this->p}ds_class_students
						SET
							class_id = %d,
							student_id = %d
							, " .
                            $this->sessionCondition,
                        $cid,
                        $sid
                    );

                    $this->db->query($sql);
                }
            }
        }

        return true;
    }

    //Mark Parents and Students as being confirmed (ie checkout completed)
    public function validateParentsAndChildren($transaction_id, $students, $parentId)
    {
        $parentId = (int)$parentId;

        //Activate parents
        $sql = $this->db->prepare("UPDATE
				{$this->p}ds_parents p
			INNER JOIN
				{$this->p}ds_billing b ON b.parent_id = p.id
			INNER JOIN
				{$this->p}ds_students s ON s.parent_id = p.id
			SET
				p.is_confirmed = 1,
				p.active = 1
			WHERE
				b.id = %d
			AND p." . $this->sessionCondition, $transaction_id);

        $this->db->query($sql);

        $studentIds = [];
        foreach ($students as $s) {
            if (isset($s->id)) {
                $studentIds[] = $s->id;
            } elseif (isset($s->student_id)) {
                $studentIds[] = $s->student_id;
            }
        }

        //Activate students
        foreach ($studentIds as $sid) {
            if (!$sid) {
                die("Error 391: No student ids found to validate.");
            }
            $sql = $this->db->prepare("

			UPDATE
				{$this->p}ds_students

			SET
				is_confirmed = 1,
				active = 1
			WHERE
				id = %d
			AND " . $this->sessionCondition, $sid);

            $this->db->query($sql);
        }

        //Delete extraneous students from previous sessions (stops needless copying of data which is stored elsewhere).
        $sql = $this->db->prepare("DELETE FROM
				{$this->p}ds_students
			WHERE
				parent_id = %d
			AND
				is_confirmed = 0
			AND
				active = 0
			AND
				can_register_online = 1
		", $parentId);

        $this->db->query($sql);
    }

    //Accepts array or object.
    //If $sendMessages is false, absolutely no emails sent. User will not be notified of new account or password.
    public function makeParentUser($input, $sendMessages = true)
    {
        if (is_array($input)) {
            $input = json_decode(json_encode($input), false);
        }

        //Hacktastic!
        if (!isset($input->parent)) {
            $input->parent = $input;
        }


        $password = wp_generate_password();
        //$passwordHash = wp_hash_password($password);
        // 		$user_login = $input->parent->email;
        // 		$user_email = $input->parent->email;
        $user_id = wp_insert_user(array(
            'user_login' => $input->parent->email,
            'user_email' => $input->parent->email,
            'first_name' => $input->parent->firstname,
            'last_name' => $input->parent->lastname,
            'user_pass' => $password
        ));

        $option = new Option();
        $contactEmail = $option->getContactEmail();
        $contactTelephone = $option->getContactTelephone();
        $blogName = get_bloginfo('name');
        $toEmail = $input->parent->email;
        $parentName = $input->parent->firstname . ' ' . $input->parent->lastname;
        $siteUrl = get_site_url();

        if ($errors = is_wp_error($user_id)) {
            //Probably already in the system.

            $userObj = get_user_by('email', $input->parent->email);

            $message = "<p>Hi {$parentName}</p>
				<p>Thank you for choosing {$blogName}. We look forward to a great year of dance. Your account details are as follows:</p>
				<p></p>
				<p style='font-weight: bold'>Email/User ID: {$toEmail}</p>
				<p style='font-weight: bold'>Password: Your password was set when you first registered.</p>
				<p>If you need to recover your password, you can do so here: <a href=''{$siteUrl}/wp-login.php?action=lostpassword'>Recover password</a>.</p>
				<p></p>
				<p>When you visit the {$blogName} DB Login page at <a href='{$siteUrl}/parent-portal/'>{$siteUrl}/parent-portal/</a> sign in using your user ID and password.</p>
				<p></p>
				<p>
					If you have any questions regarding your account, please contact the office at <a href='mailto:{$contactEmail}'>{$contactEmail}</a> or {$contactTelephone}.
				</p>
				<p></p>
				<p>Thank you.";

            if ($sendMessages) {
                wp_mail($toEmail, "{$blogName} DB Login Page Access", $message, array("From: {$contactEmail}", "Content-type: text/html"));
            }

            return $message;
        } else {
            if ($sendMessages) {
                wp_new_user_notification($user_id, null, 'both');
            }

            //Add the link between WP user and DB parent_id
            $sql =  $this->db->prepare("
				UPDATE
					{$this->p}ds_parents
				SET
					user_id = %d
				WHERE
					id = %d
				", $user_id, $input->parent->id);

            $this->db->query($sql);


            $message = "
				<p>Hi {$parentName}</p>
				<p>Thank you for choosing {$blogName}. We look forward to a great year of dance. Your account details are as follows:</p>
				<p></p>
				<p style='font-weight: bold'>Email/User ID: {$toEmail}</p>
				<p style='font-weight: bold'>Password: {$password}</p>
				<p>You can change your password after you have logged in by clicking 'Edit My Profile'.</p>
				<p></p>
				<p>When you visit the {$blogName} DB Login page at <a href='{$siteUrl}/parent-portal/'>{$siteUrl}/parent-portal/</a> sign in using your user ID and password.</p>
				<p></p>
				<p>
					If you have any questions regarding your account, please contact the office at <a href='mailto:{$contactEmail}'>{$contactEmail}</a> or {$contactTelephone}.
				</p>
				<p></p>
				<p>Thank you.";

            if ($sendMessages) {
                wp_mail($toEmail, "{$blogName} DB Login Page Access", $message, array("From: {$contactEmail}", "Content-type: text/html"));
            }
        }

        return $message;
    }

    public function getStudentsByTransactionId($id, $excludeNonRegisterable = '')
    {
        $id = (int)$id;

        if ($excludeNonRegisterable) {
            $exclude = " AND s.can_register_online = 1";
        }

        $sql = $sql = $this->db->prepare("
			SELECT
				s.*
			FROM
				{$this->p}ds_students s
			INNER JOIN
				{$this->p}ds_parents p ON p.id = s.parent_id
			INNER JOIN
				{$this->p}ds_billing b ON p.id = b.parent_id
			WHERE
				b.id = %d
			$exclude
			AND s." . $this->sessionCondition, $id);

        $this->db->query($sql);

        return $this->db->last_result;
    }

    public function checkDuplicateName($value)
    {
        $sql = $this->db->prepare("SELECT id FROM {$this->p}ds_parents WHERE CONCAT(`firstname`, ' ', `lastname`)=%s AND " . $this->sessionCondition . " LIMIT 1", $value);
        $num = $this->db->query($sql);

        return !!$num;
    }

    //Find duplicates. By default, do not find duplicates of incomplete registrations.
    public function checkDuplicateEmail($value, $isActive = 1)
    {
        $sql = $this->db->prepare("SELECT id FROM {$this->p}ds_parents WHERE email=%s AND active = %d AND " . $this->sessionCondition . " LIMIT 1", $value, $isActive);
        $num = $this->db->query($sql);

        return !!$num;
    }

    public function checkDuplicateAddress($value)
    {
        return false;
    }

    public function checkDuplicatePhone($value)
    {
        //parse json.
        return false; //return true if duplicate exists
    }

    public function getRegistrationsByDateRange($rangeStart = false, $rangeEnd = false)
    {//unixtime
        //get list of registrations. currently get ds_students date_added. Not entirely accurate, because once student is registered, they can enroll without being tracked?

        $conditions = array();

        if ($rangeStart) {
            $conditions[] = 'date_added > FROM_UNIXTIME('.$rangeStart.')';
        }

        if ($rangeEnd) {
            $conditions[] = 'date_added < FROM_UNIXTIME('.$rangeEnd.')';
        }

        $where = '';

        if (count($conditions)) {
            $where = ' WHERE ' . implode(' AND ', $conditions);
        }

        $where .= " AND is_confirmed = 1 AND active = 1 AND s." . $this->sessionCondition;
        $groupBy = " GROUP BY s.id";

        //get parents registered for date range
        //Use of inner join excludes 'active' students who are not assigned to any classes.
        $sql = "SELECT s.id FROM {$this->p}ds_students s
			INNER JOIN {$this->p}ds_class_students cs ON s.id = cs.student_id
			" . $where . $groupBy;

        $numStudents = $this->db->query($sql);

        return $numStudents;
    }

    public function getCompetitiveRegistrationsByDateRange($rangeStart = false, $rangeEnd = false)
    {//unixtime
        //get list of registrations. currently get ds_students date_added. Not entirely accurate, because once student is registered, they can enroll without being tracked?

        $conditions = array();
        if ($rangeStart) {
            $conditions[] = 'date_added > FROM_UNIXTIME('.$rangeStart.')';
        }
        if ($rangeEnd) {
            $conditions[] = 'date_added < FROM_UNIXTIME('.$rangeEnd.')';
        }
        $where = '';
        if (count($conditions)) {
            $where = ' WHERE ' . implode(' AND ', $conditions);
        }
        $where .= " AND is_confirmed = 1 AND active = 1 AND c.is_competitive = 1 AND s." . $this->sessionCondition;

        //get parents registered for date range
        $sql = "SELECT DISTINCT
					s.id
				FROM
					{$this->p}ds_students s
				INNER JOIN
					{$this->p}ds_class_students cs ON s.id = cs.student_id
				INNER JOIN
					{$this->p}ds_classes c ON c.id = cs.class_id
					" . $where;

        $numStudents = $this->db->query($sql);
        //echo $sql;
        return $numStudents;
    }

    public function getRecRegistrationsByDateRange($rangeStart = false, $rangeEnd = false)
    {//unixtime
        //get list of registrations. currently get ds_students date_added. Not entirely accurate, because once student is registered, they can enroll without being tracked?

        $conditions = array();

        if ($rangeStart) {
            $conditions[] = 'date_added > FROM_UNIXTIME('.$rangeStart.')';
        }

        if ($rangeEnd) {
            $conditions[] = 'date_added < FROM_UNIXTIME('.$rangeEnd.')';
        }

        $where = '';

        if (count($conditions)) {
            $where = ' WHERE ' . implode(' AND ', $conditions);
        }

        $where .= " AND is_confirmed = 1 AND active = 1 AND c.is_competitive = 0 AND s." . $this->sessionCondition;

        //get parents registered for date range
        $sql = "SELECT DISTINCT
					s.id
				FROM
					{$this->p}ds_students s
				INNER JOIN
					{$this->p}ds_class_students cs ON s.id = cs.student_id
				INNER JOIN
					{$this->p}ds_classes c ON c.id = cs.class_id
					" . $where;

        $numStudents = $this->db->query($sql);

        return $numStudents;
    }

    public function getPaymentTypesByDateRange($rangeStart = false, $rangeEnd = false)
    {
        $conditions = array();
        if ($rangeStart) {
            $conditions[] = 'date_added > FROM_UNIXTIME('.$rangeStart.')';
        }
        if ($rangeEnd) {
            $conditions[] = 'date_added < FROM_UNIXTIME('.$rangeEnd.')';
        }
        $where = '';
        if (count($conditions)) {
            $where = ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql = "
			SELECT
				SUM(payment_method = 'cheques') as cheques_count,
				SUM(payment_method = 'advance') as advance_count,
				SUM(payment_method = 'online') as online_count
			FROM
				{$this->p}ds_billing
			$where
			AND
				payment_confirmed = 1
			AND " .
                $this->sessionCondition
        ;
        $this->db->query($sql);
        return $this->db->last_result;
    }
}
