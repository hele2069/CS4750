<?php
require('connect-db.php');
require('user-db.php');

session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if($_SESSION["loggedin"] != true){
    header("location: index.php");
    exit;
}

$userID = $_SESSION["id"];
$POST_ALL = true;
$userListID = getListID($userID);
$list_to_update = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if (!empty($_POST['btnAction']) && $_POST['btnAction'] == "Remove")
    {
        deleteBook($_POST['list_num'], $_POST['book_to_delete']);
    }

    if (!empty($_POST['btnAction']) && $_POST['btnAction'] == "Remove List")
    {
        $userID = $_SESSION["id"];
        $POST_ALL = true;
        deleteList($_POST['list_to_delete'], $userID);
        $userListID = getListID($userID);
        $removeSuccess = 1;
    }

    if (!empty($_POST['btnAction']) && $_POST['btnAction'] == "Edit List")
    {
        $list_to_update = getListContent($_POST['list_to_update']);
        $editting_list = 1;
    }

    if(!empty($_POST['btnAction']) && $_POST['btnAction'] == "Add")
    {
        $userID = $_SESSION["id"];
        $POST_ALL = true;
        addList($_POST['list_name'], $_POST['list_description'], $userID);
        $userListID = getListID($userID);
        $addSuccess = 1;
    }

    if(!empty($_POST['btnAction']) && $_POST['btnAction'] == "Confirm Edit")
    {
        $userID = $_SESSION["id"];
        $POST_ALL = true;
        UpdateList($_POST['list_id'],$_POST['list_name'], $_POST['list_description'], $userID);
        // addList(5, "Frank", "ababa", 1);
        $userListID = getListID($userID);
        $updateSuccess = 1;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Shuo Yan">
    <meta name="description" content="include some description about your page">
    <link href="style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <title>bookDB</title>
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

            <!-- Navigation bar -->
            <div class="col-3">
                <div class="card">
                    <div class="card-body">
                    
                    <?php if(isset($editting_list)): ?>
                        <h1>Update List</h1>
                    <?php else: ?>
                        <h1>Create New List</h1>
                    <?php endif ?>

                    <form name="mainForm" action="user-booklist.php" method="post">  
                        <div class="row mb-3 mx-3">
                        <input type="hidden" class="form-control" name="list_id" placeholder="(No need to edit this)" 
                            value="<?php if ($list_to_update!=null) echo $list_to_update['list_id'] ?>"/>        
                        </div>  

                        <div class="row mb-3 mx-3">
                        List Name:
                            <input type="text" class="form-control" name="list_name" placeholder="List Name" required min="1" max="4"
                                value="<?php if ($list_to_update!=null) echo $list_to_update['list_name'] ?>"/>   
                        </div> 

                        List Description:
                        <div class="input-group">

                            <textarea class="form-control" name="list_description" placeholder="List Description" aria-label="With textarea"
                            value=""><?php if ($list_to_update!=null) echo $list_to_update['list_description'] ?></textarea>
                        </div>

                        <br>

                        <?php if(isset($editting_list)): ?>
                            <input type="submit" value="Confirm Edit" name="btnAction" class="btn btn-warning" title="confirm edit a list" />
                        <?php else: ?>
                            <input type="submit" value="Add" name="btnAction" class="btn btn-success" title="add a new list" /> 
                        <?php endif ?>
                    </form>

                    </div>
                </div>
            </div>
            
            <!-- Main Panel -->
            <div class="col-9">
                <div class="card">
                    <?php if(isset($addSuccess)): ?>
                        <div class="alert alert-success m-1" role="alert"><b>Success!</b> Added new list!</div>
                    <?php elseif(isset($updateSuccess)): ?>
                        <div class="alert alert-success m-1" role="alert"><b>Success!</b> Updated list!</div>
                    <?php elseif(isset($removeSuccess)): ?>
                        <div class="alert alert-success m-1" role="alert"><b>Success!</b> Removed list!</div>
                    <?php endif ?>

                    <div class="card-body">

                        <h1 style="text-transform: uppercase; color: #6495ED; ">
                            <strong><?php echo $_SESSION["username"], "'s Booklists"  ?></strong> 
                        </h1>
                    
                        <?php foreach ($userListID as $listID):  ?>

                            <h2><?php echo getListContent($listID['list_id'])['list_name'];?></h2>

                            <h5 style="color:#7889A6"><?php echo getListContent($listID['list_id'])['list_description'];?></h5>

                            <form name="mainForm" action="user-booklist.php" method="post">

                                <input type="submit" value="Edit List" name="btnAction" class="btn btn-primary"
                                        title="Edit list" />
                                <input type="hidden" name="list_to_update"
                                        value="<?php echo $listID['list_id'] ?>" />

                                <input type="submit" value="Remove List" name="btnAction" class="btn btn-danger"
                                        title="Remove a book from list" />
                                <input type="hidden" name="list_to_delete"
                                        value="<?php echo $listID['list_id'] ?>" />

                            </form>

                            <br>

                            <table class="w3-table w3-bordered w3-card-4" style="width:100%" >
                                <thead>
                                <tr style="background-color:#99CCFF">
                                    <th width="25%">Title</th>
                                    <th width="63%">Description</th>
                                    <th width="12%">Remove</th>
                                </tr>
                                </thead>

                            <?php $bookList = getBookList($listID['list_id'])?>
                            <?php foreach ($bookList as $book):  ?>
                                <tr>

                                    <td>    
                                        <a style="text-decoration:none; font-weight: bold;" href="./book.php?id=<?php echo $book['book_id']?>">
                                            <?php echo $book['title']; ?>
                                        </a>    
                                    </td>


                                    <td>    
                                        <a>
                                            <?php echo $book['book_description']; ?>
                                        </a>    
                                    </td>

                                    <td>
                                        <form name="mainForm" action="user-booklist.php" method="post">
                                            <input type="submit" value="Remove" name="btnAction" class="btn btn-danger"
                                                title="Remove a book from list" />
                                            <input type="hidden" name="book_to_delete"
                                                value="<?php echo $book['book_id'] ?>" />
                                            <input type="hidden" name="list_num"
                                                value="<?php echo $listID['list_id'] ?>" />
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            </table>

                        <?php endforeach; ?>

                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
