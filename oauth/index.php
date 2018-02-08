<?php
session_start();
include("header.html");
?>

	<body>
        <main role="main" class="container">
		    <form method="post" action="connexion.php" class="form-signin">
                <h2 class="form-signin-heading">Mattermost OAuth</h2>
                <div class="form-group">
					<label for="user" class="sr-only">Username</label>
                    <input name="user" type="text" class="form-control" placeholder="email address" id="user" autofocus/>
                </div>
                <div class="form-group">
					<label for="password" class="sr-only">Password</label>
                    <input type="password" class="form-control" name="password" placeholder="email password" id="password" />
                </div>
                <div class="form-group">
                    <button class="btn btn-primary" type="submit">Sign in</button>
                </div>
            </form>
        </main>
	</body>
<?php
include("footer.html");
?>
