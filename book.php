<?php 
require('connect-db.php');
require('book-db.php');
require('user-db.php');

session_start();

$book_id = $_GET['id'];
$book = getBookInfo($book_id);
$author = getAuthorInfo($book['author_id']);


$link = $_SESSION['last_viewed_browse'];

if(empty($author['bio'])) {
    $author['bio'] = "Bio not available.";
}

if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == 1) {
    $user_id = $_SESSION['id'];
    updateBookClick($book_id, $user_id);
    $userListID = getListID($user_id);

    $my_review = getUsersExistingReview($book_id, $user_id);
}

$views = getBookViews($book_id);

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if( !empty($_POST['btnAction'] && $_POST['btnAction'] == "Add"))
    {
        try {
            addBookToList($book_id, $_POST['list_id']);
            $add_success = 1;
        } catch (Exception $e) {
            $add_success = 0;
        }
    } 

    else if(!empty($_POST['btnAction'] && $_POST['btnAction'] == "Add Review"))
    {
        addReview($book_id, $_SESSION['id'], $_POST['rating'], $_POST['review-text']);
        $add_review_success = 1;
        $my_review = getUsersExistingReview($book_id, $user_id);

    }

    else if(!empty($_POST['btnAction'] && $_POST['btnAction'] == "Update Review"))
    {
        updateReview($book_id, $_SESSION['id'], $_POST['rating'], $_POST['review-text']);
        $add_review_success = 2;
        $my_review = getUsersExistingReview($book_id, $user_id);

    }
}

