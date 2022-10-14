<?php

namespace DancePressTRWA\Models;

class ClassStudents extends Model
{
    public function __construct( $sessionCondition = '' )
    {
        parent::__construct( $sessionCondition );
    }
    
    public function getAllStudents()
    {
        $sql = "\n\t\t\tSELECT\n\t\t\t\ts.*,\n\t\t\tdate_format(s.date_added, '%Y-%m-%d %h:%i %p') as date_added\n\t\t\tFROM\n\t\t\t\t{$this->p}ds_students s\n\t\t\tWHERE " . $this->sessionCondition . "\n\t\t\tORDER BY\n\t\t\t\tlastname, firstname";
        $this->db->query( $sql );
        if ( !empty($this->db->last_error) ) {
            die( $this->db->last_error );
        }
        $res = $this->db->last_result;
        return $res;
    }
    
    public function getStudentsWithClasses( $start = 0, $limit = 5 )
    {
        $sql = "SELECT id, firstname, lastname FROM {$this->p}ds_students WHERE " . $this->sessionCondition . " ORDER BY id DESC LIMIT {$start}, {$limit}";
        $this->db->query( $sql );
        $students = $this->db->last_result;
        $sql = "SELECT cs.id as csid, c.id as cid, class_id, student_id, name, week_day FROM {$this->p}ds_class_students cs INNER JOIN {$this->p}ds_classes c ON c.id=cs.class_id WHERE " . $this->sessionCondition;
        $this->db->query( $sql );
        $classStudents = $this->db->last_result;
        $studentsClasses = array();
        foreach ( $students as $student ) {
            $studentClassList = array();
            foreach ( $classStudents as $cs ) {
                if ( $cs->student_id == $student->id ) {
                    $studentClassList[] = array(
                        'class_id' => $cs->cid,
                        'name'     => $cs->name,
                        'weekday'  => $cs->week_day,
                    );
                }
            }
            $classes = array(
                'classes' => $studentClassList,
            );
            $studentsClasses[] = array_merge( (array) $student, (array) $classes );
        }
        //print_r($studentClasses);
        return $studentsClasses;
    }
    
    public function getStudentById( $id )
    {
        $id = (int) $id;
        $sql = $this->db->prepare( "\n\t\t\t\tSELECT *,\n\t\t\t\t(\n\t\t\t\t\tSELECT\n\t\t\t\t\t\tCOUNT(*)\n\t\t\t\t\tFROM\n\t\t\t\t\t\t{$this->p}ds_students s\n\t\t\t\t\tINNER JOIN\n\t\t\t\t\t\t{$this->p}ds_class_students cs ON cs.student_id = s.id\n\t\t\t\t\tINNER JOIN\n\t\t\t\t\t\t{$this->p}ds_classes c ON cs.class_id = c.id\n\t\t\t\t\tWHERE\n\t\t\t\t\t\ts.id = %s\n\t\t\t\t\tAND\n\t\t\t\t\t\tc.is_competitive = 1\n\t\t\t\t\tAND\n\t\t\t\t\t\ts.active = 1\n\n\t\t\t\t\tAND `s`.{$this->sessionCondition}\n\n\t\t\t\t) as is_company_student\n\n\t\t\t\tFROM\n\t\t\t\t\t{$this->p}ds_students\n\t\t\t\tWHERE\n\t\t\t\t\tid = %s\n\t\t\t\tAND " . $this->sessionCondition, $id, $id );
        $this->db->query( $sql );
        if ( !empty($this->db->last_error) ) {
            die( $this->db->last_error );
        }
        $res = $this->db->last_result;
        foreach ( $res as &$student ) {
            $student->meta = json_decode( $student->meta );
            if ( isset( $student->meta->{1} ) ) {
                $student->meta = $student->meta->{1};
            }
        }
        return $res;
    }
    
