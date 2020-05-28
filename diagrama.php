<?php
require_once("config.php");

$stmt = $dbConn->prepare("SELECT an as label, burse_merit as y from statistica ");
$stmt->execute();
$burse_merit = $stmt->fetchAll(\PDO::FETCH_ASSOC);
//print_r($burse_merit);

$stmt = $dbConn->prepare("SELECT an as label, burse_sociale as y from statistica ");
$stmt->execute();
$burse_sociale = $stmt->fetchAll(\PDO::FETCH_ASSOC);
//print_r($burse_sociale);

$stmt = $dbConn->prepare("SELECT an as label, burse_performanta as y from statistica ");
$stmt->execute();
$burse_performanta = $stmt->fetchAll(\PDO::FETCH_ASSOC);
//print_r($burse_performanta);


$ani = array_column($burse_sociale, 'label');

if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['afisare']))
    {
        afisare( $_POST['an_inceput'], $_POST['an_sfarsit'], $burse_merit, $burse_sociale, $burse_performanta);
    }

function afisare( $inceput, $sfarsit, &$burse_merit , &$burse_sociale, &$burse_performanta )
    {	
    	$inceput=$inceput-1999;
    	$sfarsit=$sfarsit-$inceput-1998;
    	$burse_merit = array_slice($burse_merit, $inceput, $sfarsit);
    	$burse_sociale = array_slice($burse_sociale, $inceput, $sfarsit);
    	$burse_performanta = array_slice($burse_performanta, $inceput, $sfarsit);
    	//print_r($burse_sociale);
    }
?>
<!DOCTYPE HTML>
<html>
<head> 

<script>
window.onload = function () {
 
var chart = new CanvasJS.Chart("chartContainer", {
	animationEnabled: true,
	theme: "light2",
	title:{
		text: "Numarul de burse oferite de-a lungul anilor"
	},
	axisY:{
		title: "Numarul de burse",
		logarithmic: true,
		titleFontColor: "#6D78AD",
		gridColor: "#6D78AD",
		labelFormatter: addSymbols
	},
	
	legend: {
		cursor: "pointer",
		verticalAlign: "top",
		fontSize: 16,
		itemclick: toggleDataSeries
	},
	data: [{
		type: "line",
		markerSize: 0,
		showInLegend: true,
		name: "Burse de merit",
		yValueFormatString: "#,##0 burse",
		dataPoints: <?php echo json_encode($burse_merit, JSON_NUMERIC_CHECK); ?>
	},
	{
		type: "line",
		markerSize: 0,
		showInLegend: true,
		name: "Burse sociale",
		yValueFormatString: "#,##0 burse",
		dataPoints: <?php echo json_encode($burse_sociale, JSON_NUMERIC_CHECK); ?>
	},
	{
		type: "line",
		markerSize: 0,
		showInLegend: true,
		name: "Burse de performanta",
		yValueFormatString: "#,##0 burse",
		dataPoints: <?php echo json_encode($burse_performanta, JSON_NUMERIC_CHECK); ?>
	}
	]
});
chart.render();
 
function addSymbols(e){
	var suffixes = ["", "K", "M", "B"];
 
	var order = Math.max(Math.floor(Math.log(e.value) / Math.log(1000)), 0);
	if(order > suffixes.length - 1)
		order = suffixes.length - 1;
 
	var suffix = suffixes[order];
	return CanvasJS.formatNumber(e.value / Math.pow(1000, order)) + suffix;
}
 
function toggleDataSeries(e){
	if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
		e.dataSeries.visible = false;
	}
	else{
		e.dataSeries.visible = true;
	}
	chart.render();
}
 
}
</script>

</head>
<link href="assets/dist/css/bootstrap.css" rel="stylesheet">
<body>
<div id="chartContainer" style="height: 370px; width: 100%;"></div>

<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
<div>
        <br/>
        <form action="diagrama.php" method="POST">
            <div class="d-flex justify-content-start form-group mx-sm-3 mb-2">
            <input class="btn btn-outline-primary" type="submit" name="afisare" value="Afisare pentru perioada de timp selectata" />
            
            <select name="an_inceput" class=" btn-secondary btn-sm dropdown-toggle mx-sm-3"  size="1">
            <?php foreach($ani as $an): ?>
    		<option value="<?php echo $an; ?>"><?php echo $an; ?> </option>
    		<?php endforeach; ?>
            </select>

            <select name="an_sfarsit" class=" btn-secondary btn-sm dropdown-toggle"  size="1">
            <?php foreach($ani as $an): ?>
    		<option value="<?php echo $an; ?>"><?php echo $an; ?> </option>
    		<?php endforeach; ?>
            </select>
            </div>
        </form>
        
    </div>
<!-- Web Widget -->
<div id="pb-widget"></div>
<script>
  var bot_config = {
    PB_HOST: "home",
    PB_BOTKEY: "yIjx1p9cee0XMQgf3phR1XK1EXQIpuWq553kvbNPOWgg3Ab9DjazQKZa9xJhw0sRqYqgO8b8fE1FJ4zeixXoDA~~",
    title: "Ajutor",
    subtitle: "Ai vreo intrebare? Apasa aici!",
    colors: { theme: "#4da3ff", text: "#FFFFFF" },
    conversationOpener: "Buna, cu ce te pot ajuta?",
    descriptionTitle: "Ajutor",
    placeholderText: "Scrie un mesaj...",
    botAvatar: "https://cdn3.iconfinder.com/data/icons/chat-bot-emoji-blue-filled-color/300/14134081Untitled-3-512.png",
  }
</script>
<script src="https://widget.pandorabots.com/prod/pb-widget.js" type="text/javascript"></script>
</body>

</html>