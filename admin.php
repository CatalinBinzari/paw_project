<?php
require_once("config.php");
if (!empty($_SESSION['admin_id'])){
	header("location: administrare.php");
}
$errors = [];
if (!empty($_POST)){
	if (empty($_POST['admin'])){
		$errors[] = 'Introduceti emailul';
	}
	if (empty($_POST['parola'])){
		$errors[] = 'Introduceti parola';
	}
	if (empty($errors)){
		$stmt = $dbConn->prepare("SELECT id_admin FROM administratori WHERE email = :admin and parola = :parola");
		$stmt -> execute (array('admin' => $_POST['admin'], 'parola' => $_POST['parola']));
		$id_admin = $stmt -> fetchColumn();
		if (!empty($id_admin)) {
			$_SESSION['admin_id'] = $id_admin;
			echo 'Id admin: ' . $_SESSION['admin_id'] . '<br>';
			header("location: administrare.php");

		} else {
			$errors[] = 'Va rog Introduceti datele corecte';
		}
	}
}
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    
    <title>Pagina admin</title>

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
  <h1 class="h3 mb-3 font-weight-normal">Pagina de administrare</h1>



  <label for="admin" class="sr-only">Adresa de email</label>
  <input type="text" name="admin" id="admin" class="form-control" placeholder="Adresa de email"
   required=required autofocus value="<?php echo (!empty($_POST['admin']) ? $_POST['admin'] : '');?>"/>

  <label for="inputPassword" class="sr-only">Parola</label>
  <input type="password" name="parola" id="inputPassword" class="form-control" placeholder="Parola" required>

  <div style = "color:red;">
      <?php foreach ($errors as $error) :?>
        <p><?php echo $error;?></p>
      <?php endforeach; ?>
    </div>
    <br>
  <button class="btn btn-lg btn-primary btn-block" type="submit">Acces</button>
  <p class="mt-5 mb-3 text-muted">&copy; Catalin Binzari 2020</p>
</form>
</body>
</html>