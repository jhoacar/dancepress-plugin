<?php
namespace DancePressTRWA\Models;

class ClassEvents extends Model
{
    public function __construct($sessionCondition = '')
    {
        parent::__construct($sessionCondition);
    }

    public function getAllEvents()
    {
        $sql = "
			SELECT
				{$this->p}ds_events.*,
				{$this->p}ds_venues.name as venue_name,
				(SUM(IFNULL({$this->p}ds_event_ticket_sales.quantity, 0))) as tickets_sold,
				(max_tickets - COUNT({$this->p}ds_event_ticket_sales.event_id)) as tickets_available,
				date_format(starts, '%Y-%m-%d %h:%i %p') as starts,
				date_format(ends, '%Y-%m-%d %h:%i %p') as ends
			FROM
				{$this->p}ds_events

			LEFT JOIN {$this->p}ds_venues ON ({$this->p}ds_venues.id = {$this->p}ds_events.venue_id)

			LEFT JOIN {$this->p}ds_event_ticket_sales ON ({$this->p}ds_event_ticket_sales.event_id = {$this->p}ds_events.id)

			GROUP BY {$this->p}ds_events.id

			ORDER BY
				{$this->p}ds_events.name ASC";

        $this->db->query($sql);
        $res = $this->db->last_result;
        //die($sql);
        foreach ($res as &$event) {
            $event->meta = json_decode($event->meta);
        }

        return $res;
    }

    public function getUpcomingEvents()
    {
        $sql = "
			SELECT
				{$this->p}ds_events.*,
				{$this->p}ds_venues.name as venue_name,
				(SUM(IFNULL({$this->p}ds_event_ticket_sales.quantity, 0))) as tickets_sold,
				(max_tickets - COUNT({$this->p}ds_event_ticket_sales.event_id)) as tickets_available,
				date_format(starts, '%Y-%m-%d %h:%i %p') as starts,
				date_format(ends, '%Y-%m-%d %h:%i %p') as ends
			FROM
				{$this->p}ds_events

			LEFT JOIN {$this->p}ds_venues ON ({$this->p}ds_venues.id = {$this->p}ds_events.venue_id)

			LEFT JOIN {$this->p}ds_event_ticket_sales ON ({$this->p}ds_event_ticket_sales.event_id = {$this->p}ds_events.id)

			WHERE
				{$this->p}ds_events.starts > NOW()

			GROUP BY {$this->p}ds_events.id

			ORDER BY
				{$this->p}ds_events.name ASC";

        $this->db->query($sql);
        $res = $this->db->last_result;

        foreach ($res as &$event) {
            $event->meta = json_decode($event->meta);
        }

        return $res;
    }

    public function getPastEvents()
    {
        $sql = "
			SELECT
				{$this->p}ds_events.*,
				{$this->p}ds_venues.name as venue_name,
				(SUM(IFNULL({$this->p}ds_event_ticket_sales.quantity, 0))) as tickets_sold,
				(max_tickets - COUNT({$this->p}ds_event_ticket_sales.event_id)) as tickets_available,
				date_format(starts, '%Y-%m-%d %h:%i %p') as starts,
				date_format(ends, '%Y-%m-%d %h:%i %p') as ends
			FROM
				{$this->p}ds_events

			LEFT JOIN {$this->p}ds_venues ON ({$this->p}ds_venues.id = {$this->p}ds_events.venue_id)

			LEFT JOIN {$this->p}ds_event_ticket_sales ON ({$this->p}ds_event_ticket_sales.event_id = {$this->p}ds_events.id)

			WHERE
				{$this->p}ds_events.ends < NOW()

			GROUP BY {$this->p}ds_events.id

			ORDER BY
		{$this->p}ds_events.name ASC";

        $this->db->query($sql);
        $res = $this->db->last_result;

        foreach ($res as &$event) {
            $event->meta = json_decode($event->meta);
        }

        return $res;
    }

    public function getCurrentEvents()
    {
        $sql = "
			SELECT
				{$this->p}ds_events.*,
				{$this->p}ds_venues.name as venue_name,
				(SUM(IFNULL({$this->p}ds_event_ticket_sales.quantity, 0))) as tickets_sold,
				(max_tickets - COUNT({$this->p}ds_event_ticket_sales.event_id)) as tickets_available,
				date_format(starts, '%Y-%m-%d %h:%i %p') as starts,
				date_format(ends, '%Y-%m-%d %h:%i %p') as ends
			FROM
				{$this->p}ds_events

			LEFT JOIN {$this->p}ds_venues ON ({$this->p}ds_venues.id = {$this->p}ds_events.venue_id)

			LEFT JOIN {$this->p}ds_event_ticket_sales ON ({$this->p}ds_event_ticket_sales.event_id = {$this->p}ds_events.id)

			WHERE
				{$this->p}ds_events.starts < NOW() AND {$this->p}ds_events.ends > NOW()

			GROUP BY {$this->p}ds_events.id

			ORDER BY
			{$this->p}ds_events.name ASC";

        $this->db->query($sql);
        $res = $this->db->last_result;

        foreach ($res as &$event) {
            $event->meta = json_decode($event->meta);
        }

        return $res;
    }


