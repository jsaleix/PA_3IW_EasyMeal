<h1>Easy Meal</h1>
<hr>
<section>
	<?php if(isset($connected) && $connected):?>
		<h2>Welcome <?= $pseudo;?></h2>
		<a href="logout">Logout</a>
		<a href="account">My account</a>
		<a href="account/mysites">My sites</a>
	<?php else: ?>
		<a href="login">Login</a>
		<a href="register">Register</a>
	<?php endif;?>
</section>


