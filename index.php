<?php
// Database configuration
$db_host = 'localhost';
$db_user = 'blog_admin';
$db_pass = 'password123';
$db_name = 'blogdb';

// Create connection
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle form submission
if (isset($_POST['submit'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (!empty($title) && !empty($content)) {
        // Use prepared statements for security
        $stmt = mysqli_prepare($conn, "INSERT INTO posts (title, content) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ss", $title, $content);
        
        if (mysqli_stmt_execute($stmt)) {
            // PRG Pattern: Redirect to prevent duplicate submission on refresh
            header("Location: index.php?success=1");
            exit();
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = "Please fill in all fields.";
    }
}

// Fetch posts
$result = mysqli_query($conn, "SELECT * FROM posts ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple PHP Blog</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #2c3e50;
            text-align: center;
        }
        form {
            margin-bottom: 40px;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            display: block;
            width: 100%;
            padding: 10px;
            background: #3498db;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #2980b9;
        }
        .post {
            background: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 4px;
            border-left: 5px solid #3498db;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .post h3 {
            margin-top: 0;
            color: #2980b9;
        }
        .post .date {
            font-size: 0.8em;
            color: #7f8c8d;
        }
        .alert {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>My Simple Blog</h1>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">Post published successfully!</div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php">
            <div class="form-group">
                <label for="title">Post Title</label>
                <input type="text" name="title" id="title" required placeholder="Enter title">
            </div>
            <div class="form-group">
                <label for="content">Content</label>
                <textarea name="content" id="content" rows="5" required placeholder="What's on your mind?"></textarea>
            </div>
            <button type="submit" name="submit">Publish Post</button>
        </form>

        <h2>Recent Posts</h2>
        <div id="posts-container">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="post">
                        <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                        <div class="date">Posted on: <?php echo $row['created_at']; ?></div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align:center;">No posts yet. Be the first to write something!</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
