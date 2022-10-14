<?php
namespace DancePressTRWA\Models;

class ClassReports extends Model
{
    //sorry to low-res developers :-)
    public static $studentsColumnMap =		array('column-students-firstname' => 	array('table' => 'ds_students', 		'column' => 'firstname', 		'title' => 'Student First Name', 	'filter-type' => 'text', 'filter-compare' => 'like'),
                                                'column-students-lastname' => 		array('table' => 'ds_students', 		'column' => 'lastname', 		'title' => 'Student Last Name', 	'filter-type' => 'text', 'filter-compare' => 'like'),
                                                'column-students-birthdate' => 		array('table' => 'ds_students', 		'column' => 'birthdate', 		'title' => 'Student Birthdate',		'filter-type' => false),
                                                'column-students-gender' => 		array('table' => 'ds_students', 		'column' => 'gender', 			'title' => 'Student Gender', 		'filter-type' => 'text', 'filter-compare' => 'like'),
                                                'column-students-dateadded' => 		array('table' => 'ds_students', 		'column' => "date_added", 		'title' => 'Student Date Added',	'filter-type' => false, 'date_format' => true, 'date_format_format' => "%Y-%m-%d %h:%i %p"),

                                                //json
                                                'column-students-medical' =>		array('table' => 'ds_students', 		'column' => 'meta', 			'json-field' => 'medical', 			'title' => 'Medical', 	'filter-type' => 'text', 'filter-compare' => 'like'),
                                                );

    public static $parentsColumnMap =		array('column-parents-firstname' => 	array('table' => 'ds_parents', 			'column' => 'firstname', 		'title' => 'Parent First Name', 	'filter-type' => 'text', 'filter-compare' => 'like'),
                                                'column-parents-lastname' => 		array('table' => 'ds_parents', 			'column' => 'lastname', 		'title' => 'Parent Last Name', 		'filter-type' => 'text', 'filter-compare' => 'like'),
                                                'column-parents-email' => 			array('table' => 'ds_parents', 			'column' => 'email', 			'title' => 'Parent Email', 			'filter-type' => 'text', 'filter-compare' => 'like'),
                                                'column-parents-isconfirmed' => 	array('table' => 'ds_parents', 			'column' => 'is_confirmed', 	'title' => 'Parent Is Confirmed', 	'filter-type' => 'text', 'filter-compare' => 'like'),
                                                'column-parents-dateadded' => 		array('table' => 'ds_parents', 			'column' => 'date_added', 		'title' => 'Parent Date Added',		'filter-type' => false, 'date_format' => true, 'date_format_format' => "%Y-%m-%d %h:%i %p"),
                                                'column-parents-active' => 			array('table' => 'ds_parents', 			'column' => 'active', 			'title' => 'Parent Active', 		'filter-type' => 'text', 'filter-compare' => 'exact'),

                                                //json
                                                'column-parents-emailadditional' =>	array('table' => 'ds_parents', 			'column' => 'meta', 			'json-field' => 'email_additional', 'title' => 'Email Additional', 	'filter-type' => 'text', 'filter-compare' => 'like'),
                                                'column-parents-address1' =>		array('table' => 'ds_parents', 			'column' => 'meta', 			'json-field' => 'address1', 		'title' => 'Address1', 			'filter-type' => 'text', 'filter-compare' => 'like'),
                                                'column-parents-address2' =>		array('table' => 'ds_parents', 			'column' => 'meta', 			'json-field' => 'address2', 		'title' => 'Address2', 			'filter-type' => 'text', 'filter-compare' => 'like'),
                                                'column-parents-city' =>			array('table' => 'ds_parents', 			'column' => 'meta', 			'json-field' => 'city', 			'title' => 'City', 				'filter-type' => 'text', 'filter-compare' => 'like'),
                                                'column-parents-postalcode' =>		array('table' => 'ds_parents', 			'column' => 'meta', 			'json-field' => 'postal_code', 		'title' => 'Postal Code', 		'filter-type' => 'text', 'filter-compare' => 'like'),
                                                'column-parents-phoneprimary' =>	array('table' => 'ds_parents', 			'column' => 'meta', 			'json-field' => 'phone_primary', 	'title' => 'Phone Primary', 	'filter-type' => 'text', 'filter-compare' => 'like'),
                                                'column-parents-phonesecondary' =>	array('table' => 'ds_parents', 			'column' => 'meta', 			'json-field' => 'phone_secondary', 	'title' => 'Phone Secondary', 	'filter-type' => 'text', 'filter-compare' => 'like'),
                                                'column-parents-emergencycontact' =>array('table' => 'ds_parents', 			'column' => 'meta', 			'json-field' => 'emergency_contact', 'title' => 'Emergency Contact', 'filter-type' => 'text', 'filter-compare' => 'like'),
                                                'column-parents-emergencycontactphone' =>array('table' => 'ds_parents', 	'column' => 'meta', 			'json-field' => 'emergency_contact_phone', 'title' => 'Emergency Contact Phone', 'filter-type' => 'text', 'filter-compare' => 'like'),
                                                'column-parents-emergencycontactrelationship' =>array('table' => 'ds_parents',	'column' => 'meta', 		'json-field' => 'emergency_contact_relationship', 'title' => 'Emergency Contact Relationship', 'filter-type' => 'text', 'filter-compare' => 'like'),
                                                );

