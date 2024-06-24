<!-- methodes -->
<?php
require 'vendor/autoload.php'; // Inclure Composer autoload
$client = new MongoDB\Client("mongodb://localhost:27017");
$collection = $client->tweets->tweets;


// Récupérer le nom d'utilisateur envoyé via le formulaire via la méthode POST
$user = $_POST['user'] ?? '';
// Utilisation de l'opérateur ternaire (?) pour définir une valeur par défaut en cas de non-existence du paramètre 'user' dans la requête
$message = $_POST['message'] ?? '';

$tweetId = $_POST['id'] ?? '';
$newMessage = $_POST['newMessage'] ?? '';


if ($user && $message) {
    $collection->insertOne([
        'user' => $user,
        'message' => $message,
        'timestamp' => new MongoDB\BSON\UTCDateTime()
    ]);
}

// Supprimer un tweet en fonction de son identifiant MongoDB
if ($tweetId) {
    $collection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($tweetId)]);
}
var_dump($tweetId);


// Mettre à jour le message d'un tweet en fonction de son identifiant MongoDB
// Le paramètre $newMessage doit être défini avant cette instruction
// Par exemple : $newMessage = 'nouveau message';



?>
<!-- affichage -->
<!DOCTYPE html>
<html>

<head>
    <title>Tweets</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .tweet {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
        }

        .tweet strong {
            font-weight: bold;
        }

        form {
            margin-top: 20px;
        }

        form input[type="text"],
        form input[type="submit"] {
            padding: 5px;
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <h1>Tweets</h1>
    <div class="tweets">



        <?php
        $tweets = $collection->find([], [
            'sort' => ['timestamp' => -1], // Tri par date décroissante
        ]);

        foreach ($tweets as $tweet) : ?>
            <div class='tweet'>
                <strong><?=$tweet['user']?></strong>:
                <?=$tweet['message']?>
                <?=$tweet['timestamp']->toDateTime()->format('Y-m-d H:i:s')?>
                <form method='POST'>
                    <input type='hidden' name='id' value='<?=$tweet["_id"]?>'>
                    <input type='submit' name='delete' value='Delete'>
                </form>
                <form action="update.php" method="GET">
                    <button type="submit">Update</button>
                </form>
            </div>
        <?php endforeach ?>

        <form method="POST">
            <input type="text" name="user" placeholder="Nom d'utilisateur">
            <input type="text" name="message" placeholder="Tweet">
            <input type="submit" value="Tweet">
        </form>
    </div>
</body>