    //Formatted for calendar - Formatting should be moved to view.
    public function getStudentAssignedClasses( $id )
    {
        //$sql = $this->db->prepare("","");
        $id = (int) $id;
        $sql = "\n\t\t\tSELECT\n\t\t\t\tconcat\n\t\t\t\t\t(name,' ','[Start: ',\n\t\t\t\t\tdate_format(concat(startdate,' ',starttime),\n\t\t\t\t\t'%h:%i %p'),' ','End: ',\n\t\t\t\t\tdate_format(concat(enddate,' ',endtime),'%h:%i %p'),']' )\n\t\t\t\t\tAS title,\n\t\t\t\tconcat\n\t\t\t\t\t(startdate,' ',starttime)\n\t\t\t\t\tAS start,\n\t\t\t\tconcat\n\t\t\t\t\t(enddate,' ',endtime)\n\t\t\t\t\tAS end,\n\t\t\t\tconcat\n\t\t\t\t\t('&id=',c.id)\n\t\t\t\t\tAS url,\n\t\t\t\t'false'\n\t\t\t\t\tAS allDay,\n\t\t\t\tstartdate,\n\t\t\t\tenddate\n\t\t\t\tstarttime,\n\t\t\t\tendtime,\n\t\t\t\tdays\n\t\t\t\tFROM\n\t\t\t\t\t{$this->p}ds_classes c inner join {$this->p}ds_class_students ass on\n\t\t\t\t\tc.id=ass.class_id\n\t\t\t\t\tinner join {$this->p}ds_students s on ass.student_id=s.parent_id\n\t\t\t\tWHERE\n\t\t\t\t\tass.student_id=" . $id . "\n\t\t\t\t\tand\n\t\t\t\t\tis_parent_event = 1\n\t\t\t\t\tAND " . $this->sessionCondition . "\n\t\t\t\t\torder by startdate asc\n\t\t\t\t\t";
        $this->db->query( $sql );
        //echo $sql;
        $res = $this->db->last_result;
        return $res;
    }
    
    public function getStudentClasses( $id )
    {
        $sql = "\n\t\t\tSELECT\n\t\t\t\tc.*,\n\t\t\t\tcs.id as class_student_id,\n\t\t\t\tc.days,\n\t\t\t\ttime_format(c.starttime, '%h:%i %p') as starttime,\n\t\t\t\ttime_format(c.endtime, '%h:%i %p') as endtime\n\t\t\tFROM\n\t\t\t\t{$this->p}ds_classes c\n\t\t\tINNER JOIN\n\t\t\t\t{$this->p}ds_class_students cs ON c.id = cs.class_id\n\t\t\tWHERE\n\t\t\t\tcs.student_id = {$id}\n\t\t\tAND\n\t\t\t\tis_parent_event = 1\n\t\t\tAND `c`." . $this->sessionCondition;
        $this->db->query( $sql );
        $res = $this->db->last_result;
        $this->getWeekdays();
        foreach ( $res as $k => $class ) {
            $days = json_decode( $class->days );
            $res[$k]->weekday_name = @$this->weekdays[$days[0]];
        }
        return $this->db->last_result;
    }
    
    public function addNewStudent( $input )
    {
        $sql = $this->db->prepare( "\n\t\t\tINSERT INTO\n\t\t\t\t{$this->p}ds_students\n\t\t\tSET\n\t\t\t\tfirstname = %s,\n\t\t\t\tlastname = %s,\n\t\t\t\tbirthdate = %s,\n\t\t\t\tparent_id = %s, " . $this->sessionCondition, $input['category_name'] );
        $this->db->query( $sql );
        return $this->db->last_result;
    }
    
    public function findStudents( $input, $activeStatus = false )
    {
        $sql = "\n\t\t\tSELECT\n\t\t\t\ts.*,\n\t\t\t\tdate_format(s.date_added, '%Y-%m-%d %h:%i %p') as date_added\n\t\t\tFROM\n\t\t\t\t{$this->p}ds_students s\n\t\t\tINNER JOIN\n\t\t\t\t{$this->p}ds_parents p ON s.parent_id = p.id";
        $str = false;
        
        if ( $input ) {
            $sql .= $this->db->prepare( " WHERE\n\t\t\t\t\t( s.firstname LIKE %s\n\t\t\t\tOR\n\t\t\t\t\ts.lastname like %s ) ", $input . '%', $input . '%' );
            $str = true;
        }
        
        $conjunction = ( $str ? ' AND ' : ' WHERE ' );
        
        if ( $activeStatus == true ) {
            $sql .= $conjunction . "\n\t\t\t\t\t\ts.active = 1";
        } else {
            $sql .= $conjunction . "\n\t\t\t\t\t\ts.active = 0";
        }
        
        $sql .= ' AND `s`.' . $this->sessionCondition;
        $sql .= " ORDER BY\n\t\t\t\ts.lastname ASC, s.firstname ASC";
        $this->db->query( $sql );
        if ( !empty($this->db->last_error) ) {
            die( $this->db->last_error );
        }
        return $this->db->last_result;
    }
    
