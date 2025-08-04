<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Test Form</title>
</head>
<body>
    <form action="test-handler.php" method="POST">
        <label for="meal_id">Meal ID:</label>
        <input type="number" id="meal_id" name="meal_id" required>
        <label for="ingredient_id">Ingredient ID:</label>
        <input type="number" id="ingredient_id" name="ingredient_id" required>
        <button type="submit">Submit</button>
    </form>
</body>
</html>
