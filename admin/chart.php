<?php

//chart.php

include('header.php');

$present_percentage = 0;
$absent_percentage = 0;
$absentj_percentage = 0;
$total_present = 0;
$total_absent = 0;
$total_absentj = 0;
$output = "";

$query = "
SELECT * FROM tbl_attendance 
WHERE student_id = '".$_GET['student_id']."' 
AND attendance_date >= '".$_GET["from_date"]."' 
AND attendance_date <= '".$_GET["to_date"]."'
";

$statement = $connect->prepare($query);

$statement->execute();

$result = $statement->fetchAll();

$total_row = $statement->rowCount();

foreach($result as $row)
{
	$status = '';
	if($row["attendance_status"] == "Present")
	{
		$total_present++;
		$status = '<span class="badge badge-success">Presente</span>';
	}

	if($row["attendance_status"] == "Absent")
	{
		$total_absent++;
		$status = '<span class="badge badge-danger">Falta</span>';
	}
	if($row["attendance_status"] == "AbsentJ")
	{
		$total_absentj++;
		$status = '<span class="badge badge-warning">Falta Justificada</span>';
	}

	$output .= '
		<tr>
			<td>'.$row["attendance_date"].'</td>
			<td>'.$status.'</td>
		</tr>
	';

	$present_percentage = ($total_present/$total_row) * 100;
	$absent_percentage = ($total_absent/$total_row) * 100;
	$absentj_percentage = ($total_absentj/$total_row) * 100;

}

?>

<div class="container" style="margin-top:30px">
  <div class="card">
  	<div class="card-header"><b>Lista de Presença Chart</b></div>
  	<div class="card-body">
      <div class="table-responsive">
        
        <table class="table table-bordered table-striped">
          <tr>
            <th>Nome</th>
            <td><?php echo Get_student_name($connect, $_GET["student_id"]); ?></td>
          </tr>
          <tr>
            <th>Região</th>
            <td><?php echo Get_student_grade_name($connect, $_GET["student_id"]); ?></td>
          </tr>
          <tr>
            <th>Analista Responsável</th>
            <td><?php echo Get_student_teacher_name($connect, $_GET["student_id"]); ?></td>
          </tr>
          <tr>
            <th>Intervalo de tempo</th>
            <td><?php echo $_GET["from_date"] . ' até '. $_GET["to_date"]; ?></td>
          </tr>
        </table>

        <div id="attendance_pie_chart" style="width: 100%; height: 400px;">

        </div>

        <div class="table-responsive">
        	<table class="table table-striped table-bordered">
	          <tr>
	            <th>Date</th>
	            <th>Lista de Presença Status</th>
	          </tr>
	          <?php echo $output; ?>
	      </table>
        </div>
  		
      </div>
  	</div>
  </div>
</div>

</body>
</html>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<script type="text/javascript">
	
	google.charts.load('current', {'packages':['corechart']});

	google.charts.setOnLoadCallback(drawChart);

	function drawChart()
	{
		var data = google.visualization.arrayToDataTable([
			['Lista de Presença Status', 'Porcentagem'],
			['Presença', <?php echo $present_percentage; ?>],
			['Falta', <?php echo $absent_percentage; ?>],
			['Falta Justificada', <?php echo $absentj_percentage; ?>],
		]);

		var options = {
			title : 'Overall Lista de Presença Analytics',
			hAxis : {
				title: 'Porcentagem',
		        minValue: 0,
		        maxValue: 100
			},
			vAxis : {
				title: 'Lista de Presença Status'
			}
		};

		var chart = new google.visualization.PieChart(document.getElementById('attendance_pie_chart'));

		chart.draw(data, options);
	}

</script>