    //Finds all children with same parent, based on one student_id
    public function getSiblings( $studentId )
    {
        $studentId = (int) $studentId;
        $sql = $this->db->prepare( "SELECT\n\t\t\t\tparent_id\n\t\t\tFROM\n\t\t\t\t{$this->p}ds_students s\n\t\t\tWHERE\n\t\t\t\ts.id =  %d\n\t\t\tAND s." . $this->sessionCondition, $studentId );
        $parentId = $this->db->get_var( $sql );
        $sql = $this->db->prepare( "SELECT\n\t\t\t\ts.*\n\t\t\tFROM\n\t\t\t\t{$this->p}ds_students s\n\t\t\tWHERE\n\t\t\t\ts.parent_id = %d\n\t\t\tAND s." . $this->sessionCondition, $parentId );
        $this->db->query( $sql );
        return $this->db->last_result;
    }
    
    public function getStudentsByParentId( $parentId, $exclude = false, $activeOnly = true )
    {
        
        if ( $exclude == "excludeIfCannotReg" ) {
            $exclude = " AND s.can_register_online = 1 ";
        } else {
            $exclude = '';
        }
        
        
        if ( $activeOnly ) {
            $activeOnly = " AND s.active = 1 ";
        } else {
            $activeOnly = '';
        }
        
        $sql = "\n\t\t\tSELECT\n\t\t\ts.*,\n\t\t\t\t(\n\t\t\t\t\tSELECT\n\t\t\t\t\t\tCOUNT(*)\n\t\t\t\t\tFROM\n\t\t\t\t\t\t{$this->p}ds_students s_inner\n\t\t\t\t\tINNER JOIN\n\t\t\t\t\t\t{$this->p}ds_class_students cs ON cs.student_id = s_inner.id\n\t\t\t\t\tINNER JOIN\n\t\t\t\t\t\t{$this->p}ds_classes c ON cs.class_id = c.id\n\t\t\t\t\tWHERE\n\t\t\t\t\t\tc.is_competitive = 1\n\t\t\t\t\tAND\n\t\t\t\t\t\ts_inner.active = 1\n\t\t\t\t\tAND\n\t\t\t\t\t\ts_inner.parent_id = {$parentId}\n\t\t\t\t\tAND\n\t\t\t\t\t\ts_inner.id = s.id\n\t\t\t\t\tAND\n\t\t\t\t\t\t`s_inner`.{$this->sessionCondition}\n\n\t\t\t\t\tGROUP BY `s_inner`.id\n\n\t\t\t\t) as is_company_student\n\n\t\t\tFROM\n\t\t\t\t{$this->p}ds_students s\n\t\t\tWHERE\n\t\t\t\ts.parent_id = {$parentId}\n\t\t\t{$activeOnly}\n\t\t\t{$exclude}\n\n\t\t\tAND s." . $this->sessionCondition;
        //die($sql);
        $this->db->query( $sql );
        if ( !empty($this->db->last_error) ) {
            die( $this->db->last_error );
        }
        return $this->db->last_result;
    }
    
    public function UpdateStudent( $input, $id )
    {
        $input['meta'][strtolower( str_replace( ' ', '_', $input['custom_meta_key'] ) )] = $input['custom_meta_value'];
        if ( $input['meta']['medicalbool'] == 0 ) {
            $input['meta']['medical'] = '';
        }
        foreach ( $input['meta'] as $k => $v ) {
            if ( !$v && $k != 'medical' ) {
                unset( $input['meta'][$k] );
            }
        }
        $meta = json_encode( $input['meta'] );
        $sql = $this->db->prepare(
            "\n\t\t\tUPDATE\n\t\t\t\t{$this->p}ds_students\n\t\t\tSET\n\t\t\t\tfirstname = %s,\n\t\t\t\tlastname = %s,\n\t\t\t\tbirthdate = %s,\n\t\t\t\tgender = %s,\n\t\t\t\taddress_data = %s,\n\t\t\t\tschedule_available = %d,\n\t\t\t\tmeta = %s\n\t\t\tWHERE\n\t\t\t\tid=%s\n\t\t\tAND " . $this->sessionCondition,
            $input['firstname'],
            $input['lastname'],
            $input['birthdate'],
            $input['gender'],
            json_encode( $input['address'] ),
            $input['schedule_available'],
            $meta,
            $id
        );
        $this->db->query( $sql );
        return true;
    }
    
