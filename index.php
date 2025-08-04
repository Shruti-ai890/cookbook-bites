<?php
// db_conn.php file is included to establish the database connection
include 'db_conn.php'; // Adjust the path if necessary


$sql = "SELECT r.recipe_name, r.recipe_image, r.description, c.cuisine_name 
        FROM recipes r 
        JOIN cuisines c ON r.cuisine_id = c.cuisine_id";
$result = $conn->query($sql);



?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Website</title>
    <style>
        /* Your existing styles here */
	 body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        header {
            background-color: #ff6347;
            color: #fff;
            padding: 1em 0;
            text-align: center;
            position: relative;
        }
        .login-container {
            position: absolute;
            top: 0.5em;
            left: 1em;
            display: flex;
            gap: 0.5em;
        }
        .login-container a {
            color: #fff;
            text-decoration: none;
            padding: 0.5em 1em;
            background-color: #ff4500;
            border-radius: 4px;
            display: block;
            cursor: pointer;
        }
        .login-container a:hover {
            background-color: #ff6347;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            border-radius: 8px;
        }
        .modal-content h2 {
            margin-top: 0;
        }
        .modal-content label {
            display: block;
            margin-top: 1em;
        }
        .modal-content input[type="text"],
        .modal-content input[type="password"],
        .modal-content input[type="email"],
        .modal-content textarea {
            width: 100%;
            padding: 0.5em;
            margin-top: 0.5em;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .modal-content input[type="submit"] {
            margin-top: 1em;
            padding: 0.5em 1em;
            border: none;
            border-radius: 4px;
            background-color: #ff4500;
            color: #fff;
            font-size: 1em;
            cursor: pointer;
            width: 100%;
        }
        .modal-content input[type="submit"]:hover {
            background-color: #ff6347;
        }
        #close-modal, #close-register-modal, #close-feedback-modal {
            float: right;
            font-size: 1.5em;
            cursor: pointer;
        }
        nav {
            margin: 1em 0;
        }
        nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
        }
        nav ul li {
            position: relative;
            margin: 0 0.5em;
        }
        nav a {
            color: #fff;
            text-decoration: none;
            padding: 0.5em 1em;
            background-color: #ff4500;
            border-radius: 4px;
            display: block;
        }
        nav a:hover {
            background-color: #ff6347;
        }
        .dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            display: none;
            background-color: #ff4500;
            border-radius: 4px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            min-width: 220px;
        }
        .dropdown a {
            padding: 0.5em 1em;
            color: #fff;
            text-decoration: none;
        }
        .dropdown a:hover {
            background-color: #ff6347;
        }
        nav ul li:hover .dropdown {
            display: block;
        }
        .view-more {
            display: block;
            padding: 0.5em 1em;
            background-color: #ff4500;
            text-align: center;
            cursor: pointer;
            color: #fff;
            text-decoration: none;
        }
        .view-more:hover {
            background-color: #ff6347;
        }
        .more-options {
            display: none;
        }
        .more-options.show {
            display: block;
        }
        .search-container {
            position: absolute;
            top: 50%;
            right: 1em;
            transform: translateY(-50%);
        }
        .search-container input[type="text"] {
            padding: 0.5em;
            border: none;
            border-radius: 4px;
            font-size: 1em;
            outline: none;
        }
        .search-container input[type="submit"] {
            padding: 0.5em 1em;
            border: none;
            border-radius: 4px;
            background-color: #ff4500;
            color: #fff;
            font-size: 1em;
            cursor: pointer;
        }
        .search-container input[type="submit"]:hover {
            background-color: #ff6347;
        }
        .recipe-gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 1em;
           
        }
        .recipe-gallery .recipe-item {
            position: relative;
            overflow: hidden;
            border-radius: 8px;
        }
        .recipe-gallery img {
            width: 300px;  /* Set a fixed width */
    height: 200px; /* Set a fixed height */
    object-fit: cover; /* Ensures the image is scaled to fit the container */
    border-radius: 10px; /* Optional: add rounded corners */
    margin: 10px; /* Space between images */
    transition: transform 0.3s ease-in-out; /* Smooth hover effect */
        }
        .recipe-gallery img:hover {
           transform: scale(1.05); /* Slight zoom on hover */
        }
        .recipe-item h3 {
            text-align: center;
            margin: 0.5em 0;
        }
        .toggle-links {
            margin-top: 1em;
            text-align: center;
        }
        .toggle-links a {
            color: #ff4500;
            text-decoration: none;
            cursor: pointer;
        }
        .toggle-links a:hover {
            text-decoration: underline;
        }



 /* The Popup (background) */
        .popup {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            background-color: rgba(0, 0, 0, 0.5); /* Black with transparency */
        }

        /* Popup Content */
        .popup-content {
            background-color: #fefefe;
            margin: 15% auto; /* Centered */
            padding: 20px;
            border: 1px solid #888;
            width: 200px; /* Fixed width for small dialog box */
            text-align: center;
            border-radius: 10px;
        }

        /* The Close Button */
        .close {
            color: #aaa;
            float: right;
            font-size: 20px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Centered social media icons */
        .img-container {
            margin-top: 10px;
        }

        .img-container img {
            margin: 10px;
            width: 40px;
            height: 40px;
        }


    </style>
</head>
<body>
    <header>
         <div class="login-container">
            <a href="#login" id="login-button">Login</a>
            <a href="#" id="admin-login-button">Admin Login</a>
        </div>
        <h1>COOKBOOK BITES</h1>
        <p>Delicious recipes for every occasion</p>
        <nav>
            <ul>
                <li>
                    <a href="#about">About Us</a>
                    <div class="dropdown">
                        <a href="about-us.html">About Us</a>
                        <a href="how-to add-recipe.html">How To Add Recipe</a>
                    </div>
                </li>

   <li>
    <a href="#meals">Meals</a>
    <div class="dropdown">
        <?php
        include 'db_conn.php'; // Ensure the correct path to your database connection

        // Fetch all meals from the database
        $query = "SELECT * FROM meals";
        $result = mysqli_query($conn, $query);

        if ($result) {
            // Check if there are any results
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    // Output each meal as a link
                    echo '<a href="recipes_by_meal.php?meal_id=' . urlencode($row['id']) . '">' . htmlspecialchars($row['meal_name'], ENT_QUOTES, 'UTF-8') . '</a>';
                }
            } else {
                echo 'No meals found.';
            }
            mysqli_free_result($result); // Free the result set
        } else {
            echo 'Error fetching meals: ' . mysqli_error($conn); // Show MySQL error if query fails
        }

        mysqli_close($conn);
        ?>
    </div>
