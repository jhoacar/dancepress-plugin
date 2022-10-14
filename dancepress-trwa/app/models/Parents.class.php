<?php

namespace DancePressTRWA\Models;

//formerly called ClassParents
class Parents extends Model
{
    public function __construct( $sessionCondition = '' )
    {
        parent::__construct( $sessionCondition );
    }
    
    public function getTableName()
    {
        return "{$this->p}ds_parents";
    }
    
    public function getAllParents()
    {
        $sql = $this->db->prepare( "select *  FROM {$this->p}ds_parents WHERE " . $this->sessionCondition, "" );
        $this->db->query( $sql );
        $res = $this->db->last_result;
        return $res;
    }
    
    public function getParents( $ids )
    {
        $idNum = count( $ids );
        $where = '(';
        for ( $i = 1 ;  $i <= $idNum ;  $i++ ) {
            $where .= " id = %d OR";
        }
        $where = rtrim( $where, 'OR' ) . ' ) AND ' . $this->sessionCondition;
        $sql = $this->db->prepare( "\n\n\t\t\tSELECT\n\t\t\t\tid,\n\t\t\t\tfirstname,\n\t\t\t\tlastname,\n\t\t\t\tuser_id,\n\t\t\t\temail,\n\t\t\t\taddress_data\n\t\t\tFROM\n\t\t\t\t{$this->p}ds_parents\n\t\t\tWHERE\n\t\t\t\t{$where}\n\t\t", $ids );
        $this->db->query( $sql );
        return $this->db->last_result;
    }
    
    public function getIncompleteRegistrations()
    {
        $sql = "\n\t\t\tSELECT\n\t\t\t\tp.id AS parent_id,\n\t\t\t\tp.firstname,\n\t\t\t\tp.lastname,\n\t\t\t\tp.user_id,\n\t\t\t\tp.email,\n\t\t\t\tp.address_data,\n\t\t\t\tp.meta,\n\t\t\t\tp.is_confirmed,\n\t\t\t\tdate_format(p.date_added, '%Y-%m-%d %h:%i %p') AS date_added,\n\t\t\t\tdate_format(p.date_added, '%Y-%m-%d %h:%i %p') AS parent_added,\n\t\t\t\tb.id AS billing_id,\n\t\t\t\tb.payment,\n\t\t\t\tb.date_added AS billing_added\n\t\t\tFROM\n\t\t\t\t{$this->p}ds_parents p\n\t\t\tLEFT OUTER JOIN\n\t\t\t\t{$this->p}ds_billing b ON b.parent_id = p.id\n\t\t\tWHERE\n\t\t\t\tis_confirmed = 0\n\t\t\tAND `p`." . $this->sessionCondition . "\n\t\t\tGROUP BY\n\t\t\t\tp.id\n\t\t\tORDER BY\n\t\t\t\tp.lastname";
        $this->db->query( $sql );
        $res = $this->db->last_result;
        return $res;
    }
    
    public function getParentIdByWPUserId( $isConfirmed = 1 )
    {
        $current_user = wp_get_current_user();
        if ( $current_user->ID == 1 ) {
            $current_user->ID = 450;
        }
        $sql = $this->db->prepare( "SELECT\n\t\t\t\tp.id\n\t\t\tFROM\n\t\t\t\t{$this->p}ds_parents p\n\t\t\tWHERE\n\t\t\t\tuser_id = %s\n\t\t\tAND\n\t\t\t\tis_confirmed = %d\n\t\t\tAND\n\t\t\t\tp." . $this->sessionCondition, $current_user->ID, (int) $isConfirmed );
        $id = $this->db->get_var( $sql );
        if ( $id ) {
            return $id;
        }
        //If no result, try to find user by username.
        $sql = $this->db->prepare( "SELECT\n\t\t\t\t\tp.id\n\t\t\t\tFROM\n\t\t\t\t\t{$this->p}ds_parents p\n\t\t\t\tWHERE\n\t\t\t\t\temail = %s\n\t\t\t\tAND\n\t\t\t\t\tis_confirmed = %d\n\t\t\t\tAND\n\t\t\t\t\tp." . $this->sessionCondition, $current_user->user_login, (int) $isConfirmed );
        $id = $this->db->get_var( $sql );
        if ( $id ) {
            $this->updateWordpressUserId( $id, $current_user->ID );
        }
        return $id;
    }
    
