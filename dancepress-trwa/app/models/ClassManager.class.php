<?php

namespace DancePressTRWA\Models;

class ClassManager extends Model
{
    public function __construct( $sessionCondition = '' )
    {
        parent::__construct( $sessionCondition );
    }
    
    public function addNew( $input )
    {
        $weekdays = ( isset( $input['days'] ) ? json_encode( $input['days'] ) : '' );
        if ( empty($input['class_fee']) ) {
            $input['class_fee'] = 0;
        }
        if ( empty($input['costume_fee']) ) {
            $input['costume_fee'] = 0;
        }
        $operation = "INSERT";
        $idExpression = '';
        
        if ( !empty($input['id']) ) {
            $operation = "REPLACE";
            $idExpression = "id = {$input['id']},";
        }
        
        $starttimeObj = new \DateTime( $input['starttime'] );
        if ( !empty($input['starttime']) ) {
            $input['starttime'] = $starttimeObj->format( 'H:i' );
        }
        
        if ( isset( $input['endtime'] ) && !empty($input['endtime']) ) {
            $endtimeObj = new \DateTime( $input['endtime'] );
            $input['endtime'] = $endtimeObj->format( 'H:i' );
        }
        
        
        if ( isset( $input['duration'] ) ) {
            $starttimeObj->modify( '+' . $input['duration'] . ' minutes' );
            $endtime = $starttimeObj->format( 'H:i' );
        } else {
            $endtime = $input['endtime'];
        }
        
        
        if ( isset( $input['use_global_installments'] ) && $input['use_global_installments'] ) {
            $input['class_fee'] = 0;
            $input['ds_installment_fees'] = array();
        } elseif ( !empty($input['class_fee']) && $input['class_fee'] > 0 ) {
            $input['use_global_installments'] = 0;
            $input['ds_installment_fees'] = array();
        } elseif ( !empty($input['ds_installment_fees']) ) {
            $input['use_global_installments'] = 0;
            $input['class_fee'] = 0;
        }
        
        $sql = $this->db->prepare(
            "\n\t\t\t{$operation} INTO\n\t\t\t\t{$this->p}ds_classes\n\t\t\tSET\n                {$idExpression}\n\t\t\t\tname = %s,\n\t\t\t\tclass_fee = %s,\n\t\t\t\tcostume_fee = %s,\n\t\t\t\tcategory_id = %s,\n\t\t\t\tclassroom = %s,\n\t\t\t\tages = %s,\n\t\t\t\texperience = %d,\n\t\t\t\tis_competitive = %d,\n\t\t\t\tstartdate = %s,\n\t\t\t\tenddate = %s,\n\t\t\t\tdays = %s,\n\t\t\t\tstarttime = %s,\n\t\t\t\tendtime = %s,\n\t\t\t\tdescription = %s,\n\t\t\t\tis_parent_event = 1,\n                is_registerable = %d,\n                use_global_installments = %d,\n\t\t\t\t" . $this->sessionCondition,
            $input['class_name'],
            $input['class_fee'],
            $input['costume_fee'],
            $input['category_id'],
            $input['classroom'],
            $input['ages'],
            $input['experience'],
            $input['is_competitive'],
            $input['startdate'],
            $input['enddate'],
            $weekdays,
            $input['starttime'],
            $endtime,
            $input['description'],
            $input['is_registerable'],
            $input['use_global_installments']
        );
        $this->query( $sql );
        $parent_id = $this->db->insert_id;
        
        if ( !empty($input['ds_installment_fees']) ) {
            if ( !$parent_id ) {
                throw new \Exception( "Cannot insert class fee installments. Class id not set" );
            }
            foreach ( $input['ds_installment_fees'] as $installment ) {
                $statement = "INSERT INTO {$this->p}ds_class_installment_fees\n\t\t\t\t\t\t\t\t(class_id, name, payment_date, amount)\n\t\t\t\t\t\t\t\tVALUES ({$parent_id}, '{$installment['name']}', '{$installment['date']}', {$installment['amount']})";
                $this->db->query( $statement );
            }
        }
        
        //Create recurring child events for each weekday in class period
        //Iterate over every day in course period and test to see if a class is held that day.
        $startstamp = strtotime( $input['startdate'] );
        $endstamp = strtotime( $input['enddate'] );
        for ( $t = $startstamp ;  $t < $endstamp ;  $t += 86400 ) {
            foreach ( $input['days'] as $day ) {
                $day = (string) $day;
                if ( $day == '7' ) {
                    $day = '0';
                }
                
                if ( date( 'w', $t ) === $day ) {
                    $tFormatted = date( "Y-m-d", $t );
                    $sql = $this->db->prepare(
                        "\n\t\t\t\t\t\t\tINSERT INTO\n\t\t\t\t\t\t\t\t{$this->p}ds_classes\n\t\t\t\t\t\t\tSET\n\t\t\t\t\t\t\t\tname = %s,\n\t\t\t\t\t\t\t\tclass_fee = %s,\n\t\t\t\t\t\t\t\tcostume_fee = %s,\n\t\t\t\t\t\t\t\tcategory_id = %s,\n\t\t\t\t\t\t\t\tclassroom = %s,\n\t\t\t\t\t\t\t\tages = %s,\n\t\t\t\t\t\t\t\texperience = %d,\n\t\t\t\t\t\t\t\tis_competitive = %d,\n\t\t\t\t\t\t\t\tstartdate = %s,\n\t\t\t\t\t\t\t\tenddate = %s,\n\t\t\t\t\t\t\t\tstarttime = %s,\n\t\t\t\t\t\t\t\tendtime = %s,\n\t\t\t\t\t\t\t\tdescription = %s,\n\t\t\t\t\t\t\t\tis_parent_event = 0,\n\t\t\t\t\t\t\t\tparent_id = %d, " . $this->sessionCondition,
                        $input['class_name'],
                        $input['class_fee'],
                        $input['costume_fee'],
                        $input['category_id'],
                        $input['classroom'],
                        $input['ages'],
                        $input['experience'],
                        $input['is_competitive'],
                        $tFormatted,
                        $tFormatted,
                        $input['starttime'],
                        $endtime,
                        $input['description'],
                        $parent_id
                    );
                    $this->db->query( $sql );
                }
            
            }
        }
        return $this->db->last_result;
    }
    
