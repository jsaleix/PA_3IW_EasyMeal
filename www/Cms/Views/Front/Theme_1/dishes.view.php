<section id="menu-page-section">
    <?php if(isset($categories) && $categories && count($categories) > 0): ?>
        <?php foreach($categories as $category): ?>
            <?php if(count($category['dishes']) > 0): ?>
                <div class="menu-container">
                    <h2><?= $category['category']['name']?></h2></a>
                    <div class="dishes-row">
                        <?php foreach($category['dishes'] as $dish): ?>
                            <div class="dish-item">
                                <a class="dish-picture" href="ent/dish?id=<?= $dish['id'] ?>" ><img class="dish-picture" src='<?=  DOMAIN . '/' . $dish['image'] ?>'/></a>
                                <h3><?= $dish['name'] ?></h3>
                                <p>#<?= $dish['category'] ?></p>
                                <p>$<?= $dish['price'] ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach;?>
    <?php endif; ?>

</section>

<style>

    .menu-container{
        box-shadow: 0 6px 6px rgba(0,0,0,0.2);
        width: 80vw;
        padding: 10px;
        margin-bottom: 10px;
    }

    .dishes-row{
        display: flex;
        flex-direction: row;
    }

    .dish-item{
        height: 25em;
        width: 25em;
        display: flex;
        flex-direction: column;
    }

    .dish-item > * {
        margin: 0;
    }

    .dish-picture{
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>