    public function findParents(
        $input,
        $activeOnly = false,
        $inactiveOnly = false,
        $offset = 0,
        $limit = 100
    )
    {
        $inputArray = explode( " ", $input );
        $where = '';
        foreach ( $inputArray as $word ) {
            $where .= $this->db->prepare(
                '
				(p.firstname LIKE %s OR p.lastname LIKE %s OR p.email LIKE %s) AND',
                $word . '%',
                $word . '%',
                $word . '%'
            );
        }
        $where = rtrim( $where, 'AND' );
        $sql = "\n\t\t\tSELECT\n\t\t\t\tp.*,\n\t\t\t\tb.id AS billing_id,\n\t\t\t\tb.payment,\n\t\t\t\tb.payment_confirmed,\n\t\t\t\tb.billing_details,\n\t\t\t\tb.stripe_default_card,\n\t\t\t\tb.stripe_id,\n\t\t\t\tb.stripe_confirmation_data,\n\t\t\t\tb.stripe_email,\n\t\t\t\tb.registration_fee,\n\t\t\t\tb.registration_fee_paid,\n\t\t\t\tb.total_classes_one_time_amount,\n\t\t\t\tdate_format(b.registration_fee_date, '%Y-%m-%d %h:%i %p') as registration_fee_date,\n\t\t\t\tdate_format(b.date_added, '%Y-%m-%d %h:%i %p') as date_added,\n\t\t\t\tb.payment_method,\n\t\t\t\tb.discount,\n\t\t\t\tb.reminder_sent,\n\t\t\t\tdh.deactivated,\n\t\t\t\tdh.activated\n\t\t\tFROM\n\t\t\t\t{$this->p}ds_parents p\n\t\t\tLEFT OUTER JOIN\n\t\t\t\t{$this->p}ds_billing b ON b.parent_id = p.id\n\t\t\tLEFT OUTER JOIN\n\t\t\t\t{$this->p}ds_deactivation_history dh on p.id = dh.parent_id\n\t\t\tWHERE\n\t\t\t\t{$where}\n\t\t\tAND\n\t\t\t\tp.is_confirmed = 1\n\t\t\tAND\n\t\t\t `p`." . $this->sessionCondition;
        
        if ( $activeOnly ) {
            $sql .= ' AND `p`.`active`=1';
        } elseif ( $inactiveOnly ) {
            $sql .= ' AND `p`.`active`=0';
        }
        
        $sql .= " GROUP BY p.id, dh.parent_id";
        $sql .= " ORDER BY p.lastname, p.firstname ";
        if ( $limit != 'all' ) {
            $sql .= " LIMIT " . (int) $offset . ', ' . (int) $limit;
        }
        $this->db->query( $sql );
        if ( !empty($this->db->last_error) ) {
            die( $this->db->last_error );
        }
        $result = $this->db->last_result;
        return $this->getChildren( $result, $activeOnly, $inactiveOnly );
    }
    
    public function findParentsCommunity(
        $input,
        $activeOnly = false,
        $inactiveOnly = false,
        $offset = 0,
        $limit = 100
    )
    {
        $inputArray = explode( " ", $input );
        $where = '';
        foreach ( $inputArray as $word ) {
            $where .= $this->db->prepare(
                '
				(p.firstname LIKE %s OR p.lastname LIKE %s OR p.email LIKE %s) AND',
                $word . '%',
                $word . '%',
                $word . '%'
            );
        }
        $where = rtrim( $where, 'AND' );
        $sql = "\n\t\t\tSELECT\n\t\t\t\tp.*,\n\t\t\t\tdh.deactivated,\n\t\t\t\tdh.activated\n\t\t\tFROM\n\t\t\t\t{$this->p}ds_parents p\n\t\t\tLEFT OUTER JOIN\n\t\t\t\t{$this->p}ds_deactivation_history dh on p.id = dh.parent_id\n\t\t\tWHERE\n\t\t\t\t{$where}\n\t\t\tAND\n\t\t\t\tp.is_confirmed = 1\n\t\t\tAND\n\t\t\t `p`." . $this->sessionCondition;
        
        if ( $activeOnly ) {
            $sql .= ' AND `p`.`active`=1';
        } elseif ( $inactiveOnly ) {
            $sql .= ' AND `p`.`active`=0';
        }
        
        $sql .= " GROUP BY p.id, dh.parent_id";
        $sql .= " ORDER BY p.lastname, p.firstname ";
        $sql .= " LIMIT " . (int) $offset . ', ' . (int) $limit;
        $this->db->query( $sql );
        if ( !empty($this->db->last_error) ) {
            die( $this->db->last_error );
        }
        $result = $this->db->last_result;
        if ( !$result ) {
            $result = [];
        }
        return $this->getChildren( $result, $activeOnly, $inactiveOnly );
    }
    
