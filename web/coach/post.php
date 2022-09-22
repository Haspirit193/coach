<?php
  include 'lib/secure.php';
  include 'lib/connect.php';
  include 'lib/queryContent.php';
  include 'lib/content.php';
  include 'lib/queryCategory.php';


  $points = "";      // いま点数？
  $why = "";        // どうしてその点数？
  $future = "";         // 本当はどうなったらいい？
  $what = "";         // その画像のなにがいい？
  $step = "";         // 今すぐできそうな一歩目は？
  $why_alert = "";  // 「どうしてその点数？」のエラー文言
  $future_alert = "";   // 「本当はどうなったらいい？」のエラー文言
  $what_alert = "";  // 「その画像のなにがいい？」のエラー文言
  $step_alert = "";   // 「今すぐできそうな一歩目は？」のエラー文言


  $queryCategory = new QueryCategory();
  $categories = $queryCategory->findAll();

  if (!empty($_POST['points']) &&!empty($_POST['why']) && !empty($_POST['future']) && !empty($_POST['what']) && !empty($_POST['step'])){

    $points = $_POST['points'];
    $why = $_POST['why'];
    $future = $_POST['future'];
    $what = $_POST['what'];
    $step = $_POST['step'];

    $content = new Content();
    if (!empty($_POST['category'])){
      $category = $queryCategory->find($_POST['category']);
      if ($category){
        $content->setCategoryId($category->getId());
      } 
    }
    $content->setPoints($points);
    $content->setWhy($why);
    $content->setFuture($future);
    if (isset($_FILES['image']) && is_uploaded_file($_FILES['image']['tmp_name'])){ 
      $content->setFile($_FILES['image']);
    }
    $content->setWhat($what);
    $content->setStep($step);
    $content->save();

    header('Location: note.php');
  } else if(!empty($_POST)){

    if (!empty($_POST['points'])){
      $points = $_POST['points'];
    } else {
      $why_alert = "いま何点か入力してください";
    }

    if (!empty($_POST['why'])){
      $why = $_POST['why'];
    } else {
      $why_alert = "なぜその点数が入ったのかを入力してください。";
    }

    if (!empty($_POST['future'])){
      $future = $_POST['future'];
    } else {
      $future_alert = "ありたい未来を入力してください。";
    }

    if (!empty($_POST['what'])){
      $what = $_POST['what'];
    } else {
      $what_alert = "その画像のなにがいいかを入力してください。";
    }

    if (!empty($_POST['step'])){
      $step = $_POST['step'];
    } else {
      $step_alert = "具体的な行動を入力してください。";
    }
  }
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

<form action="post.php" method="post" enctype="multipart/form-data"> 

<div class="mb-3"> 
   <label class="form-label">いま気になっていることは？？</label>
   <select name="category" class="form-control">
     <option value="0">なし</option>
     <?php foreach ($categories as $c): ?>
     <option value="<?php echo $c->getId() ?>"><?php echo $c->getName() ?></option>
     <?php endforeach ?>
   </select>
 </div>

 <div class="form-group">
 <label for="select1">最高にうまくいっている状態が１０点、最低が１点としたら、いまの状態は何点？</label>
 <select name="points" id="select1" class="form-control">
   <option>１０点</option>
   <option>９点</option>
   <option>８点</option>
   <option>７点</option>
   <option>６点</option>
   <option>５点</option>
   <option>４点</option>
   <option>３点</option>
   <option>２点</option>
   <option>１点</option>
 </select>
</div>

<div class="mt-3">
   <label class="form-label">どうしてその点数なの？？</label>
   <?php echo !empty($why_alert)? '<div class="alert alert-danger">'.$why_alert.'</div>': '' ?>
   <textarea name="why" class="form-control" rows="10"><?php echo $why; ?></textarea>
 </div>

 <div class="mt-3">
   <label class="form-label">それが本当はどうなったらいい？</label>
   <?php echo !empty($future_alert)? '<div class="alert alert-danger">'.$future_alert.'</div>': '' ?>
   <textarea name="future" class="form-control" rows="10"><?php echo $future; ?></textarea>
 </div>

 <div class="mt-3">
         <label class="form-label">理想の状態に一番近い画像をアップロードしよう！</label>
         <input type="file" name="image" class="form-control">
       </div>

 <div class="mt-3">
   <label class="form-label">この画像の何が良いんだろう？？</label>
   <?php echo !empty($what_alert)? '<div class="alert alert-danger">'.$what_alert.'</div>': '' ?>
   <textarea name="what" class="form-control" rows="10"><?php echo $what; ?></textarea>
 </div>

<div class="mt-3">
   <label class="form-label">その状態に近づくために、今すぐできそうなことは？？</label>
   <?php echo !empty($step_alert)? '<div class="alert alert-danger">'.$step_alert.'</div>': '' ?>
   <input type="text" name="step" value="<?php echo $step; ?>" class="form-control">
 </div>
 
 <div class="my-3">
   <button type="submit" class="btn btn-primary">いいね！！！</button>
 </div>
</form>

   </div>

    </div>

  </div><!-- /.row -->

</main><!-- /.container -->

  </body>
</html>
