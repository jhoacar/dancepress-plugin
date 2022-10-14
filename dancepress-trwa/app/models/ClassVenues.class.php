<?php
namespace DancePressTRWA\Models;

class ClassVenueAddressData
{
    public $address1;
    public $address2;
    public $city;
    public $postal_code;
    public $phone;
}

class ClassVenues extends Model
{
    public function __construct($sessionCondition = '')
    {
        parent::__construct($sessionCondition);
    }

    public function getAllVenues()
    {
        $sql = "
			SELECT
				{$this->p}ds_venues.*,
				COUNT({$this->p}ds_events.venue_id) as total_upcoming_events
			FROM
				{$this->p}ds_venues
				
			LEFT JOIN {$this->p}ds_events ON ({$this->p}ds_events.venue_id = {$this->p}ds_venues.id AND {$this->p}ds_events.starts > NOW())
			
			GROUP BY {$this->p}ds_venues.id
			
			ORDER BY
				name";

        $this->db->query($sql);
        $res = $this->db->last_result;
        
        foreach ($res as &$venue) {
            $venue->meta = json_decode($venue->meta);
            $venue->address_data = json_decode($venue->address_data);
        }
        
        return $res;
    }
    
    public function findVenues(array $input)
    {
        $sql = "
			SELECT
				
				{$this->p}ds_venues.*,
				
				COUNT({$this->p}ds_events.venue_id) as total_upcoming_events
			FROM
				{$this->p}ds_venues
				
			LEFT JOIN {$this->p}ds_events ON ({$this->p}ds_events.venue_id = {$this->p}ds_venues.id AND {$this->p}ds_events.starts > NOW()) ";
            
        if (!empty($input['search'])) {
            $sql .= "WHERE LOWER({$this->p}ds_venues.name) LIKE LOWER('%{$input['search']}%') OR LOWER({$this->p}ds_venues.address_data) LIKE LOWER('%{$input['search']}%') OR LOWER({$this->p}ds_venues.meta LIKE '%{$input['search']}%')";
        }
            
        $sql.= " GROUP BY {$this->p}ds_venues.id
			
		      ORDER BY
		      
				{$this->p}ds_events.name ASC";
        

        $this->db->query($sql);
        $res = $this->db->last_result;
        
        foreach ($res as &$venue) {
            $venue->meta = json_decode($venue->meta);
            $venue->address_data = json_decode($venue->address_data);
        }
        
        return $res;
    }
    
    public function getVenueById($id)
    {
        if (empty($id)) {
            return null;
        }

        $id = (int)$id;
        $sql = $this->db->prepare(
            "
				SELECT 
					*
				FROM 
					{$this->p}ds_venues  
				WHERE		
					id = %s ",
            $id
        );

        
        $this->db->query($sql);
        $venue = $this->db->last_result;
        $venue = $venue[0];
        $venue->address_data = json_decode($venue->address_data);
        $venue->meta = json_decode($venue->meta);
        
        if (empty($venue->address_data)) {
            $venue->address_data = new ClassVenueAddressData;
        }

        return $venue;
    }
    
    public function addVenue($input)
    {
        if (!empty($input['custom_meta_key'])) {
            $input['meta'][strtolower(str_replace(' ', '_', $input['custom_meta_key']))] = $input['custom_meta_value'];
        }
        
        $meta = json_encode(!empty($input['meta']) ? $input['meta'] : array());
        $address_data = json_encode(
            array(
                    'address1' => $input['address1'],
                    'address2' => $input['address2'],
                    'city' => $input['city'],
                    'postal_code' => $input['postal_code'],
                    'phone' => $input['phone']
                )
        );
        
        
        $sql = $this->db->prepare("
			INSERT INTO
				{$this->p}ds_venues
			(name, address_data, meta)
			VALUES (%s, %s, %s) ", $input['name'], $address_data, $meta);

        $this->db->query($sql);
        return $this->db->insert_id;
    }
    
    public function updateVenue($input, $id)
    {
        if (!empty($input['custom_meta_key'])) {
            $input['meta'][strtolower(str_replace(' ', '_', $input['custom_meta_key']))] = $input['custom_meta_value'];
        }
    
        $meta = json_encode(!empty($input['meta']) ? $input['meta'] : array());
        $address_data = json_encode(
            array(
                    'address1' => $input['address1'],
                    'address2' => $input['address2'],
                    'city' => $input['city'],
                    'postal_code' => $input['postal_code'],
                    'phone' => $input['phone']
                )
        );
        
        
        $sql = $this->db->prepare("
			UPDATE
				{$this->p}ds_venues
			SET
				name = %s,
				address_data = %s,
				meta = %s
			WHERE 
				id= %d ", $input['name'], $address_data, $meta, $id);

        $this->db->query($sql);
        return true;
    }
    
    public function deleteVenue($id)
    {
        if (empty($id)) {
            return null;
        }

        $sql = $this->db->prepare(
            "
				DELETE FROM 
					{$this->p}ds_venues
				WHERE
					id = %d
		
				LIMIT 1",
            $id
        );

        $this->db->query($sql);
        return true;
    }
    
    public function venueExists($venueName)
    {
        if (!$venueName) {
            return false;
        }
        

        $sql = $this->db->prepare("SELECT *
				FROM
					{$this->p}ds_venues
				WHERE
					name = %s ", $venueName);
                    
                    
        $this->db->query($sql);
        $venue = $this->db->last_result;
        
        if ($venue) {
            return true;
        }
        
        return false;
    }
}