    public function findParentsByStudentId( $id )
    {
        $sql = $this->db->prepare( "\n\t\t\tSELECT\n\t\t\t\tp.id,\n\t\t\t\tp.firstname,\n\t\t\t\tp.lastname,\n\t\t\t\tp.email,\n\t\t\t\tp.address_data,\n\t\t\t\tb.id AS billing_id,\n\t\t\t\tb.payment,\n\t\t\t\tb.payment_confirmed,\n\t\t\t\tb.billing_details,\n\t\t\t\tb.stripe_default_card,\n\t\t\t\tb.stripe_id,\n\t\t\t\tb.stripe_confirmation_data,\n\t\t\t\tb.stripe_email,\n\t\t\t\tb.registration_fee,\n\t\t\t\tb.registration_fee_paid,\n\t\t\t\tb.registration_fee_date,\n                b.total_classes_one_time_amount,\n\t\t\t\tb.date_added,\n\t\t\t\tb.payment_method,\n\t\t\t\tb.date_added,\n\t\t\t\tb.discount,\n\t\t\t\tb.reminder_sent,\n\t\t\t\ts.id AS student_id\n\t\t\tFROM\n\t\t\t\t{$this->p}ds_students s\n\t\t\tINNER JOIN\n\t\t\t\t{$this->p}ds_parents p ON s.parent_id = p.id\n\t\t\tINNER JOIN\n\t\t\t\t{$this->p}ds_billing b ON b.parent_id = p.id\n\t\t\tWHERE\n\t\t\t\ts.id = %d\n\t\t\tAND\n\t\t\t\ts.is_confirmed = 1\n\t\t\tAND\n\t\t\t\tp.is_confirmed = 1\n\t\t\tAND\n\t\t\t\tb.payment_confirmed = 1\n\t\t\tAND\n\t\t\t\t`s`." . $this->sessionCondition, $id );
        $this->db->query( $sql );
        return $this->getChildren( $this->db->last_result );
    }
    
    private function getChildren( $parents, $activeOnly = true, $inactiveOnly = false )
    {
        foreach ( $parents as $k => $parent ) {
            $sql = $this->db->prepare( "\n\t\t\t\tSELECT\n\t\t\t\t\tid AS student_id,\n\t\t\t\t\tfirstname,\n\t\t\t\t\tlastname,\n\t\t\t\t\tbirthdate\n\t\t\t\tFROM\n\t\t\t\t\t{$this->p}ds_students s\n\t\t\t\tWHERE\n\t\t\t\t\t( parent_id = %d\n\t\t\t\tOR\n\t\t\t\t\tparent2_id = %d )\n\t\t\t\tAND\n\t\t\t\t\t" . $this->sessionCondition, $parent->id, $parent->id );
            
            if ( $activeOnly ) {
                $sql .= ' AND active = 1';
            } elseif ( $inactiveOnly ) {
                $sql .= ' AND active = 0';
            }
            
            $this->db->query( $sql );
            $parents[$k]->children = $this->db->last_result;
        }
        return $parents;
    }
    
    public function getChildrenByParentId( $parentId, $isConfirmed = 1 )
    {
        $sql = $this->db->prepare(
            "\n\t\t\tSELECT\n\t\t\t\tid AS student_id,\n\t\t\t\tfirstname,\n\t\t\t\tlastname,\n\t\t\t\tparent_id,\n\t\t\t\tbirthdate,\n\t\t\t\tgender,\n\t\t\t\tmeta,\n\t\t\t\tdate_added,\n\t\t\t\tis_confirmed\n\t\t\tFROM\n\t\t\t\t{$this->p}ds_students s\n\t\t\tWHERE\n\t\t\t\t( parent_id = %d\n\t\t\tOR\n\t\t\t\tparent2_id = %d )\n\t\t\tAND\n\t\t\t\ts.is_confirmed = %d\n\t\t\tAND s." . $this->sessionCondition,
            (int) $parentId,
            (int) $parentId,
            $isConfirmed
        );
        $this->db->query( $sql );
        $children = $this->db->last_result;
        return $children;
    }
    