    public function updateStudentMeta( $studentId, $meta )
    {
        $sql = $this->db->prepare( "UPDATE {$this->p}ds_students SET meta = %s WHERE id=%d AND " . $this->sessionCondition, $meta, $studentId );
        $this->db->query( $sql );
    }
    
    public function removeStudentFromClass( $classStudentsId )
    {
        //get detail
        $classDetail = $this->db->get_row( $this->db->prepare( "SELECT * FROM {$this->p}ds_class_students WHERE id=%d", $classStudentsId ) );
        //delate
        $sql = $this->db->prepare( "\n\t\t\t\tDELETE FROM\n\t\t\t\t\t{$this->p}ds_class_students\n\t\t\t\tWHERE\n\t\t\t\t\tid = %d\n\t\t\t\tAND " . $this->sessionCondition . " LIMIT 1\n\n\t\t\t", $classStudentsId );
        $this->db->query( $sql );
        //add to history
        $sql = $this->db->prepare(
            "\n\t\t\t\t\t\t\t\tINSERT INTO {$this->p}ds_students_history\n\t\t\t\t\t\t\t\tSET student_id=%d, `type`=%s, `value`=%s, `value2`=%s, " . $this->sessionCondition,
            $classDetail->student_id,
            'DROP',
            time(),
            $classDetail->class_id
        );
        $this->db->query( $sql );
    }
    
    public function addStudentToClass( $classId, $weekDay, $studentId )
    {
        $sql = $this->db->prepare(
            "INSERT IGNORE INTO\n\t\t\t\t\t{$this->p}ds_class_students\n\t\t\t\tSET\n\t\t\t\t\tclass_id=%d,\n\t\t\t\t\tstudent_id=%d,\n\t\t\t\t\tweek_day=%d, " . $this->sessionCondition,
            $classId,
            $studentId,
            $weekDay
        );
        $this->db->query( $sql );
        //add to history
        $sql = $this->db->prepare(
            "\n\t\t\t\t\t\t\t\tINSERT INTO {$this->p}ds_students_history\n\t\t\t\t\t\t\t\tSET student_id=%d, `type`=%s, `value`=%s, `value2`=%s, " . $this->sessionCondition,
            $studentId,
            'ENROLL',
            time(),
            $classId
        );
        $this->db->query( $sql );
    }
    
    public function getStudentHistory( $type = false, $startTimestamp = false, $endTimestamp = false )
    {
        //round the end timestamp to midnight of the following day (otherwise today's changes are omitted_:
        $endTimestamp = $endTimestamp + 86400;
        $sql = "SELECT `id`, `student_id`, `type`, `value`, `value2`\n\t\t\t\tFROM {$this->p}ds_students_history";
        $arrWhere = array();
        if ( $type ) {
            $arrWhere[] = $this->db->prepare( '`type`=%s', $type );
        }
        
        if ( $type == 'DROP' || $type == 'ENROLL' ) {
            if ( $startTimestamp ) {
                $arrWhere[] = $this->db->prepare( '`value`>= %s', $startTimestamp );
            }
            if ( $endTimestamp ) {
                $arrWhere[] = $this->db->prepare( '`value`<= %s', $endTimestamp );
            }
        }
        
        
        if ( count( $arrWhere ) ) {
            $sql .= ' WHERE' . implode( ' AND ', $arrWhere );
            $sql .= ' AND ' . $this->sessionCondition;
        } else {
            $sql .= ' WHERE ' . $this->sessionCondition;
        }
        
        $this->db->query( $sql );
        return $this->db->last_result;
    }
    
