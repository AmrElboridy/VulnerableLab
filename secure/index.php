<?php
session_start();

// --- Setup in-memory SQLite DB for demo ---
$db = new SQLite3(':memory:');
$db->exec("CREATE TABLE users (id INTEGER PRIMARY KEY, username TEXT, password TEXT)");
$db->exec("INSERT INTO users (username, password) VALUES ('admin', 'admin123'), ('test', 'test123')");

// --- Stored XSS (fixed) ---
if (!isset($_SESSION['comments'])) {
    $_SESSION['comments'] = [];
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $_SESSION['comments'][] = htmlspecialchars($_POST['comment'], ENT_QUOTES, 'UTF-8'); // ✅ encode
}

// --- SQL Injection login (fixed) ---
$loginMsg = '';
if (isset($_POST['username'], $_POST['password'])) {
    $stmt = $db->prepare("SELECT * FROM users WHERE username = :u AND password = :p");
    $stmt->bindValue(':u', $_POST['username'], SQLITE3_TEXT);
    $stmt->bindValue(':p', $_POST['password'], SQLITE3_TEXT);
    $result = $stmt->execute();
    if ($row = $result->fetchArray()) {
        $loginMsg = "Welcome, " . htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8') . "! (Logged in)";
    } else {
        $loginMsg = "❌ Invalid credentials";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>🛡️ Secure Lab</title></head>
<body>
  <h1>🛡️ Secure Lab (XSS + SQLi)</h1>

  <h2>1. Stored XSS (Fixed)</h2>
  <form method="POST">
    <input type="text" name="comment" placeholder="Leave a comment"/>
    <button type="submit">Post</button>
  </form>
  <ul>
    <?php foreach ($_SESSION['comments'] as $c) {
        echo "<li>$c</li>"; // ✅ safe
    } ?>
  </ul>

  <h2>2. Reflected XSS (Fixed)</h2>
  <?php
  if (isset($_GET['q'])) {
      $search = htmlspecialchars($_GET['q'], ENT_QUOTES, 'UTF-8'); // ✅ encode
      echo "You searched for: " . $search;
  }
  ?>

  <h2>3. DOM XSS (Fixed)</h2>
  <div id="output"></div>
  <script>
    document.getElementById('output').textContent = location.hash.substring(1); // ✅ safe
  </script>

  <h2>4. SQL Injection Login (Fixed)</h2>
  <form method="POST">
    <input type="text" name="username" placeholder="Username"/>
    <input type="password" name="password" placeholder="Password"/>
    <button type="submit">Login</button>
  </form>
  <p><?= $loginMsg ?></p>
</body>
</html>