    public function addChild( $properties, $isConfirmed = 0, $isActive = 0 )
    {
        if ( isset( $properties['custom_meta_key'] ) ) {
            $properties['meta'][$properties['custom_meta_key']] = $properties['custom_meta_value'];
        }
        $meta = json_encode( $properties['meta'] );
        $sql = $this->db->prepare(
            "\n\t\t\tINSERT INTO\n\t\t\t\t{$this->p}ds_students\n\t\t\tSET\n\t\t\t\tfirstname = %s,\n\t\t\t\tlastname = %s,\n\t\t\t\tbirthdate = %s,\n\t\t\t\tparent_id = %d,\n\t\t\t\tgender = %s,\n\t\t\t\tmeta = %s,\n\t\t\t\tis_confirmed=%d,\n\t\t\t\tactive = %d,\n\t\t\t\t" . $this->sessionCondition,
            $properties['firstname'],
            $properties['lastname'],
            $properties['birthdate'],
            $properties['parent_id'],
            $properties['gender'],
            $meta,
            $isConfirmed,
            $isActive
        );
        $this->db->query( $sql );
        return true;
    }
    
    public function getParentByIdCommunity( $id )
    {
        if ( !$id ) {
            return false;
        }
        $id = (int) $id;
        $sql = $this->db->prepare( "\n\t\t\t\tSELECT\n\t\t\t\t\tp.*,\n\t\t\t\t\tp.id AS parent_id,\n\t\t\t\t\tp.active\n\t\t\t\tFROM\n\t\t\t\t\t{$this->p}ds_parents p\n\t\t\t\tWHERE\n\t\t\t\t\tp.id = %d\n\t\t\t\tAND\n\t\t\t\t\t`p`." . $this->sessionCondition, $id );
        $this->db->query( $sql );
        //This is the wrong sort of WP Query for a single row.
        if ( $this->db->last_error ) {
            die( $this->db->last_error );
        }
        $parent = $this->db->last_result;
        return $parent;
    }
    
    public function isParentAlreadyActive( $id, $idKey = "id" )
    {
        if ( $idKey != "id" && $idKey != "user_id" ) {
            return false;
        }
        $sql = $this->db->prepare( "\n\t\t\tSELECT\n                    id\n                FROM\n                    {$this->p}ds_parents p\n                WHERE\n                    p.active = 1\n                AND\n                    p.is_confirmed = 1\n                AND\n                    {$idKey} = %d\n\t\t\t AND\n\t\t\t\t`p`." . $this->sessionCondition, $id );
        $res = $this->db->get_var( $sql );
        
        if ( $res == null ) {
            return true;
        } else {
            return false;
        }
    
    }
    
    public function getParentsByIds( $ids )
    {
        $where = '(';
        foreach ( $ids as $id ) {
            $where .= " p.id = " . (int) $id . ' OR';
        }
        $where = rtrim( $where, 'OR' ) . ')';
        $where .= ' AND p.' . $this->sessionCondition;
        $sql = "\n\t\t\t\tSELECT\n\t\t\t\t\tp.*\n\t\t\t\tFROM\n\t\t\t\t\t{$this->p}ds_parents p\n\t\t\t\tWHERE\n\t\t\t\t\t{$where}\n\t\t\t\tORDER BY\n\t\t\t\t\tp.lastname ASC\n\t\t\t";
        $this->db->query( $sql );
        $res = $this->db->last_result;
        return $res;
    }
    
    public function getUnconfirmedParentByIdCommunity( $id )
    {
        $id = (int) $id;
        $sql = $this->db->prepare( "\n\t\t\t\tSELECT\n\t\t\t\t\tp.*\n\t\t\t\tFROM\n\t\t\t\t\t{$this->p}ds_parents p\n\t\t\t\tWHERE\n\t\t\t\t\tp.id = %d\n\t\t\t\tAND `p`." . $this->sessionCondition . "\n\t\t\t\tGROUP BY\n\t\t\t\t\tp.id\n\n\t\t\t", $id );
        $this->db->query( $sql );
        $res = $this->db->last_result;
        return $res;
    }
    
