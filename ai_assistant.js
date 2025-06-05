const chatBox = document.getElementById("chat-box");
const userInput = document.getElementById("user-input");
const sendBtn = document.getElementById("send-btn");

// Load chat history from localStorage
function loadChatHistory() {
  const saved = localStorage.getItem("chatHistory");
  if (saved) {
    chatBox.innerHTML = saved;
  }
}

// Save chat history to localStorage
function saveChatHistory() {
  localStorage.setItem("chatHistory", chatBox.innerHTML);
}

// Add user message to chat
function addUserMessage(message) {
  const div = document.createElement("div");
  div.className = "message user-message";
  div.innerText = message;
  chatBox.appendChild(div);
  saveChatHistory();
}

// Add AI response to chat
function addBotMessage(message) {
  const div = document.createElement("div");
  div.className = "message bot-message";
  div.innerText = message;
  chatBox.appendChild(div);
  saveChatHistory();
}

// Send message to PHP
sendBtn.addEventListener("click", () => {
  const message = userInput.value.trim();
  if (message === "") return;
  addUserMessage(message);
  userInput.value = "";

  fetch("ai_chat_response.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `message=${encodeURIComponent(message)}`
  })
  .then(res => res.json())
  .then(data => {
    addBotMessage(data.response);
  });
});

// Load chat on page load
window.onload = loadChatHistory;
