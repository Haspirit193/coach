<?php
  include 'lib/secure.php';
  include 'lib/connect.php';
  include 'lib/queryContent.php';
  include 'lib/content.php';

  if (!empty($_GET['id'])){
    $queryContent = new QueryContent();
    $content = $queryContent->find($_GET['id']);
    if ($content){
      $content->delete();
    }
  }
  header('Location: note.php');