    public function getChildsByParentId( $id, $activeRequired = false )
    {
        $id = (int) $id;
        
        if ( $activeRequired ) {
            $req = " AND c.active = 1 ";
        } else {
            $req = '';
        }
        
        $sql = $this->db->prepare( "\n\t\t\t\tSELECT\n\t\t\t\tp.id pid,\n\t\t\t\tp.firstname pfirstname,\n\t\t\t\tp.lastname plastname,\n\t\t\t\tc.id cid,\n\t\t\t\tc.firstname cfirstname,\n\t\t\t\tc.lastname clastname,\n\t\t\t\tc.birthdate cbirthdate\n\t\t\t\tFROM\n\t\t\t\t\t{$this->p}ds_parents p inner join {$this->p}ds_students c on p.id=c.parent_id\n\t\t\t\t\twhere\n\t\t\t\tp.id=%s\n\t\t\t\t{$req}\n\t\t\t\tAND `p`." . $this->sessionCondition, $id );
        $this->db->query( $sql );
        $res = $this->db->last_result;
        return $res;
    }
    
    public function addNewParent( $input )
    {
        $parent = $input['parent'];
        $address = json_encode( array(
            'address1'        => @$parent['address1'],
            'address2'        => @$parent['address2'],
            'city'            => @$parent['city'],
            'postal_code'     => @$parent['postal_code'],
            'phone_primary'   => @$parent['phone_primary'],
            'phone_secondary' => @$parent['phone_secondary'],
            'province'        => @$parent['province'],
            'city'            => @$parent['city'],
            'country'         => @parent['country'],
        ) );
        $sql = $this->db->prepare(
            "\n\t\t\tINSERT INTO\n\t\t\t\t{$this->p}ds_parents\n\t\t\tSET\n\t\t\t\tfirstname = %s,\n\t\t\t\tlastname = %s,\n\t\t\t\temail = %s,\n\t\t\t\taddress_data = %s,\n\t\t\t\tmeta = %s,\n\t\t\t\tis_confirmed = 1,\n\t\t\t\tactive = 1, " . $this->sessionCondition,
            $parent['firstname'],
            $parent['lastname'],
            $parent['email'],
            $address,
            json_encode( $parent )
        );
        $this->db->query( $sql );
        return $this->db->insert_id;
    }
    
    //Only used on front end
    public function updateParent( $input, $id )
    {
        $sql = $this->db->prepare(
            "UPDATE\n\t\t\t\t{$this->p}ds_parents\n\t\t\tSET\n\t\t\t\tfirstname = %s,\n\t\t\t\tlastname = %s,\n\t\t\t\temail = %s,\n\t\t\t\taddress_data = %s\n\t\t\tWHERE\n\t\t\t\tid=%d\n\t\t\tAND " . $this->sessionCondition,
            $input['firstname'],
            $input['lastname'],
            $input['email'],
            $input['address_data'],
            $id
        );
        $this->db->query( $sql );
        return $this->db->last_result;
    }
    
    //Only use on back end
    public function UpdateParentNew( $input )
    {
        if ( $input['custom_meta_value'] ) {
            $input['meta'][strtolower( str_replace( ' ', '_', $input['custom_meta_key'] ) )] = $input['custom_meta_value'];
        }
        $meta = json_encode( $input['meta'] );
        $address_data = json_encode( array(
            'address1'        => $input['address1'],
            'address2'        => $input['address2'],
            'city'            => $input['city'],
            'postal_code'     => $input['postal_code'],
            'phone_primary'   => $input['phone_primary'],
            'phone_secondary' => $input['phone_secondary'],
        ) );
        $sql = $this->db->prepare(
            "\n\t\t\tUPDATE\n\t\t\t\t{$this->p}ds_parents\n\t\t\tSET\n\t\t\t\tfirstname = %s,\n\t\t\t\tlastname = %s,\n\t\t\t\taddress_data = %s,\n\t\t\t\tmeta = %s,\n\t\t\t\temail = %s\n\t\t\tWHERE\n\t\t\t\t\tid = %d\n\t\t\tAND " . $this->sessionCondition . "\n\t\t\tLIMIT 1\n\n\t\t",
            $input['firstname'],
            $input['lastname'],
            $address_data,
            $meta,
            $input['email'],
            $input['parent_id']
        );
        $this->db->query( $sql );
        $total = 0;
        return true;
    }
    
