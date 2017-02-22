<?php

include('config.php');

class bookings_db extends mysqli {
	function __construct() {
		parent::__construct(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	}
	
	function get_bookers() {
		$q = "Select * from booker"
			." Order by Name";
		$s = $this->prepare($q);
		$s->execute();
		return $s->get_result();
	}
	
	function get_booker($id) {
		if ($id == 0) {
			return ['Name'=>'', 'Address'=>'', 'Phone'=>'', 'Email'=>'', 'Notes'=>''];
		}
		$q = "Select * from booker"
			." Where Id_Booker = ?";
		$s = $this->prepare($q);
		$s->bind_param('i', $id);
		$s->execute();
		return $s->get_result()->fetch_assoc();
	}
	
	function update_booker($fields) {
		$id = $fields['Id_Booker'];
		$q = $id == 0 ? "Insert into booker (Name, Address, Phone, Email, Notes) Values (?, ?, ?, ?, ?)"
			: "Update booker Set Name = ?, Address = ?, Phone = ?, Email = ?, Notes = ?"
				." Where ID_Booker = ?";
		$s = $this->prepare($q);
		if ($id == 0) {
			$s->bind_param('sssss', $fields['Name'], $fields['Address'], $fields['Phone'], $fields['Email'], $fields['Notes']);
		} else {
			$s->bind_param('sssssi', $fields['Name'], $fields['Address'], $fields['Phone'], $fields['Email'], $fields['Notes'], $id);
		}
		$s->execute();
	} 
	
	function get_facilities() {
		$q = 'Select Id_Facility, Name, `Order`,
				 (Select Count(*) from room_facility Where room_facility.Id_Facility = facility.Id_Facility) as RoomCount,
				 (Select Count(*) from booking_facility Where booking_facility.Id_Facility = facility.Id_Facility) as BookingCount  
				from facility
				Order By `Order`';
		$s = $this->prepare($q);
		$s->execute();
		return $s->get_result();
	}
	
	function getmaxorder($table) {
		$q = "Select Max(`Order`) from $table";
		$s = $this->prepare($q);
		$s->bind_result($max);
		$s->execute();
		$s->fetch();
		if ($max == null) $max = 0;
		return $max;
	}
	
	function add_facility($name) {
		$q = 'Insert into facility (Name, `Order`) Values (?, ?)';
		$s = $this->prepare($q);
		$max = $this->getmaxorder('facility') + 1;
		$s->bind_param('si', $name, $max);
		$s->execute();
		return $this->insert_id;
	}
	
	function rename_facility($id, $name) {
		$q = "Update facility Set Name = ? Where Id_Facility = ?";
		$s = $this->prepare($q);
		$s->bind_param('si', $name, $id);
		$s->execute();
	}
	
	function delete_facility($id) {
		$q = "Delete From facility Where Id_Facility = ?";
		$s = $this->prepare($q);
		$s->bind_param('i', $id);
		$s->execute();
	}
	
	function get_rooms() {
		$q = "Select * from room Order by `Order`";
		$s = $this->prepare($q);
		$s->execute();
		return $s->get_result();
	}
	
	function add_room($name, $color) {
		$q = "Insert into room (Name, Color, `Order`) Values (?, ?, ?)";
		$s = $this->prepare($q);
		$max = $this->getmaxorder('room') + 1;
		$s->bind_param('ssi', $name, $color, $max);
		$s->execute();
		return $this->insert_id;
	}
	
	function rename_room($id, $name, $color) {
		$q = "Update room Set Name = ?, Color = ? Where Id_Room = ?";
		$s = $this->prepare($q);
		$s->bind_param('ssi', $name, $color, $id);
		$s->execute();
	}
	
	function delete_room($id) {
		$q = "Delete From room Where Id_Room = ?";
		$s = $this->prepare($q);
		$s->bind_param('i', $id);
		$s->execute();
	}

	function get_roomfacilities($roomid) {
		$q = 'select facility.Id_Facility, facility.Name,
				case when room_facility.id_Room is null then 0 else 1 end as Used
				from facility left join room_facility
				on facility.Id_Facility = room_facility.id_Facility
				and room_facility.id_Room = ?';
		$s = $this->prepare($q);
		$s->bind_param('i', $roomid);
		$s->execute();
		return $s->get_result();
	}
	
	function add_room_facility($roomid, $facid) {
		$q = 'insert into room_facility (id_Room, id_Facility) values (?, ?)';
		$s = $this->prepare($q);
		$s->bind_param('ii', $roomid, $facid);
		$s->execute();
	}
	
	function remove_room_facility($roomid, $facid) {
		$q = 'delete from room_facility where id_Room = ? and id_Facility = ?';
		$s = $this->prepare($q);
		$s->bind_param('ii', $roomid, $facid);
		$s->execute();
	}
	
	function get_bookings($start = null, $end = null) {
		$q = 'Select Id_Booking as Id, Title, booking.Id_Booker, Date, Start, Duration, DATE_FORMAT(Date, "%W %d %m %Y") as BookingDate,
				room.Name as RoomName, booker.Name as BookerName, room.Color
				from booking Inner Join room
				on booking.Id_Room = room.Id_Room
				Left Join booker
				on booking.Id_Booker = booker.Id_Booker ';
		if ($start != null) $q .= 'Where Date >= ? ';
		if ($end != null) $q .= 'And Date <= ? ';
		$q .= 'Order By Date';
		$s = $this->prepare($q);
		if ($start != null && $end == null) {
			$start = date('Y-m-d', strtotime($start));
			$s->bind_param('s', $start);
		} else if ($start != null && $end != null) {
			$start = date('Y-m-d', strtotime($start));
			$end = date('Y-m-d', strtotime($end));
			$s->bind_param('ss', $start, $end);
		}
		$s->execute();
		return $s->get_result();
	}
	
	function get_bookings_range($start, $end) {
		$q = "Select * From booking 
				Where Date >= ? and Date <= ?";
		$s = $this->prepare($q);
		$s->bind_param('dd', $start, $end);
		$s->execute();
		return $s->get_result();
	}
	
	function get_bookings_wp() {
		$q = 'Select Date, Start, Duration, room.Name as RoomName, room.Color
				from booking inner join room
				on booking.Id_Room = room.Id_Room
				Where Date >= ?
				Order By Date, Start';
		$now = getdate();
		$start = sprintf('%04d-%02d-%02d', $now['year'], $now['mon'], $now['mday']); 
		$s = $this->prepare($q);
		$s->bind_param('s', $start);
		$s->execute();
		return $s->get_result();
	}
	
	function get_booking($id) {
		if ($id == 0) {
			return ['Id_Booking' => 0, 'Id_Booker' => 0, 'Id_Room' => 0, 'Title' => '', 'BookingDate' => '', 'Start' => 12, 'Duration' => 4, 'Notes' => '', 'Color' => ''];	
		}
		$q = 'Select booking.*, DATE_FORMAT(Date, "%d-%m-%Y") as BookingDate
				From booking
				Where Id_Booking = ?';
		$s = $this->prepare($q);
		$s->bind_param('i', $id);
		$s->execute();
		return $s->get_result()->fetch_assoc();
	}
	
	function get_booking_facilities($bookingId) {
		$q = "Select bf.Id_Facility 
				From booking_facility bf Inner Join room_facility rf
				On bf.Id_Facility = rf.id_Facility
				Inner Join booking
				On booking.Id_Booking = bf.Id_Booking  
				And booking.Id_Room = rf.id_Room
				Where booking.Id_Booking = ?";
		$s = $this->prepare($q);
		$s->bind_param('i', $bookingId);
		$s->execute();
		
		$fs = [];	
		foreach ($s->get_result() as $f) {
			$fs[] = $f['Id_Facility'];
		}
		return $fs;
	}
	
	function createBooker($name) {
		$q = "Insert into booker (Name) Values (?)";
		$s = $this->prepare($q);
		$s->bind_param('s', $name);
		$s->execute();
		return $this->insert_id;
	}
	
	function update_booking($fields) {
		$id = $fields['Id_Booking'];
		$bookerId = $fields['Id_Booker'];

		if (isset($fields['NewBooker']) && $fields['NewBooker'] != '') {
			$bookerId = $this->createBooker($fields['NewBooker']);
		}		
		
		$this->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
		
		$q = $id == 0 ? "Insert into booking (Id_Booker, Id_Room, Title, Date, Start, Duration, Notes) Values (?, ?, ?, ?, ?, ?, ?)"
			: "Update booking Set Id_Booker = ?, Id_Room = ?, Title = ?, Date = ?, Start = ?, Duration = ?, Notes = ?
				Where Id_Booking = ?";
		$s = $this->prepare($q);
		$date = date('Y-m-d', strtotime($fields['Date']));
		$duration = $fields['Duration'];
		$start = $fields['Start'];
		if ($start + $duration > 24) $duration = 24 - $start;
		if ($id == 0) {
			$s->bind_param('iissiis', $bookerId, $fields['Id_Room'], $fields['Title'], $date, $start, $duration, $fields['Notes']);
		} else {
			$s->bind_param('iissiisi', $bookerId, $fields['Id_Room'], $fields['Title'], $date, $start, $duration, $fields['Notes'], $id);
		}
		$s->execute();
		
		if ($id == 0) $id = $this->insert_id;
		
		$q = "Delete From booking_facility Where Id_Booking = ?";
		$s = $this->prepare($q);
		$s->bind_param('i', $id);
		$s->execute();
		
		$q = "Insert into booking_facility (Id_Booking, Id_Facility) Values (?, ?)";
		$s = $this->prepare($q);
		
		foreach ($fields['facilities'] as $facId) {
			$s->bind_param('ii', $id, $facId);
			$s->execute();
		}
		
		$this->commit();
	} 
	
	function delete_booking($id) {
		$this->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
		
		$q = "Delete from booking_facility Where Id_Booking = ?";		
		$s = $this->prepare($q);
		$s->bind_param('i', $id);
		$s->execute();
		
		$q = "Delete from booking Where Id_Booking = ?";
		$s = $this->prepare($q);
		$s->bind_param('i', $id);
		$s->execute();
		
		$this->commit();
	}
}

?>