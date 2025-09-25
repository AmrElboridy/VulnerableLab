<?php
session_start();
if (!isset($_SESSION['comments'])) {
    $_SESSION['comments'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $_SESSION['comments'][] = $_POST['comment']; // No sanitization!
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>XSS Playground (Vulnerable)</title>
</head>
<body>
  <h1>🚨 XSS Vulnerable Lab</h1>

  <h2>1. Stored XSS</h2>
  <form method="POST">
    <input type="text" name="comment" placeholder="Leave a comment" />
    <button type="submit">Post</button>
  </form>
  <ul>
    <?php foreach ($_SESSION['comments'] as $c) {
      echo "<li>$c</li>"; // ❌ Vulnerable
    } ?>
  </ul>

  <h2>2. Reflected XSS</h2>
  <?php
  if (isset($_GET['q'])) {
      $search = $_GET['q'];
      echo "You searched for: " . $search; // ❌ Vulnerable
  }
  ?>

  <h2>3. DOM-Based XSS</h2>
  <div id="output"></div>
  <script>
    // ❌ Vulnerable DOM sink
    document.getElementById('output').innerHTML = location.hash.substring(1);
  </script>
</body>
</html>