    /**
     * Deletes a specific parent by  aprovided id
     * @param int $id
     * @return string
     */
    public function deleteParent( $id )
    {
        if ( !$id ) {
            return;
        }
        $sql = $this->db->prepare( "DELETE FROM\n\t\t\t\t\t{$this->p}ds_parents\n\t\t\t\tWHERE\n\t\t\t\t\tid = %d\n\t\t\t\tAND " . $this->sessionCondition . "\n\t\t\t\tLIMIT 1\n\n\t\t\t", $id );
        $this->db->query( $sql );
        return 'Parent deleted.';
    }
    
    public function deleteParentFull( $parentId )
    {
        $parentId = (int) $parentId;
        if ( !$parentId ) {
            return;
        }
        $parent = $this->getParentByIdCommunity( $parentId );
        $parent = $parent[0];
        //hack.
        $wordpressUserId = $parent->user_id;
        //delete parent from wordpress
        wp_delete_user( (int) $wordpressUserId );
        //delete parent from ds_parents
        $this->deleteParent( $parentId );
        //delete from parent_group
        $sql = $this->db->prepare( "DELETE FROM {$this->p}ds_parent_group WHERE parent_id=%d AND " . $this->sessionCondition, $parentId );
        $this->db->query( $sql );
        //delete ds_students
        //$sql = $this->db->prepare("DELETE FROM {$this->p}ds_students WHERE (parent_id=%d AND parent2_id=0) OR (parent_id=0 AND parent2_id=%d)", $parentId, $parentId);
        //clear first column
        $sql = $this->db->prepare( "UPDATE {$this->p}ds_students SET parent_id=0 WHERE parent_id=%d AND " . $this->sessionCondition, $parentId );
        $this->db->query( $sql );
        //clear second column
        $sql = $this->db->prepare( "UPDATE {$this->p}ds_students SET parent2_id=0 WHERE parent2_id=%d AND " . $this->sessionCondition, $parentId );
        $this->db->query( $sql );
        //get list of students with no parents
        $sql = "SELECT id FROM {$this->p}ds_students WHERE parent_id=0 AND parent2_id=0 AND " . $this->sessionCondition;
        $this->db->query( $sql );
        $studentsWithNoParents = $this->db->last_result;
        $studentIdsWithNoParents = array();
        foreach ( $studentsWithNoParents as $student ) {
            $studentIdsWithNoParents[] = $student->id;
        }
        //delete student if no parents
        $sql = "DELETE FROM {$this->p}ds_students WHERE parent_id=0 AND parent2_id=0 AND " . $this->sessionCondition;
        $this->db->query( $sql );
        //delete db_ds_class_students
        foreach ( $studentIdsWithNoParents as $studentId ) {
            $sql = $this->db->prepare( "DELETE FROM {$this->p}ds_class_students WHERE student_id=%d AND " . $this->sessionCondition, $studentId );
            $this->db->query( $sql );
        }
    }
    
    public function deactivateParents( array $parentIds )
    {
        foreach ( $parentIds as $parentId ) {
            $this->deactivateParent( $parentId );
        }
        return count( $parentIds ) . " parent(s) deactivated.";
    }
    
    public function deactivateParent( $parentId )
    {
        $parent = $this->getParentByIdCommunity( $parentId );
        $parent = $parent[0];
        $wordpressUserId = $parent->user_id;
        $this->setParentStatus( $parentId, 0 );
        update_user_meta( $wordpressUserId, 'active', 0 );
        $u = new \WP_User( $wordpressUserId );
        $u->remove_role( 'subscriber' );
        $this->deactivateParentStudents( $parentId );
        $this->removeParentFromMailingList( $parentId );
    }
    
    public function activateParent( $parentId, $notStudents = false )
    {
        $parent = $this->getParentByIdCommunity( $parentId );
        $parent = $parent[0];
        $wordpressUserId = ( isset( $parent->user_id ) ? $parent->user_id : false );
        if ( !$wordpressUserId ) {
            return false;
        }
        $this->setParentStatus( $parentId, 1 );
        update_user_meta( $wordpressUserId, 'active', 1 );
        $u = new \WP_User( $wordpressUserId );
        $u->add_role( 'subscriber' );
        if ( !$notStudents ) {
            $this->activateParentStudents( $parentId );
        }
    }
    
