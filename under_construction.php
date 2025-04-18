<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Under Construction</title>
    <link rel="stylesheet" href="/grocery_store/styles.css?v=1.1">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
            text-align: center;
        }

        .construction-container {
            opacity: 0;
            transform: translateY(30px);
            animation: fadeSlideIn 0.6s ease-out forwards;
        }

        h1 {
            font-size: 36px;
            color: #314401;
            margin-bottom: 15px;
        }

        p {
            font-size: 18px;
            color: #555;
        }

        a.back-home {
            display: inline-block;
            margin-top: 25px;
            padding: 10px 20px;
            background-color: #314401;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        a.back-home:hover {
            background-color: #94a84e;
        }

        @keyframes fadeSlideIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="construction-container">
        <h1>🚧 This page is under construction</h1>
        <p>We're working hard to bring this page to life. Please check back later.</p>
        <a href="/grocery_store/index.php" class="back-home">Back to Home</a>
    </div>
</body>
</html>
