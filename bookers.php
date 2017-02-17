<?php
$usesDatatables = 1; 
include('header.php');
?>

<div ng-cloak class="container-fluid" ng-app="bookings" ng-controller="bookers">

<h1>Bookers</h1>

<table id="table" class="table table-hover" datatable="ng">
	<thead>
		<th>Name</th>
		<th>Phone</th>
		<th>Email</th>
	</thead>
	<tbody>
		<tr ng-repeat="booker in bookers track by $index">
			<td><a role="button" ng_click="display(booker.Id_Booker)">{{DisplayName(booker)}}</a></td>
			<td>{{booker.Phone}}</td>
			<td>{{booker.Email}}</td>
		</tr>
	</tbody>
</table>
<button ng-click="display(0)">Create New Booker</button>

</div>

<script>
var app = angular.module("bookings", ['datatables']);
app.controller("bookers", function($scope, $http, $window) {
	$http.get("getdata.php?type=bookers")
	.then(function(response) {
		$scope.bookers = response.data;
	});
	
	$scope.DisplayName = function(booker) {
		return booker.Name == "" ? "(Unnamed)" : booker.Name;
	}; 
	$scope.display = function(bookerId) {
		$window.location.href = "booker.php?id=" + bookerId;
	};
});
</script>

<?php include('footer.php'); ?>