    public static $classStudentsColumnMap = array('column-classes-classid' => 		array('table' => 'ds_class_students', 	'column' => 'class_id',			'title' => 'Class Id', 				'filter-type' => 'select-class', 'filter-compare' => 'exact'),
                                                'column-classes-weekday' => 		array('table' => 'ds_class_students', 	'column' => 'week_day', 		'title' => 'Week Day', 				'filter-type' => 'select-weekday', 'filter-compare' => 'exact')
                                                );

    public static $allColumnMap = false; //can't combine arrays here, so we do it below in a function

    public function __construct($sessionCondition = '')
    {
        parent::__construct($sessionCondition);
    }

    public static function getAllColumnMap()
    {
        if (self::$allColumnMap == false) {
            self::$allColumnMap = self::$studentsColumnMap + self::$parentsColumnMap + self::$classStudentsColumnMap;
        }
        return self::$allColumnMap;
    }

    public function doMagicQuery2($selectColumns, $filters = array(), $is_confirmed = 1)
    {
        //$searchesMeta = false; //json search bool
        // SELECT COLUMNS
        $columns = array();

        foreach ($selectColumns as $colsk => $cols) {
            if (empty($cols)) {
                continue;
            }

            foreach ($cols as $c) {
                if (!empty($c['date_format'])) {
                    $columns[] = 'date_format(`' . $this->p . $colsk . '`.`' . $c['column'] . '`, "%y-%m-%d %h:%i %p")  as `' . str_replace('', '', $colsk) . '_' . $c['column'] . '`';
                } else {
                    $columns[] = '`' . $this->p . $colsk . '`.`' . $c . '` as `' . str_replace('', '', $colsk) . '_' . $c . '`';
                }
                //if($c == 'meta'){ $searchesMeta = true; } //mark for json search
            }
        }
        if (empty($columns)) {
            return false;
        }
        //Add Distinct - all rows should be unique, students need to be merged if class details omitted.
        $sql = 'SELECT DISTINCT ' . implode(', ', $columns) . ' FROM `' . $this->p . 'ds_students`
					LEFT JOIN `' . $this->p . 'ds_parents` ON `' . $this->p . 'ds_parents`.`id` = `' . $this->p . 'ds_students`.`parent_id`
					LEFT JOIN `' . $this->p . 'ds_class_students` ON `' . $this->p . 'ds_class_students`.`student_id` = `' . $this->p . 'ds_students`.`id`

					RIGHT JOIN `' . $this->p . 'ds_classes` ON `' . $this->p . 'ds_classes`.id=`' . $this->p . 'ds_class_students`.`class_id`
					';

        // WHERE
        $conditions = array();
        foreach ($filters as $table => $cols) {
            foreach ($cols as $col => $val) {
                if (strpos($col, 'meta-') === 0) {//$col == 'meta'){ //  Array ( [ds_parents] => Array ( [meta] => Array ( [value] => 12 [compare] => like [json-field] => phone_primary ) ) )
                    $conditions[] =  "`{$this->p}" . $table . '`.`'. 'meta' . '` REGEXP \'"' . $val['json-field'] . '":"([^"]*)' . $val['value'] . '([^"]*)"\'';
                //	'"key_name":"([^"]*)key_word([^"]*)"';
                } else {
                    switch ($val['compare']) {
                        case 'like': $conditions[] =  "`{$this->p}" . $table . '`.`'. $col . '` LIKE \'%' . $val['value'] . '%\''; break;
                        case 'exact': $conditions[] =  "`{$this->p}" . $table . '`.`'. $col . '` = \'' . $val['value'] . '\''; break;
                    }
                }
            }
        }
        if ($is_confirmed) {
            $conditions[] = "`{$this->p}ds_parents`.`is_confirmed` = 1";
        }

        $where = count($conditions) ? ' WHERE ' . implode(' AND ', $conditions) : '';

        $sql .= $where . ' AND `' . $this->p . 'ds_students`.'. $this->sessionCondition;
        //echo '<br>'.$sql.'<br>';

        // QUERY
        $this->db->query($sql);

        if (!empty($this->db->last_error)) {
            die($this->db->last_error);
        }

        $res = $this->db->last_result;

        return $res;
    }

