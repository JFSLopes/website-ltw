<?php
    function draw_furniture($dbh, $filter){
        $stmt = $dbh->prepare('SELECT name FROM Furniture');
        $stmt->execute();
        $furnitures = $stmt->fetchAll();

        ?> 
        <ul>
            <li>
                <?php if ($filter){
                    echo '<label><input type="radio" name="furniture" value="none" checked> No filter </label>';
                } ?>
            </li>
        <?php
        foreach ($furnitures as $furniture){ ?>
            <li>
                <label><input type="radio" name="furniture" value="<?= $furniture['name'] ?>"><?= $furniture['name'] ?></label>
            </li>
        <?php }
        ?> </ul> <?php
    } ?>

<?php
    function draw_decoration($dbh, $filter){
        $stmt = $dbh->prepare('SELECT name FROM Decoration');
        $stmt->execute();
        $decorations = $stmt->fetchAll();

        ?>
        <ul>
            <li>
                <?php if ($filter){
                     echo '<label><input type="radio" name="decoration" value="none" checked> No filter </label>';
                } ?>
            </li>
        <?php
        foreach ($decorations as $decoration){ ?>
            <li>
                <label><input type="radio" name="decoration" value="<?= $decoration['name'] ?>"><?= $decoration['name'] ?></label>
            </li>
        <?php }
        ?> </ul> <?php
    } ?>


<?php
    function draw_condition($dbh, $filter){
        $stmt = $dbh->prepare('SELECT name FROM Condition');
        $stmt->execute();
        $conditions = $stmt->fetchAll();

        ?> 
            <select id="condition" name="condition">
                <?php if ($filter){
                    echo '<option value="none" selected > -- No filter -- </option>';
                } ?>
        <?php
        foreach ($conditions as $condition){ ?>
            <option value="<?= $condition['name'] ?>"><?= $condition['name'] ?></option>
        <?php }
        ?> </select> <?php
    } ?>


<?php
    function draw_size($dbh, $filter){
        $stmt = $dbh->prepare('SELECT name FROM Size');
        $stmt->execute();
        $sizes = $stmt->fetchAll();

        ?>
            <select id="size" name="size">
                <?php if ($filter){
                    echo '<option value="none" selected > -- No filter -- </option>';
                } ?>
        <?php
        foreach ($sizes as $size){ ?>
            <option value="<?= $size['name'] ?>"><?= $size['name'] ?></option>
        <?php }
        ?> </select> <?php
    } ?>

<?php
    /* The function will not show the user products that is logged in, wouldn't make sense */
    function draw_products($dbh, $user_id, $searchQuery = '', $page = 1, $limit = 8){
        require (__DIR__ . '/../product_info/product_info.php');

        $offset = ($page - 1) * $limit;

        try {
            if (!empty($searchQuery)) {
                // If a search query is provided, filter products based on the search query
                $stmt = $dbh->prepare('SELECT P.* FROM Product P JOIN Selling S ON P.id = S.product WHERE S.user != ? AND P.name LIKE ? LIMIT ? OFFSET ?');
                $stmt->execute(array($user_id, '%' . $searchQuery . '%', $limit, $offset));
            } else {
                // Otherwise, fetch all products excluding those of the logged-in user
                $stmt = $dbh->prepare('SELECT P.* FROM Product P JOIN Selling S ON P.id = S.product WHERE S.user != ? LIMIT ? OFFSET ?');
                $stmt->execute(array($user_id, $limit, $offset));
            }
            $products = $stmt->fetchAll();

            ?> <ul> <?php
            foreach ($products as $row){ 
                $product = new Product($dbh, $row['id']);
                ?>
                <li class="product">
                    <img src="<?= $product->getImage() ?>" alt="<?= $product->getName() ?>">
                    <h3><?= $product->getName() ?></h3>
                    <p class="price">Price: <?= $product->getPrice() ?>€</p>
                    <p class="more-info"><a href="product.php?id=<?= $product->getID() ?>">More info...</a></p>
                </li>
            <?php }
            ?> </ul> <?php

            if (!empty($searchQuery)) {
                $countStmt = $dbh->prepare('SELECT COUNT(*) FROM Product P JOIN Selling S ON P.id = S.product WHERE S.user != ? AND P.name LIKE ?');
                $countStmt->execute(array($user_id, '%' . $searchQuery . '%'));
            } else {
                $countStmt = $dbh->prepare('SELECT COUNT(*) FROM Product P JOIN Selling S ON P.id = S.product WHERE S.user != ?');
                $countStmt->execute(array($user_id));
            }

            // Pagination
            $totalProducts = $countStmt->fetchColumn();
            $totalPages = ceil($totalProducts / $limit);

            if ($totalPages > 1) {
                echo '<div class="paginations">';
                if ($page > 1) {
                    echo '<a href="?page=' . ($page - 1) . '">Previous</a>';
                }
                for ($i = 1; $i <= $totalPages; $i++) {
                    if ($i == $page) {
                        echo '<span>' . $i . '</span>';
                    } else {
                        echo '<a href="?page=' . $i . '">' . $i . '</a>';
                    }
                }
                if ($page < $totalPages) {
                    echo '<a href="?page=' . ($page + 1) . '">Next</a>';
                }
                echo '</div>';
            }

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
?>