    //Delete and recreate a complete course of classes.
    public function adminUpdateClass( $input )
    {
        $sql = $this->db->prepare(
            "\n\t\t\tUPDATE\n\t\t\t\t{$this->p}ds_classes\n\t\t\tSET\n\t\t\t\tclassroom = %s,\n\t\t\t\tstartdate = %s,\n\t\t\t\tenddate = %s,\n\t\t\t\tstarttime = %s,\n\t\t\t\tendtime = %s,\n\t\t\t\tdescription = %s\n\t\t\tWHERE\n\t\t\t\tid = %d\n\t\t\tAND " . $this->sessionCondition,
            $input['classroom'],
            $input['startdate'],
            $input['startdate'],
            $input['starttime'],
            $input['endtime'],
            $input['description'],
            $input['id']
        );
        $this->query( $sql );
        return $this->db->last_result;
    }
    
    //Delete and recreate a complete course of classes.
    public function adminUpdateAllClasses( $input )
    {
        $sql = $this->db->prepare( "\n\t\t\tDELETE FROM\n\t\t\t\t{$this->p}ds_classes\n\t\t\tWHERE\n\t\t\t\tparent_id = %d\n\t\t\tAND\n\t\t\t\tis_parent_event = 0\n\t\t\tAND " . $this->sessionCondition, $input['id'] );
        $this->query( $sql );
        return $this->addNew( $input );
    }
    
    /**
     *
     * @param int $id
     * @return string
     */
    public function deleteClass( $id )
    {
        $sql = $this->db->prepare( "\n\t\t\t\tDELETE FROM\n\t\t\t\t\t{$this->p}ds_classes\n\t\t\t\tWHERE\n\t\t\t\t\t(id = %d\n\t\t\t\tOR\n\t\t\t\t\tparent_id = %d)\n\n\t\t\t\tAND " . $this->sessionCondition, $id, $id );
        $this->db->query( $sql );
        return "Class deleted.";
    }
    
