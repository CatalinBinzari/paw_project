<?php
require_once("config.php");
$columns = array('e.nume', 'e.prenume', 's.denumirea_institutiei', 'b.tip_bursa');
$column = isset($_GET['column']) && in_array($_GET['column'], $columns) ? $_GET['column'] : $columns[0];
$sort_order = isset($_GET['order']) && strtolower($_GET['order']) == 'desc' ? 'DESC' : 'ASC';

$up_or_down = str_replace(array('ASC','DESC'), array('up','down'), $sort_order); 
$asc_or_desc = $sort_order == 'ASC' ? 'desc' : 'asc';
$add_class = ' class="highlight"';
$search = $_GET['search'] ?? "";
//echo $search;	
//echo $asc_or_desc . $column;
//and  e.nume = '' or e.prenume = ''
if ($search == "")
$search_querry = "";
else
$search_querry ="and  (e.nume = '".$search."' or e.prenume = '".$search."')";
//echo $search_querry;
$stmt = $dbConn->prepare("SELECT e.nume, e.prenume, s.denumirea_institutiei, b.tip_bursa
						  FROM elevi e, bursieri b, scoli s 
						  WHERE e.id_elev = b.id_elev and 
						        e.id_scoala = s.id_scoala 
						        ". "$search_querry" ."
						  ORDER BY  " . $column . " " .$asc_or_desc 

						);
$stmt->execute();

$bursieri = [];
$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
foreach(new TableRows(new RecursiveArrayIterator($stmt->fetchAll())) as $k=>$v) {
		$bursieri[] =$v;
	}


?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<style>

.button1 {
  position: absolute;
  top: 5px;
  right: 7px;
}

.button {
  border: none;
  color: white;
  padding: 16px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 4px 2px;
  transition-duration: 0.4s;
  cursor: pointer;
}

.button1 {
  background-color: white; 
  color: black; 
  border: 2px solid #4CAF50;
}

.button1:hover {
  background-color: #4CAF50;
  color: white;
}
table {
  width:100%;
}
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
th, td {
  padding: 15px;
  text-align: left;
}
table#t01 tr:nth-child(even) {
  background-color: #eee;
}
table#t01 tr:nth-child(odd) {
 background-color: #fff;
}
table#t01 th {
  background-color: black;
  color: white;
}
th {
				background-color: #54585d;
				border: 1px solid #54585d;
			}
			th:hover {
				background-color: #64686e;
			}
			th a {
				display: block;
				text-decoration:none;
				padding: 10px;
				color: #ffffff;
				font-weight: bold;
				font-size: 13px;
			}
			th a i {
				margin-left: 5px;
				color: rgba(255,255,255,0.4);
			}
* {
  box-sizing: border-box;
}

form.button2 input[type=text] {
  padding: 10px;
  font-size: 17px;
  border: 1px solid grey;
  float: left;
  width: 80%;
  background: #f1f1f1;
}

form.button2 button {
  float: left;
  width: 20%;
  padding: 10px;
  background: #2196F3;
  color: white;
  font-size: 17px;
  border: 1px solid grey;
  border-left: none;
  cursor: pointer;
}
</style>
<link href="pricing.css" rel="stylesheet">
<link href="assets/dist/css/bootstrap.css" rel="stylesheet">
</head>
<body>
    
</div>

<div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
  <h1 class="display-4">Tabel burse</h1>
  <button class="button button1"><a href="topdf.php">Descarca tabel burse</a></button>
  <p class="lead">Anul de invatamant 2019-2020</p>
</div>

<body>
<div>
<form class="button2" action="burse.php" style="margin:auto;max-width:300px">
  <input type="text" placeholder="Nume sau prenume" name="search">
  <button type="submit"><i class="fa fa-search"></i></button>
</form>
</div>
<?php

?>
<table id="t01">
  <tr>
	    <th><a href="burse.php?column=e.nume&order=<?php echo $asc_or_desc; ?>">Nume</a></th>
	    <th><a href="burse.php?column=e.prenume&order=<?php echo $asc_or_desc; ?>">Prenume</a></th>
	    <th><a href="burse.php?column=s.denumirea_institutiei&order=<?php echo $asc_or_desc; ?>">Liceu</th>
	    <th><a href="burse.php?column=b.tip_bursa&order=<?php echo $asc_or_desc; ?>">Tip bursa</th>
  </tr>
  <div style = "color:blue;">
					<?php $tmp=0;
					 foreach ($bursieri as $bursier) :?>
					 		<?php if ($tmp == 0): ?>
						  		<tr>
						    <?php endif; ?>
						 	<td>
						 	<?php
						    echo $bursier;
						  	++$tmp;
						  	?>
						  	</td> 
						  	<?php if ($tmp == 4): $tmp =0; ?>
						  		</tr>
						    <?php endif; ?>
						   
					<?php endforeach; ?>
  </div>
</table>

<!-- Web Widget -->
<div id="pb-widget"></div>
<script>
  var bot_config = {
    PB_HOST: "home",
    PB_BOTKEY: "yIjx1p9cee0XMQgf3phR1XK1EXQIpuWq553kvbNPOWgg3Ab9DjazQKZa9xJhw0sRqYqgO8b8fE1FJ4zeixXoDA~~",
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