    public function getHistoryClassCounts( $history )
    {
        $counts = array();
        foreach ( $history as $h ) {
            if ( $h->value2 == '' ) {
                continue;
            }
            if ( !isset( $counts[$h->value2] ) ) {
                $counts[$h->value2] = 0;
            }
            $counts[$h->value2]++;
        }
        return $counts;
    }
    
    public function deleteStudent( $id )
    {
        $id = (int) $id;
        $sql = $this->db->prepare( "\n\t\t\t\tDELETE FROM\n\t\t\t\t\t{$this->p}ds_students\n\t\t\t\tWHERE\n\t\t\t\t\tid = %d\n\t\t\t\tAND " . $this->sessionCondition . "\n\t\t\t\tLIMIT 1\n\n\t\t\t", $id );
        $this->db->query( $sql );
        return "Student deleted.";
    }
    
    public function getStudentIdByClassStudentsId( $classStudentId )
    {
        $sql = $this->db->prepare( "\n\t\t\tSELECT\n\t\t\t\tstudent_id\n\t\t\tFROM\n\t\t\t\t{$this->p}ds_class_students\n\t\t\tWHERE\n\t\t\t\tid = %d\n\t\t\tAND " . $this->sessionCondition, $classStudentId );
        $this->db->query( $sql );
        $res = $this->db->last_result;
        if ( !$res ) {
            return false;
        }
        return $res[0]->student_id;
    }
    
    //This uses the same algorythm as the admin-managestudents screen
    //so MUST produce an identical number of results.
    //It includes any students NOT assigned to classes, but who ARE registered.
    public function getCountRegisteredStudents()
    {
        return count( $this->findStudents( '', 1 ) );
    }
    
    public function disableRegistrationsById( $ids )
    {
        foreach ( $ids as $id ) {
            $sql = $this->db->prepare( "\n\t\t\t\tUPDATE\n\t\t\t\t\t{$this->p}ds_students\n\t\t\t\tSET\n\t\t\t\t\tcan_register_online = 0\n\t\t\t\tWHERE\n\t\t\t\t\tid = %d\n\t\t\t\tAND " . $this->sessionCondition, $id );
            $this->db->query( $sql );
        }
        return true;
    }
    
    public function enableRegistrationsById( $ids )
    {
        foreach ( $ids as $id ) {
            $sql = $this->db->prepare( "\n\t\t\t\tUPDATE\n\t\t\t\t\t{$this->p}ds_students\n\t\t\t\tSET\n\t\t\t\t\tcan_register_online = 1\n\t\t\t\tWHERE\n\t\t\t\t\tid = %d\n\t\t\t\tAND " . $this->sessionCondition, $id );
            $this->db->query( $sql );
        }
        return true;
    }
    
    public function enableScheduleById( $ids )
    {
        foreach ( $ids as $id ) {
            $sql = $this->db->prepare( "\n\t\t\t\tUPDATE\n\t\t\t\t\t{$this->p}ds_students\n\t\t\t\tSET\n\t\t\t\t\tschedule_available = 1\n\t\t\t\tWHERE\n\t\t\t\t\tid = %d\n\t\t\t\tAND " . $this->sessionCondition, $id );
            $this->db->query( $sql );
        }
        return true;
    }
    
    public function disableScheduleById( $ids )
    {
        foreach ( $ids as $id ) {
            $sql = $this->db->prepare( "\n\t\t\t\tUPDATE\n\t\t\t\t\t{$this->p}ds_students\n\t\t\t\tSET\n\t\t\t\t\tschedule_available = 0\n\t\t\t\tWHERE\n\t\t\t\t\tid = %d\n\t\t\t\tAND " . $this->sessionCondition, $id );
            $this->db->query( $sql );
        }
        return true;
    }
    
    public function updateScheduleAvailability( $student_id, $schedule_available )
    {
        if ( empty($student_id) ) {
            return false;
        }
        $sql = $this->db->prepare( "\n\t\t\tUPDATE\n\t\t\t\t{$this->p}ds_students\n\t\t\tSET\n\t\t\t\tschedule_available = {$schedule_available}\n\t\t\tWHERE\n\t\t\t\tid = %d\n\t\t\tAND " . $this->sessionCondition, $student_id );
        $this->db->query( $sql );
        if ( !empty($this->db->last_error) ) {
            die( $this->db->last_error );
        }
        return true;
    }

}