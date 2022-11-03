<?php
require('connect-db.php');
require('book-db.php');

// Initialize the session
session_start();

$list_of_genres = getAllGenres();
$list_of_subjects = getAllSubjects();

if(isset($_GET['page'])) {
    $page_num = $_GET['page'];
}



if(empty($page_num)) {
    $page_num = 1;
}


if(isset($_GET['subject'])) {
    $subject = $_GET['subject'];
    $list_of_books = getNextBooksWithSubject($page_num, $subject);
    $next_page_link = './index.php?subject=' . $subject . '&page=' . ($page_num + 1);
} elseif(isset($_GET['genre'])) {
    $genre = $_GET['genre'];
    $list_of_books = getNextBooksWithGenre($page_num, $genre);
    $next_page_link = './index.php?ge
    nre=' . $genre . '&page=' . ($page_num + 1);
} else {
    $list_of_books = getNextBooks($page_num);
    $next_page_link = './index.php?page=' . ($page_num + 1);
}

$_SESSION['last_viewed_browse'] = $_SERVER['REQUEST_URI'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <title>bookDB</title>
    <script>
    </script>
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">bookDB</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarText">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="user-booklist.php">

                            <?php
                            // Check if the user is already logged in, if yes then redirect him to welcome page
                            if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true){
                                echo "Lists";
                            }
                            ?>

                        </a>
                    </li>
                </ul>
                <span class="navbar-text">  
                    <a class="nav-link" href="reset-password.php">
                            <?php
                            // Check if the user is already logged in, if yes then redirect him to welcome page
                            if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true){
                                echo "Reset Password";
                            }
                            ?>
                    </a>    
                </span>
                <span class="navbar-text">                
                    <a class="nav-link" href="login.php">
                            <?php
                            // Check if the user is already logged in, if yes then redirect him to welcome page
                            if(!isset($_SESSION["loggedin"]) or $_SESSION["loggedin"] != true){
                                echo "Login";
                            }
                            ?>
                    </a>
                    <a class="nav-link" href="logout.php">
                        <?php
                        // Check if the user is already logged in, if yes then redirect him to welcome page
                        if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true){
                            echo "Logout";
                        }
                        ?>

                </a>
             </span>
            </div>
        </div>
    </nav>
</header>

<div class="container">
    <div class="row">
        <div class="col-3">
            <div class="card">
                <div class="card-body">
                    <h5> Subjects </h5>
                    <div class="genre-filters">
                        <div class="genre-bg">
                            <?php foreach($list_of_subjects as $subject): ?>
                                <a class="genre" href="./index.php?subject=<?php echo $subject['book_subject'] ?>"><?php echo $subject['book_subject'] ?> (<?php echo $subject['count']?>)</a>
                            <?php endforeach ?>
                        </div>
                    </div>

                    <h5>Genres</h5>

                    <div class="genre-filters">
                        <div class="genre-bg" style="background:linear-gradient(to bottom, #c0ebc4, #aad9f0);">
                            <?php foreach($list_of_genres as $genre): ?>
                                <a class="genre" href="./index.php?genre=<?php echo $genre['genre'] ?>"><?php echo $genre['genre'] ?> (<?php echo $genre['count']?>)</a>
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-9">
            <div class="card">
                <div class="card-body">
                    <h2>Books</h2>
                    <div class="catalog">
                        <div class="books">
                            <?php foreach($list_of_books as $book): ?>

                                <div class="book">

                                    <a href="./book.php?id=<?php echo $book['book_id']?>">
                                        <img class="cover" src="https://covers.openlibrary.org/b/olid/<?php echo $book['book_id'] ?>-L.jpg">
                                        <h5 class="book-title"><?php echo $book['title']?></h5>
                                    </a>
                                </div>
                            <?php endforeach ?>
                        </div>

                        <a class="next-page" href="<?php echo $next_page_link?>">Next page</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


</body>
</html>