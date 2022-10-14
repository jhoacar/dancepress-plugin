<?php
namespace DancePressTRWA\Models;

class ListManager extends Model
{
    public function __construct($sessionCondition = '')
    {
        parent::__construct($sessionCondition);
    }

    public function getListByCriteria($input)
    {
        $fields = '';
        $where = '';
        if (isset($input['ds_parents'])) {
            foreach ($input['ds_parents'] as $field_name => $value) {
                $fields .= "p.`" . esc_sql($field_name) . "`,\n";
            }
        }
        if (isset($input['ds_students'])) {
            foreach ($input['ds_students'] as $field_name => $value) {
                $fields .= "s.`" . esc_sql($field_name) . "` AS student_".esc_sql($field_name).",\n";
            }
        }
        foreach ($input['ids'] as $id) {
            $where .= 'cs.class_id = ' . (int) $id . " OR ";
        }
        
        $fields = rtrim($fields, ",\n");
        $where = rtrim($where, "OR ");
            
        $sql = "
			SELECT
				p.id AS parent_id,
				s.id AS student_id,
				cs.class_id,
				c.name AS class_name,
				c.classroom,
				c.ages as class_ages,
				c.description as class_description,
				c.is_competitive as class_is_competitive,
				c.days as class_days,
				c.starttime as class_starttime,
				c.endtime as class_endtime,
				$fields
			FROM
				{$this->p}ds_parents p
			INNER JOIN
				{$this->p}ds_students s ON s.parent_id = p.id
			INNER JOIN
				{$this->p}ds_class_students cs ON s.id = cs.student_id
			INNER JOIN
				{$this->p}ds_classes c ON cs.class_id = c.id
			WHERE
				( $where )
			AND
				c.is_parent_event = 1
			AND
				s.active = 1
			AND
				p.is_confirmed = 1
			AND `p`." .
                $this->sessionCondition
            . "
			ORDER BY
				cs.class_id, c.name, p.lastname, s.lastname
			";

        $this->db->query($sql);
        $res = $this->db->last_result;
        
        foreach ($res as &$row) {
            $row->meta = json_decode(@$row->meta);
            $row->address_data = json_decode(@$row->address_data);
            
            if (isset($row->meta)) {
                foreach ($row->meta as $fieldname => $metavalue) {
                    if (array_key_exists($fieldname, @$input['ds_parents']['meta'])) {
                        $row->$fieldname = $metavalue;
                    }
                }
            }
            if (isset($row->address_data)) {
                foreach ($row->address_data as $fieldname => $metavalue) {
                    if (array_key_exists($fieldname, @$input['ds_parents']['address_data'])) {
                        $row->$fieldname = $metavalue;
                    }
                }
            }
            $weekday = json_decode($row->class_days);
            $row->class_week_day = $weekday[0];
            $this->weekdays = $this->getWeekdays();
            $row->class_weekday_name = @$this->weekdays[$row->class_week_day];

            unset($row->meta);
            unset($row->address_data);
        }

        return $res;
    }
}