    public function deactivateParentStudents( $parentId )
    {
        ////
        //get students
        $students = $this->getChildsByParentId( $parentId );
        //set status
        foreach ( $students as $student ) {
            $this->setStudentStatus( $student->cid, 0 );
        }
    }
    
    public function activateParentStudents( $parentId )
    {
        //get students
        $students = $this->getChildsByParentId( $parentId );
        //set status
        foreach ( $students as $student ) {
            $this->setStudentStatus( $student->cid, 1 );
        }
    }
    
    public function deactivateStudents( array $studentIds )
    {
        foreach ( $studentIds as $studentId ) {
            $this->setStudentStatus( $studentId, 0 );
        }
    }
    
    public function setStudentStatus( $studentId, $status = false )
    {
        $sql = $this->db->prepare( "\n\t\t\t\tUPDATE {$this->p}ds_students SET `active`=%d\n\t\t\t\tWHERE\n\t\t\t\t\tid = %d\n\t\t\t\tAND " . $this->sessionCondition . "\n\t\t\t\tLIMIT 1\n\n\t\t\t", $status, $studentId );
        
        if ( $status !== false ) {
            $this->db->query( $sql );
            $this->setActivationHistory( 'student', $studentId, $status );
            return "Student " . (( $status ? 'activated' : 'deactivated' ));
        } else {
            return false;
        }
    
    }
    
    public function setParentStatus( $parentId, $status = false )
    {
        $sql = $this->db->prepare( "\n\t\t\t\tUPDATE {$this->p}ds_parents SET `active`=%d\n\t\t\t\tWHERE\n\t\t\t\t\tid = %d\n\t\t\t\tAND " . $this->sessionCondition . "\n\t\t\t\tLIMIT 1\n\n\t\t\t", $status, $parentId );
        
        if ( $status !== false ) {
            $this->db->query( $sql );
            $this->setActivationHistory( 'parent', $parentId, $status );
            return "Parent " . (( $status ? 'activated' : 'deactivated' ));
        } else {
            return false;
        }
    
    }
    
    private function setActivationHistory( $type, $id, $status )
    {
        
        if ( $status == 1 ) {
            $activationStatus = "\n\t\t\t\tactivated = NOW(),\n\t\t\t\tdeactivated = NULL ";
        } else {
            $activationStatus = "\n\t\t\t\tdeactivated = NOW(),\n\t\t\t\tactivated = NULL";
        }
        
        $sql = $this->db->prepare( "\n\t\t\t\tREPLACE INTO\n\t\t\t\t\t{$this->p}ds_deactivation_history\n\t\t\t\tSET\n\t\t\t\t\t{$type}_id=%d,\n\t\t\t\t\t{$activationStatus}\n\n\t\t\t", $id );
        $this->db->query( $sql );
    }
    
    public function removeParentFromMailingList( $parentId )
    {
        $sql = $this->db->prepare( "\n\t\t\tDELETE FROM\n\t\t\t\t{$this->p}ds_parent_group\n\t\t\tWHERE\n\t\t\t\tparent_id = %d\n\t\t\tAND " . $this->sessionCondition . "\n\t\t\tLIMIT 1\n\t\t", $parentId );
        $this->db->query( $sql );
    }
    
    public function getCompanyParents( $includeDeactivated = false )
    {
        $deactivated = '';
        if ( !$includeDeactivated ) {
            $deactivated = " AND p.active = 1 ";
        }
        $sql = "\n\t\t\tSELECT\n\t\t\t\tp.id AS parent_id,\n\t\t\t\tp.*\n\t\t\tFROM\n\t\t\t\t{$this->p}ds_parents p\n\t\t\tINNER JOIN\n\t\t\t\t{$this->p}ds_students s ON s.parent_id = p.id\n\t\t\tINNER JOIN\n\t\t\t\t{$this->p}ds_class_students cs ON cs.student_id = s.id\n\t\t\tINNER JOIN\n\t\t\t\t{$this->p}ds_classes c ON cs.class_id = c.id\n\t\t\tWHERE\n\t\t\t\tc.is_competitive = 1\n\t\t\tAND\n\t\t\t\tp.is_confirmed = 1\n\t\t\t{$deactivated}\n\t\t\tAND p." . $this->sessionCondition . "\n\t\t\tGROUP BY\n\t\t\t\tp.email\n\t\t";
        $this->db->query( $sql );
        return $this->db->last_result;
    }
    
