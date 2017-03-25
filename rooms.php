<?php include('header.php') ?>

<div ng-cloak class='container-fluid' ng-app='bookings' ng-controller='rooms'>

<h1>Rooms</h1>

<table class="table">
	<tr ng-repeat="room in rooms track by $index">
		<td>
			<input type="text" ng-model="room.Name">
			<span>Confirmed Bookings:</span>
			<input type="color" ng-model="room.Color">
			<span>Provisional Bookings:</span>
			<input type="color" ng-model="room.ColorProv">
			<span ng-repeat="fac in room.facilities">
				<br/><input type="checkbox" ng-change="changefac(room.Id_Room, fac.Id_Facility, fac.Used)" ng-model="fac.Used" ng-true-value="1" ng-false-value="0">{{fac.Name}}
			</span>
		</td>
		<td><button ng-click="save(room.Id_Room, room.Name, room.Color, room.ColorProv)">Save</button></td>
		<td><button ng-click="delete(room, $index)">Delete</button></td>
	</tr>
	<tr>
		<td>
			<input type="text" placeholder="New Room" ng-model="newRoom" required>
			<span>Confirmed Bookings:</span>
			<input type="color" ng-model="newColor" ng-disabled="newRoom == '' || newRoom == null">
			<span>Provisional Bookings:</span>
			<input type="color" ng-model="newColorProv" ng-disabled="newRoom == '' || newRoom == null">
		</td>
		<td><button ng-click="add()" ng-disabled="newRoom == '' || newRoom == null">Add</button>
	</tr>

</table>


</div>

<script>
var app = angular.module("bookings", []);
app.controller("rooms", function($scope, $http) {
	$http.get("getdata.php?type=rooms")
		.then(function(response) {
			$scope.rooms = response.data;
		});
	$scope.newRoom = "";
	$scope.newColor = 0;
	$scope.newColorProv = 0;
	
	$scope.add = function () {
		$http.post("update_room.php?action=add", {
			'Name': $scope.newRoom,
			'Color': $scope.newColor,
			'ColorProv': $scope.newColorProv
		}).then(function(response) {
			$scope.rooms.push(response.data);
			$scope.newRoom = "";
			$scope.newColor = 0;
			$scope.newColorProv = 0;
		});
	};
	
	$scope.save = function(id, name, color, colorProv) {
		$http.post("update_room.php?action=update", {
			'id': id,
			'Name': name,
			'Color': color,
			'ColorProv': colorProv
		});
	};
	$scope.changefac = function(roomid, facid, used) {
		$http.post("update_room.php?action=updatefac", {
			'roomid': roomid,
			'facid': facid,
			'available': used == 1
		});
	};
	$scope.delete = function(room, ix) {
		if (!confirm("Delete the room '" + room.Name + "'?")) return;
		$http.post("update_room.php?action=delete", {
			'id': room.Id_Room
		});
		$scope.rooms.splice(ix, 1);
	};
});

</script>

<?php include('footer.php') ?>