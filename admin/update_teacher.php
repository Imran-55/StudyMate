<?php

include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   header('location:login.php');
}

if(isset($_GET['get_id'])){
   $get_id = $_GET['get_id'];
}else{
   $get_id = '';
   header('location:teachers.php');
}

if(isset($_POST['submit'])){

   $title = $_POST['title'];
   $title = filter_var($title, FILTER_SANITIZE_STRING);
   // $description = $_POST['description'];
   // $description = filter_var($description, FILTER_SANITIZE_STRING);
   // $status = $_POST['status'];
   // $status = filter_var($status, FILTER_SANITIZE_STRING);

   $update_playlist = $conn->prepare("UPDATE `teachers` SET teacher_name = ? WHERE id = ?");
   $update_playlist->execute([$title, $get_id]);
   

   $old_image = $_POST['old_image'];
   $old_image = filter_var($old_image, FILTER_SANITIZE_STRING);
   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $ext = pathinfo($image, PATHINFO_EXTENSION);
   $rename = unique_id().'.'.$ext;
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = '../uploaded_files/'.$rename;

   if(!empty($image)){
      if($image_size > 2000000){
         $message[] = 'image size is too large!';
      }else{
         $update_image = $conn->prepare("UPDATE `teachers` SET teacher_image = ? WHERE id = ?");
         $update_image->execute([$rename, $get_id]);
         move_uploaded_file($image_tmp_name, $image_folder);
         if($old_image != '' AND $old_image != $rename){
            unlink('../uploaded_files/'.$old_image);
         }
      }
   } 

   $message[] = 'Teachers updated!';  

}

if(isset($_POST['delete'])){
   $delete_id = $_POST['id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);
   $delete_teacher_image = $conn->prepare("SELECT * FROM `teachers` WHERE id = ? LIMIT 1");
   $delete_teacher_image->execute([$delete_id]);
   $fetch_thumb = $delete_teacher_image->fetch(PDO::FETCH_ASSOC);
   
   $delete_teacher = $conn->prepare("DELETE FROM `teachers` WHERE id = ?");
   $delete_teacher->execute([$delete_id]);
   header('location:teachers.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Update Playlist</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>
   
<section class="playlist-form">

   <h1 class="heading">update playlist</h1>

   <?php
         $select_teacher = $conn->prepare("SELECT * FROM `teachers` WHERE id = ?");
         $select_teacher->execute([$get_id]);
         if($select_teacher->rowCount() > 0){
         while($fetch_teacher = $select_teacher->fetch(PDO::FETCH_ASSOC)){
            $playlist_id = $fetch_teacher['id'];
            $count_videos = $conn->prepare("SELECT * FROM `content` WHERE playlist_id = ?");
            $count_videos->execute([$playlist_id]);
            $total_videos = $count_videos->rowCount();
      ?>
   <form action="" method="post" enctype="multipart/form-data">
   <input type="hidden" name="old_image" value="<?= isset($fetch_teacher['thumb']) ? htmlspecialchars($fetch_teacher['teacher_image']) : ''; ?>">
      
      <p>Teacher name <span>*</span></p>
      <input type="text" name="title" maxlength="100" required placeholder="enter playlist title" value="<?= isset($fetch_teacher['teacher_name']) ? htmlspecialchars($fetch_teacher['teacher_name']) : ''; ?>" class="box">

      
      <p>Teacher image <span>*</span></p>
      <div class="thumb">
         <!-- <span><?= $total_videos; ?></span> -->
         <img src="../uploaded_files/<?= $fetch_teacher['teacher_image']; ?>" alt="not found">
      </div>
      <input type="file" name="image" accept="image/*" class="box">
      <input type="submit" value="update playlist" name="submit" class="btn">
      <div class="flex-btn">
         <input type="submit" value="delete" class="delete-btn" onclick="return confirm('delete this playlist?');" name="delete">
        
      </div>
   </form>
   <?php
      } 
   }else{
      echo '<p class="empty">no playlist added yet!</p>';
   }
   ?>

</section>
















<script src="../js/admin_script.js"></script>

</body>
</html>