<?php
require_once("config.php");
echo "in administrare" . '<br>' ;
if (!empty($_SESSION['admin_id'])){
	echo $_SESSION['admin_id'];
}
else {
	header("location: admin.php");
}

//scot toti userii din lista de asteptare pentru a putea fi migrati 
$stmt = $dbConn->prepare("SELECT cnp,nume,prenume,data_nasterii,id_scoala,email FROM lista_de_asteptare");
$stmt->execute();
// set the resulting array to associative
  $elevi_in_asteptare = [];
  $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
  foreach(new TableRows(new RecursiveArrayIterator($stmt->fetchAll())) as $k=>$v) {
    $elevi_in_asteptare[] =$v ;
  }
$stmt = $dbConn->prepare("SELECT id_elev, tip_bursa, aprobare FROM cereri_bursa WHERE vazut_de_admin = 0;");
$stmt->execute();
// set the resulting array to associative
  $burse_in_asteptare = [];
  $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
  foreach(new TableRows(new RecursiveArrayIterator($stmt->fetchAll())) as $k=>$v) {
    $burse_in_asteptare[] =$v ;
  }
$stmt = $dbConn->prepare("SELECT id_elev, tip_bursa, aprobare FROM cereri_bursa WHERE vazut_de_admin = 1  AND aprobare = 'acceptat';");
$stmt->execute();
// set the resulting array to associative
  $acceptati = [];
  $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
  foreach(new TableRows(new RecursiveArrayIterator($stmt->fetchAll())) as $k=>$v) {
    $acceptati[] =$v ;
  }
?>
<?php
    if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['migrare']))
    {
        migrare($dbConn,  $_POST['cnp_cont']);
    }
    else if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['actiune_bursa']))
    {
        echo "actiune,bursa";
        actiune_pe_bursa($dbConn,  $_POST['cnp_cerere_bursa'],
         $_POST['tip_actiune_bursa'], $_POST['tip_bursa']);
    }
    else if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['migrare_acceptati']))
    {
        echo "acceptati,bursa";
        migrare_acceptati($dbConn,  $_POST['id_acceptat'], $_POST['tip_bursa_acceptat']);
    }
    function migrare($dbConn,  $cnp)
    {	
    	$dbConn->beginTransaction();
    	$dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    	$sql = "insert into elevi
    				(cnp, nume, prenume, data_nasterii, id_scoala, email, parola) 
    			select 
    				cnp, nume, prenume, data_nasterii, id_scoala, email, parola
    			 from 
    			 	lista_de_asteptare
    			 where 
    			 	cnp = $cnp;";
		$dbConn->exec($sql);
		$sql = "delete from 
    			 	lista_de_asteptare
    			 where 
    			 	cnp = $cnp;";
    	$dbConn->exec($sql);
    	$dbConn->commit();
        header("Refresh:0");
        echo "migrare reusita"; 
    }
    function actiune_pe_bursa($dbConn,  $id_elev, $tip_actiune_bursa, $tip_bursa)
    {   
        $dbConn->beginTransaction();
        $dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "UPDATE cereri_bursa
                SET aprobare = '$tip_actiune_bursa', vazut_de_admin = 1 
                WHERE id_elev = $id_elev AND tip_bursa= '$tip_bursa'"
                ;
        $dbConn->exec($sql);
        
        //$sql = "";
        //$dbConn->exec($sql);
        $dbConn->commit();
        //header("Refresh:0");
        echo "migrare reusita"; 
    }
    function migrare_acceptati($dbConn,  $id, $tip_bursa_acceptat)
    {   
        $dbConn->beginTransaction();
        $dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "INSERT INTO  bursieri
                    (id_elev, tip_bursa) 
                VALUES ('$id', '$tip_bursa_acceptat')"
                ;
        $dbConn->exec($sql);
        $sql = "DELETE FROM 
                    cereri_bursa
                 WHERE 
                    id_elev = $id AND
                    tip_bursa = '$tip_bursa_acceptat' AND
                    aprobare = 'acceptat'
                    ";
        $dbConn->exec($sql);
        $dbConn->commit();
        header("Refresh:0");
        echo "migrare reusita"; 
    }
