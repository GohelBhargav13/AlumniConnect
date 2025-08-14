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

      <?php include "./sidebar.php" ?>

        <!-- Main Content Area -->
        <div style="flex-grow: 1; padding: 20px; box-sizing: border-box; background-color: #0d1117;">
            <h1 style="text-align: center; font-size: 24px; margin-bottom: 40px; border-bottom: 1px solid #30363d; padding-bottom: 20px;">All Posts</h1>
            
            <!-- Posts container -->
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <!-- Post Item 1 -->
                <div style="background-color: #161b22; padding: 20px; border-radius: 8px; border: 1px solid #30363d;">
                    <h2 style="font-size: 20px; margin-top: 0; margin-bottom: 10px;">Post Title 1</h2>
                    <p style="font-size: 16px; margin: 0;">This is a brief description or snippet of the first post. It provides a summary of the content to the user.</p>
                </div>
                <!-- Post Item 2 -->
                <div style="background-color: #161b22; padding: 20px; border-radius: 8px; border: 1px solid #30363d;">
                    <h2 style="font-size: 20px; margin-top: 0; margin-bottom: 10px;">Post Title 2</h2>
                    <p style="font-size: 16px; margin: 0;">This is a brief description or snippet of the second post. It provides a summary of the content to the user.</p>
                </div>
                <!-- Post Item 3 (You can add more as needed) -->
                <div style="background-color: #161b22; padding: 20px; border-radius: 8px; border: 1px solid #30363d;">
                    <h2 style="font-size: 20px; margin-top: 0; margin-bottom: 10px;">Post Title 3</h2>
                    <p style="font-size: 16px; margin: 0;">This is a brief description or snippet of the third post. It provides a summary of the content to the user.</p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
