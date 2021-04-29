<?php 

use App\Entity\Book;

require_once __DIR__. "/bootstrap.php";

if ($argc !== 5) {
    echo "Usage ${argv[0]} <name> <value>\n";
    exit(1);
}

$newBookTitle = stringify($argv[1]);
$newBookIsbn = stringify($argv[2]);
$newBookAuthor = stringify($argv[3]);
$newBookImage = stringify($argv[4]);


$book = new Book();
$book->setTitle($newBookTitle);
$book->setIsbn($newBookIsbn);
$book->setAuthor($newBookAuthor);
$book->setImage($newBookImage);
echo "saved book image is " . $book->getImage() . "\n";

$entityManager->persist($book);
$entityManager->flush();

echo "Created Book with ID " . $book->getId() . "\n";

function stringify(string $input) {
    $divider = "_";
    $replacer = " ";
    if (str_contains($input, $divider)) {
        $input = str_replace($divider, $replacer, $input);
        return $input;
    }
    return $input;
}
