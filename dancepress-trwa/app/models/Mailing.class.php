<?php
namespace DancePressTRWA\Models;

use \Exception as Exception;

class Mailing extends Model
{
    private $to;
    private $from;
    private $message;
    private $headers;
    private $attachments;
    private $to_dev = "root@localhost";

    public function __construct($sessionCondition = '')
    {
        parent::__construct($sessionCondition);
        $telephone = get_option('ds_contact_telephone');
        $address = get_option('ds_contact_address');
        $email = get_option('ds_contact_email');
        $site_url = get_bloginfo('url');
        $site_url_minus_scheme = preg_replace("/http(s)?:\/\//", "", $site_url);
        $site_tagline = get_bloginfo('description');
        $mailingDescription = get_option('ds_mailing_description');
        $mailingSignatureImage = get_option('ds_mailing_signature_image');
        
        $this->headers[] = "Content-type: text/html";
        $this->headers[] = "From: " . get_bloginfo('name') . ' <' . $email . '>';
        $this->subject = "Message from " . get_bloginfo('name');
        
        
        $this->signature = "
			<img src='{$mailingSignatureImage}' width='200' height='100' alt='" . get_bloginfo('name') . "'/>
			<span style='font-size: smaller'>{$address}/span>
			<span style='font-size: smaller'>{$telephone}</span>
			<span style='font-size: smaller'><a href='mailto:{$email}'>{$email}</a></span>
			<span style='font-size: smaller'><a href='{$site_url}'>{$site_url_minus_scheme}</a></span>";
        
        if ($site_tagline) {
            $this->signature = $this->signature . "
				<span style='font-size: smaller'>$site_tagline</span>";
        }
        
        if ($mailingDescription) {
            $this->signature = $this->signature . "	<strong style='font-size: smaller'>{$mailingDescription}</strong>";
        }
        
        $this->message = '';
    }

    public function getGroups()
    {
        $sql = "
			SELECT
				*
			FROM
				{$this->p}ds_mailing_group
			WHERE " . $this->sessionCondition;
        $this->db->query($sql);
        return $this->db->last_result;
    }

