<?php

//vendor_fetch.php

include('database_connection.php');

$query = '';

$output = array();

$query .= "SELECT * FROM vendor ";

if(isset($_POST["search"]["value"]))
{
	$query .= 'WHERE vendor_name LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR vendor_status LIKE "%'.$_POST["search"]["value"].'%" ';
}

if(isset($_POST['order']))
{
	$query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
}
else
{
	$query .= 'ORDER BY vendor_id DESC ';
}

if($_POST['length'] != -1)
{
	$query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}

$statement = $connect->prepare($query);

$statement->execute();

$result = $statement->fetchAll();

$data = array();

$filtered_rows = $statement->rowCount();

foreach($result as $row)
{
	$status = '';
	if($row['vendor_status'] == 'active')
	{
		$status = '<span class="label label-success">Active</span>';
	}
	else
	{
		$status = '<span class="label label-danger">Inactive</span>';
	}
	$sub_array = array();
	$sub_array[] = $row['vendor_id'];
	$sub_array[] = $row['vendor_name'];
	$sub_array[] = $row['vendor_contact'];
	$sub_array[] = $status;
	$sub_array[] = '<button type="button" name="update" id="'.$row["vendor_id"].'" class="btn btn-warning btn-xs update">Update</button>';
	$sub_array[] = '<button type="button" name="delete" id="'.$row["vendor_id"].'" class="btn btn-danger btn-xs delete" data-status="'.$row["vendor_status"].'">Delete</button>';
	$data[] = $sub_array;
}

$output = array(
	"draw"			=>	intval($_POST["draw"]),
	"recordsTotal"  	=>  $filtered_rows,
	"recordsFiltered" 	=> 	get_total_all_records($connect),
	"data"				=>	$data
);

function get_total_all_records($connect)
{
	$statement = $connect->prepare("SELECT * FROM vendor");
	$statement->execute();
	return $statement->rowCount();
}

echo json_encode($output);

?>