<!-- AI Chat Assistant Button and Box -->
<style>
#ai-chat-btn {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: #0d6efd;
    color: white;
    border: none;
    border-radius: 50%;
    width: 55px;
    height: 55px;
    font-size: 24px;
    z-index: 1000;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

#ai-chat-box {
    position: fixed;
    bottom: 90px;
    right: 20px;
    width: 320px;
    background: white;
    border: 1px solid #ddd;
    border-radius: 10px;
    display: none;
    z-index: 1000;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    overflow: hidden;
}

#ai-chat-box header {
    background: #0d6efd;
    color: white;
    padding: 10px 15px;
    font-weight: bold;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

#ai-chat-box .messages {
    height: 250px;
    overflow-y: auto;
    padding: 10px;
    font-size: 14px;
}

#ai-chat-box form {
    display: flex;
    border-top: 1px solid #ddd;
    padding: 10px;
}

#ai-chat-box input {
    flex: 1;
    padding: 8px;
    font-size: 14px;
}

#ai-chat-box button {
    background: #0d6efd;
    border: none;
    color: white;
    padding: 0 12px;
    border-radius: 5px;
    margin-left: 5px;
}
</style>

<button id="ai-chat-btn">?</button>

<div id="ai-chat-box">
    <header>
        SnapBazaar AI
        <span id="ai-chat-close" style="cursor:pointer;">×</span>
    </header>
    <div class="messages" id="ai-chat-messages"></div>
    <form id="ai-chat-form">
        <input type="text" id="ai-user-input" placeholder="Type your question..." required>
        <button type="submit">Send</button>
    </form>
</div>

<!-- JS File -->
<script src="assets/js/ai_assistant.js"></script>
