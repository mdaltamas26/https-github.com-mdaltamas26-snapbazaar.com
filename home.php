<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>SnapBazaar - Home</title>
  <style>
    /* Reset & base */
    * {
      box-sizing: border-box;
    }
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f9fafb;
      margin: 0;
      padding: 0;
      color: #333;
      line-height: 1.6;
    }

    /* Navbar */
    .navbar {
      background-color: #1f2937;
      color: white;
      display: flex;
      justify-content: space-between;
      padding: 0.75rem 1.5rem;
      align-items: center;
      position: sticky;
      top: 0;
      z-index: 100;
    }
    .navbar a {
      color: white;
      text-decoration: none;
      margin-right: 1.25rem;
      font-weight: 600;
      transition: color 0.2s ease;
    }
    .navbar a:last-child {
      margin-right: 0;
    }
    .navbar a:hover {
      color: #3b82f6;
    }
    .logout-btn {
      background-color: #ef4444;
      padding: 0.5rem 1rem;
      border: none;
      border-radius: 5px;
      color: white;
      font-weight: 600;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    .logout-btn:hover {
      background-color: #b91c1c;
    }

    /* Container */
    .container {
      max-width: 1200px;
      margin: 2rem auto 4rem;
      padding: 0 1rem;
      text-align: center;
    }

    /* Heading */
    h1 {
      font-size: 2.75rem;
      margin-bottom: 0.25rem;
      color: #111827;
    }
    p.subtitle {
      font-size: 1.125rem;
      color: #6b7280;
      margin-bottom: 2rem;
    }

    /* Search Bar */
    .search-bar {
      max-width: 600px;
      margin: 0 auto 3rem;
      display: flex;
      border-radius: 9999px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgb(0 0 0 / 0.1);
      border: 1px solid #d1d5db;
    }
    .search-bar input {
      flex-grow: 1;
      padding: 0.75rem 1rem;
      font-size: 1rem;
      border: none;
      outline: none;
    }
    .search-bar button {
      background-color: #3b82f6;
      border: none;
      color: white;
      padding: 0 1.25rem;
      cursor: pointer;
      font-weight: 600;
      transition: background-color 0.3s ease;
    }
    .search-bar button:hover {
      background-color: #2563eb;
    }
    .search-bar button.voice {
      background: #10b981;
      font-size: 1.25rem;
      padding: 0 1rem;
      line-height: 1;
    }
    .search-bar button.voice:hover {
      background: #047857;
    }

    /* Products Grid */
    .products {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 1.5rem;
      justify-items: center;
    }
    .product-card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgb(0 0 0 / 0.1);
      padding: 1rem;
      width: 100%;
      max-width: 220px;
      display: flex;
      flex-direction: column;
      align-items: center;
      transition: transform 0.3s ease;
    }
    .product-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgb(0 0 0 / 0.15);
    }
    .product-card img {
      width: 140px;
      height: 140px;
      object-fit: contain;
      margin-bottom: 1rem;
    }
    .product-card h3 {
      font-size: 1.125rem;
      margin: 0.5rem 0;
      color: #111827;
    }
    .product-card p.price {
      font-weight: 700;
      color: #ef4444;
      margin-bottom: 1rem;
      font-size: 1.125rem;
    }
    .btn-add-cart {
      background-color: #2563eb;
      color: white;
      padding: 0.5rem 1rem;
      border-radius: 6px;
      text-decoration: none;
      font-weight: 600;
      width: 100%;
      text-align: center;
      transition: background-color 0.3s ease;
      user-select: none;
    }
    .btn-add-cart:hover {
      background-color: #1e40af;
    }

    /* Support Ticket Link */
    #support-link {
      margin-top: 3rem;
      display: inline-block;
      font-size: 1.125rem;
      color: #3b82f6;
      text-decoration: underline;
      cursor: pointer;
      transition: color 0.3s ease;
    }
    #support-link:hover {
      color: #2563eb;
    }

    /* Live Chat Button */
    #chat-button {
      position: fixed;
      bottom: 25px;
      right: 25px;
      background-color: #3b82f6;
      color: white;
      border-radius: 9999px;
      width: 52px;
      height: 52px;
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 24px;
      cursor: pointer;
      box-shadow: 0 4px 12px rgb(0 0 0 / 0.15);
      transition: background-color 0.3s ease;
      user-select: none;
    }
    #chat-button:hover {
      background-color: #2563eb;
    }
  </style>
</head>
<body>

  <nav class="navbar" role="navigation" aria-label="Main navigation">
    <div>
      <a href="home.php">Home</a>
      <a href="shop.php">Shop</a>
      <a href="cart.php">Cart</a>
      <a href="profile.php">Profile</a>
      <a href="ai_chat_box.html">AI Assistant</a>
    </div>
    <div>
      <a href="logout.php" class="logout-btn">Logout</a>
    </div>
  </nav>

  <main class="container" role="main">
    <h1>Welcome to SnapBazaar</h1>
    <p class="subtitle">Your one-stop destination for online shopping.</p>

    <section aria-label="Product search">
      <div class="search-bar">
        <input type="text" id="search-input" placeholder="Search for products..." aria-label="Search products" />
        <button type="button" onclick="searchProduct()" aria-label="Search">Search</button>
        <button type="button" onclick="startVoiceSearch()" aria-label="Voice Search" class="voice">🎤</button>
      </div>
    </section>

    <section aria-label="Featured products">
      <h2>Featured Products</h2>
      <div class="products">
        <?php
          $query = "SELECT * FROM products LIMIT 4";
          $result = mysqli_query($conn, $query);

          while ($row = mysqli_fetch_assoc($result)) {
            $imagePath = "uploads/" . htmlspecialchars($row['image']);
            $name = htmlspecialchars($row['name']);
            $price = number_format($row['price'], 2);
            $id = (int)$row['id'];

            echo <<<HTML
              <article class="product-card" role="article" aria-label="Product: $name">
                <img src="$imagePath" alt="Image of $name" loading="lazy" />
                <h3>$name</h3>
                <p class="price">₹$price</p>
                <a href="add_to_cart.php?id=$id" class="btn-add-cart" role="button" aria-label="Add $name to cart">Add to Cart</a>
              </article>
            HTML;
          }
        ?>
      </div>
    </section>

    <a href="support_tickets.php" id="support-link">Need Help? Raise a Support Ticket</a>
  </main>

  <button id="chat-button" aria-label="Open live chat" onclick="openChat()">💬</button>

  <script>
    function openChat() {
      let chatWindow = window.open('live_chat.php', 'Chat', 'width=400,height=500');
      if (chatWindow) chatWindow.focus();
    }

    function searchProduct() {
      const query = document.getElementById('search-input').value.trim();
      if (query) {
        window.location.href = `search_results.php?q=${encodeURIComponent(query)}`;
      }
    }

    function startVoiceSearch() {
      const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
      if (!SpeechRecognition) {
        alert('Voice search is not supported in your browser.');
        return;
      }
      const recognition = new SpeechRecognition();
      recognition.lang = 'en-US';
      recognition.start();

      recognition.onresult = event => {
        const transcript = event.results[0][0].transcript;
        document.getElementById('search-input').value = transcript;
        searchProduct();
      };

      recognition.onerror = event => {
        console.error('Voice recognition error:', event.error);
      };
    }
  </script>

</body>
</html>
