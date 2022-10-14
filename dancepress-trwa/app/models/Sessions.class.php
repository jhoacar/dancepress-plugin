<?php
namespace DancePressTRWA\Models;

class Sessions extends Model
{
    public function __construct($sessionCondition = '')
    {
        parent::__construct($sessionCondition);
    }

    public function getSessions()
    {
        $sql = "SELECT id, `name` FROM {$this->p}ds_sessions";
        $this->db->query($sql);
        $res = $this->db->last_result;
        return $res;
    }

    public function getSessionNameById($id)
    {
        $sql = $this->db->prepare("
			SELECT
				`name`
			FROM
				{$this->p}ds_sessions
			WHERE
				id = %d
			", $id);
        return $this->db->get_var($sql);
    }

    public function addSession($name)
    {
        if (trim($name)=='') {
            return false;
        }
        
        $this->db->query("INSERT INTO {$this->p}ds_sessions (name) VALUES ('$name')");
        return $this->db->insert_id;
    }


    public function copySessionClasses($toSessId, $fromSessId)
    {

        //Only copy parent events, as all child events
        //are meaningless in a new session.
        $sql = $this->db->prepare("
			SELECT
				*
			FROM
				{$this->p}ds_classes
			WHERE
				sessions_id = %d
			AND
				is_parent_event = 1
		", (int)$fromSessId);

        $this->db->query($sql);
        $res = $this->db->last_result;

        foreach ($res as $c) {
            $sql = $this->db->prepare("
			 INSERT INTO
				 {$this->p}ds_classes
			 SET
				 name = %s,
				 class_fee = %s,
				 costume_fee = %s,
				 category_id = %d,
				 classroom = %d,
				 ages = %s,
				 experience = %s,
				 description = %s,
				 is_parent_event = %d,
				 parent_id = %d,
				 is_competitive = %d,
				 is_registerable = %d,
				 sessions_id = %d
		  ", $c->name, $c->class_fee, $c->costume_fee, $c->category_id, $c->classroom, $c->ages, $c->experience, $c->description, $c->is_parent_event, $c->parent_id, $c->is_competitive, $c->is_registerable, $toSessId);
            $res = $this->db->query($sql);
        }

        $this->copyClassCategories($toSessId, $fromSessId);
    }

    public function copyClassCategories($toSessId, $fromSessId)
    {
        $sql = $this->db->prepare("
			SELECT
				*
			FROM
				{$this->p}ds_class_categories
			WHERE
				sessions_id = %d
		", (int)$fromSessId);

        $this->db->query($sql);
        $res = $this->db->last_result;

        foreach ($res as $c) {
            $sql = $this->db->prepare("
			 INSERT INTO
				 {$this->p}ds_class_categories
			 SET
				 category_name = %s,
				 sessions_id = %d
			", $c->category_name, $toSessId);

            $this->db->query($sql);
        }

        return true;
    }

    public function copySessionParentsStudents($toSessId, $fromSessId)
    {
        if (!$toSessId || !$fromSessId) {
            die("Missing session ids.");
        }

        $sql = $this->db->prepare("
		SELECT
			p.id AS parent_id,
			p.firstname,
			p.lastname,
			p.user_id,
			p.user_level,
			p.email,
			p.address_data,
			p.meta,
			p.sessions_id,
			s.id AS student_id,
			s.firstname AS student_firstname,
			s.lastname AS student_lastname,
			s.birthdate,
			s.gender,
			s.address_data AS student_address_data,
			s.measurements,
			s.meta AS student_meta
		FROM
			{$this->p}ds_parents p
		INNER JOIN
			{$this->p}ds_students s ON s.parent_id = p.id
		WHERE
			p.sessions_id = %d
		AND
			s.sessions_id = %d
		AND
			s.active = 1
		AND
			p.is_confirmed = 1
		ORDER BY
			p.id ASC
	   ", (int)$fromSessId, (int)$fromSessId);

        $this->db->query($sql);
        $res = $this->db->last_result;

        $lastId = false;
        $parentId = false;

        foreach ($res as $p) {

            //Insert parent only once
            if ($p->parent_id != $lastId) {
                $sql1 = $this->db->prepare("
				INSERT INTO
					{$this->p}ds_parents
				SET
					firstname = %s,
					lastname = %s,
					address_data = %s,
					user_id = %d,
					user_level = %s,
					email = %s,
					meta = %s,
					sessions_id = %d,
					is_confirmed = 1
				", $p->firstname, $p->lastname, $p->address_data, $p->user_id, $p->user_level, $p->email, $p->meta, (int)$toSessId);

                $res = $this->db->query($sql1);

                $lastId = $p->parent_id;
                $parentId = $this->db->insert_id;

                //Create an empty billing entry,
                $sql2 = $this->db->prepare("
				INSERT INTO
					{$this->p}ds_billing
				SET
					parent_id = %d,
					payment_confirmed = 1,
					sessions_id = %d
				", (int)$parentId, (int)$toSessId);
                $res = $this->db->query($sql2);
            }
            if (!$parentId) {
                die("Parent ID should not be false");
            }

            //Insert all children of a parent
            $sql = $this->db->prepare("
				INSERT INTO
					{$this->p}ds_students
				SET
					firstname = %s,
					lastname = %s,
					parent_id = %d,
					birthdate = %s,
					gender = %s,
					address_data = %s,
					measurements = %s,
					meta = %s,
					is_confirmed = 1,
					active = 0,
					sessions_id = %d
				", $p->student_firstname, $p->student_lastname, $parentId, $p->birthdate, $p->gender, $p->student_address_data, $p->measurements, $p->student_meta, $toSessId);
            $res = $this->db->query($sql);
        }
    }
}