    public function getAllClass( $excludeCompetitive = false, $excludeNonRegisterable = false, $orderBy = 'ca.category_name, c.name' )
    {
        $excl = '';
        if ( $excludeCompetitive ) {
            $excl .= " AND c.is_competitive = 0 ";
        }
        if ( $excludeNonRegisterable ) {
            $excl .= " AND c.is_registerable = 1 ";
        }
        $sql = "\n\t\t\tSELECT c.*,\n\t\t\t\tca.category_name,\n                REPLACE(LOWER(ca.category_name), ' ', '') as category_slug_name\n\n\t\t\tFROM\n\t\t\t\t{$this->p}ds_classes c\n\t\t\tINNER JOIN\n\t\t\t\t{$this->p}ds_class_categories ca ON c.category_id = ca.id\n\n\t\t\tWHERE\n\t\t\t\tis_parent_event = 1\n\t\t\t\t{$excl}\n\t\t\t\t AND `c`." . $this->sessionCondition . "\n\t\t\tGROUP BY\n\t\t\t\tc.id\n\t\t\tORDER BY\n\t\t\t\t{$orderBy}";
        $this->db->query( $sql );
        $res = $this->db->last_result;
        foreach ( $res as $k => &$v ) {
            $students = $this->getClassEnrollment( $v->id, 'activeonly' );
            
            if ( is_object( $students ) ) {
                $v->enrollment = $students->enrollment;
            } else {
                $v->enrollment = 0;
            }
            
            $weekday = json_decode( $v->days );
            $v->week_day = ( isset( $weekday[0] ) ? $weekday[0] : false );
        }
        $this->weekdays = $this->getWeekdays();
        foreach ( $res as $k => $class ) {
            $days = json_decode( $class->days );
            
            if ( !$days ) {
                $res[$k]->weekday_name = false;
                continue;
            }
            
            $res[$k]->weekday_name = ( isset( $this->weekdays[$days[0]] ) ? $this->weekdays[$days[0]] : false );
        }
        return $res;
    }
    
    /**
     * Gets the installment fees that has been defined for a particular course by a provided id
     * @param int $class_id The id of the course/class
     * @throws \Exception Throws and instane of \Exception is the course id was not provided
     * @return array A list of installments if any have been defined for the course, and empty array otherwise
     */
    public function getClassInstallmentFees( $class_id )
    {
        $class_id = (int) $class_id;
        $this->query( "SELECT * FROM {$this->p}ds_class_installment_fees where class_id = {$class_id}" );
        return $this->db->last_result;
    }
    
    public function getClassAverageAge( $classId )
    {
        $classId = (int) $classId;
        $sql = $this->db->prepare( "\n\n\t\t\tSELECT\n\t\t\t\tAVG(TIMESTAMPDIFF(YEAR,s.birthdate,NOW())) as `average`\n\t\t\tFROM\n\t\t\t\t{$this->p}ds_students s\n\t\t\tINNER JOIN\n\t\t\t\t{$this->p}ds_class_students cs ON s.id = cs.student_id\n\t\t\tWHERE\n\t\t\t\tcs.class_id = %d\n\t\t\tAND\n\t\t\t\ts.active = 1\n\t\t\tAND `s`." . $this->sessionCondition, $classId );
        return $this->db->get_var( $sql );
    }
    
    public function getClassAverageAgeByTimestamp( $classId, $timestamp )
    {
        $sql = $this->db->prepare( "\n\n\t\t\tSELECT\n\t\t\t\tAVG(TIMESTAMPDIFF(YEAR,s.birthdate,FROM_UNIXTIME(%d))) as `average`\n\t\t\tFROM\n\t\t\t\t{$this->p}ds_students s\n\t\t\tINNER JOIN\n\t\t\t\t{$this->p}ds_class_students cs ON s.id = cs.student_id\n\t\t\tWHERE\n\t\t\t\tcs.class_id = %d\n\t\t\tAND\n\t\t\t\ts.active = 1\n\t\t\tAND `s`." . $this->sessionCondition, $timestamp, $classId );
        return $this->db->get_var( $sql );
    }
    
    public function getClassEnrollment( $classId, $activeOnly = 'activeonly' )
    {
        $classId = (int) $classId;
        
        if ( $activeOnly == 'activeonly' ) {
            $andActive = " AND s.active = 1 ";
        } else {
            $andActive = '';
        }
        
        $sql = $this->db->prepare( "\n\t\t\t\tSELECT\n\t\t\t\t\tcount(*) AS enrollment,\n\t\t\t\t\tcs.week_day\n\t\t\t\tFROM\n\t\t\t\t\t{$this->p}ds_class_students cs\n\t\t\t\tINNER JOIN\n\t\t\t\t\t{$this->p}ds_students s ON cs.student_id = s.id\n\t\t\t\tWHERE\n\t\t\t\t\tcs.class_id = %d\n\t\t\t\tAND s.is_confirmed = 1\n\t\t\t\t{$andActive}\n\t\t\t\tAND `cs`." . $this->sessionCondition . "\n\t\t\t\tGROUP BY\n\t\t\t\t\tcs.class_id\n\t\t\t", $classId );
        return $this->db->get_row( $sql );
    }
    
