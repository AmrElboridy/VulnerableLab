<?php
session_start();

// --- Setup in-memory SQLite DB for demo ---
$db = new SQLite3(':memory:');
$db->exec("CREATE TABLE users (id INTEGER PRIMARY KEY, username TEXT, password TEXT)");
$db->exec("INSERT INTO users (username, password) VALUES ('admin', 'admin123'), ('test', 'test123')");

// --- Stored XSS ---
if (!isset($_SESSION['comments'])) {
    $_SESSION['comments'] = [];
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $_SESSION['comments'][] = $_POST['comment']; // ❌ no sanitization
}

// --- SQL Injection login ---
$loginMsg = '';
if (isset($_POST['username'], $_POST['password'])) {
    $u = $_POST['username'];
    $p = $_POST['password'];
    // ❌ vulnerable query (string concatenation)
    $query = "SELECT * FROM users WHERE username = '$u' AND password = '$p'";
    $result = $db->query($query);
    if ($row = $result->fetchArray()) {
        $loginMsg = "Welcome, " . $row['username'] . "! (Logged in)";
    } else {
        $loginMsg = "❌ Invalid credentials";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>🚨 Vulnerable Lab</title></head>
<body>
  <h1>🚨 Vulnerable Lab (XSS + SQLi)</h1>

  <h2>1. Stored XSS</h2>
  <form method="POST">
    <input type="text" name="comment" placeholder="Leave a comment"/>
    <button type="submit">Post</button>
  </form>
  <ul>
    <?php foreach ($_SESSION['comments'] as $c) {
        echo "<li>$c</li>"; // ❌ vulnerable
    } ?>
  </ul>

  <h2>2. Reflected XSS</h2>
  <?php
  if (isset($_GET['q'])) {
      $search = $_GET['q'];
      echo "You searched for: " . $search; // ❌ vulnerable
  }
  ?>

  <h2>3. DOM XSS</h2>
  <div id="output"></div>
  <script>
    document.getElementById('output').innerHTML = location.hash.substring(1); // ❌ vulnerable
  </script>

  <h2>4. SQL Injection Login</h2>
  <form method="POST">
    <input type="text" name="username" placeholder="Username"/>
    <input type="password" name="password" placeholder="Password"/>
    <button type="submit">Login</button>
  </form>
  <p><?= $loginMsg ?></p>
</body>
</html>
