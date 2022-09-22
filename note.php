<?php
  include 'lib/secure.php';
  include 'lib/connect.php';
  include 'lib/queryContent.php';
  include 'lib/content.php';
  include 'lib/queryCategory.php';


  $limit = 10; 
  $page = 1;

  if (!empty($_GET['page']) && intval($_GET['page']) > 0){ 
    $page = intval($_GET['page']);
  }

  $queryContent = new QueryContent();
  $pager = $queryContent->getPager($page, $limit);
  $queryCategory = new QueryCategory();
  $categories = $queryCategory->findAll();
?>

<!doctype html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Coaching Note</title>

    <link href="./css/bootstrap.min.css" rel="stylesheet">

    <style>
      body {
        padding-top: 5rem;
      }
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }

      .bg-red {
        background-color: #000080 !important;
      }
    </style>

    <link href="./css/note.css" rel="stylesheet">
  </head>
  <body>

<?php include('lib/nav.php'); ?>


<main class="container">
  <div class="row">
    <div class="col-md-12">

    <h1>コーチングノート</h1>

    <?php if ($pager['contents']): ?>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>きになることは？</th>
            <th>いま何点？</th>
            <th>なぜその点数？</th>
            <th>本当はどうなったらいい？</th>
            <th>ありたい姿</th>
            <th>その画像のなにがいい？</th>
            <th>今すぐできる一歩目は？</th>
            <th>更新日</th>
            <th>閲覧</th>
            <th>削除</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($pager['contents'] as $content): ?>
          <tr>
            <td><?php echo isset($categories[$content->getCategoryId()])? $categories[$content->getCategoryId()]->getName(): 'なし' ?></td>
            <td><?php echo $content->getPoints() ?></td>
            <td><?php echo $content->getWhy() ?></td>
            <td><?php echo $content->getFuture() ?></td>
            <td><?php echo $content->getFilename()? '<img src="./album/thumbs-'.$content->getFilename().'">': 'なし' ?></td>
            <td><?php echo $content->getWhat() ?></td>
            <td><?php echo $content->getStep() ?></td>
            <td><?php echo $content->getUpdatedAt() ?></td>
            <td><a href="edit.php?id=<?php echo $content->getId() ?>" class="btn btn-success">ノートをみる</a></td>
            <td><a href="delete.php?id=<?php echo $content->getId() ?>" class="btn btn-danger">ノートを削除</a></td>

          </tr>
<?php endforeach ?>
        </tbody>
      </table>
<?php else: ?>
      <div class="alert alert-info">
        <p>ノートはありません。</p>
      </div>
<?php endif ?>

<?php if (!empty($pager['total'])): ?>
      <nav aria-label="Page navigation example">
        <ul class="pagination">
  <?php for ($i = 1; $i <= ceil($pager['total'] / $limit); $i++): ?>
          <li class="page-item"><a class="page-link" href="note.php?page=<?php echo $i ?>"><?php echo $i ?></a></li>
  <?php endfor ?>
        </ul>
      </nav>
<?php endif ?>

    </div>

  </div><!-- /.row -->

</main><!-- /.container -->

  </body>
</html>