?>
<!DOCTYPE html>
<html>
<head>
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
	<title> Administrare burse </title>
	<meta charset="UTF-8">
</head>
<body>
<h1>Administrare conturi</h1>
<table id="t01">
  <tr>
    <th>Cnp</th>
    <th>Nume</th>
    <th>Prenume</th> 
    <th>Data nasterii</th>
    <th>ID scoala</th>
    <th>Email</th>
  </tr>
  <div style = "color:blue;">
                    <?php $tmp=0;
                     foreach ($elevi_in_asteptare as $elev) :?>
                            <?php if ($tmp == 0): ?>
                                <tr>
                            <?php endif; ?>
                            <td>
                            <?php
                            echo $elev;
                            ++$tmp;
                            ?>
                            </td> 
                            <?php if ($tmp == 6): $tmp =0; ?>
                                </tr>
                            <?php endif; ?>
                           
                    <?php endforeach; ?>
  </div>
</table>
<div>
	<div>
		<br/>
		<form action="administrare.php" method="POST">
    		<input type="submit" name="migrare" value="Mirgrare utilizator cu cnp: " />
			<input type="text" name="cnp_cont" required="" value=""/>
			
		</form>
	</div>
</div>
<h1>Administrare burse</h1>
<div>
    <table id="t01">
  <tr>
    <th>ID elev</th>
    <th>Tip bursa</th>
    <th>Statut</th> 
    
  </tr>
  <div style = "color:blue;">
                    <?php $tmp=0;
                     foreach ($burse_in_asteptare as $bursa) :?>
                            <?php if ($tmp == 0): ?>
                                <tr>
                            <?php endif; ?>
                            <td>
                            <?php
                            echo $bursa;
                            ++$tmp;
                            ?>
                            </td> 
                            <?php if ($tmp == 3): $tmp =0; ?>
                                </tr>
                            <?php endif; ?>
                           
                    <?php endforeach; ?>
  </div>
</table>
    <div>
        <br/>
        <form action="administrare.php" method="POST">
            <input type="submit" name="actiune_bursa" value="Executare pentru utilizatorul cu id elev: " />
            <input type="text" name="cnp_cerere_bursa" required="" value=""/>

            <select name="tip_bursa" size="1">
            <option value="sociala">Bursa sociala</option>
            <option value="performanta">Bursa de performanta</option>
            <option value="merit">Bursa de merit</option>
            </select>

            <select name="tip_actiune_bursa" size="1">
            <option value="refuz">Refuz</option>
            <option value="vazut">Vazut</option>
            <option value="acceptat">Acceptat</option>   
            </select>
        </form>
    </div>
</div>
<h1>Bursieri acceptati </h1>
<div>
     <table id="t01">
  <tr>
    <th>ID elev</th>
    <th>Tip bursa</th>
    <th>Statut</th> 
    
  </tr>
  <div style = "color:blue;">
                    <?php $tmp=0;
                     foreach ($acceptati as $acceptat) :?>
                            <?php if ($tmp == 0): ?>
                                <tr>
                            <?php endif; ?>
                            <td>
                            <?php
                            echo $acceptat;
                            ++$tmp;
                            ?>
                            </td> 
                            <?php if ($tmp == 3): $tmp =0; ?>
                                </tr>
                            <?php endif; ?>
                           
                    <?php endforeach; ?>
  </div>
  </table>
    <div>
        <br/>
        <form action="administrare.php" method="POST">
            <input type="submit" name="migrare_acceptati" value="Migrare acceptati in tabelul oficial: " />
            <input type="text" name="id_acceptat" required="" value=""/>

            <select name="tip_bursa_acceptat" size="1">
            <option value="sociala">Bursa sociala</option>
            <option value="performanta">Bursa de performanta</option>
            <option value="merit">Bursa de merit</option>
            </select>
        </form>
    </div>
</div>
</body>
</html>