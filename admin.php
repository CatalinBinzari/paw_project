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
	<title> My Guest Book</title>
	<meta charset="UTF-8">
</head>
<body>
<h1>Pagina de autentificare</h1>
<div>
	<form method="POST">
		<div style = "color:red;">
			<?php foreach ($errors as $error) :?>
				<p><?php echo $error;?></p>
			<?php endforeach; ?>
		</div>
		<div>
			<label>Email:</label>
			<div>
				<input type="text" name="admin" required="" value="<?php echo (!empty($_POST['admin']) ? $_POST['admin'] : '');?>"/>
			</div>
		</div>
		<div>
			<label>Parola:</label>
			<div>
				<input type="password" name="parola" required="" value=""/>
			</div>
		</div>
		<div>
			<br/>
			<input type="submit" name="submit" value="Login">
		</div>
		</form>
</div>
</body>
</html>