    public function getChildrenByClassId( $id )
    {
        $id = (int) $id;
        $sql = $this->db->prepare( "\n\t\t\tSELECT c.*,\n\t\t\t\tca.category_name,\n\t\t\t\tc.days,\n\t\t\t\ts.id AS student_id,\n\t\t\t\ts.firstname,\n\t\t\t\ts.lastname,\n\t\t\t\ts.parent_id,\n\t\t\t\ts.birthdate,\n\t\t\t\ts.gender,\n\t\t\t\ts.address_data,\n\t\t\t\ts.meta,\n\t\t\t\ts.date_added,\n\t\t\t\ts.is_confirmed,\n\t\t\t\ts.active\n\t\t\tFROM\n\t\t\t\t{$this->p}ds_classes c\n\t\t\tINNER JOIN\n\t\t\t\t{$this->p}ds_class_categories ca ON c.category_id = ca.id\n\t\t\tINNER JOIN\n\t\t\t\t{$this->p}ds_class_students cs ON cs.class_id = c.id\n\t\t\tINNER JOIN\n\t\t\t\t{$this->p}ds_students s ON cs.student_id = s.id\n\t\t\tWHERE\n\t\t\t\tis_parent_event = 1\n\t\t\tAND\n\t\t\t\ts.is_confirmed = 1\n\t\t\tAND\n\t\t\t\ts.active = 1\n\t\t\tAND\n\t\t\t\tc.id = %d\n\t\t\tAND `c`." . $this->sessionCondition . "\n\t\t\tORDER BY s.lastname ASC, s.firstname ASC", $id );
        $this->db->query( $sql );
        $res = $this->db->last_result;
        $this->weekdays = $this->getWeekdays();
        foreach ( $res as $k => $class ) {
            $days = json_decode( $class->days );
            $res[$k]->weekday_name = @$this->weekdays[$days[0]];
        }
        //This sucks bad: Medical should be its own db field;
        foreach ( $res as $k => $class ) {
            //yeah - i know
            $meta = json_decode( $class->meta );
            if ( !$meta ) {
                continue;
            }
            foreach ( $meta as $sv ) {
                if ( $sv == '' ) {
                    continue;
                }
                //bad data: ignore
                if ( strtolower( @$sv->firstname ) == strtolower( $class->firstname ) && strtolower( @$sv->lastname ) == strtolower( $class->lastname ) ) {
                    $res[$k]->medical = $sv->medical;
                }
            }
        }
        return $res;
    }
    
    //For admin registration only ... get optional listing of all classes, not narrowed by age or discipline
    public function getClassesUnNarrowed( &$children )
    {
        $res = $this->getAllClass( false );
        foreach ( $res as $k => $class ) {
            foreach ( $children as $k => $cv ) {
                $catName = strtolower( $class->category_name );
                $catName = str_replace( " ", "", $catName );
                //FIXME CLudgy
                $children->{$k}->validClasses[] = $class->id;
            }
        }
        return $res;
    }
    
    /**
     * Select age-appropriate classes, and classes where child has required experience level.
     * @param array $children
     * @param boolean $excludeCompetitive
     * @param boolean $excludeNonRegisterable
     * @return array|NULL
     */
    public function getClassesByChildAge( array &$children, $excludeCompetitive = false, $excludeNonRegisterable = false )
    {
        foreach ( $children as &$child ) {
            $birthDate = new \DateTime( $child->dateofbirth->year . '-' . $child->dateofbirth->month . '-' . $child->dateofbirth->day );
            $currentDate = new \DateTime();
            $diff = $birthDate->diff( $currentDate );
            $child->age = $diff->y;
        }
        $classes = $this->getAllClass( $excludeCompetitive, $excludeNonRegisterable );
        foreach ( $classes as $class ) {
            $ages = array();
            
            if ( preg_match( "/^[\\d]{1,}\\+\$/", $class->ages ) ) {
                $ages[0] = intval( $class->ages );
                $ages[1] = 110;
            } else {
                $ages = explode( '-', $class->ages );
                if ( empty($ages[1]) ) {
                    $ages[1] = $ages[0];
                }
            }
            
            foreach ( $children as &$child ) {
                
                if ( $child->age >= floor( $ages[0] ) && $child->age <= $ages[1] ) {
                    $catName = $class->category_slug_name;
                    
                    if ( isset( $child->{$catName} ) && $class->experience > 0 ) {
                        
                        if ( $child->{$catName . '_years'} >= $class->experience ) {
                            $child->validClasses[] = $class->id;
                        } else {
                        }
                    
                    } elseif ( isset( $child->{$catName} ) && $class->experience == 0 ) {
                        $child->validClasses[] = $class->id;
                    } else {
                        continue;
                    }
                
                }
            
            }
        }
        return $classes;
    }
    
