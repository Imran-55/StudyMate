<?php

include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   header('location:login.php');
}
print_r($_POST);
if(isset($_POST['delete'])){
   $delete_id = $_POST['playlist_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   $verify_playlist = $conn->prepare("SELECT * FROM `teachers` WHERE id = ? AND tutor_id = ? LIMIT 1");
   $verify_playlist->execute([$delete_id, $tutor_id]);

   if($verify_playlist->rowCount() > 0){

   

   $delete_playlist_thumb = $conn->prepare("SELECT * FROM `teachers` WHERE id = ? LIMIT 1");
   $delete_playlist_thumb->execute([$delete_id]);
   $fetch_thumb = $delete_playlist_thumb->fetch(PDO::FETCH_ASSOC);
   unlink('../uploaded_files/'.$fetch_thumb['teacher_image']);
   

   $delete_playlist = $conn->prepare("DELETE FROM `teachers` WHERE id = ?");
   $delete_playlist->execute([$delete_id]);
   $message[] = 'Teachers deleted!';
   }else{
      $message[] = 'Teachers already deleted!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Playlists</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="playlists">

   <h1 class="heading">added Teachers</h1>

   <div class="box-container">
   
      <div class="box teacher-box" style="text-align: center;">
         <h3 class="title" style="margin-bottom: .5rem;">Add new Teachers</h3>
         <a href="add_teacher.php" class="btn">add Teachers</a>
      </div>

      <?php
         $select_teacher = $conn->prepare("SELECT * FROM `teachers` WHERE tutor_id = ? ");
         $select_teacher->execute([$tutor_id]);
         if($select_teacher->rowCount() > 0){
         while($fetch_teacher = $select_teacher->fetch(PDO::FETCH_ASSOC)){
            $playlist_id = $fetch_teacher['id'];
           
      ?>
      <div class="box">
         <div class="flex">
            <!-- <div><i class="fas fa-circle-dot" style="<?php if($fetch_teacher['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"></i><span style="<?php if($fetch_teacher['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"><?= $fetch_teacher['status']; ?></span></div>
            <div><i class="fas fa-calendar"></i><span><?= $fetch_teacher['date']; ?></span></div> -->
         </div>
         <h3 class="title"><?= $fetch_teacher['teacher_name']; ?></h3>
         <div class="thumb">
            <!-- <span><?= $total_videos; ?></span> -->
            <img src="../uploaded_files/<?= $fetch_teacher['teacher_image']; ?>" alt="">
         </div>
         <!-- <h3 class="title"><?= $fetch_teacher['title']; ?></h3>
         <p class="description"><?= $fetch_teacher['description']; ?></p> -->
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="playlist_id" value="<?= $playlist_id; ?>">
            <a href="update_teacher.php?get_id=<?= $playlist_id; ?>" class="option-btn">update</a>
            <input type="submit" value="delete" class="delete-btn" onclick="return confirm('delete this teacher?');" name="delete">
         </form>
        
      </div>
      <?php
         } 
      }else{
         echo '<p class="empty">no Teachers added yet!</p>';
      }
      ?>

   </div>

</section>














<script src="../js/admin_script.js"></script>

<script>
   document.querySelectorAll('.playlists .box-container .box .description').forEach(content => {
      if(content.innerHTML.length > 100) content.innerHTML = content.innerHTML.slice(0, 100);
   });
</script>

</body>
</html>