    public function appendJSONFields($studentsParentsClasses, $selectedColumns)
    {
        //print_r($selectedColumns); //Array ( [0] => column-students-firstname [1] => column-students-lastname [2] => column-students-birthdate [3] => column-students-gender [4] => column-students-dateadded
        //[5] => column-students-medical [6] => column-parents-dateadded [7] => column-parents-emailadditional [8] => column-classes-weekday )
        // EXTRACT JSON
        $maxNumParentFields = 0;
        $maxNumStudentFields = 0;
        if (!$studentsParentsClasses) {
            return false;
        }

        foreach ($studentsParentsClasses as &$spc) {
            if (isset($spc->ds_parents_meta) && $spc->ds_parents_meta) {
                $ds_parents_meta_extracted = json_decode($spc->ds_parents_meta, true);
                $numParentFields = 0;
                foreach (self::$parentsColumnMap as $pcmk => $pcm) {////
                    if (isset($pcm['json-field'])) {
                        $numParentFields++;
                        $varName = 'ds_parents_' . $pcm['json-field'];
                        //echo $pcm['json-field'].'';
                        //if(in_array('column-parents-' . $pcm['json-field'], $selectedColumns)){echo 'AAA<br>';
                        if (in_array($pcmk, $selectedColumns)) {//echo 'AAA<br>';
                            $spc->$varName = isset($ds_parents_meta_extracted[$pcm['json-field']]) ? $ds_parents_meta_extracted[$pcm['json-field']].'' : '';
                        }
                    }
                }
                if ($numParentFields > $maxNumParentFields) {
                    $maxNumParentFields = $numParentFields;
                }
            } else {

                //append filler columns when json is missing
                for ($i = 0; $i < $maxNumParentFields; $i++) {
                    $varName = 'spacer-'.$i;
                    $spc->$varName = 'a';
                }
            }
            unset($spc->ds_parents_meta);


            if (isset($spc->ds_students_meta) && $spc->ds_students_meta) {
                $ds_students_meta_extracted = json_decode($spc->ds_students_meta, true);
                $numStudentFields = 0;
                foreach (self::$studentsColumnMap as $scm) {
                    if (isset($scm['json-field'])) {
                        $varName = 'ds_students_' . $scm['json-field'];
                        if (in_array('column-students-' . $scm['json-field'], $selectedColumns)) {
                            $spc->$varName = isset($ds_students_meta_extracted[$scm['json-field']]) ? $ds_students_meta_extracted[$scm['json-field']] : ''; //pull 1st student info / but why is there 2 students?
                        }
                    }
                }
                if ($numStudentFields > $maxNumStudentFields) {
                    $maxNumStudentFields = $numStudentFields;
                }
            } else {
                //append filler columns when json is missing
                for ($i = 0; $i < $maxNumStudentFields; $i++) {
                    $varName = 'spacer-'.$i;
                    $spc->$varName = '';
                }
            }
            unset($spc->ds_students_meta); //get rid of meta field from results
        }

        return $studentsParentsClasses;
    }

