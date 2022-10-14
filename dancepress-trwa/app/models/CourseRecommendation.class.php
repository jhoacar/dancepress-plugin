<?php
namespace DancePressTRWA\Models;

class CourseRecommendation extends Model
{
    public function __construct($sessionCondition = '')
    {
        parent::__construct($sessionCondition);
    }

    public function add($studentId, $courseIds)
    {
        $studentId = (int) $studentId;
        
        foreach ($courseIds as $courseId) {
            $sql = $this->db->prepare(
                "
					INSERT INTO
						{$this->p}ds_course_recommendations
					SET
						student_id = %d,
						course_id = %d, " .

                        $this->sessionCondition,
                $studentId,
                $courseId
            );
                
            $this->db->query($sql);
        }
    }
    
    public function getRecommendationByStudentClass($studentId, $classId)
    {
        $sql = $this->db->prepare("SELECT * FROM  {$this->p}ds_course_recommendations WHERE student_id=%d and course_id=%d and ".$this->sessionCondition." LIMIT 1", $studentId, $classId);
    
        $this->db->query($sql);
        $res = $this->db->last_result;
        
        if (count($res)) {
            return $res[0];
        }
        
        return false;
    }
    
    public function getRecommendationsByIds($parents)
    {
        $sql =
            "SELECT
				s.id AS student_id,
				p.id AS parent_id,
				c.id AS class_id,
				s.firstname AS student_firstname,
				s.lastname AS student_lastname,
				s.birthdate,
				p.firstname AS parent_firstname,
				p.lastname AS parent_lastname,
				p.email,
				c.name AS class_name,
				c.description,
				c.days,
				c.starttime,
				c.endtime
			FROM
				{$this->p}ds_parents p
			INNER JOIN
				{$this->p}ds_students s on s.parent_id = p.id
			INNER JOIN
				{$this->p}ds_course_recommendations cr ON s.id = cr.student_id
			INNER JOIN
				{$this->p}ds_classes c ON c.id = cr.course_id
			WHERE			
			";
        $where = '';
        foreach ($parents as $p) {
            $where .= $this->db->prepare("p.id = %d OR ", $p->parent_id);
        }
        $where = rtrim($where, ' OR ');
        $sql .= $where . ' AND ';
        $sql .= 'p.' . $this->sessionCondition;
        $this->db->query($sql);
        return $this->db->last_result;
    }

    public function deleteRecommendation($id)
    {
        $id = (int) $id;
        
        $sql = $this->db->prepare("DELETE FROM {$this->p}ds_course_recommendations WHERE id=%d", $id);
        $this->db->query($sql);
        $this->db->last_result;
    }
}