    public function findEvents($input)
    {
        $sql = "
			SELECT

				{$this->p}ds_events.*,
				{$this->p}ds_venues.name as venue_name,
				(SUM(IFNULL({$this->p}ds_event_ticket_sales.quantity, 0))) as tickets_sold,
				(max_tickets - COUNT({$this->p}ds_event_ticket_sales.event_id)) as tickets_available,
				date_format(starts, '%Y-%m-%d %h:%i %p') as starts,
				date_format(ends, '%Y-%m-%d %h:%i %p') as ends

			FROM
				{$this->p}ds_events

			LEFT JOIN {$this->p}ds_venues ON ({$this->p}ds_venues.id = {$this->p}ds_events.venue_id)

			LEFT JOIN {$this->p}ds_event_ticket_sales ON ({$this->p}ds_event_ticket_sales.event_id = {$this->p}ds_events.id) ";

        if (!empty($input['search'])) {
            $sql .= "WHERE LOWER({$this->p}ds_events.name) LIKE LOWER('%{$input['search']}%') OR LOWER({$this->p}ds_events.meta) LIKE LOWER('%{$input['search']}%'))";
        }

        $sql.= "GROUP BY {$this->p}ds_events.id

				ORDER BY

				{$this->p}ds_events.name ASC";


        $this->db->query($sql);
        $res = $this->db->last_result;

        foreach ($res as &$event) {
            $event->meta = json_decode($event->meta);
        }

        return $res;
    }


    public function getEventById($id)
    {
        if (empty($id)) {
            return null;
        }
        $objClassVenues = new ClassVenues($this->sessionCondition);

        $id = (int)$id;
        $sql = (
            "
				SELECT
					{$this->p}ds_events.*,
					{$this->p}ds_venues.name as venue_name,
					{$this->p}ds_venues.id as venue_id,
					(SUM(IFNULL({$this->p}ds_event_ticket_sales.quantity, 0))) as tickets_sold,
					(max_tickets - COUNT({$this->p}ds_event_ticket_sales.event_id)) as tickets_available,
					date_format(starts, '%Y-%m-%d %h:%i %p') as starts,
					date_format(ends, '%Y-%m-%d %h:%i %p') as ends

				FROM
					{$this->p}ds_events

				LEFT JOIN {$this->p}ds_venues ON ({$this->p}ds_venues.id = {$this->p}ds_events.venue_id)

				LEFT JOIN {$this->p}ds_event_ticket_sales ON ({$this->p}ds_event_ticket_sales.event_id = {$this->p}ds_events.id)

				WHERE
					{$this->p}ds_events.id = {$id}

				GROUP BY {$this->p}ds_events.id

				LIMIT 1"
        );


        $this->db->query($sql);

        if (!empty($this->db->last_error)) {
            die($this->db->last_error);
        }

        $event = $this->db->last_result;
        $event = $event[0];
        $event->meta = json_decode($event->meta);
        $event->venue = $objClassVenues->getVenueById($event->venue_id);

        return $event;
    }


    public function addEvent($input)
    {
        if (!empty($input['custom_meta_key'])) {
            $input['meta'][strtolower(str_replace(' ', '_', $input['custom_meta_key']))] = $input['custom_meta_value'];
        }

        $meta = json_encode(!empty($input['meta']) ? $input['meta'] : array());

        $input['starts'] = date("y-m-d H:i:00", strtotime("{$input['starts']}"));
        $input['ends'] = date("y-m-d H:i:00", strtotime("{$input['ends']}"));

        $sql = $this->db->prepare("
			INSERT INTO
				{$this->p}ds_events
			(name, venue_id, starts, ends, ticket_price, max_tickets, description, image_url, meta)
			VALUES (%s, %d, %s, %s, %d, %d, %s, %s, %s) ", $input['name'], $input['venue_id'], $input['starts'], $input['ends'], $input['ticket_price'], $input['max_tickets'], $input['description'], $input['image_url'], $meta);

        $this->db->query($sql);
        return $this->db->insert_id;
    }

    public function updateEvent($input, $id)
    {
        if (!$id) {
            return null;
        }

        if (!empty($input['custom_meta_key'])) {
            $input['meta'][strtolower(str_replace(' ', '_', $input['custom_meta_key']))] = $input['custom_meta_value'];
        }

        $meta = json_encode(!empty($input['meta']) ? $input['meta'] : array());

        $input['starts'] = date("y-m-d H:i:00", strtotime("{$input['starts']}"));
        $input['ends'] = date("y-m-d H:i:00", strtotime("{$input['ends']}"));

        $sql = $this->db->prepare("
			UPDATE
				{$this->p}ds_events
			SET
				name = %s,
				venue_id = %d,
				starts = %s,
				ends = %s,
				ticket_price = %d,
				max_tickets = %d,
				description = %s,
				image_url = %s,
				meta = %s
			WHERE
				id = %d", $input['name'], $input['venue_id'], $input['starts'], $input['ends'], $input['ticket_price'], $input['max_tickets'], $input['description'], $input['image_url'], $meta, (int)$id);

        $this->db->query($sql);
        return true;
    }

    public function deleteEvent($id)
    {
        if (empty($id)) {
            return null;
        }

        $sql = $this->db->prepare(
            "
				DELETE FROM
					{$this->p}ds_events
				WHERE
					id = %d

				LIMIT 1",
            $id
        );

        $this->db->query($sql);
        return true;
    }
}
