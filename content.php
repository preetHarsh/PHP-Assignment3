<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bookReview";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $sql = "DELETE FROM book_reviews WHERE id = '$id'";
        $conn->query($sql);
    } elseif (isset($_POST['add'])) {
        $book_name = $_POST['book_name'];
        $author = $_POST['author'];
        $year = $_POST['year'];
        $edition = $_POST['edition'];
        $rating = $_POST['rating'];
        $review = $_POST['review'];

        // Handle file upload
        $target_dir = "img/";
        $target_file = $target_dir . basename($_FILES["book_image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (move_uploaded_file($_FILES["book_image"]["tmp_name"], $target_file)) {
            $book_image = $target_file;
            $sql = "INSERT INTO book_reviews (book_name, author, year, edition, rating, review, book_image) VALUES ('$book_name', '$author', '$year', '$edition', '$rating', '$review', '$book_image')";
            $conn->query($sql);
        } else {
            echo "Error uploading the image.";
        }
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $book_name = $_POST['book_name'];
        $author = $_POST['author'];
        $year = $_POST['year'];
        $edition = $_POST['edition'];
        $rating = $_POST['rating'];
        $review = $_POST['review'];
        $book_image = $_POST['book_image'];

        $sql = "UPDATE book_reviews SET book_name='$book_name', author='$author', year='$year', edition='$edition', rating='$rating', review='$review', book_image='$book_image' WHERE id='$id'";
        $conn->query($sql);
    }
}

$sql = "SELECT * FROM book_reviews";
$result = $conn->query($sql);

if (!$result) {
    echo "Error: " . $conn->error;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Reviews</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function editReview(id, book_name, author, year, edition, rating, review, book_image) {
            document.getElementById("editForm").style.display = "block";
            document.getElementById("edit_id").value = id;
            document.getElementById("edit_book_name").value = book_name;
            document.getElementById("edit_author").value = author;
            document.getElementById("edit_year").value = year;
            document.getElementById("edit_edition").value = edition;
            document.getElementById("edit_rating").value = rating;
            document.getElementById("edit_review").value = review;
            document.getElementById("edit_book_image").value = book_image;
        }
    </script>
</head>
<body>
    <nav>
        <div class="brand-logo">
            <a href="index.php">BOOK REVIEW</a>
        </div>
        <a href="index.php">Login</a>
        <a href="content.php">View Content</a>
    </nav>

    <form action="content.php" method="POST" enctype="multipart/form-data">
        <label for="add_review" class=addreview>ADD REVIEW</label>
        <label for="book_image">Book Image:</label>
        <input type="file" name="book_image" id="book_image" required>
        <br>
        <label for="book_name">Book Name:</label>
        <input type="text" name="book_name" id="book_name" required>
        <br>
        <label for="author">Author:</label>
        <input type="text" name="author" id="author" required>
        <br>
        <label for="year">Year:</label>
        <input type="number" name="year" id="year" required>
        <br>
        <label for="edition">Edition:</label>
        <input type="text" name="edition" id="edition" required>
        <br>
        <label for="rating">Rating:</label>
        <input type="number" name="rating" id="rating" step="0.1" min="0" max="10" required>
        <br>
        <label for="review">Review:</label>
        <textarea name="review" id="review" required></textarea>
        <br>
        <input type="submit" name="add" value="Add Review">
    </form>
    <form id="editForm" action="content.php" method="POST" style="display: none;">
            <input type="hidden" name="id" id="edit_id">
            <label for="edit_book_image">Book Image URL:</label>
            <input type="text" name="book_image" id="edit_book_image" required>
            <br>
            <label for="edit_book_name">Book Name:</label>
            <input type="text" name="book_name" id="edit_book_name" required>
            <br>
            <label for="edit_author">Author:</label>
            <input type="text" name="author" id="edit_author" required>
            <br>
            <label for="edit_year">Year:</label>
            <input type="number" name="year" id="edit_year" required>
            <br>
            <label for="edit_edition">Edition:</label>
            <input type="text" name="edition" id="edit_edition" required>
            <br>
            <label for="edit_rating">Rating:</label>
            <input type="number" name="rating" id="edit_rating" step="0.1" min="0" max="10" required>
            <br>
            <label for="edit_review">Review:</label>
            <textarea name="review" id="edit_review" required></textarea>
            <br>
            <input type="submit" name="edit" value="Edit Review">
        </form>

        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='review-container'>";
                echo "<div class='button-wrapper'>";
                echo "<button class='edit-button' onclick=\"editReview('" . $row['id'] . "','" . addslashes($row['book_name']) . "','" . addslashes($row['author']) . "','" . $row['year'] . "','" . addslashes($row['edition']) . "','" . $row['rating'] . "','" . addslashes($row['review']) . "','" . $row['book_image'] . "')\">Edit</button>";
                echo "</div>"; // Close the button-wrapper div
                echo "<div class='review-image'><img src='" . $row['book_image'] . "' alt='" . $row['book_name'] . "' width='200'></div>";
                echo "<div class='review-content'>";
                echo "<h2>" . $row['book_name'] . "</h2>";
                echo "<p><strong>Author:</strong> " . $row['author'] . "</p>";
                echo "<p><strong>Year:</strong> " . $row['year'] . "</p>";
                echo "<p><strong>Edition:</strong> " . $row['edition'] . "</p>";
                echo "<p><strong>Rating:</strong> " . $row['rating'] . "/10</p>";
                echo "<p><strong>Review:</strong> " . $row['review'] . "</p>";
                echo "</div>"; // Close the review-content div
                
                // delet button wrap
                echo "<div class='delete-button-wrapper'>";
                echo "<form action='content.php' method='POST'>";
                echo "<input type='hidden' name='id' value='" . $row['id'] . "'>";
                echo "<input class='delete-button' type='submit' name='delete' value='Delete'>";
                echo "</form>";
                echo "</div>"; // Close 
                
                echo "</div>";            
            }
        } else {
            echo "No reviews posted yet!";
        }
        $conn->close();
        ?>
    </body>
</html>
