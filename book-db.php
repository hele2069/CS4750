<?php

function getAllBooks() {
    global $db;
    $query = "SELECT * FROM Book ORDER BY book_id LIMIT 50";

    $statement = $db->prepare($query);
    $statement->execute();

    $results = $statement->fetchAll();

    $statement->closeCursor();

    return $results;
}

function getNextBooks($page_num) {
    global $db;
    $offset = 50 * ($page_num - 1);
    $query = "SELECT * FROM Book LIMIT $offset, 50";

    $statement = $db->prepare($query);
    $statement->execute();

    $results = $statement->fetchAll();

    $statement->closeCursor();

    return $results;
}

function getNextBooksWithGenre($page_num, $genre) {
    global $db;
    $offset = 50 * ($page_num - 1);
    $query = "SELECT * FROM Book NATURAL JOIN BookGenre WHERE genre = :genre LIMIT $offset, 50";

    $statement = $db->prepare($query);
    $statement->bindValue(':genre', $genre);
    $statement->execute();

    $results = $statement->fetchAll();

    $statement->closeCursor();

    return $results;
}

function getNextBooksWithSubject($page_num, $subject) {
    global $db;
    $offset = 50 * ($page_num - 1);
    $query = "SELECT * FROM Book NATURAL JOIN BookSubject WHERE book_subject = :subject LIMIT $offset, 50";

    $statement = $db->prepare($query);
    $statement->bindValue(':subject', $subject);
    $statement->execute();

    $results = $statement->fetchAll();

    $statement->closeCursor();

    return $results;
}

function getBookInfo($book_id) {
    global $db;
    $query = "SELECT * FROM Book WHERE book_id= :book_id";

    $statement = $db->prepare($query);
    $statement->bindValue(':book_id', $book_id);
    $statement->execute();

    $results = $statement->fetch();

    $statement->closeCursor();

    return $results;
}

function getAuthorInfo($author_id) {
    global $db;
    $query = "SELECT * FROM Author WHERE author_id= :author_id";

    $statement = $db->prepare($query);
    $statement->bindValue(':author_id', $author_id);
    $statement->execute();

    $results = $statement->fetch();

    $statement->closeCursor();

    return $results;
}

function getAllGenres() {
    global $db;
    $query = "SELECT genre, COUNT(*) AS count FROM BookGenre GROUP BY genre ORDER BY count DESC";

    $statement = $db->prepare($query);
    $statement->execute();

    $results = $statement->fetchAll();

    $statement->closeCursor();

    return $results;
}

function getAllSubjects() {
    global $db;
    $query = "SELECT book_subject, COUNT(*) AS count FROM BookSubject GROUP BY book_subject ORDER BY count DESC LIMIT 500";

    $statement = $db->prepare($query);
    $statement->execute();

    $results = $statement->fetchAll();

    $statement->closeCursor();

    return $results;
}

function updateBookClick($book_id, $user_id) {
    global $db;
    $query = "INSERT INTO BookClick VALUES (:book_id, :user_id, NOW())";

    $statement = $db->prepare($query);
    $statement->bindValue(':book_id', $book_id);
    $statement->bindValue(':user_id', $user_id);

    try {
        $statement->execute();
    } catch (PDOException $e) {
        
    }
    $statement->closeCursor();
}

function getBookViews($book_id) {
    global $db;
    $query = "SELECT COUNT(*) FROM BookClick WHERE book_id = :book_id";

    $statement = $db->prepare($query);
    $statement->bindValue(':book_id', $book_id);

    $statement->execute();

    $results = $statement->fetch();

    $statement->closeCursor();

    return $results;
}

function getBookRating($book_id) {
    global $db;
    $query = "SELECT AVG(number_rating) AS rating, COUNT(*) AS num_reviews FROM Review WHERE book_id = :book_id";

    $statement = $db->prepare($query);
    $statement->bindValue(':book_id', $book_id);

    $statement->execute();

    $results = $statement->fetch();

    $statement->closeCursor();

    return $results;
}

function addReview($book_id, $user_id, $rating, $review) {
    global $db;
    $query = "INSERT INTO Review VALUES (:book_id, :user_id, :rating, :review)";

    $statement = $db->prepare($query);
    $statement->bindValue(':book_id', $book_id);
    $statement->bindValue(':user_id', $user_id);
    $statement->bindValue(':rating', $rating);
    $statement->bindValue(':review', $review);

    $statement->execute();

    $statement->closeCursor();
}

function getReviews($book_id) {
    global $db;
    $query = "SELECT number_rating, text_review, users_name FROM Review r LEFT JOIN User u ON r.user_id = u.users_id WHERE book_id=:book_id";

    $statement = $db->prepare($query);
    $statement->bindValue(':book_id', $book_id);

    $statement->execute();

    $results = $statement->fetchAll();

    $statement->closeCursor();

    return $results;
}

function updateReview($book_id, $user_id, $rating, $review) {
    global $db;
    $query = "UPDATE Review SET number_rating = :rating, text_review = :review WHERE book_id = :book_id AND user_id = :user_id";

    $statement = $db->prepare($query);
    $statement->bindValue(':book_id', $book_id);
    $statement->bindValue(':user_id', $user_id);
    $statement->bindValue(':rating', $rating);
    $statement->bindValue(':review', $review);

    $statement->execute();

    $statement->closeCursor();
}

function getUsersExistingReview($book_id, $user_id) {
        global $db;
    $query = "SELECT * FROM Review WHERE book_id=:book_id AND user_id=:user_id";

    $statement = $db->prepare($query);
    $statement->bindValue(':book_id', $book_id);
    $statement->bindValue(':user_id', $user_id);

    $statement->execute();

    $results = $statement->fetch();

    $statement->closeCursor();

    return $results;
}


?>