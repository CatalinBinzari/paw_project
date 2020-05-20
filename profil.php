<?php
require_once("config.php");
if (empty($_SESSION['user_id'])){
	header("location: login.php");
}
//utlizatorul poate aplica pentru una din cele 3 burse 
//utilizatorul poate vedea bursierii si sa-i sorteze?

echo $_SESSION['user_id'];
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
    	$dbConn->commit();
        header("Refresh:0");
        echo "Sters."; 
    }
?>
<!DOCTYPE html>
<html>
<head>
	<title> Profil utilizator </title>
	<meta charset="UTF-8">
</head>
<body>
<h1>Pagina utilizatorului</h1>
<div>
	<div>
		<br/>
		<form action="profil.php" method="POST">
    		<input type="submit" name="aplicare" value="Aplica pentru o bursa" />

			<select name="tip_bursa_aplica" size="1">
			<option value="sociala">Bursa sociala</option>
			<option value="performanta">Bursa de performanta</option>
			<option value="merit">Bursa de merit</option>
			</select>
		<br/>
			<input type="submit" name="stergere_aplicare" value="Sterge aplicarea" />

			<select name="tip_bursa_stearsa" size="1">
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
</body>
</html>

