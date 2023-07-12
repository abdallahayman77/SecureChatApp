<?php require_once 'server/server.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="resources/css/index.css?"<?php echo time(); ?>>
  <link rel="stylesheet" type="text/css" href="resources/css/queries.css?"<?php echo time(); ?>>
  <link rel="stylesheet" type="text/css" href="vendors/css/bootstrap.min.css?"<?php echo time(); ?>>
  <link rel="stylesheet" type="text/css" href="vendors/css/normalize.css?"<?php echo time(); ?>>
  <link rel="stylesheet" type="text/css" href="vendors/css/Grid.css?"<?php echo time(); ?>>
  <script src="https://kit.fontawesome.com/4733528720.js" crossorigin="anonymous"></script>
    <title>Secured Chat App</title>
</head>

<body>
    <div class="title-chat-c">
        <div class="title-chat" style="width:100%">
        <!-- Title -->
        <h1 class="justify-content-left d-inline">Secured Chat App</h1>      
        </div>
    </div>
    <div class="login-form">
        <h1 style="text-align: center; font-size:64px;">Login</h1>
        <form action="" method="POST" style="max-width: 500px; margin: 20px auto;">
            <div class="form-group">
                <input type="text" class="form-control" placeholder="Username" required name="username">
            </div>
            <div class="input-group mb-3">
                <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password" name="password" required>
                <div class="input-group-append">
                    <button type="submit" class="btn btn-outline-primary w-100" name="login">Login</button>
                </div>
            </div>
        </form>
        <div id="small" style="text-align: center;">
            <small><a href="register.php" class="text-secondary">Register?</a></small>
        </div>
    </div>
</body>
</html>

<script>
	if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>