    public function getRecommendedCourses( $childId )
    {
        $childId = (int) $childId;
        $sql = $this->db->prepare( "\n\t\t\tSELECT\n\t\t\t\t*\n\t\t\tFROM\n\t\t\t\t{$this->p}ds_course_recommendations\n\t\t\tWHERE\n\t\t\t\tstudent_id=%d\n\t\t\tAND\n\t\t\t" . $this->sessionCondition, $childId );
        $this->db->query( $sql );
        $res = $this->db->last_result;
        $recommended = array();
        $ids = array();
        foreach ( $res as $r ) {
            $ids[] = $r->course_id;
        }
        if ( !isset( $ids ) ) {
            return false;
        }
        $recommended = $this->getClassesByIds( $ids );
        foreach ( $recommended as $k => $v ) {
            $classEnroll = $this->getClassEnrollment( $v->id, 'activeonly' );
            
            if ( !isset( $classEnroll->enrollment ) ) {
                $recommended[$k]->enrollment = 0;
            } else {
                $recommended[$k]->enrollment = $classEnroll->enrollment;
            }
        
        }
        return $recommended;
    }
    
    public function getAllStudents()
    {
        $sql = $this->db->prepare( "select *  FROM {$this->p}ds_students", "" );
        $this->db->query( $sql );
        $res = $this->db->last_result;
        return $res;
    }
    
    public function getClassById( $id )
    {
        $id = (int) $id;
        $sql = $this->db->prepare( "\n\t\t\t\tSELECT\n\t\t\t\t\tcs.*,\n\t\t\t\t\tcategory_name\n\t\t\t\tFROM\n\t\t\t\t\t{$this->p}ds_classes cs\n\t\t\t\tINNER JOIN\n\t\t\t\t\t{$this->p}ds_class_categories ca ON cs.category_id = ca.id\n\t\t\t\tWHERE\n\t\t\t\t\tcs.id = %d\n\t\t\t\tAND\n\t\t\t\t\t`cs`." . $this->sessionCondition, $id );
        $this->db->query( $sql );
        $res = $this->db->last_result;
        $res = $res[0];
        $res->days = json_decode( $res->days );
        
        if ( $res->days ) {
            $this->getWeekdays();
            $res->weekday_name = $this->weekdays[$res->days[0]];
        } else {
            $res->weekday_name = false;
        }
        
        $st = new \DateTime( $res->starttime );
        $et = new \DateTime( $res->endtime );
        $res->starttime = $st->format( 'h:i A' );
        $res->endtime = $et->format( 'h:i A' );
        
        if ( is_array( $res->days ) ) {
            $wd = array();
            foreach ( $res->days as $v ) {
                $wd[$v] = $v;
            }
            $res->days = $wd;
        }
        
        $res->installments = false;
        return $res;
    }
    
    public function getClassesByIds( $ids, $orderBy = 'cc.category_name ASC, c.`name` ASC' )
    {
        if ( !$ids ) {
            return array();
        }
        $where = '';
        foreach ( $ids as $id ) {
            $where .= " c.id = " . (int) $id . ' OR ';
        }
        $where = rtrim( $where, 'OR ' );
        $sql = "\n\t\t\tSELECT\n\t\t\t\tc.*,\n\t\t\t\tcc.category_name\n\t\t\tFROM\n\t\t\t\t{$this->p}ds_classes c\n\t\t\tINNER JOIN\n\t\t\t\t{$this->p}ds_class_categories cc ON cc.id = c.category_id\n\t\t\tWHERE\n\t\t\t\t( {$where} )\n\t\t\tAND c.{$this->sessionCondition}\n\t\t\tORDER BY\n\t\t\t\t{$orderBy}\n\t\t";
        $this->db->query( $sql );
        $res = $this->db->last_result;
        foreach ( $res as $r ) {
            $r->days = json_decode( $r->days );
            $this->getWeekdays();
            $r->weekday_name = @$this->weekdays[$r->days[0]];
            
            if ( is_array( $r->days ) ) {
                foreach ( $r->days as $v ) {
                    $wd[$v] = $v;
                }
                $r->days = $wd;
            }
        
        }
        return $res;
    }
    