    public function getGroupParents($id)
    {
        $id = (int) $id;
        
        if (!$id) {
            throw new Exception("Group id not provided");
        }
        
        $sql = $this->db->prepare("
			SELECT
				p.id AS parent_id,
				p.firstname,
				p.lastname,
				p.email,
				p.meta,
				mg.name AS group_name,
				mg.id AS group_id
			FROM
				{$this->p}ds_parents p
			INNER JOIN
				{$this->p}ds_parent_group pg ON pg.parent_id = p.id
			INNER JOIN
				{$this->p}ds_mailing_group mg ON pg.group_id = mg.id
			WHERE
				is_confirmed = 1
			AND
				p.active = 1
			AND
				pg.group_id = %d
			AND `p`." . $this->sessionCondition . "
			GROUP BY
				email
		", $id);
        $this->db->query($sql);
        return $this->db->last_result;
    }

    public function deleteGroupMember($parent_id, $group_id)
    {
        $parent_id = (int) $parent_id;
        $group_id = (int) $group_id;
        
        $sql = $this->db->prepare("
			DELETE FROM
				{$this->p}ds_parent_group
			WHERE
				parent_id = %d
			AND
				group_id = %d
			AND " . $this->sessionCondition . "
			LIMIT 1
		", $parent_id, $group_id);

        return $this->db->query($sql);
    }

    public function saveNewGroup($data)
    {
        $sql = $this->db->prepare("
			SELECT
				*
			FROM
				{$this->p}ds_mailing_group
			WHERE
				name = %s
			AND " . $this->sessionCondition, $data[ 'group_name' ]);

        if ($this->db->get_row($sql) != null) {
            return false;
        }

        $sql = $this->db->prepare("
			INSERT INTO
				{$this->p}ds_mailing_group
			SET
				name = %s,
				" . $this->sessionCondition, $data[ 'group_name' ]);
                
        $this->db->query($sql);
        $groupId = $this->db->insert_id;

        foreach ($data[ 'ids' ] as $id) {
            $sql = $this->db->prepare("
				INSERT INTO
					{$this->p}ds_parent_group
				SET
					parent_id = %d,
					group_id = %d, " . $this->sessionCondition, $id, $groupId);
            $this->db->query($sql);
        }
        
        return true;
    }

    public function updateGroup($data)
    {
        $groupId = ( int ) $data[ 'group_id' ];

        if ($data[ 'update_type' ] == 'add') {
            foreach ($data[ 'ids' ] as $id) {
                $sql = $this->db->prepare("
					INSERT IGNORE INTO
						{$this->p}ds_parent_group
					SET
						parent_id = %d,
						group_id = %d, " . $this->sessionCondition, $id, $groupId);
                $this->db->query($sql);
            }
            
            return true;
        } elseif ($data[ 'update_type' ] == 'replace') {
            $sql = $this->db->prepare("
					DELETE FROM
						{$this->p}ds_parent_group
					WHERE
						group_id = %d

				", $groupId);
                        
            $this->db->query($sql);
            
            foreach ($data[ 'ids' ] as $id) {
                $sql = $this->db->prepare("
					INSERT INTO
						{$this->p}ds_parent_group
					SET
						parent_id = %d,
						group_id = %d, " . $this->sessionCondition, $id, $groupId);
                $this->db->query($sql);
            }
            return true;
        }

        return true;
    }

    public function retrieveGroupMembers($data, $includeDeactivated = false)
    {
        $objParents = new Parents($this->sessionCondition);

        $deactivated = '';
        if (! $includeDeactivated) {
            $deactivated = " AND p.active = 1 ";
        }

        if ($data[ 'group_id' ] == 'all') {
            $sql = "
				SELECT
					p.id AS parent_id,
					p.firstname,
					p.lastname,
					p.email,
					p.meta
				FROM
					{$this->p}ds_parents p
				WHERE
					p.is_confirmed = 1
					$deactivated
				AND " . $this->sessionCondition . "
				GROUP BY
					email
			";
            $this->db->query($sql);
            $res = $this->db->last_result;
        } elseif ($data[ 'group_id' ] == 'company') {
            $res = $objParents->getCompanyParents($includeDeactivated);
        } elseif ($data[ 'group_id' ] == 'recreational') {
            $res = $objParents->getRecreationalParents($includeDeactivated);
        } else {
            $sql = $this->db->prepare("
				SELECT
					p.id AS parent_id,
					p.email,
					p.meta
				FROM
					{$this->p}ds_parents p
				INNER JOIN
					{$this->p}ds_parent_group pg ON pg.parent_id = p.id
				WHERE
					is_confirmed = 1
				AND
					pg.group_id = %d
					$deactivated
				AND p." . $this->sessionCondition . " GROUP BY
					email
			", $data[ 'group_id' ]);
            $this->db->query($sql);
            $res = $this->db->last_result;
        }

        return $res;
    }

    public function sendToGroup($data, $includeDeactivated)
    {
        $this->message = $data[ 'message' ];
        $this->subject = $data[ 'subject' ];
        $emails = array();
        
        $res = $this->retrieveGroupMembers($data, $includeDeactivated);

        if (! count($res)) {
            die('Error: No emails found to send mail to. Is group empty?');
        }

        foreach ($res as $v) {
            if ($v->email = is_email($v->email)) {
                $emails[] = $v->email;
            }
            
            $meta = json_decode($v->meta);
            
            if (isset($meta->email_additional) && $meta->email_additional != '') {
                if ($meta->email_additional = is_email($meta->email_additional)) {
                    $emails[] = $meta->email_additional;
                }
            }
        }
        
        return $this->processMailing($emails);
    }

    // Send email including custom recommendations to group
    public function sendRecsToGroup($parents, $recs, $input, $includeDeactivated)
    {
        $this->message = $input[ 'message' ];
        $originalMessage = $this->message;
        $this->subject = $input[ 'subject' ];

        if (! count($parents)) {
            die('Error: No emails found to send mail to. Is group empty?');
        }

        $i = 1;
        $r = 0;
        
        foreach ($parents as $v) {
            $emails = [ ]; // Very important to avoid spamming users with a thousand messages.
            $this->message = $originalMessage;
            if ($v->email = is_email($v->email)) {
                $emails[] = $v->email;
            }
            $meta = json_decode($v->meta);
            if (isset($meta->email_additional) && $meta->email_additional != '') {
                if ($meta->email_additional = is_email($meta->email_additional)) {
                    $emails[] = $meta->email_additional;
                }
            }
            if ($this->concatenateRecommendationMessage($v, $recs)) {
                if (count($emails) > 2) {
                    die("This should never happen ... more than two emails in array!!");
                }
                $this->processMailing($emails);
                $i ++;
                $r = $r + (count($emails));
            }
        }
        
        echo "<br/>$i total messages sent.<br/>";
        
        return true;
    }

    private function concatenateRecommendationMessage($parent, $allRecs)
    {
        $student = array();
        
        foreach ($allRecs as $r) {
            if ($r->parent_id == $parent->parent_id) {
                $student[ $r->student_id ][ 'student_id' ] = $r->student_id;
                $student[ $r->student_id ][ 'firstname' ] = $r->student_firstname;
                $student[ $r->student_id ][ 'lastname' ] = $r->student_lastname;
                $student[ $r->student_id ][ 'birthdate' ] = $r->birthdate;
                $student[ $r->student_id ][ 'classes' ][ $r->class_id ] = new stdClass();
                $student[ $r->student_id ][ 'classes' ][ $r->class_id ]->class_id = $r->class_id;
                $student[ $r->student_id ][ 'classes' ][ $r->class_id ]->class_name = $r->class_name;
                $student[ $r->student_id ][ 'classes' ][ $r->class_id ]->description = $r->description;
                $student[ $r->student_id ][ 'classes' ][ $r->class_id ]->days = $r->days;
                $student[ $r->student_id ][ 'classes' ][ $r->class_id ]->starttime = $r->starttime;
                $student[ $r->student_id ][ 'classes' ][ $r->class_id ]->endtime = $r->endtime;
            }
        }
        
        $parent->students = $student;
        
        if (! count($student)) {
            return false;
        }
        $recString = "
		<h2 style=\"text-align: center;\">Class Recommendations</h2>
		<style type=\"text/css\">
			table {border-collapse: collapse; margin: 0 auto; font-size: 10pt;}
			td {border: 1px solid silver; padding: 0 2px;}
		</style>
		<table align=\"center\">
		";
        $oldStudent = false;
        foreach ($parent->students as $sid => $s) {
            if ($sid != $oldStudent) {
                $oldStudent = $sid;
                $recString .= "
					<tr>
						<th colspan=\"2\"><h3 style=\"margin-top: 10px\">Recommendations for {$s['firstname']} {$s['lastname']}</h3></th>
					</tr>
				";
            }
            
            foreach ($s[ 'classes' ] as $c) {
                $weekdays = $this->getWeekdays();
                $days = json_decode($c->days);
                $dayStr = '';
                foreach ($days as $day) {
                    $dayStr .= $weekdays[ $day ] . ', ';
                }
                $dayStr = rtrim($dayStr, ', ');

                $recString .= "
					<tr style=\"vertical-align: top;\">
						<td><h4 style=\"margin: 0\">$c->class_name</h4></td>
						<td>
							$dayStr
							(" . date("g:i a", strtotime($c->starttime)) . "-" . date("g:i a", strtotime($c->endtime)) . ")
						</td>
					</tr>


				";
            }
        }
        $recString .= "</table>";
        $this->message = str_replace('[RECOMMEND]', $recString, $this->message);
        return true;
    }

    public function deleteGroup($id)
    {
        $id = (int) $id;

        $sql = $this->db->prepare("
			DELETE FROM
				{$this->p}ds_mailing_group
			WHERE
				id = %d
			AND " . $this->sessionCondition . "
			LIMIT 1
		", $id);
                
        return $this->db->query($sql);
    }

    public function sendToIds($data, $includeDeactivated = false)
    {
        $this->message = $data[ 'message' ];
        $this->subject = $data[ 'subject' ];
        $emails = array();
        
        $deactivated = '';
        
        if (! $includeDeactivated) {
            $deactivated = " AND p.active = 1 ";
        }

        $where = '( ';

        foreach ($data[ 'ids' ] as $id) {
            $where .= "id = %d OR ";
        }

        $where = rtrim($where, 'OR ');
        $where .= ' )';

        $sql = $this->db->prepare("
				SELECT
					email,
					meta
				FROM
					{$this->p}ds_parents p
				WHERE
					$where
					$deactivated
				AND
					is_confirmed = 1
				AND " . $this->sessionCondition . " GROUP BY
					email
			", $data[ 'ids' ]);

        $this->db->query($sql);
        $res = $this->db->last_result;

        foreach ($res as $v) {
            if ($v->email = is_email($v->email)) {
                $emails[] = $v->email;
            }
            $meta = json_decode($v->meta);
            if (isset($meta->email_additional) && $meta->email_additional != '') {
                if ($meta->email_additional = is_email($meta->email_additional)) {
                    $emails[] = $meta->email_additional;
                }
            }
        }
        return $this->processMailing($emails);
    }

    // Send mail to an array of emails of a single address.
    private function processMailing($emails)
    {
        if (is_array($emails) || is_object($emails)) {
            $option = new Option();
            $contactEmail = $option->getContactEmail();
            
            
            $this->message = wpautop($this->message . $this->signature);
            $emailList = "<h2>This message was sent to:</h2>" . implode('<br/>', $emails);
            
            foreach ($emails as $to) {
                if (! $this->sendEmail($to, $this->subject, $this->message, $this->headers, $this->attachments)) {
                    throw new \Exception('Error sending mail to ' . $to . '. Please report to developer.');
                }
                
                $this->sendEmail($contactEmail, $this->subject, $this->message . $emailList, $this->headers, $this->attachments);
            }
        }
        return true;
    }

    private function sendEmail($to, $subject, $message, $headers = '', $attachments = '')
    {
        return wp_mail($to, $subject, $message, $headers, $attachments);
    }
}
