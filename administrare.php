<?php
require_once("config.php");
//echo "in administrare" . '<br>' ;
if (empty($_SESSION['admin_id'])){
	header("location: admin.php");
}

$delogare = $_GET['delogare'] ?? false;
if ($delogare == true){
  session_destroy();
  header("Refresh:0");
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



$stmt = $dbConn->prepare("SELECT id_elev, cnp, nume, prenume, id_scoala, medie 
                            FROM elevi
                            ");

$stmt->execute();
// set the resulting array to associative
  $elevi = [];
  $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
  foreach(new TableRows(new RecursiveArrayIterator($stmt->fetchAll())) as $k=>$v) {
    $elevi[] =$v ;
  }

?>
<?php
    if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['migrare']))
    {
        migrare($dbConn,  $_POST['cnp_cont']);
    }
    else if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['declin']))
    {
        declin($dbConn,  $_POST['cnp_cont']);
    }
    else if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['actiune_bursa']))
    {
        //echo "actiune,bursa";
        actiune_pe_bursa($dbConn,  $_POST['cnp_cerere_bursa'],
         $_POST['tip_actiune_bursa'], $_POST['tip_bursa']);
    }
    else if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['migrare_acceptati']))
    {
        //echo "acceptati,bursa";
        migrare_acceptati($dbConn,  $_POST['id_acceptat'], $_POST['tip_bursa_acceptat']);
    }
    if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['migrare_toti']))
    {
        //migrare_toti($dbConn);
    }
    else if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['anulare_acceptati']))
    {
        ///echo "anulare,bursa";
        anulare_acceptati($dbConn,  $_POST['id_acceptat'], $_POST['tip_bursa_acceptat']);
    }
    else if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['filtrare']))
    {
        //echo "filtrare ";
        filtrare($_POST['filtru_cnp'], $_POST['filtru_nume'], $_POST['filtru_prenume'], $_POST['filtru_scoala'], $filter);
        //echo 'filer2:'.$filter;
        $stmt = $dbConn->prepare("SELECT id_elev, cnp, nume, prenume, id_scoala, medie 
                            FROM elevi
                            $filter
                            ");
        //echo $filter;
        $stmt->execute();
        $elevi = [];
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        foreach(new TableRows(new RecursiveArrayIterator($stmt->fetchAll())) as $k=>$v) {
            $elevi[] =$v ;
        }

    }
    else if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['setare_medie']))
    {
        echo "setare,nota";
        setare_medie($dbConn, $_POST['medie_setare_elev'], $_POST['id_elev_medie']);
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
        $sql = "insert into logs
                   (utilizator_id, functie, actiune_logica) 
                values ('".$_SESSION['admin_id']."','admin','migrare utilizator cu CNP $cnp') ";
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
    function declin($dbConn,  $cnp)
    { 
      $dbConn->beginTransaction();
      $dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      
        $sql = "insert into logs
                   (utilizator_id, functie, actiune_logica) 
                values ('".$_SESSION['admin_id']."','admin','declin utilizator cu CNP $cnp') ";
        $dbConn->exec($sql);
    $sql = "delete from 
            lista_de_asteptare
           where 
            cnp = $cnp;";
      $dbConn->exec($sql);
      $dbConn->commit();
        header("Refresh:0");
        echo "declin reusit"; 
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
        $sql = "insert into logs
                   (utilizator_id, functie, actiune_logica) 
                values ('".$_SESSION['admin_id']."','admin','setare 
                $tip_actiune_bursa la bursa $tip_bursa utilizatorului cu id: $id_elev') ";
        $dbConn->exec($sql);
        
        //$sql = "";
        //$dbConn->exec($sql);
        $dbConn->commit();
        header("Refresh:0");
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
         $sql = "insert into logs
                   (utilizator_id, functie, actiune_logica) 
                values ('".$_SESSION['admin_id']."','admin',
                'insertare in tabel bursieri utlizator cu id: $id si tip bursa: bursa $tip_bursa_acceptat') ";
        $dbConn->exec($sql);
        $dbConn->commit();
        header("Refresh:0");
        echo "migrare reusita"; 
    }
    function anulare_acceptati($dbConn,  $id, $tip_bursa_acceptat)
    {   
        $dbConn->beginTransaction();
        $dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "UPDATE 
                    cereri_bursa
                SET aprobare = 'In asteptare',vazut_de_admin = '0'

                 WHERE 
                    id_elev = $id AND
                    tip_bursa = '$tip_bursa_acceptat'
                    ";
        $dbConn->exec($sql);
         $sql = "insert into logs
                   (utilizator_id, functie, actiune_logica) 
                values ('".$_SESSION['admin_id']."','admin',
                'anulare bursa bursieri utlizator cu id: $id si tip bursa: bursa $tip_bursa_acceptat') ";
        $dbConn->exec($sql);
        $dbConn->commit();
        header("Refresh:0");
        echo "migrare reusita"; 
    }
    function filtrare($cnp, $nume, $prenume, $scoala, &$filter)
    {   
        //echo $cnp . "," . $nume . "," .$prenume . "," .$scoala . ",";
        $conditions = array();
        if(! empty($cnp)) {
             $conditions[] = "cnp='$cnp'";
        }
        if(! empty($nume)) {
             $conditions[] = "nume='$nume'";
        }
        if(! empty($prenume)) {
             $conditions[] = "prenume='$prenume'";
        }
        if(! empty($scoala)) {
             $conditions[] = "id_scoala='$scoala'";
        }
        if (count($conditions) > 0) {
             $filter .= " WHERE " . implode(' AND ', $conditions);
        }

       //$filter= "where cnp = 188";
        //echo $filter;
    }
    function setare_medie($dbConn, $medie, $id_elev)
    {   echo $medie.$id_elev;
        $dbConn->beginTransaction();
        $dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "UPDATE elevi
                SET medie = '$medie' 
                WHERE id_elev = '$id_elev'"
                ;
        $dbConn->exec($sql);
        $sql = "insert into logs
                   (utilizator_id, functie, actiune_logica) 
                values ('".$_SESSION['admin_id']."','admin',
                'setare medie: $medie utilizatoruluicu id: $id_elev') ";
        $dbConn->exec($sql);
        $dbConn->commit();
        header("Refresh:0");
         
    }
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    
    <title>Pagina administrare burse</title>


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
  <h5 class="my-0 mr-md-auto font-weight-normal"><a href="administrare.php">Home</h5>
  <a class="btn btn-outline-primary mx-sm-3 " href="diagrama.php">Statistica</a>

  <a class="btn btn-outline-primary" href="burse.php">Mergi la burse</a>

  <a class="btn btn-outline-primary mx-sm-3" href="administrare.php?delogare=true">Delogare</a>
</div>

<div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
  <h1 class="display-4">Administrare conturi</h1>
  <p class="lead"></p>
</div>
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
      <div class="d-flex justify-content-start form-group mx-sm-3 mb-2">
    		<input type="submit" name="migrare" class="btn btn-outline-primary" value="Mirgrare utilizator cu cnp: " />
			  <input class="mx-sm-3"type="text" name="cnp_cont" required="" placeholder="CNP" value=""/>
        <input type="submit" name="declin" class="btn btn-outline-danger" value="Declin " />
			</div>
		</form>
	</div>
</div>
<div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
  <h1 class="display-4">Administrare burse</h1>
  <p class="lead"></p>
</div>
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
            <div class="d-flex justify-content-start form-group mx-sm-3 mb-2">
                <input type="submit" name="actiune_bursa" class="btn btn-outline-primary" value="Executare pentru utilizatorul cu id elev: " />
                <input class="form-group mx-sm-3 mb-2" type="text" name="cnp_cerere_bursa" placeholder="ID elev" required="" value=""/>

                <select name="tip_bursa" class=" btn-secondary btn-sm dropdown-toggle" size="1">
                <option value="sociala">Bursa sociala</option>
                <option value="performanta">Bursa de performanta</option>
                <option value="merit">Bursa de merit</option>
                </select>

                <select name="tip_actiune_bursa" class=" btn-secondary btn-sm dropdown-toggle mx-sm-3 " size="1">
                <option value="refuz">Refuz</option>
                <option value="vazut">Vazut</option>
                <option value="acceptat">Acceptat</option>   
                </select>
            </div>
        </form>
    </div>
</div>
<div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
  <h1 class="display-4">Bursieri acceptati</h1>
  <p class="lead"></p>
</div>
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
            <div class="d-flex justify-content-start form-group mx-sm-3 mb-2">
            <input type="submit" name="migrare_acceptati" class="btn btn-outline-primary" value="Migrare acceptati in tabelul oficial: " />
            <input class="form-group mx-sm-3 mb-2" type="text" name="id_acceptat" required="" placeholder="ID elev" value=""/>

            <select name="tip_bursa_acceptat" class=" btn-secondary btn-sm dropdown-toggle" size="1">
            <option value="sociala">Bursa sociala</option>
            <option value="performanta">Bursa de performanta</option>
            <option value="merit">Bursa de merit</option>
            </select>
            <input type="submit" name="anulare_acceptati" class="btn btn-outline-danger mx-sm-3" value="Anulare" />
            </div>
            
        </form>
        <form action="administrare.php" method="POST">
          <input type="submit" name="migrare_toti" class="btn btn-outline-warning form-group mx-sm-3 mb-2" value="Migreaza toti utilizatorii" />
        </form>
    </div>
</div>

<div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
  <h1 class="display-4">Seteaza media</h1>
  <p class="lead"></p>
</div>
<div>
    <form action="administrare.php" method="POST">
        <div class="d-flex justify-content-start form-group mx-sm-3 mb-2">
            <input class="btn btn-outline-primary" type="submit" name="filtrare" value="Filtrare" />
            <input class="form-group mx-sm-3 mb-2" type="text" name="filtru_cnp"  placeholder="CNP" value="<?php echo (!empty($_POST['filtru_cnp']) ? $_POST['filtru_cnp'] : '');?>"/>
            <input  type="text" name="filtru_nume"  placeholder="Nume" value="<?php echo (!empty($_POST['filtru_nume']) ? $_POST['filtru_nume'] : '');?>"/>
            <input class="form-group mx-sm-3 mb-2" type="text" name="filtru_prenume"  placeholder="Prenume" value="<?php echo (!empty($_POST['filtru_prenume']) ? $_POST['filtru_prenume'] : '');?>"/>
            <input  type="text" name="filtru_scoala"  placeholder="ID scoala" value="<?php echo (!empty($_POST['filtru_scoala']) ? $_POST['filtru_scoala'] : '');?>"/>
        </div>
    </form>
</div>
<div>
     <table id="t01">
  <tr>
    <th>ID elev</th>
    <th>CNP</th>
    <th>Nume</th> 
    <th>Prenume</th> 
    <th>Scoala</th>
    <th>Medie</th>  
    
  </tr>
  <div style = "color:blue;">
                    <?php $tmp=0;
                     foreach ($elevi as $elev) :?>
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
        <br/>
        <form action="administrare.php" method="POST">
            <div  class="d-flex justify-content-start form-group mx-sm-3 mb-2">
                <input type="submit" class="btn btn-outline-primary" name="setare_medie" value="Setare medie:" />
                <input class= "mx-sm-3" type="text" name="medie_setare_elev" required="" placeholder="Indicati media"value=""/>
                <input type="text" name="id_elev_medie" required="" placeholder="Alegeti ID elev"value=""/>
            </div>
        </form>
    </div>
</div>
</body>
</html>
