<?php
namespace DancePressTRWA\Models;

class Dance extends Model
{
    public function __construct($sessionCondition = '')
    {
        parent::__construct($sessionCondition);
    }

    #	public function getJobProperties(){
#
#		$sql = $this->db->prepare(
#			"
#				SELECT
#					id,
#					job_title,
#					business_address,
#					images,
#					description,
#					date_added,
#					date_expires
#				FROM
#					{$this->p}dance_jobs
#				WHERE
#					user_id = %d
#
#			", $this->userid
#		);
#		$this->db->query($sql);
#
#		$res = $this->db->last_result[0];
#
#		$res->images = json_decode($res->images);
#		$address = json_decode($res->address);
#		foreach ($address as $k => $v){
#			$res->$k = $v;
#		}
#
#		return $res;
#	}
#
#	public function getJobById($id){
#
#		$id = (int)$id;
#
#		$sql = $this->db->prepare(
#			"
#				SELECT
#					id,
#					job_title,
#					business_name,
#					business_address,
#					location,
#					images,
#					employment_type,
#					industry,
#					description,
#					requirements,
#					date_added,
#					date_expires
#				FROM
#					{$this->p}dance_jobs
#				WHERE
#					id = %d
#
#			", $id
#		);
#		$this->db->query($sql);

#		$res = $this->db->last_result[0];
#
#		$res->images = json_decode($res->images);
#
#		return $res;
#	}
#
##	public function updateJob($input){
##
##		$input['description'] = sanitize_text_field($input['description']);
##		$input['email'] = sanitize_email($input['email']);
##		$url = esc_url($input['url'], array('http', 'https'));
##		if (!$url){
##			$url = esc_url('http://' . $input['url'], array('http', 'https'));
##		}
##		$input['url'] = $url;
##
##		$addressKeys = array('address_street', 'city', 'province', 'postcode', 'country', 'phone', 'fax', 'email', 'url', 'email_visible');
##
##		foreach ($input as $k => $v){
##			if (in_array($k, $addressKeys)){
##				$address->$k = $v;
##			}
##		}
##		$address = json_encode($address);
##
##		$rowExists = $this->db->get_var($this->db->prepare("SELECT id FROM {$this->p}dance_jobs WHERE user_id = %d", $this->userid));
##
##		if ($rowExists === null){
##
##		$sql = $this->db->prepare(
##			"
##				INSERT INTO
##					{$this->p}dance_jobs
##				SET
##					user_id = %d,
##					stall_number = %s,
##					job_title = %s,
##					address = %s,
##					description = %s
##			", $this->userid, $input['stall_number'], $input['job_title'], $address, $input['description']);
##		}else{
##			$sql = $this->db->prepare(
##			"
##				UPDATE
##					{$this->p}dance_jobs
##				SET
##					stall_number = %s,
##					job_title = %s,
##					address = %s,
##					description = %s
##				WHERE
##					user_id = %d
##			", $input['stall_number'], $input['job_title'], $address, $input['description'], $this->userid);
##		}
##		$this->db->query($sql);
##		return $this->db->last_result;
##
##	}
#
#	public function adminUpdateJob($input){
#
#		$sql = $this->db->prepare(
#		"
#			UPDATE
#				{$this->p}dance_jobs
#			SET
#				job_title = %s,
#				business_address = %s,
#				location = %s,
#				employment_type = %s,
#				industry = %s,
#				description = %s,
#				requirements = %s,
#				date_expires = %s
#			WHERE
#				id = %d
#		", $input['job_title'], $input['business_address'], $input['location'], $input['employment_type'], $input['industry'], $input['description'], $input['requirements'], $input['date_expires'], $input['id']);

#		$this->db->query($sql);
#		return $this->db->last_result;
#
#	}
#
#	public function addNew($input){
#
#
#		$sql = $this->db->prepare(
#		"
#			INSERT INTO
#				{$this->p}dance_jobs
#			SET
#				job_title = %s,
#				business_address = %s,
#				location = %s,
#				employment_type = %s,
#				industry = %s,
#				description = %s,
#				requirements = %s,
#				date_expires = %s
#		", $input['job_title'], $input['business_address'], $input['location'], $input['employment_type'], $input['industry'], $input['description'], $input['requirements'], $input['date_expires']);
#
#		$this->db->query($sql);
#		return $this->db->last_result;
#
#	}
#
#	public function searchJobs($input, $offset, $limit){
#
#		$inputWild = '%' . $input . '%';
#
#		$sql = $this->db->prepare(
#			"
#				SELECT
#					*
#				FROM
#					{$this->p}dance_jobs
#				WHERE
#					job_title LIKE %s
#				OR
#					business_name LIKE %s
#				OR
#					business_address LIKE %s
#				OR
#					location LIKE %s
#				OR
#					employment_type LIKE %s
#				OR
#					industry LIKE %s
#				OR
#					MATCH (description) AGAINST (%s)
#				OR
#					MATCH (requirements) AGAINST (%s)
#				ORDER BY
#					job_title ASC
#				LIMIT %d, %d
#			", $inputWild, $inputWild, $inputWild, $inputWild, $inputWild, $inputWild,$input, $input, $offset, $limit);

#		$this->db->query($sql);
#
#		$res = $this->db->last_result;
#
#		return $res;
#
#
#	}
#
#	public function getJobs($offset, $limit){
#
#		$limit = $limit + 1;
#
#		$sql = $this->db->prepare(
#			"
#				SELECT
#					*
#				FROM
#					{$this->p}dance_jobs
#				WHERE
#					job_title != ''
#				ORDER BY job_title ASC
#				LIMIT %d OFFSET %d
#			",$limit, $offset);
#		$this->db->query($sql);
#		$res = $this->db->last_result;

#		return $res;
#
#	}
#
#	public function getJobsByAlpha($input, $offset, $limit){
#
#		$inputWild = $input . '%';
#
#		$sql = $this->db->prepare(
#			"
#				SELECT
#					*
#				FROM
#					{$this->p}dance_jobs
#				WHERE
#					job_title LIKE %s
#				ORDER BY job_title ASC
#				LIMIT %d, %d
#			", $inputWild, $offset, $limit);

#		$this->db->query($sql);
#
#		$res = $this->db->last_result;
#		foreach ($res as $key => $value){
#			$res[$key]->images = json_decode($res[$key]->images);
#			$address = json_decode($res[$key]->address);
#			if (is_object($address)){
#				foreach ($address as $k => $v){
#					$res[$key]->$k = $v;
#				}
#			}else{
#				$res[$key]->city = $address;
#			}
#			$res[$key]->email = $this->obfuscateEmail($res[$key]->email);
#		}
#		return $res;
#
#
#	}
#
#	public function validateSubmission($input){
#
#		$errors = false;
#		if (!$input['job_title']){
#			$errors[] = "Job name is required";
#		}
#		if (!$input['city']){
#			$errors[] = "City is required";
#		}
#		if (!$input['province']){
#			$errors[] = "Province or State is required";
#		}
#		if (!$input['postcode']){
#			$errors[] = "Postal code or zip code is required";
#		}
#		if (!$input['email']){
#			$errors[] = "Email address is required";
#		}
#		if (!$input['description']){
#			$errors[] = "A description of your business is required";
#		}
#		if (strlen($input['description']) > 1000){
#			$errors[] = "Your description cannot be longer than 1000 characters";
#		}
#		return $errors;
#	}

#	public function deleteJob($id){
#
#		$id = (int)$id;
#
#		$sql = $this->db->prepare(
#			"
#				DELETE FROM
#					{$this->p}dance_jobs
#				WHERE
#					id = %d
#				LIMIT 1
#			", $id);

#		$this->db->query($sql);
#		return "Job deleted.";
#
#	}
}