    public function getClassStudentDataByIds( $classData )
    {
        $arrWhere = array();
        foreach ( $classData as $cs ) {
            $arrWhere[] = 'c.id=' . (int) $cs->value2;
        }
        if ( !count( $arrWhere ) ) {
            $arrWhere[] = '1=2';
        }
        $sql = '
		SELECT
			c.id,
			c.name,
			c.class_fee,
			c.costume_fee,
			c.category_id,
			c.classroom,
			c.ages,
			c.experience,
			c.description,
			c.startdate,
			c.enddate,
			c.days,
			c.starttime,
			c.endtime,
			c.is_parent_event,
			c.parent_id,
			c.is_competitive,
			s.id AS student_id,
			s.firstname,
			s.lastname,
			sh.value AS timestamp
		FROM
			' . $this->p . 'ds_classes c
		INNER JOIN
			' . $this->p . 'ds_students_history sh ON c.id = sh.value2
		INNER JOIN
			' . $this->p . 'ds_students s ON sh.student_id = s.id
		WHERE
			(' . implode( ' OR ', $arrWhere ) . ') AND c.' . $this->sessionCondition . '
		AND
			sh.type = \'DROP\'
		ORDER BY
			c.name';
        $this->db->query( $sql );
        return $this->db->last_result;
    }
    
    public function getClassChildrenById( $id )
    {
        $id = (int) $id;
        $sql = $this->db->prepare( "\n\t\t\tSELECT\n\t\t\t\tcs.*,\n\t\t\t\tcategory_name\n\t\t\tFROM\n\t\t\t\t{$this->p}ds_classes cs\n\t\t\tINNER JOIN\n\t\t\t\t{$this->p}ds_class_categories ca\n\t\t\tON\n\t\t\t\tcs.category_id = ca.id\n\t\t\tWHERE parent_id = %d AND `cs`." . $this->sessionCondition, $id );
        $this->db->query( $sql );
        return $this->db->last_result;
    }
    
    public function getAllClassForFront( $url )
    {
        $url = esc_url( $url );
        $sql = "\n\t\t\tSELECT DISTINCT\n\t\t\t\tid AS parent_class_id,\n\t\t\t\tconcat\n\t\t\t\t\t(name,' ','[Start: ',\n\t\t\t\t\tdate_format(concat(startdate,' ',starttime),\n\t\t\t\t\t'%h:%i %p'),' ','End: ',\n\t\t\t\t\tdate_format(concat(enddate,' ',endtime),'%h:%i %p'),']' )\n\t\t\t\t\tAS title,\n\t\t\t\tconcat\n\t\t\t\t\t(startdate,' ',starttime)\n\t\t\t\t\tAS start,\n\t\t\t\tconcat\n\t\t\t\t\t(enddate,' ',endtime)\n\t\t\t\t\tAS end,\n\t\t\t\tconcat\n\t\t\t\t\t('{$url}&id=',id)\n\t\t\t\t\tAS url,\n\t\t\t\t'false'\n\t\t\t\t\tAS allDay,\n\t\t\t\tages,\n\t\t\t\tdescription,\n\t\t\t\tdays,\n\t\t\t\tclassroom\n\t\t\t\tFROM\n\t\t\t\t\t{$this->p}ds_classes\n\t\t\t\tWHERE\n\t\t\t\t\tis_parent_event = 1\n\t\t\t\tAND " . $this->sessionCondition . " ORDER BY title";
        $res = $this->db->get_results( $sql );
        $weekdays = $this->getWeekdays();
        if ( $res ) {
            foreach ( $res as $k => $class ) {
                $classDaysInt = json_decode( $class->days );
                if ( $classDaysInt ) {
                    foreach ( $classDaysInt as $dayInt ) {
                        $res[$k]->weekday_name[] = @$weekdays[$dayInt];
                    }
                }
            }
        }
        return $res;
    }
    
    public function AssignClassToStudents( $input )
    {
        $sql = $this->db->prepare( "\n\t\t\tINSERT INTO\n\t\t\t\t{$this->p}ds_class_students\n\t\t\tSET\n\t\t\t\tclass_id = %s,\n\t\t\t\tstudent_id = %s,\n\t\t\t\t" . $this->sessionCondition, $input['class_id'], $input['student_id'] );
        $res = $this->db->query( $sql );
        return $this->db->last_result;
    }
    
