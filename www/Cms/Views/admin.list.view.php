<?php if(isset($errors)):?>

<?php foreach ($errors as $error):?>
	<li style="color:red"><?=$error;?></li>
<?php endforeach;?>

<?php endif;?>

<?php if(isset($message)):?>
    <h3> <?=$message?> </h3>
<?php endif;?>

<main class="main-container">
    <?php if(!empty($content)){ echo $content;}?>
    <?php !empty($list) && App\Core\ListBuilder::render($list)?>
</main>