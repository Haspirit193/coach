<?php
class QueryContent extends connect{
  private $content;
  const THUMBS_WIDTH = 200; // サムネイルの幅


  public function __construct(){
    parent::__construct();
  }

  public function setContent(Content $content){
    $this->content = $content;
  }

  // 画像アップロード
  private function saveFile($old_name){
    $new_name = date('YmdHis').mt_rand();

    if ($type = exif_imagetype($old_name)){
      // 元画像の縦横サイズを取得
      list($width, $height) = getimagesize($old_name);

      // サムネイルの比率を求める
      $rate = self::THUMBS_WIDTH / $width;  // 比率
      $thumbs_height = $rate * $height;

      // キャンバス作成
      $canvas = imagecreatetruecolor(self::THUMBS_WIDTH, $thumbs_height);

      switch($type){
        case IMAGETYPE_JPEG:
          $new_name .= '.jpg';

          // サムネイルを保存
          $image = imagecreatefromjpeg($old_name);
          imagecopyresampled($canvas, $image, 0, 0, 0, 0, self::THUMBS_WIDTH, $thumbs_height, $width, $height);
          imagejpeg($canvas, __DIR__.'/../album/thumbs-'.$new_name);
          break;

        case IMAGETYPE_GIF:
          $new_name .= '.gif';

          // サムネイルを保存
          $image = imagecreatefromgif($old_name);
          imagecopyresampled($canvas, $image, 0, 0, 0, 0, self::THUMBS_WIDTH, $thumbs_height, $width, $height);
          imagegif($canvas, __DIR__.'/../album/thumbs-'.$new_name);
          break;

        case IMAGETYPE_PNG:
          $new_name .= '.png';

          // サムネイルを保存
          $image = imagecreatefrompng($old_name);
          imagecopyresampled($canvas, $image, 0, 0, 0, 0, self::THUMBS_WIDTH, $thumbs_height, $width, $height);
          imagepng($canvas, __DIR__.'/../album/thumbs-'.$new_name);
          break;

        default:
          // JPEG・GIF・PNG以外の画像なら処理しない
          imagedestroy($canvas);
          return null;
      }
      imagedestroy($canvas);
      imagedestroy($image);

      // 元サイズの画像をアップロード
      move_uploaded_file($old_name, __DIR__.'/../album/'.$new_name);

      // 保存したファイル名を返す
      return $new_name;

    } else {
      // 画像以外なら処理しない
      return null;
    }
  }

  public function save(){
    // bindParam用
    $category_id = $this->content->getCategoryId();
    $points = $this->content->getPoints();
    $why = $this->content->getWhy();
    $future = $this->content->getFuture();
    $filename = $this->content->getFilename();
    $what = $this->content->getWhat();
    $step = $this->content->getStep();
    if ($this->content->getId()){
      // IDがあるときは上書き
      $id = $this->content->getId();
      // 新しいファイルがアップロードされたとき
      if ($file = $this->content->getFile()){
        // ファイルが既にある場合、古いファイルを削除する
        $this->deleteFile();
        // 新しいファイルのアップロード
        $this->content->setFilename($this->saveFile($file['tmp_name']));
        $filename = $this->content->getFilename();
      }

      $stmt = $this->dbh->prepare("UPDATE contents
                SET category_id=:category_id, points=:points, why=:why, future=:future, filename=:filename, what=:what, step=:step, updated_at=NOW() WHERE id=:id");
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    } else {
      // IDがなければ新規作成
      if ($file = $this->content->getFile()){
        $this->content->setFilename($this->saveFile($file['tmp_name']));
        $filename = $this->content->getFilename();
      }
      $stmt = $this->dbh->prepare("INSERT INTO contents (category_id, points, why, future, filename, what, step, created_at, updated_at)
                VALUES (:category_id, :points, :why, :future, :filename, :what, :step, NOW(), NOW())");
    }
      $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
      $stmt->bindParam(':points', $points, PDO::PARAM_STR);
      $stmt->bindParam(':why', $why, PDO::PARAM_STR);
      $stmt->bindParam(':future', $future, PDO::PARAM_STR);
      $stmt->bindParam(':filename', $filename, PDO::PARAM_STR);
      $stmt->bindParam(':what', $what, PDO::PARAM_STR);
      $stmt->bindParam(':step', $step, PDO::PARAM_STR);
      $stmt->execute();
    
  }

  private function deleteFile(){
    if ($this->content->getFilename()){
      unlink(__DIR__.'/../album/thumbs-'.$this->content->getFilename());
      unlink(__DIR__.'/../album/'.$this->content->getFilename());
    }
  }

  public function delete(){
    if ($this->content->getId()){
      // 画像の削除
      $this->deleteFile();
      $id = $this->content->getId();
      $stmt = $this->dbh->prepare("UPDATE contents SET is_delete=1 WHERE id=:id");
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
    }   
  }


  public function find($id){
    $stmt = $this->dbh->prepare("SELECT * FROM contents WHERE id=:id AND is_delete=0");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $contents = $this->getContents($stmt->fetchAll(PDO::FETCH_ASSOC));
    return $contents[0];
  }

  public function findAll(){
    $stmt = $this->dbh->prepare("SELECT * FROM contents WHERE is_delete=0 ORDER BY created_at DESC"); 
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $contents = $this->getContents($stmt->fetchAll(PDO::FETCH_ASSOC));
    return $contents;
  }

  public function getPager($page = 1, $limit = 10){
    $start = ($page - 1) * $limit;  // LIMIT x, y：1ページ目を表示するとき、xは0になる
    $pager = array('total' => null, 'contents' => null);

    // 総記事数
    $stmt = $this->dbh->prepare("SELECT COUNT(*) FROM contents WHERE is_delete=0");
    $stmt->execute();
    $pager['total'] = $stmt->fetchColumn();

    // 表示するデータ
    $stmt = $this->dbh->prepare("SELECT * FROM contents
      WHERE is_delete=0
      ORDER BY created_at DESC
      LIMIT :start, :limit");
    $stmt->bindParam(':start', $start, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $pager['contents'] = $this->getContents($stmt->fetchAll(PDO::FETCH_ASSOC));
    return $pager;
  }

  private function getContents($results){
    $contents = array();
    foreach ($results as $result){
      $content = new Content();
      $content->setId($result['id']);
      $content->setCategoryId($result['category_id']);
      $content->setPoints($result['points']);
      $content->setWhy($result['why']);
      $content->setFuture($result['future']);
      $content->setFilename($result['filename']);
      $content->setWhat($result['what']);
      $content->setStep($result['step']);
      $content->setCreatedAt($result['created_at']);
      $content->setUpdatedAt($result['updated_at']);
      $contents[] = $content;
    }
    return $contents;
  }

}
