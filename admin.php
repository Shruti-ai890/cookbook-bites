<?php
// Establishing database connection
$conn = mysqli_connect("localhost", "root", "", "recipe");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Total count of recipes
$sql_total = "SELECT COUNT(*) AS total FROM recipes";
$result_total = mysqli_query($conn, $sql_total);

if (!$result_total) {
    die('Error in total count query: ' . mysqli_error($conn));
}

$row_count = mysqli_fetch_assoc($result_total);
$total_records = $row_count['total'];
echo "Total Number of Recipes: " . $total_records . "<br>";


<h4>All Recipes</h4>
<table class="table table-bordered shadow">
    <thead>
        <tr>
            <th>Sr No.</th>
            <th>Recipe Name</th>
            <th>Chef</th>
            <th>Meal</th>
            <th>Ingredient</th>
            <th>Cuisine</th>
            <th>Description</th>
            <th>Image</th>
            <th>Servings</th>
            <th>Cooking Time</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $i = 0;
        foreach ($recipes as $recipe) {
            $i++;
        ?>
        <tr>
            <td><?=$i?></td>
            <td><?=$recipe['recipe_name']?></td>
            <td>
                <?php
                $chefName = "Undefined";
                foreach ($chefs as $chef) {
                    if ($chef['chef_id'] == $recipe['chef_id']) {
                        $chefName = $chef['chef_name'];
                        break;
                    }
                }
                echo $chefName;
                ?>
            </td>
            <td>
                <?php
                $mealName = "Undefined";
                foreach ($meals as $meal) {
                    if ($meal['id'] == $recipe['meal_id']) {
                        $mealName = $meal['meal_name'];
                        break;
                    }
                }
                echo $mealName;
                ?>
            </td>
            <td>
                <?php
                $ingredientName = "Undefined";
                foreach ($ingredients as $ingredient) {
                    if ($ingredient['id'] == $recipe['ingredient_id']) {
                        $ingredientName = $ingredient['ingredient_name'];
                        break;
                    }
                }
                echo $ingredientName;
                ?>
            </td>
            <td>
                <?php
                $cuisineName = "Undefined";
                foreach ($cuisines as $cuisine) {
                    if ($cuisine['cuisine_id'] == $recipe['cuisine_id']) {
                        $cuisineName = $cuisine['cuisine_name'];
                        break;
                    }
                }
                echo $cuisineName;
                ?>
            </td>
            <td><?=$recipe['description']?></td>
            <td>
                <img width="100" src="uploads/recipes/<?=$recipe['recipe_image']?>" alt="<?=$recipe['recipe_name']?>">
            </td>
            <td><?=$recipe['servings']?></td>
            <td><?=$recipe['cooking_time']?></td>
            <td>
                <a href="edit-recipe.php?id=<?=$recipe['id']?>" class="btn btn-warning">Edit</a>
                <a href="delete-recipe.php?id=<?=$recipe['id']?>" class="btn btn-danger">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>

// Closing the database connection
mysqli_close($conn);
?>