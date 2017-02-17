<?php include('header.php') ?>

<div ng-cloak class='container-fluid' ng-app='bookings' ng-controller='rooms'>

<h1>Rooms</h1>

<table class="table">
	<tr ng-repeat="room in rooms track by $index">
		<td>
			<input type="text" ng-model="room.Name">
			<input type="color" ng-model="room.Color">
			<span ng-repeat="fac in room.facilities">
				<br/><input type="checkbox" ng-change="changefac(room.Id_Room, fac.Id_Facility, fac.Used)" ng-model="fac.Used" ng-true-value="1" ng-false-value="0">{{fac.Name}}
			</span>
		</td>
		<td><button ng-click="save(room.Id_Room, room.Name, room.Color)">Save</button></td>
		<td><button ng-click="delete(room.Id_Room, $index)">Delete</button></td>
	</tr>
	<tr>
		<td>
			<input type="text" placeholder="New Room" ng-model="newRoom">
			<input type="color" ng-model="newColor">
		</td>
		<td><button ng-click="add()">Add</button>
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
	
	$scope.add = function () {
		if ($scope.newRoom == "") {
			alert("You must enter a new Room name");
			return;
		}
		$http.post("update_room.php?action=add", {
			'Name': $scope.newRoom,
			'Color': $scope.newColor
		}).then(function(response) {
			$scope.rooms.push(response.data);
			$scope.newRoom = "";
			$scope.newColor = 0;
		});
	};
	
	$scope.save = function(id, name, color) {
		$http.post("update_room.php?action=update", {
			'id': id,
			'Name': name,
			'Color': color
		});
	};
	$scope.changefac = function(roomid, facid, used) {
		$http.post("update_room.php?action=updatefac", {
			'roomid': roomid,
			'facid': facid,
			'available': used == 1
		});
	};
	$scope.delete = function(id, ix) {
		$http.post("update_room.php?action=delete", {
			'id': id
		});
		$scope.rooms.splice(ix, 1);
	};
});

</script>

<?php include('footer.php') ?>