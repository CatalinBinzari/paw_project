<?php
require_once("config.php");
if (empty($_SESSION['user_id'])){
	header("location: login.php");
}

$delogare = $_GET['delogare'] ?? false;
if ($delogare == true){
  session_destroy();
  header("Refresh:0");
}
//utlizatorul poate aplica pentru una din cele 3 burse 
//utilizatorul poate vedea bursierii si sa-i sorteze?

//echo $_SESSION['user_id'];
$stmt = $dbConn->prepare("SELECT tip_bursa, aprobare from cereri_bursa where id_elev = {$_SESSION['user_id']}");
$stmt->execute();
$aplicari = [];
$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
foreach(new TableRows(new RecursiveArrayIterator($stmt->fetchAll())) as $k=>$v) {
		$aplicari[] = $v ;
	}
?>

<?php
    if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['aplicare']))
    {
    	echo $_SESSION['user_id'];
    	echo '<br>' . $_POST['tip_bursa_aplica'];
        aplica_bursa($dbConn,  $_POST['tip_bursa_aplica'], $_SESSION['user_id']);
    }
    else if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['stergere_aplicare']))
    {
    	echo 'a mers';
    	echo '<br>' . $_POST['tip_bursa_stearsa'];
        stergere_bursa($dbConn,  $_POST['tip_bursa_stearsa'], $_SESSION['user_id']);
    }
    function aplica_bursa($dbConn,  $tip_bursa_aplica, $id)
    {	
    	$dbConn->beginTransaction();
    	$dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    	$sql = "insert into cereri_bursa
    				(id_elev, tip_bursa) 
    			values
    			('$id', '$tip_bursa_aplica')"
    			;
		$dbConn->exec($sql);
        $sql = "insert into logs
                   (utilizator_id, functie, actiune_logica) 
                values ('".$_SESSION['user_id']."','elev',
                'aplicare pentru bursa $tip_bursa_aplica') ";
        $dbConn->exec($sql);
    	$dbConn->commit();
        header("Refresh:0");
        echo "In asteptare"; 
    }
    function stergere_bursa($dbConn,  $tip_bursa_stearsa, $id)
    {	
    	$dbConn->beginTransaction();
    	$dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    	$sql = "DELETE FROM cereri_bursa 
    			WHERE 
    			id_elev = '$id' AND  tip_bursa = '$tip_bursa_stearsa'"
    			;
		$dbConn->exec($sql);
        $sql = "insert into logs
                   (utilizator_id, functie, actiune_logica) 
                values ('".$_SESSION['user_id']."','elev',
                'stergere aplicare pentru bursa $tip_bursa_stearsa') ";
        $dbConn->exec($sql);
    	$dbConn->commit();
        header("Refresh:0");
        echo "Sters."; 
    }
?>

<html lang="en">
  <head>
    <meta charset="utf-8">
    
    <title>Administrare burse</title>


    <!-- Bootstrap core CSS -->
<link href="assets/dist/css/bootstrap.css" rel="stylesheet">

    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
      input[type=text] {
      width: 10%;
      padding: 5px 20px;
      margin: 8px 0;
      box-sizing: border-box;
      border: 1px solid black;
      border-radius: 3px;
    </style>
    <!-- Custom styles for this template -->
    <link href="pricing.css" rel="stylesheet">
  </head>
  <body>
    <div class="d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom shadow-sm">
  <h5 class="my-0 mr-md-auto font-weight-normal"><a href="profil.php">Home</h5>
   <a class="btn btn-outline-primary mx-sm-3 " href="diagrama.php">Statistica</a>

  <a class="btn btn-outline-primary" href="burse.php">Mergi la burse</a>

  <a class="btn btn-outline-primary mx-sm-3" href="profil.php?delogare=true">Delogare</a>
</div>

<div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
  <h1 class="display-4">Pagina utilizatorului</h1>
  <p class="lead">Aplicati pentru bursa pe care o doriti</p>
</div>
<body>

<div>
	   <div class="d-flex justify-content-start form-group ">
    		<br/>
    		<form action="profil.php" method="POST">
        		<input class="btn btn-outline-primary mx-sm-3" type="submit" name="aplicare"  value="Alegere bursa   " />

    			<select class=" btn-secondary btn-sm dropdown-toggle " name="tip_bursa_aplica" size="1">
    			<option value="sociala">Bursa sociala</option>
    			<option value="performanta">Bursa de performanta</option>
    			<option value="merit">Bursa de merit</option>
    			</select>
		<br><br>
			<input class="btn btn-outline-primary mx-sm-3 " type="submit" name="stergere_aplicare" value="Sterge aplicarea" />

			<select class=" btn-secondary btn-sm dropdown-toggle" name="tip_bursa_stearsa" size="1">
			<option value="sociala">Bursa sociala</option>
			<option value="performanta">Bursa de performanta</option>
			<option value="merit">Bursa de merit</option>	
			</select>
		</form>
	</div>
</div>
<h1>Dvs. ati aplicat pentru urmatoarele burse</h1>
<style>
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

</style>
</head>
<body>
        
<table id="t01">
  <tr>
    <th>Tip bursa</th>
    <th>Statut</th> 
  </tr>
  <div style = "color:blue;">
                    <?php $tmp=0;
                     foreach ($aplicari as $burse) :?>
                            <?php if ($tmp == 0): ?>
                                <tr>
                            <?php endif; ?>
                            <td>
                            <?php
                            echo $burse;
                            ++$tmp;
                            ?>
                            </td> 
                            <?php if ($tmp == 2): $tmp =0; ?>
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

