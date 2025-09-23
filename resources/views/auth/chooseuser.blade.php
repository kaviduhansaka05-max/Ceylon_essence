<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Choose Login</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f6f9;
      text-align: center;
      padding-top: 100px;
    }
    h2 {
      margin-bottom: 30px;
    }
    .btn {
      padding: 15px 30px;
      margin: 15px;
      font-size: 18px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: 0.3s;
    }
    .user-btn {
      background-color: #3498db;
      color: white;
    }
    .admin-btn {
      background-color: #e74c3c;
      color: white;
    }
    .btn:hover {
      opacity: 0.8;
    }
  </style>
</head>
<body>
  <h2>Choose Login Type</h2>

 <a href="{{ route('login.user') }}">
    <button class="btn user-btn">Login as User</button>
</a>

<a href="{{ route('login.admin') }}">
    <button class="btn admin-btn">Login as Admin</button>
</a>

</body>
</html>
