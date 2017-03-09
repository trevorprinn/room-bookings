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
		return $this->insert_id;
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
	
	function get_room_name($roomid) {
		$q = "Select Name from room Where Id_Room = ?";
		$s = $this->prepare($q);
		$s->bind_param('i', $roomid);
		$s->execute();
		$r = $s->get_result();
		$f = $r->fetch_assoc();
		if ($f == null) return "Unknown";
		return $f['Name'];
	}
	
	function add_room($name, $color, $colorprov) {
		$q = "Insert into room (Name, Color, ColorProv, `Order`) Values (?, ?, ?, ?)";
		$s = $this->prepare($q);
		$max = $this->getmaxorder('room') + 1;
		$s->bind_param('sssi', $name, $color, $colorprov, $max);
		$s->execute();
		return $this->insert_id;
	}
	
	function rename_room($id, $name, $color, $colorprov) {
		$q = "Update room Set Name = ?, Color = ?, ColorProv = ? Where Id_Room = ?";
		$s = $this->prepare($q);
		$s->bind_param('sssi', $name, $color, $colorprov, $id);
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
		$q = 'Select Id_Booking as Id, Title, booking.Id_Booker, Date, Start, Duration,
				room.Name as RoomName, booker.Name as BookerName, Provisional,
				case Provisional when 1 then room.ColorProv else room.Color end as Color 
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
	
	function get_bookings_wp($start, $end) {
		$q = 'Select Date, Start, Duration, room.Name as RoomName, Provisional,
				case Provisional when 1 then room.ColorProv else room.Color end as Color 
				from booking inner join room
				on booking.Id_Room = room.Id_Room
				Where Date >= ? and Date <= ?
				Order By Date, Start';
		$now = getdate()['0'];
		$start = strtotime($start);
		if ($start < $now) $start = $now;
		$start = date('Y-m-d', $start);
		$s = $this->prepare($q);
		$s->bind_param('ss', $start, $end);
		$s->execute();
		return $s->get_result();
	}
	
	function get_booking($id) {
		if ($id == 0) {
			return ['Id_Booking' => 0, 'Id_Booker' => 0, 'Id_Room' => 0, 'Title' => '', 'BookingDate' => '', 'Start' => 12, 'Duration' => 4, 'Notes' => '', 'Color' => '', 'ColorProv' => '', 'Provisional' => 0];	
		}
		$q = 'Select Id_Booking, Id_Booker, Id_Room, Title, Date, Notes, Start, Duration, Provisional
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
	
	function update_booking($fields, $intrans = false) {
		$id = $fields['Id_Booking'];
		$bookerId = $fields['Id_Booker'];

		if (isset($fields['NewBooker']) && $fields['NewBooker'] != '') {
			$bookerId = $this->createBooker($fields['NewBooker']);
		}		
		
		if (!$intrans) $this->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
		
		$q = $id == 0 ? "Insert into booking (Id_Booker, Id_Room, Title, Date, Start, Duration, Notes, Provisional) Values (?, ?, ?, ?, ?, ?, ?, ?)"
			: "Update booking Set Id_Booker = ?, Id_Room = ?, Title = ?, Date = ?, Start = ?, Duration = ?, Notes = ?, Provisional = ?
				Where Id_Booking = ?";
		$s = $this->prepare($q);
		$date = date('Y-m-d', strtotime($fields['Date']));
		$duration = $fields['Duration'];
		$start = $fields['Start'];
		if ($start + $duration > 24) $duration = 24 - $start;
		if ($id == 0) {
			$s->bind_param('iissiisi', $bookerId, $fields['Id_Room'], $fields['Title'], $date, $start, $duration, $fields['Notes'], $fields['Provisional']);
		} else {
			$s->bind_param('iissiisii', $bookerId, $fields['Id_Room'], $fields['Title'], $date, $start, $duration, $fields['Notes'], $fields['Provisional'], $id);
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
		
		if (!$intrans) $this->commit();
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
	
	function find_booker($name, $email) {
		$q = "Select Id_Booker from booker Where Email = ? and Name = ? Limit 1";
		$s = $this->prepare($q);
		$s->bind_param('ss', $email, $name);
		$s->execute();
		$r = $s->get_result();
		$fs = $r->fetch_assoc();
		if ($fs != null) return $fs['Id_Booker']; 
		
		$q = "Select Id_Booker from booker Where Email = ? or Name = ? Limit 1";
		$s = $this->prepare($q);
		$s->bind_param('ss', $email, $name);
		$s->execute();
		$r = $s->get_result();
		$fs = $r->fetch_assoc();
		if ($fs != null) return $fs['Id_Booker']; 
		
		return 0;
	}
	
	function merge_bookers($ids, $fields) {
		$keepid = $ids[0];
		$otherids = $ids;
		array_splice($otherids, 0, 1);
		
		$this->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
		
		$q = 'Update booker Set Name = ?, Address = ?, Phone = ?, Email = ?, Notes = ? Where Id_Booker = ?';
		$s = $this->prepare($q);
		$s->bind_param('sssssi', $fields['Name'], $fields['Address'], $fields['Phone'], $fields['Email'], $fields['Notes'], $keepid);
		$s->execute();
		
		$q = 'Update booking Set Id_Booker = ? Where Id_Booker = ?';
		$s = $this->prepare($q);
		foreach ($otherids as $otherid) {
			$s->bind_param('ii', $keepid, $otherid);
			$s->execute();
		}				
		
		$q = 'Delete From booker Where Id_Booker = ?';
		$s = $this->prepare($q);
		foreach ($otherids as $otherid) {
			$s->bind_param('i', $otherid);
			$s->execute();
		}
		
		$this->commit();
	}
}

?>