    public function getClassStudentsByClassId( $id )
    {
        $id = (int) $id;
        
        if ( $id > 0 ) {
            $sql = $this->db->prepare( "\n\t\t\t\tSELECT cs . * , c.name AS classname, concat( s.firstname, ' ', s.lastname ) AS studentname\n\t\t\t\tFROM {$this->p}ds_class_students cs\n\t\t\t\tINNER JOIN {$this->p}ds_classes c ON cs.class_id = c.id\n\t\t\t\tINNER JOIN {$this->p}ds_students s ON cs.student_id = s.id\n\t\t\t\t\twhere\n\t\t\t\t\tcs.class_id=%s\n\t\t\t\t\tAND\n\t\t\t\t\t" . $this->sessionCondition, $id );
        } else {
            $sql = "SELECT\n\t\t\t\t\tcs . * ,\n\t\t\t\t\tc.name AS classname,\n\t\t\t\t\tconcat( s.firstname, ' ', s.lastname ) AS studentname,\n\t\t\t\t\tcs.week_day\n\t\t\t\tFROM\n\t\t\t\t\t{$this->p}ds_class_students cs\n\t\t\t\tINNER JOIN\n\t\t\t\t\t{$this->p}ds_classes c ON cs.class_id = c.id\n\t\t\t\tINNER JOIN\n\t\t\t\t\t{$this->p}ds_students s ON cs.student_id = s.id\n\t\t\t\tWHERE\n\t\t\t\t\t" . $this->sessionCondition . "\n\t\t\t\tORDER BY c.name, cs.week_day, s.lastname";
        }
        
        $this->db->query( $sql );
        $res = $this->db->last_result;
        $this->getWeekdays();
        foreach ( $res as $k => $class ) {
            $res[$k]->weekday_name = @$this->weekdays[$class->week_day];
        }
        return $res;
    }
    
    public function getClassesByStudentIds( $ids )
    {
        $idNum = count( $ids );
        $this->getWeekdays();
        $where = '';
        for ( $i = 1 ;  $i <= $idNum ;  $i++ ) {
            $where .= " student_id = %d OR";
        }
        $where = rtrim( $where, 'OR' );
        $sql = $this->db->prepare( "\n\n\t\t\t   SELECT\n\t\t\t\t\t   cs.class_id,\n\t\t\t\t\t   cs.student_id,\n\t\t\t\t\t   cs.week_day,\n\t\t\t\t\t   c.name,\n\t\t\t\t\t   c.category_id,\n\t\t\t\t\t   c.ages,\n\t\t\t\t\t   c.description,\n\t\t\t\t\t   c.startdate,\n\t\t\t\t\t   c.enddate,\n\t\t\t\t\t   c.days,\n\t\t\t\t\t   c.starttime,\n\t\t\t\t\t   c.endtime,\n\t\t\t\t\t   cc.category_name\n\t\t\t   FROM\n\t\t\t\t\t   {$this->p}ds_class_students cs\n\t\t\t   INNER JOIN\n\t\t\t\t\t   {$this->p}ds_classes c ON cs.class_id = c.id\n\t\t\t   INNER JOIN\n\t\t\t\t\t   {$this->p}ds_class_categories cc ON c.category_id = cc.id\n\t\t\t   WHERE\n\t\t\t\t\t   ( {$where} )\n\t\t\t   AND\n\t\t\t\t\t   c.is_parent_event = 1\n\t\t\t\tAND\n\t\t\t\t\tcs." . $this->sessionCondition, $ids );
        $this->db->query( $sql );
        $res = $this->db->last_result;
        //FIXME This is bad too. Arose when originally classes could be held on multiple days.
        //I've commented out old way of getting the weekday from old redundant column in student_classes table
        //Now all ways use the direct from classes table.
        //Multiple days for classes no longer supported and should be removed from code.
        foreach ( $res as $k => $class ) {
            //		   if ($class->week_day >= 1){
            //			   $res[$k]->weekday_name = $this->weekdays[$class->week_day];
            //		   }else{
            $days = json_decode( $class->days, 1 );
            $res[$k]->weekday = $days[0];
            //In case this variable is required anywhere else in code;
            $res[$k]->weekday_name = $this->weekdays[$days[0]];
            //		   }
        }
        return $res;
    }
    