$rating = getBookRating($book_id);
$reviews = getReviews($book_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
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
            <div class="col-9">
            <a href="<?php echo $link ?>">Back</a>

                <div class="card">
                    <div class="card-body">
                        <?php if(isset($add_success) && $add_success == 1) {
                            echo '<div class="alert alert-success" role="alert">
                            <b>Success!</b> Added book to ' . getListContent($_POST['list_id'])['list_name'] . 
                          '<span style="float:right"><a href="user-booklist.php">View list</a></span></div>';
                            }

                            if(isset($add_success) && $add_success == 0) {
                                echo '<div class="alert alert-danger" role="alert">
                                <b>Error:</b> book already in ' . getListContent($_POST['list_id'])['list_name'] . 
                              '<span style="float:right"><a href="user-booklist.php">View list</a></span></div>';
                                }
                            
                            if(isset($add_review_success) && $add_review_success == 1) {
                                echo '<div class="alert alert-success" role="alert">
                                <b>Success!</b> Added review!</div>';
                                }
                            if(isset($add_review_success) && $add_review_success == 2) {
                                echo '<div class="alert alert-success" role="alert">
                                <b>Success!</b> Updated review!</div>';
                                }
                        ?>
                        <div class="book-container">
                            <div class="book-cover-container">
                                <img class="cover" src="https://covers.openlibrary.org/b/olid/<?php echo $book['book_id'] ?>-L.jpg">
                                <button class="btn btn-success" data-toggle="modal" data-target='#addToListModal'>Add to list</button>

                            </div>
                            <div class="book-info-container">
                                <h2 class="title" style="margin-bottom:0px"><?php echo $book['title']?></h2>
                                <div style="margin-top: 0px">
                                <span>by</span>
                                <span class='author-name'><?php echo $author['author_name']?></span>
                                </div>
                                <div>
                                <span class='rating'>Rating:</span>
                                <?php
                                for ($x = 0; $x < floor($rating['rating']); $x++) {
                                echo "<span class='star' id='filled'>★</span>";
                                } 
                                for ($x = floor($rating['rating']); $x < 5; $x++) {
                                    echo "<span class='star' id='unfilled'>★</span>";
                                    } 
                                ?>
                                <span class="rating"><?php echo $rating['rating'] ?></span>
                                <p class="reviews">Reviews: <?php echo $rating['num_reviews']?></p>
                                </div>
                                <div class="description">
                                    <p><?php echo $book['book_description']?><p>
                                </div>
                                <div class="meta">
                                <p class="pages">Pages: <?php echo $book['number_of_pages']?></p>
                                <p class='publish-year'> Published: <?php echo $book['publish_year']?></p>
                                <p class='publisher'>Publisher: <?php echo $book['publisher']?></p>
                                <p class='views'>Views: <?php echo $views[0]?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" style="margin-top: 1em; margin-bottom: 3em">
            <div class="col-3">
                <div class="card" style="min-height: 50vh">
                    <div class="card-body">
                        <?php if(!empty($my_review)): ?>
                            <h6>Update your review:</h6>
                        <?php else: ?>
                            <h6>Write a review:</h6>
                        <?php endif; ?>
                        <?php if(isset($_SESSION['loggedin'])): ?>
                            <form name="reviewForm" action="book.php?id=<?php echo $book_id?>" method="post">
                                <div class="row mb-3 mx-3">
                                    Rating:
                                    <div class="rating-radio">
                                        <?php if(!empty($my_review)): ?>
                                        <input type="radio" id="one" name="rating" value="1" <?php echo ($my_review['number_rating']=='1')?'checked':''?>>
                                        <input type="radio" id="two" name="rating" value="2" <?php echo ($my_review['number_rating']=='2')?'checked':''?>>
                                        <input type="radio" id="three" name="rating" value="3" <?php echo ($my_review['number_rating']=='3')?'checked':''?>>
                                        <input type="radio" id="four" name="rating" value="4" <?php echo ($my_review['number_rating']=='4')?'checked':''?>>
                                        <input type="radio" id="five" name="rating" value="5" <?php echo ($my_review['number_rating']=='5')?'checked':''?>>
                                        <?php else: ?>
                                        <input type="radio" id="one" name="rating" value="1">
                                        <input type="radio" id="two" name="rating" value="2">
                                        <input type="radio" id="three" name="rating" value="3">
                                        <input type="radio" id="four" name="rating" value="4">
                                        <input type="radio" id="five" name="rating" value="5">
                                        <?php endif ?>
                                    </div>
                                </div>
                                <div class="row mb-3 mx-3">
                                    Review (optional):
                                    <textarea name="review-text" cols=7 style="min-height: 20vh"><?php if(!empty($my_review))echo $my_review['text_review']?></textarea>
                                </div>

                                <?php if(!empty($my_review)): ?>
                                    <input type="submit" value="Update Review" name="btnAction" class="btn btn-success" 
                                    title="Update review" />
                                <?php else: ?>
                                    <input type="submit" value="Add Review" name="btnAction" class="btn btn-success" 
                                    title="Add review" />
                                <?php endif ?>
                            </form>
                            <?php else: ?>
                                <div style="margin-top: 50%">
                                    <p>To use this feature, please <a href="login.php">login</a>.<p>
                                </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
            <div class="col-9">
                <div class="card">
                    <div class="card-body">
                        <h6>Reviews:</h6>
                        <hr>
                        <br>
                        <?php foreach ($reviews as $review): ?>
                        <div>
                            <p style="margin-bottom: 0px"><?php echo $review['users_name']?></p>
                            <div>
                            <span class='rating'>Rating:</span>
                                <?php
                                for ($x = 0; $x < $review['number_rating']; $x++) {
                                echo "<span class='star' id='filled'>★</span>";
                                } 
                                for ($x = $review['number_rating']; $x < 5; $x++) {
                                    echo "<span class='star' id='unfilled'>★</span>";
                                    } 
                                ?>      
                            </div>                      
                            <p><?php echo $review['text_review']?></p>
                        </div>
                        <hr>
                        <?php endforeach ?>
                        <?php if(empty($reviews)): ?>
                            <p>This book has no reviews yet.</p>
                            <p>Leave the first review!</p>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="addToListModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Your list(s)</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
                <table class="table" style="width:100%" >
                <?php if(isset($_SESSION['loggedin'])): ?>
                <?php foreach($userListID as $listID): ?>
                    <tr>
                                    <td><?php echo getListContent($listID['list_id'])['list_name']; ?></td>
                                    <td>
                                        <form name="mainForm" action="book.php?id=<?php echo $book_id?>" method="post">
                                            <input type="submit" value="Add" name="btnAction" class="btn btn-success"
                                                title="Add book to list" />
                                            <input type="hidden" name="list_id"
                                            value="<?php echo $listID['list_id'] ?>" />
                                        </form>
                                    </td>
                    </tr>
                <?php endforeach ?>
                </table>
                <?php 
                        if(empty($userListID) == 1) {
                            echo '<div>You have no lists.</div>';
                        } 
                    ?>
                <a href='user-booklist.php'> Create a new list </a>
            <?php  endif; ?>
            <?php if(empty($_SESSION['loggedin'])): ?>
                <p>To use this feature, please <a href="login.php">login</a>.<p>
            <?php endif ?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
        </div>
    </div>
    </div>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>