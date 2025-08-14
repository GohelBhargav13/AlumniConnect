<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlumniConnect | Admin</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Inter', sans-serif; background-color: #0d1117; color: white;">

    <!-- Main container for the entire page -->
    <div style="display: flex; height: 100vh; overflow: hidden; border: 1px solid #30363d; border-radius: 10px; margin: 20px;">

        <!-- Sidebar Navigation -->
        <?php include "./sidebar.php" ?>

        <!-- Main Content Area -->
        <div style="flex-grow: 1; padding: 20px; box-sizing: border-box; background-color: #0d1117;">
            <h1 style="text-align: center; font-size: 24px; margin-bottom: 40px; border-bottom: 1px solid #30363d; padding-bottom: 20px;">Student's</h1>
            
            <!-- Alumni cards container -->
            <div style="display: flex; flex-wrap: wrap; justify-content: flex-start; gap: 20px;">
                <!-- Alumni Card 1 -->
                <div style="width: calc(50% - 10px); background-color: #161b22; padding: 20px; border-radius: 8px; text-align: center; border: 1px solid #30363d; box-sizing: border-box;">
                    <div style="width: 80px; height: 80px; background-color: #0d1117; border-radius: 50%; margin: 0 auto 20px auto; border: 1px solid #30363d;"></div>
                    <h2 style="font-size: 20px; margin-top: 0; margin-bottom: 5px;">Student Name 1</h2>
                    <p style="margin: 0; font-size: 16px;">Passout Year: 2020</p>
                    <p style="margin: 0; font-size: 16px;">College: Google</p>
                </div>
                <!-- Alumni Card 2 -->
                 <div style="width: calc(50% - 10px); background-color: #161b22; padding: 20px; border-radius: 8px; text-align: center; border: 1px solid #30363d; box-sizing: border-box;">
                    <div style="width: 80px; height: 80px; background-color: #0d1117; border-radius: 50%; margin: 0 auto 20px auto; border: 1px solid #30363d;"></div>
                    <h2 style="font-size: 20px; margin-top: 0; margin-bottom: 5px;">Student Name 2</h2>
                    <p style="margin: 0; font-size: 16px;">Passout Year: 2020</p>
                    <p style="margin: 0; font-size: 16px;">College: Google</p>
                </div>
                <!-- You can add more alumni cards here -->
            </div>
        </div>
    </div>

</body>
</html>
