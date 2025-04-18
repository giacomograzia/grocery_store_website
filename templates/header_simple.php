<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grocery Store</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/grocery_store/styles.css?v=1.1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
            background-color: #314401;
        }

        .logo {
            height: 75px;
            width: auto;
            border-radius: 5px;
        }

        .navbar {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar a {
            font-size: 16px;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
            border: none;
            background: none;
            cursor: pointer;
            font-family: inherit;
            border-radius: 5px;
        }

        .navbar a:hover {
            background-color: #94a84e;
        }
    </style>
</head>
<body>
    <header>
        <a href="/grocery_store/index.php">
            <img src="/grocery_store/assets/logo_horizontal.png" alt="Grocery Store Logo" class="logo">
        </a>
        <div class="navbar">
            <a href="/grocery_store/index.php">Back to Shop</a>
        </div>
    </header>
