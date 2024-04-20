<?php
require_once 'vendor/autoload.php';
use jcobhams\NewsApi\NewsApi;

$url_noimage = "https://as2.ftcdn.net/v2/jpg/00/89/55/15/1000_F_89551596_LdHAZRwz3i4EM4J0NHNHy2hEUYDfXc0j.jpg";

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$pageSize = 10;

// Obtener el término de búsqueda del formulario
$searchTerm = isset($_GET['search']) ? $_GET['search'] : "tesla";

$newsapi = new NewsApi('9a4f39a6b14e4b4785fc4325d1552076');
$source = $newsapi->getEverything($searchTerm, null, null, null, null, null, null, null, $pageSize, $page);


$articles = $source->articles;

$articles = array_map(function ($article) use ($url_noimage) {
    $user = getDataUser();
    $user_name = $user['results'][0]['name']['title'].' '.$user['results'][0]['name']['first'].' '.$user['results'][0]['name']['last'];
    return [
        'title' => $article->title,
        'author' => $user_name,
        'urlToImage' => $article->urlToImage ? $article->urlToImage : $url_noimage,
        'description' => $article->description,
        'url' => $article->url,
    ];
}, $articles);

function getDataUser(){
    // URL de la API de Random User Generator
    $url_randomUser = 'https://randomuser.me/api/?inc=name';
    
    $response_randomuser = file_get_contents($url_randomUser);

    $data_randomuser = json_decode($response_randomuser, true);

    return $data_randomuser;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css">
    <title>News with Boostrap</title>

</head>
<body>
    <div class="container mt-3">
        <h1>Latest news about <?php echo $searchTerm ?></h1>
        <form action="" method="get" class="mb-3">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search news">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit">Search</button>
                </div>
            </div>
        </form>
        <div class="row">
            <?php foreach ($articles as $article): ?>
                <div class="col-md-6">
                    <div class="card mb-3">
                        <img src="<?php echo $article['urlToImage']; ?>" class="card-img-top" alt="..." style="width: 100%; height: 200px;">
                        <div class="card-body">
                            <h4 class="card-title"><?php echo $article['title']; ?></h4>
                            <h6 class="card-subtitle mb-2 text-body-secondary">Author: <?php echo $article['author']; ?></h6>
                            <p class="card-text"><?php echo $article['description']; ?></p>
                            <a href="<?php echo $article['url']; ?>" class="btn btn-primary">Read more</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                        <span class="sr-only">Previous</span>
                    </a>
                </li>
                <li class="page-item <?php echo count($articles) < $pageSize ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                        <span class="sr-only">Next</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    
    <script src="vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