    public function getClassesByParentId( $id )
    {
        $this->weekdays = $this->getWeekdays();
        $sql = "\n\n\t\t\t    SELECT\n\t\t\t\t\t    cs.class_id,\n\t\t\t\t\t    cs.student_id,\n\t\t\t\t\t    cs.week_day,\n\t\t\t\t\t    c.name,\n\t\t\t\t\t    c.category_id,\n\t\t\t\t\t    c.ages,\n\t\t\t\t\t    c.description,\n\t\t\t\t\t    c.startdate,\n\t\t\t\t\t    c.enddate,\n\t\t\t\t\t    c.days,\n\t\t\t\t\t    c.starttime,\n\t\t\t\t\t    c.endtime,\n\t\t\t\t\t    cc.category_name,\n\t\t\t\t\t    p.id AS parent_id\n\t\t\t    FROM\n\t\t\t\t\t    {$this->p}ds_class_students cs\n\t\t\t    INNER JOIN\n\t\t\t\t\t    {$this->p}ds_classes c ON cs.class_id = c.id\n\t\t\t    INNER JOIN\n\t\t\t\t\t    {$this->p}ds_class_categories cc ON c.category_id = cc.id\n\t\t\t    INNER JOIN\n\t\t\t\t\t {$this->p}ds_students s ON s.id = cs.student_id\n\t\t\t    INNER JOIN\n\t\t\t\t\t {$this->p}ds_parents p ON p.id = s.parent_id\n\n\t\t\t    WHERE\n\t\t\t\t\t    p.id = {$id}\n\t\t\t    AND\n\t\t\t\t\t    c.is_parent_event = 1\n\t\t\t    AND\n\t\t\t\t\t p.is_confirmed = 1\n\t\t\t\t AND\n\t\t\t\t\t cs." . $this->sessionCondition;
        $this->db->query( $sql );
        $res = $this->db->last_result;
        foreach ( $res as $k => $class ) {
            $days = json_decode( $class->days, 1 );
            $res[$k]->weekday = $days[0];
            //In case this variable is required anywhere else in code;
            $res[$k]->weekday_name = $this->weekdays[$days[0]];
        }
        return $res;
    }
    
    public function getParentIdsByClassId( $id )
    {
        $id = (int) $id;
        $sql = $this->db->prepare( "\n\n\t\t\t\t   SELECT\n\t\t\t\t\t\t   p.id AS parent_id\n\t\t\t\t   FROM\n\t\t\t\t\t\t   {$this->p}ds_class_students cs\n\t\t\t\t   INNER JOIN\n\t\t\t\t\t\t   {$this->p}ds_classes c ON cs.class_id = c.id\n\t\t\t\t   INNER JOIN\n\t\t\t\t\t\t   {$this->p}ds_class_categories cc ON c.category_id = cc.id\n\t\t\t\t   INNER JOIN\n\t\t\t\t\t\t{$this->p}ds_students s ON s.id = cs.student_id\n\t\t\t\t   INNER JOIN\n\t\t\t\t\t\t{$this->p}ds_parents p ON p.id = s.parent_id\n\n\t\t\t\t   WHERE\n\t\t\t\t\t\t   c.id = %d\n\t\t\t\t   AND\n\t\t\t\t\t\t   c.is_parent_event = 1\n\t\t\t\t   AND\n\t\t\t\t\t\tp.is_confirmed = 1\n\t\t\t\t\tAND p." . $this->sessionCondition, $id );
        $this->db->query( $sql );
        $res = $this->db->last_result;
        $ids = false;
        foreach ( $res as $r ) {
            $ids[] = $r->parent_id;
        }
        return $ids;
    }
    
    public function bulkDisableClasses( $ids )
    {
        foreach ( $ids as $id ) {
            $sql = $this->db->prepare( "\n\t\t\t\tUPDATE\n\t\t\t\t\t{$this->p}ds_classes\n\t\t\t\tSET\n\t\t\t\t\tis_registerable = 0\n\t\t\t\tWHERE\n\t\t\t\t\tid = %d\n\t\t\t\tAND " . $this->sessionCondition . "\n\t\t\t\tLIMIT 1\n\t\t\t", $id );
            $this->db->query( $sql );
        }
    }
    
    public function bulkEnableClasses( $ids )
    {
        foreach ( $ids as $id ) {
            $sql = $this->db->prepare( "\n\t\t\t\tUPDATE\n\t\t\t\t\t{$this->p}ds_classes\n\t\t\t\tSET\n\t\t\t\t\tis_registerable = 1\n\t\t\t\tWHERE\n\t\t\t\t\tid = %d\n\t\t\t\tAND " . $this->sessionCondition . "\n\t\t\t\tLIMIT 1\n\t\t\t", $id );
            $this->db->query( $sql );
        }
    }
    
    /**
     * Find a set of classes by a provided array of ids
     *
     * @param array $ids The ids of the classes to load
     * @return array
     */
    public function findByIds( array $classIds )
    {
        $classes = array();
        if ( empty($classIds) ) {
            return $classes;
        }
        $sql = "\n\t\t\tSELECT\n\t\t\t\t*\n\t\t\tFROM\n\t\t\t\t{$this->p}ds_classes\n\t\t\tWHERE\n\t\t\t\tis_parent_event = 1\n\t\t\tAND id IN (" . implode( ', ', $classIds ) . ")\n\n\t\t\tAND " . $this->sessionCondition;
        $this->db->query( $sql );
        $classes = $this->db->last_result;
        return $classes;
    }

}