</li>



                <li>
    <a href="#ingredients">Ingredients</a>
    <div class="dropdown">
        <?php
        include 'db_conn.php'; // Ensure the correct path to your database connection

        // Fetch all ingredients from the database
        $query = "SELECT * FROM ingredients";
        $result = mysqli_query($conn, $query);

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<a href="recipes.php?ingredient=' . urlencode($row['ingredient_name']) . '">' . htmlspecialchars($row['ingredient_name'], ENT_QUOTES, 'UTF-8') . '</a>';
            }
            mysqli_free_result($result); // Free the result set
        } else {
            echo 'Error fetching ingredients: ' . mysqli_error($conn); // Show MySQL error if query fails
        }

        mysqli_close($conn);
        ?>
    </div>
</li>

                <li>
                    <a href="#cuisines">Cuisines</a>
<div class="dropdown">
    <?php
    include 'db_conn.php'; // Ensure the correct path to your database connection

    // Fetch all cuisines from the database
    $query = "SELECT * FROM cuisines";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    while ($row = mysqli_fetch_assoc($result)) {
        echo '<a href="recipes_by_cuisine.php?cuisine=' . urlencode($row['cuisine_name']) . '">' . htmlspecialchars($row['cuisine_name'], ENT_QUOTES, 'UTF-8') . '</a>';
    }

    mysqli_close($conn);
    ?>