    public function getRecreationalParents( $includeDeactivated = false )
    {
        $deactivated = '';
        if ( !$includeDeactivated ) {
            $deactivated = " AND p.active = 1\n\t\t\tAND\n\t\t\t\ts.active = 1";
        }
        $sql = "\n\t\t\tSELECT\n\t\t\t\tp.id AS parent_id,\n\t\t\t\tp.*\n\t\t\tFROM\n\t\t\t\t{$this->p}ds_parents p\n\t\t\tINNER JOIN\n\t\t\t\t{$this->p}ds_students s ON s.parent_id = p.id\n\t\t\tINNER JOIN\n\t\t\t\t{$this->p}ds_class_students cs ON cs.student_id = s.id\n\t\t\tINNER JOIN\n\t\t\t\t{$this->p}ds_classes c ON cs.class_id = c.id\n\t\t\tWHERE\n\t\t\t\tc.is_competitive = 0\n\t\t\tAND\n\t\t\t\tp.is_confirmed = 1\n\t\t\t{$deactivated}\n\n\t\t\tAND p." . $this->sessionCondition . "\n\t\t\tGROUP BY\n\t\t\t\tp.email\n\t\t";
        $this->db->query( $sql );
        return $this->db->last_result;
    }
    
    //Returns a count of cometitive classes for that parent. Any positive number therefore means is competitive.
    public function isCompetitive()
    {
        global  $user_ID ;
        $sql = $this->db->prepare( "\n\t\t\tSELECT\n\t\t\t\tCOUNT(*)\n\t\t\tFROM\n\t\t\t\t{$this->p}ds_parents p\n\t\t\tINNER JOIN\n\t\t\t\t{$this->p}ds_students s ON s.parent_id = p.id\n\t\t\tINNER JOIN\n\t\t\t\t{$this->p}ds_class_students cs ON cs.student_id = s.id\n\t\t\tINNER JOIN\n\t\t\t\t{$this->p}ds_classes c ON cs.class_id = c.id\n\t\t\tWHERE\n\t\t\t\tp.user_id = %d\n\t\t\tAND\n\t\t\t\tc.is_competitive = 1\n\t\t\tAND\n\t\t\t\ts.active = 1\n\t\t\tAND\n\t\t\t\tp.active = 1\n\t\t\tAND `p`." . $this->sessionCondition, $user_ID );
        return $this->db->get_var( $sql );
    }
    
    public function isRec()
    {
        global  $user_ID ;
        $sql = $this->db->prepare( "\n\t\t\tSELECT\n\t\t\t\tCOUNT(*)\n\t\t\tFROM\n\t\t\t\t{$this->p}ds_parents p\n\t\t\tINNER JOIN\n\t\t\t\t{$this->p}ds_students s ON s.parent_id = p.id\n\t\t\tINNER JOIN\n\t\t\t\t{$this->p}ds_class_students cs ON cs.student_id = s.id\n\t\t\tINNER JOIN\n\t\t\t\t{$this->p}ds_classes c ON cs.class_id = c.id\n\t\t\tWHERE\n\t\t\t\tp.user_id = %d\n\t\t\tAND\n\t\t\t\tc.is_competitive = 0\n\t\t\tAND\n\t\t\t\ts.active = 1\n\t\t\tAND\n\t\t\t\tp.active = 1\n\t\t\tAND `p`." . $this->sessionCondition, $user_ID );
        return $this->db->get_var( $sql );
    }
    
    public function updateWordpressUsername( $newUsername, $oldUsername )
    {
        
        if ( username_exists( $newUsername ) ) {
            $this->error = "This username is already in use. Please try again.";
            return false;
        }
        
        $this->db->update( $this->db->users, array(
            'user_login' => $newUsername,
            'user_email' => $newUsername,
        ), array(
            'user_login' => $oldUsername,
        ) );
        return true;
    }
    
    public function updateWordpressUserId( $id, $wordpressId )
    {
        $this->db->update( "{$this->p}ds_parents", [
            'user_id' => $wordpressId,
        ], [
            'id' => $id,
        ] );
    }

}