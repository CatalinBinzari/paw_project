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
		$stmt = $dbConn->prepare('INSERT INTO lista_de_asteptare(`cnp`,`nume`,`prenume`,`data_nasterii`,`email`,`parola`)
			 VALUES(:cnp, :nume, :prenume, :data_nasterii, :email, :parola)');
		$stmt -> execute (array('cnp' => $_POST['cnp'], 'nume' => $_POST['user_name'], 'prenume' => $_POST['user_prename'],
		                        'data_nasterii' => $_POST['data'], 'email' => $_POST['email'], 'parola' => sha1($_POST['parola'].SALT) ));
	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<title> Burse scoalri </title>
	<meta charset="UTF-8">
</head>
<body>
<h1>Pagina de inregistrare</h1>
<div>
	<form method="POST">
		<div style = "color:red;">
			<?php foreach ($errors as $error) :?>
				<p><?php echo $error;?></p>
			<?php endforeach; ?>
		</div>
		<div>
			<label>Nume:</label>
			<div>
				<input type="text" name="user_name" required="" value="<?php echo (!empty($_POST['user_name']) ? $_POST['user_name'] : '');?>"/>
			</div>
		</div>
		<div>
			<label>Prenume:</label>
			<div>
				<input type="text" name="user_prename" required="" value="<?php echo (!empty($_POST['user_prename']) ? $_POST['user_prename'] : '');?>"/>
			</div>
		</div>
		<div>
			<label>Cnp:</label>
			<div>
				<input type="text" name="cnp" required="" value="<?php echo (!empty($_POST['cnp']) ? $_POST['cnp'] : '');?>"/>
			</div>
		</div>
		<div>
			<label>Data nastere ex'1990-09-01':</label>
			<div>
				<input type="text" name="data" required="" value="<?php echo (!empty($_POST['data']) ? $_POST['data'] : '');?>"/>
			</div>
		</div>
		<div>
			<label>Email:</label>
			<div>
				<input type="text" name="email" required="" value="<?php echo (!empty($_POST['email']) ? $_POST['email'] : '');?>"/>
			</div>
		</div>
		<div>
			<label>Parola:</label>
			<div>
				<input type="password" name="parola" required="" value=""/>
			</div>
		</div>
		<div>
			<label>Confirmare parola:</label>
			<div>
				<input type="password" name="confirmare_parola" required="" value=""/>
			</div>
		</div>
		<div>
			<br/>
			<input type="submit" name="submit" value="Inregistrare">
		</div>
	</form>
</div>
</body>
</html>
