<?php
function getUserName($userID) {
    global $db;
    $query = "SELECT users_name FROM UserList  WHERE users_id = :userID";

    $statement = $db->prepare($query);
    $statement->bindValue(':userID', $userID);

    $statement->execute();

    $results = $statement->fetchAll();

    $statement->closeCursor();

    return $results;
}

function getBookList($listID) {
    global $db;
    $query = "SELECT * FROM ListContent l LEFT JOIN Book b ON l.book_id = b.book_id WHERE list_id = :listID";

    $statement = $db->prepare($query);
    $statement->bindValue(':listID', $listID);
    $statement->execute();

    $results = $statement->fetchAll();

    $statement->closeCursor();

    return $results;
}

function getListID($userID) {
    global $db;
    $query = "SELECT DISTINCT list_id FROM UserList WHERE user_id = :userID";

    $statement = $db->prepare($query);
    $statement->bindValue(':userID', $userID);

    $statement->execute();

    $results = $statement->fetchAll();

    $statement->closeCursor();

    return $results;
}

function getListContent($listID) {
    global $db;
    $query = "SELECT list_id, list_name, list_description FROM List WHERE list_id = :listID";

    $statement = $db->prepare($query);
    $statement->bindValue(':listID', $listID);

    $statement->execute();

    $results = $statement->fetch();

    $statement->closeCursor();

    return $results;
}

function UpdateList($listID, $listName, $listDescription) {
    global $db;
    $query = "update List set list_name = :listName, list_description = :listDescription WHERE list_id = :listID";

    $statement = $db->prepare($query);
    $statement->bindValue(':listID', $listID);
    $statement->bindValue(':listName', $listName);
    $statement->bindValue(':listDescription', $listDescription);

    $statement->execute();

    $results = $statement->fetch();

    $statement->closeCursor();

    return $results;
}

function deleteList($listID, $userID)
{
    // db handler
    global $db;

    // write sql
    $query = "delete from List where list_id = :listID; delete from UserList where (list_id = :listID and user_id = :userID)";


    // execute the sql
    $statement = $db->prepare($query);   // query() will compile and execute the sql
    $statement->bindValue(':listID', $listID);
    $statement->bindValue(':userID', $userID);

    $statement->execute();
    // release; free the connection to the server so other sql statements may be issued
    $statement->closeCursor();
}

function deleteBook($listID, $bookID)
{
    // db handler
    global $db;

    // write sql
    $query = "delete from ListContent where (list_id = :listID and book_id = :bookID)";


    // execute the sql
    $statement = $db->prepare($query);   // query() will compile and execute the sql
    $statement->bindValue(':listID', $listID);
    $statement->bindValue(':bookID', $bookID);

    $statement->execute();
    // release; free the connection to the server so other sql statements may be issued
    $statement->closeCursor();
}


function addList($listName, $listDescription, $userID)
{
    // db handler
    global $db;
    $query = "insert into List(list_name, list_description) values(:listName, :listDescription); insert into UserList values(LAST_INSERT_ID(), :userID)";

    // execute the sql
    $statement = $db->prepare($query);   // query() will compile and execute the sql

    $statement->bindValue(':userID', $userID);
    $statement->bindValue(':listName', $listName);
    $statement->bindValue(':listDescription', $listDescription);
    $statement->execute();
    // release; free the connection to the server so other sql statements may be issued
    $statement->closeCursor();
}

function addBookToList($book_id, $list_id) {
    // db handler
    global $db;
    $query = "INSERT INTO ListContent VALUES (:list_id, :book_id)";
    // execute the sql
    $statement = $db->prepare($query);   // query() will compile and execute the sql

    $statement->bindValue(':book_id', $book_id);
    $statement->bindValue(':list_id', $list_id);

    $statement->execute();
    // release; free the connection to the server so other sql statements may be issued
    $statement->closeCursor();
}