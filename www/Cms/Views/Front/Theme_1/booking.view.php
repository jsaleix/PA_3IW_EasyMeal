    <?php if(isset($errors)):?>

    <?php foreach ($errors as $error):?>
        <li style="color:red"><?=$error;?></li>
    <?php endforeach;?>

    <?php endif;?>

    <?php if(isset($message)):?>
        <h3> <?=$message?> </h3>
    <?php endif;?>
    <div class="row" style="justify-content: center;">
        <?php App\Core\FormBuilder::render($form)?>    
    </div>