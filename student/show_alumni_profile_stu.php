<?php 
require_once '../utills/db_conn.php';

    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }

    if($_SERVER['REQUEST_METHOD'] === 'GET' && urldecode(isset($_GET['alumni_id']))){
        if(urldecode($_GET['alumni_id'])){
            $alumni_id_get = $_GET['alumni_id'];
        }
    }
   
    if(!isset($alumni_id_get)){
        header("Location: student_dashboard.php");
        exit();
    }

    $find_alumni_sql = "SELECT * FROM alumnimaster WHERE alumni_id = ?";
    $find_alumni_sql_stmt = $conn->prepare($find_alumni_sql);
    $find_alumni_sql_stmt->bind_param('i',$alumni_id_get);

    $alumni_details = [];
    if($find_alumni_sql_stmt->execute()){
        $final_data = $find_alumni_sql_stmt->get_result();
        while($final_data_st = $final_data->fetch_assoc()){
            $alumni_details = $final_data_st;
        }
       
        
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni profile | AlumniConnect</title>
</head>
<body>
    <?php include './sidebar.php' ?>

    <div class="conatiner" style="text-align: center;">
        <form action="./show_alumni_profile_stu.php?alumni_id=<?= $alumni_id_get ?>" method="post">
        <p><?= htmlspecialchars($alumni_details['alumni_name']); ?></p>
        <button type="submit">connect</button>
        </form>
    </div>
</body>
</html>