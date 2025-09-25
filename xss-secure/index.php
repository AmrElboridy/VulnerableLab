<?php
session_start();
if (!isset($_SESSION['comments'])) {
    $_SESSION['comments'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    // ✅ Encode before storing
    $_SESSION['comments'][] = htmlspecialchars($_POST['comment'], ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>XSS Playground (Secure)</title>
</head>
<body>
  <h1>🛡️ XSS Secure Lab</h1>

  <h2>1. Stored XSS (Fixed)</h2>
  <form method="POST">
    <input type="text" name="comment" placeholder="Leave a comment" />
    <button type="submit">Post</button>
  </form>
  <ul>
    <?php foreach ($_SESSION['comments'] as $c) {
      echo "<li>$c</li>"; // ✅ Safe output
    } ?>
  </ul>

  <h2>2. Reflected XSS (Fixed)</h2>
  <?php
  if (isset($_GET['q'])) {
      $search = htmlspecialchars($_GET['q'], ENT_QUOTES, 'UTF-8');
      echo "You searched for: " . $search; // ✅ Safe output
  }
  ?>

  <h2>3. DOM-Based XSS (Fixed)</h2>
  <div id="output"></div>
  <script>
    // ✅ Use textContent instead of innerHTML
    document.getElementById('output').textContent = location.hash.substring(1);
  </script>
</body>
</html>