    public function getMergedReport($selectedColumns, $filters = array())
    {
        $selectColumns = array('ds_students' => array(), 'ds_class_students' => array(), 'ds_parents' => array());

        foreach (self::$studentsColumnMap as $cmk => $cm) { // $studentsColumnMap =		array('column-students-firstname' => array('column' => 'students_firstname', 'title' => 'First Name'),
            if (in_array($cmk, $selectedColumns)) { // $selectedColumns = Array ( [0] => column-students-firstname [1] => column-students-lastname [2] => column-students-birthdate [3] => column-students-gender [4] => column-students-dateadded [5] => column-parents-firstname [6] => column-parents-lastname [7] => column-parents-isconfirmed [8] => column-parents-dateadded [9] => column-parents-active [10] => column-classes-classid [11] => column-classes-weekday ) aaaaaaaaaaaaherree
                if (!empty($cm['date_format'])) {
                    $selectColumns['ds_students'][] = array(
                        'column' => str_replace('students_', '', $cm['column']),
                        'date_format' => true,
                        'date_format_format' => $cm['date_format_format']
                    );
                } else {
                    $selectColumns['ds_students'][] = str_replace('students_', '', $cm['column']);
                }
            }
        }

        foreach (self::$classStudentsColumnMap as $cmk => $cm) {
            if (in_array($cmk, $selectedColumns)) {
                $selectColumns['ds_class_students'][] = str_replace('class_students_', '', $cm['column']);
            }
        }

        foreach (self::$parentsColumnMap as $cmk => $cm) {
            if (in_array($cmk, $selectedColumns)) {
                if (!empty($cm['date_format'])) {
                    $selectColumns['ds_parents'][] = array(
                        'column' => str_replace('parents_', '', $cm['column']),
                        'date_format' => true,
                        'date_format_format' => $cm['date_format_format']
                    );
                } else {
                    $selectColumns['ds_parents'][] = str_replace('parents_', '', $cm['column']);
                }
            }
        }

        //fix filters for yes/no => 1/0 translation
        //print_r($filters); //Array ( [ds_parents] => Array ( [is_confirmed] => Array ( [value] => yes [compare] => like ) [active] => Array ( [value] => no [compare] => exact ) ) )
        if (isset($filters['ds_parents'])) {
            if (isset($filters['ds_parents']['is_confirmed'])) {
                if (strcasecmp($filters['ds_parents']['is_confirmed']['value'], 'yes') == 0) {
                    $filters['ds_parents']['is_confirmed']['value'] = 1;
                } else {
                    $filters['ds_parents']['is_confirmed']['value'] = 0;
                }
            }

            if (isset($filters['ds_parents']['active'])) {
                if (strcasecmp($filters['ds_parents']['active']['value'], 'yes') == 0) {
                    $filters['ds_parents']['active']['value'] = 1;
                } else {
                    $filters['ds_parents']['active']['value'] = 0;
                }
            }
        }
        //print_r($filters);


        $studentParentClasses = $this->doMagicQuery2($selectColumns, $filters);

        return $studentParentClasses;
    }
}
