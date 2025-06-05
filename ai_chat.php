<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>AI Chat Assistant</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f0f2f5;
      font-family: 'Segoe UI', sans-serif;
    }
    .chat-container {
      max-width: 600px;
      margin: 50px auto;
      background: white;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      overflow: hidden;
    }
    .chat-header {
      background: #0d6efd;
      color: white;
      padding: 15px;
      text-align: center;
      font-weight: bold;
    }
    .chat-box {
      height: 400px;
      overflow-y: auto;
      padding: 20px;
    }
    .message {
      margin-bottom: 15px;
    }
    .user-msg {
      text-align: right;
      color: #0d6efd;
    }
    .bot-msg {
      text-align: left;
      color: #343a40;
    }
    .chat-input {
      display: flex;
      padding: 15px;
      border-top: 1px solid #ddd;
    }
    .chat-input input {
      flex: 1;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    .chat-input button {
      margin-left: 10px;
    }
  </style>
</head>
<body>

<div class="chat-container">
  <div class="chat-header">SnapBazaar AI Assistant</div>
  <div class="chat-box" id="chatBox"></div>

  <div class="chat-input">
    <input type="text" id="userInput" placeholder="Type your message..." class="form-control">
    <button class="btn btn-primary" onclick="sendMessage()">Send</button>
  </div>
</div>

<script>
function sendMessage() {
  const input = document.getElementById('userInput');
  const chatBox = document.getElementById('chatBox');
  const userMsg = input.value.trim();

  if (userMsg === '') return;

  // Show user message
  chatBox.innerHTML += `<div class='message user-msg'><strong>You:</strong> ${userMsg}</div>`;
  chatBox.scrollTop = chatBox.scrollHeight;

  // Clear input
  input.value = '';

  // Send to backend
  fetch('ai_chat_response.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'message=' + encodeURIComponent(userMsg)
  })
  .then(res => res.json())
  .then(data => {
    chatBox.innerHTML += `<div class='message bot-msg'><strong>AI:</strong> ${data.response}</div>`;
    chatBox.scrollTop = chatBox.scrollHeight;
  })
  .catch(() => {
    chatBox.innerHTML += `<div class='message bot-msg'><strong>AI:</strong> Sorry, something went wrong.</div>`;
  });
}
</script>

</body>
</html>
