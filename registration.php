<?php
require_once("config.php");
if (!empty($_SESSION['user_id'])){
	header("location: profil.php");
}
$errors = [];//va arata erorile de inauntru
if (!empty($_POST)){
	if(empty($_POST['user_name'])) {
		$errors[] = 'Va rog introduceti numele utilizator';
	}
	if(empty($_POST['user_prename'])) {
		$errors[] = 'Va rog introduceti prenumele';
	}
	if(empty($_POST['cnp'])) {
		$errors[] = 'Va rog introduceti cnp';
	}
	if(empty($_POST['data'])) {
		$errors[] = 'Va rog introduceti data nasterii';
	}
	if(empty($_POST['email'])) {
		$errors[] = 'Va rog introduceti email-ul';
	}
	if(empty($_POST['parola'])) {
		$errors[] = 'Va rog introduceti parola';
	}
	if(empty($_POST['confirmare_parola'])) {
		$errors[] = 'Va rog confirmati parola';
	}
	if(strlen($_POST['parola']) < 6) {
		$errors[] ='Parola  este prea scurta';
	}
	if($_POST['parola'] !== $_POST['confirmare_parola']) {
		$errors[] ='Parola nu coincide';
	}
	if(empty($errors)) {
		$stmt = $dbConn->prepare('INSERT INTO lista_de_asteptare(`cnp`,`nume`,`prenume`,`data_nasterii`,`id_scoala`,`email`,`parola`)
			 VALUES(:cnp, :nume, :prenume, :data_nasterii, :id_scoala, :email, :parola)');
		$stmt -> execute (array('cnp' => $_POST['cnp'], 'nume' => $_POST['user_name'], 'prenume' => $_POST['user_prename'],
		                        'data_nasterii' => $_POST['data'],'id_scoala' => $_POST['id_scoala'], 'email' => $_POST['email'], 'parola' => sha1($_POST['parola'].SALT) ));
		header("location: login.php");
	}
}

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Pagina administrare burse</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/4.5/examples/sign-in/">

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
    </style>
    <!-- Custom styles for this template -->
    <link href="signin.css" rel="stylesheet">
  </head>
  <body class="text-center">
    <form class="form-signin" method="POST">
		  <img class="mb-4" src="assets/img/book" alt="" width="72" height="72">
		  <h1 class="h3 mb-3 font-weight-normal">Pagina de inregistrare</h1>

		   <label for="name" class="sr-only">Nume</label>
		  <input type="text" name="user_name" id="name" class="form-control" placeholder="Nume de familie"
		   required=required autofocus value="<?php echo (!empty($_POST['user_name']) ? $_POST['user_name'] : '');?>"/>

		   <label for="prename" class="sr-only">Nume</label>
		  <input type="text" name="user_prename" id="prename" class="form-control" placeholder="Prenume"
		   required=required autofocus value="<?php echo (!empty($_POST['user_prename']) ? $_POST['user_prename'] : '');?>"/>

		   <label for="cnp" class="sr-only">cnp</label>
		  <input type="text" name="cnp" id="cnp" class="form-control" placeholder="CNP"
		   required=required autofocus value="<?php echo (!empty($_POST['cnp']) ? $_POST['cnp'] : '');?>"/>

		   <label for="data" class="sr-only">Data nastere (Ex. 1990-09-01)</label>
		  <input type="text" name="data" id="data" class="form-control" placeholder="Data nastere (Ex. 1990-09-01)"
		   required=required autofocus value="<?php echo (!empty($_POST['data']) ? $_POST['data'] : '');?>"/>

		   <select name="id_scoala" class=" btn-sm dropdown-toggle" size="1">
                <option value="1">SCOALA  NR.1 "NICOLAE MANTU"</option>
                <option value="2">SCOALA  NR.2 </option>
                <option value="3">SCOALA  NR.3 "I.L.CARAGIALE"</option>
                <option value="4">SCOALA  NR.5  "CUZA VODA"  </option>
                <option value="5">SCOALA  NR.7  "CONSTANTIN BRANCOVE"</option>
           </select>

		  <label for="inputEmail" class="sr-only">Adresa de email</label>
		  <input type="email" name="email" id="inputEmail" class="form-control" placeholder="Adresa de email"
		   required=required autofocus value="<?php echo (!empty($_POST['email']) ? $_POST['email'] : '');?>"/>
		   <br>
		  <label for="inputPassword" class="sr-only">Parola</label>
		  <input type="password" name="parola" id="inputPassword" 
		  class="form-control" placeholder="Parola" required>

		  <label for="confirmPassword" class="sr-only">Parola</label>
		  <input type="password" name="confirmare_parola" id="confirmPassword" 
		  class="form-control" placeholder="Confirmare parola" required>

		  <br>
		  <div style = "color:red;">
		      <?php foreach ($errors as $error) :?>
		        <p><?php echo $error;?></p>
		      <?php endforeach; ?>
		  </div>
		  <button class="btn btn-lg btn-primary btn-block" type="submit" name="submit" >Inregistrare</button>

		  <p class="mt-5 mb-3 text-muted">&copy; Catalin Binzari 2020</p>
	</form>
</body>
</html>