</div>
           
                </li>
                <li>
                    <a href="#contact">Contact Us</a>
                    <div class="dropdown">
                        <a href="mailto:augustina5489@gmail.com">Email Us</a>
                        <a href="#" id="socialMediaLink">Social Media</a>
                    </div>
                </li>
            </ul>
        </nav>
        <div class="search-container">
            <form action="search.php" method="GET">
    <input type="text" name="query" placeholder="Search here">
    <input type="submit" value="Search">
</form>

        </div>
    </header>
<section id="recipe-gallery">
    <h2>Recipe Gallery</h2>
    <div class="recipe-gallery">
        <?php
        // Include your database connection file
        include 'db_conn.php';

        // Check if the connection is successful
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // Query to fetch all recipes from both the recipes and user_recipes tables with origin flag
        $query = "
            SELECT id, recipe_name, recipe_image, 'admin' AS source
            FROM recipes
            UNION ALL
            SELECT id, recipe_name, recipe_image, 'user' AS source
            FROM user_recipes
        ";

        $result = mysqli_query($conn, $query);

        if (!$result) {
            die("Query failed: " . mysqli_error($conn));
        }

        // Loop through each recipe and display it
        while ($row = mysqli_fetch_assoc($result)):
        ?>
        <div class="recipe-item">
            <!-- Determine the detail page based on the source flag -->
            <a href="<?php echo ($row['source'] === 'user' ? 'user_recipe_detail.php' : 'recipe_detail.php'); ?>?id=<?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?>">
                <img src="uploads/<?php echo htmlspecialchars($row['recipe_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($row['recipe_name'], ENT_QUOTES, 'UTF-8'); ?>">
                <h3><?php echo htmlspecialchars($row['recipe_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
            </a>
        </div>
        <?php endwhile; ?>
    </div>
</section>







   <!-- Admin Login Modal -->
<div id="admin-login-modal" class="modal">
    <div class="modal-content">
        <span id="close-modal">&times;</span>
        <h2>Admin Login</h2>
        <form id="admin-login-form" method="post" action="admin_login.php">
            <label for="username">Username:</label>
            <input type="text" id="admin-username" name="username" required>
            <label for="password">Password:</label>
            <input type="password" id="admin-password" name="password" required>
            <input type="submit" value="Login">
        </form>
    </div>
</div>

 

<!-- Customer Login Modal -->
<div id="login-modal" class="modal">
    <div class="modal-content">
        <span id="close-login-modal" class="close">&times;</span>
        <h2>Login</h2>
        <form id="login-form" method="post" action="login.php">
            <label for="login-email">Email:</label>
            <input type="email" id="login-email" name="email" required>
            <label for="login-password">Password:</label>
            <input type="password" id="login-password" name="password" required>
            <input type="submit" value="Login">
        </form>
        <div class="toggle-links">
            <a href="#" id="show-register">New user? Register here</a>
        </div>
    </div>
</div>

<!-- Customer Registration Modal -->
<div id="register-modal" class="modal">
    <div class="modal-content">
        <span id="close-register-modal" class="close">&times;</span>
        <h2>Register</h2>
        <form id="register-form" method="post" action="register.php">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <input type="submit" value="Register">
        </form>
        <div class="toggle-links">
            <a href="#" id="show-login">Already registered? Sign in here</a>
        </div>
    </div>
</div>



 <!-- The Popup -->
    <div id="socialMediaPopup" class="popup">
        <div class="popup-content">
            <span class="close">&times;</span>
            <div class="img-container">
                <a target="_blank" href="https://www.instagram.com/onepage1021?igsh=MTZhN2t1OGp4a2QwdQ==">
                    <img src="Instagram_icon.png" alt="Instagram">
                </a>
                <a target="_blank" href="https://x.com/onepage1021?t=rDVSqT4DQ6jSrhYrXLCzAg&s=09">
                    <img src="X_icon.png" alt="X (Twitter)">
                </a>
            </div>
        </div>
    </div>

 <script>
        // Get the popup
        var popup = document.getElementById("socialMediaPopup");

        // Get the link that opens the popup
        var socialMediaLink = document.getElementById("socialMediaLink");

        // Get the <span> element that closes the popup
        var span = document.getElementsByClassName("close")[0];

        // When the user clicks the link, open the popup 
        socialMediaLink.onclick = function(event) {
            event.preventDefault(); // Prevent default link behavior
            popup.style.display = "block";
        }

        // When the user clicks on <span> (x), close the popup
        span.onclick = function() {
            popup.style.display = "none";
        }

        // When the user clicks anywhere outside of the popup, close it
        window.onclick = function(event) {
            if (event.target == popup) {
                popup.style.display = "none";
            }
        }
    </script>



  <script>
    document.addEventListener('DOMContentLoaded', function() {
        const viewMoreLink = document.querySelector('.view-more');
        const moreOptions = document.querySelector('.more-options');
        const adminLoginButton = document.getElementById('admin-login-button');
        const adminLoginModal = document.getElementById('admin-login-modal');
        const closeAdminModal = document.getElementById('close-modal');
        const registerButton = document.getElementById('login-button');
        const registerModal = document.getElementById('register-modal');
        const closeRegisterModal = document.getElementById('close-register-modal');
        const loginModal = document.getElementById('login-modal');
        const closeLoginModal = document.getElementById('close-login-modal');
        const showLogin = document.getElementById('show-login');
        const showRegister = document.getElementById('show-register');

        // Function to close modals
        function closeModal(modal) {
            modal.style.display = 'none';
        }

        if (viewMoreLink && moreOptions) {
            viewMoreLink.addEventListener('click', function() {
                moreOptions.classList.toggle('show');
            });
        }

        if (adminLoginButton) {
            adminLoginButton.addEventListener('click', function() {
                adminLoginModal.style.display = 'block';
            });
        }

        if (closeAdminModal) {
            closeAdminModal.addEventListener('click', function() {
                closeModal(adminLoginModal);
            });
        }

        if (registerButton) {
            registerButton.addEventListener('click', function() {
                registerModal.style.display = 'block';
            });
        }

        if (closeRegisterModal) {
            closeRegisterModal.addEventListener('click', function() {
                closeModal(registerModal);
            });
        }

        if (showLogin) {
            showLogin.addEventListener('click', function() {
                closeModal(registerModal);
                loginModal.style.display = 'block';
            });
        }

        if (showRegister) {
            showRegister.addEventListener('click', function() {
                closeModal(loginModal);
                registerModal.style.display = 'block';
            });
        }

        // Remove feedback modal related code
        // const feedbackButton = document.querySelector('#feedback-button');
        // if (feedbackButton) {
        //     feedbackButton.addEventListener('click', function() {
        //         feedbackModal.style.display = 'block';
        //     });
        // }

        // if (closeFeedbackModal) {
        //     closeFeedbackModal.addEventListener('click', function() {
        //         closeModal(feedbackModal);
        //     });
        // }

        // const urlParams = new URLSearchParams(window.location.search);
        // const registrationSuccess = urlParams.get('registration');
        // const modalToShow = urlParams.get('modal');

        // if (registrationSuccess === 'success' && modalToShow === 'feedback') {
        //     feedbackModal.style.display = 'block';
        // }

        window.addEventListener('click', function(event) {
            if (event.target === adminLoginModal) {
                closeModal(adminLoginModal);
            }
            if (event.target === registerModal) {
                closeModal(registerModal);
            }
            if (event.target === loginModal) {
                closeModal(loginModal);
            }
        });
    });
</script>


<!-- Your existing modals here... -->

<!-- Success Message -->
<div id="success-message" style="display: none; background-color: #d4edda; color: #155724; padding: 1em; margin: 1em 0; border: 1px solid #c3e6cb; border-radius: 4px;">
    Feedback submitted successfully!
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const successMessage = document.getElementById('success-message');

        // Check URL parameters for feedback status
        const urlParams = new URLSearchParams(window.location.search);
        const feedbackStatus = urlParams.get('feedback');

        if (feedbackStatus === 'success') {
            successMessage.style.display = 'block';
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 5000); // Message disappears after 5 seconds
        }
    });
</script>
</body>

</body>
</html>