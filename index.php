<?php
session_start();

/* DATABASE CONNECTION */

$host = "localhost";
$user = "root";
$pass = "";
$db = "chicken_coop";

$conn = new mysqli($host,$user,$pass,$db);

if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}


/* FAN CONTROL */

if(isset($_POST['fan'])){
    $status = $_POST['fan'];
    $conn->query("UPDATE fan_control SET status='$status' WHERE id=1");
}


/* GET FAN STATUS */

$fanQuery = $conn->query("SELECT status FROM fan_control WHERE id=1");
$fanRow = $fanQuery->fetch_assoc();
$fanStatus = $fanRow['status'];


/* GET SENSOR DATA */

$result = $conn->query("SELECT * FROM sensor_data ORDER BY recorded_at DESC LIMIT 10");

$data = [];
while($row = $result->fetch_assoc()){
    $data[] = $row;
}

$latestTemp = $data[0]['temperature'] ?? "--";
$latestHumidity = $data[0]['humidity'] ?? "--";

?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<title>IoT Chicken Coop Dashboard</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

body{
font-family: Arial;
background:#f4f6f9;
}

header{
text-align:center;
padding:20px;
background:#2c3e50;
color:white;
}

.container{
display:flex;
gap:20px;
padding:20px;
}

.left-panel{
flex:2;
}

.right-panel{
flex:1;
}

.card{
background:white;
padding:20px;
margin-bottom:20px;
border-radius:10px;
box-shadow:0 2px 10px rgba(0,0,0,0.1);
}

button{
padding:10px 15px;
margin:5px;
border:none;
cursor:pointer;
border-radius:5px;
}

.on{
background:green;
color:white;
}

.off{
background:red;
color:white;
}

table{
width:100%;
border-collapse:collapse;
}

th,td{
padding:8px;
border-bottom:1px solid #ddd;
text-align:center;
}

canvas{
width:100%;
height:300px;
}

</style>

</head>

<body>

<header>
<h1>🐔 Smart Chicken Coop System</h1>
<p>Admin Dashboard</p>
</header>

<div class="container">

<div class="left-panel">

<div class="card">

<h2>Sensor Monitoring</h2>

<p>Temperature: <span class="value"><?php echo $latestTemp ?></span> °C</p>
<p>Humidity: <span class="value"><?php echo $latestHumidity ?></span> %</p>

</div>


<div class="card">

<h2>Ventilation Control</h2>

<p>Fan Status: <span class="value"><?php echo $fanStatus ?></span></p>

<form method="POST">
<button class="on" name="fan" value="ON">Turn ON Fan</button>
<button class="off" name="fan" value="OFF">Turn OFF Fan</button>
</form>

</div>


<div class="card">

<h2>System Logs</h2>

<table>

<thead>
<tr>
<th>Date & Time</th>
<th>Temperature</th>
<th>Humidity</th>
<th>Fan</th>
</tr>
</thead>

<tbody>

<?php foreach($data as $row): ?>

<tr>
<td><?php echo $row['recorded_at'] ?></td>
<td><?php echo $row['temperature'] ?></td>
<td><?php echo $row['humidity'] ?></td>
<td><?php echo $row['fan_status'] ?></td>
</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

</div>


<div class="right-panel">

<div class="card">

<h2>Environmental Graph</h2>

<canvas id="sensorChart"></canvas>

</div>

</div>

</div>


<script>

const labels = [];
const temps = [];
const hums = [];

<?php
$chartData = array_reverse($data);
foreach($chartData as $row){
    echo "labels.push('".$row['recorded_at']."');";
    echo "temps.push(".$row['temperature'].");";
    echo "hums.push(".$row['humidity'].");";
}
?>

const ctx = document.getElementById('sensorChart').getContext('2d');

new Chart(ctx,{

type:'line',

data:{
labels:labels,
datasets:[

{
label:'Temperature °C',
borderColor:'red',
data:temps
},

{
label:'Humidity %',
borderColor:'blue',
data:hums
}

]

},

options:{
responsive:true
}

});

</script>

</body>
</html> 