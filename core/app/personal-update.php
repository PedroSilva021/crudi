<?php
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$RFC = "";
$Nombre = "";
$Ap_Paterno = "";
$Ap_Materno = "";

$RFC_err = "";
$Nombre_err = "";
$Ap_Paterno_err = "";
$Ap_Materno_err = "";


// Processing form data when form is submitted
if(isset($_POST["Id_personal"]) && !empty($_POST["Id_personal"])){
    // Get hidden input value
    $Id_personal = $_POST["Id_personal"];

        // Prepare an update statement

        $RFC = trim($_POST["RFC"]);
		$Nombre = trim($_POST["Nombre"]);
		$Ap_Paterno = trim($_POST["Ap_Paterno"]);
		$Ap_Materno = trim($_POST["Ap_Materno"]);
		

        $dsn = "mysql:host=$db_server;dbname=$db_name;charset=utf8mb4";
        $options = [
          PDO::ATTR_EMULATE_PREPARES   => false, // turn off emulation mode for "real" prepared statements
          PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //make the default fetch be an associative array
        ];
        try {
          $pdo = new PDO($dsn, $db_user, $db_password, $options);
        } catch (Exception $e) {
          error_log($e->getMessage());
          exit('Something weird happened');
        }
        $stmt = $pdo->prepare("UPDATE personal SET RFC=?,Nombre=?,Ap_Paterno=?,Ap_Materno=? WHERE Id_personal=?");

        if(!$stmt->execute([ $RFC,$Nombre,$Ap_Paterno,$Ap_Materno,$Id_personal  ])) {
                echo "Something went wrong. Please try again later.";
                header("location: error.php");
            } else{
                $stmt = null;
                header("location: personal-read.php?Id_personal=$Id_personal");
            }
} else {
    // Check existence of id parameter before processing further
	$_GET["Id_personal"] = trim($_GET["Id_personal"]);
    if(isset($_GET["Id_personal"]) && !empty($_GET["Id_personal"])){
        // Get URL parameter
        $Id_personal =  trim($_GET["Id_personal"]);

        // Prepare a select statement
        $sql = "SELECT * FROM personal WHERE Id_personal = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            // Set parameters
            $param_id = $Id_personal;

            // Bind variables to the prepared statement as parameters
			if (is_int($param_id)) $__vartype = "i";
			elseif (is_string($param_id)) $__vartype = "s";
			elseif (is_numeric($param_id)) $__vartype = "d";
			else $__vartype = "b"; // blob
			mysqli_stmt_bind_param($stmt, $__vartype, $param_id);

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);

                if(mysqli_num_rows($result) == 1){
                    /* Fetch result row as an associative array. Since the result set
                    contains only one row, we don't need to use while loop */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                    // Retrieve individual field value

                    $RFC = $row["RFC"];
					$Nombre = $row["Nombre"];
					$Ap_Paterno = $row["Ap_Paterno"];
					$Ap_Materno = $row["Ap_Materno"];
					

                } else{
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php");
                    exit();
                }

            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        // Close statement
        mysqli_stmt_close($stmt);

    }  else{
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Record</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
</head>
<body>
    <section class="pt-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="page-header">
                        <h2>Update Record</h2>
                    </div>
                    <p>Please edit the input values and submit to update the record.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">

                        <div class="form-group">
                            <label>RFC</label>
                            <input type="text" name="RFC" maxlength="100"class="form-control" value="<?php echo $RFC; ?>">
                            <span class="form-text"><?php echo $RFC_err; ?></span>
                        </div>
						<div class="form-group">
                            <label>Nombre</label>
                            <input type="text" name="Nombre" maxlength="555"class="form-control" value="<?php echo $Nombre; ?>">
                            <span class="form-text"><?php echo $Nombre_err; ?></span>
                        </div>
						<div class="form-group">
                            <label>Apellido Paterno</label>
                            <input type="text" name="Ap_Paterno" maxlength="555"class="form-control" value="<?php echo $Ap_Paterno; ?>">
                            <span class="form-text"><?php echo $Ap_Paterno_err; ?></span>
                        </div>
						<div class="form-group">
                            <label>Apellido Materno</label>
                            <input type="text" name="Ap_Materno" maxlength="555"class="form-control" value="<?php echo $Ap_Materno; ?>">
                            <span class="form-text"><?php echo $Ap_Materno_err; ?></span>
                        </div>

                        <input type="hidden" name="Id_personal" value="<?php echo $Id_personal; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="personal-index.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
