<div id="chat-box">
    <div id="chat-messages"></div>
    <input type="text" id="chat-input" placeholder="Type a message..." />
    <button onclick="sendMessage()">Send</button>
</div>

<script>
function sendMessage() {
    let message = document.getElementById('chat-input').value;
    if (message.trim() === '') return;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "send_message.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById('chat-messages').innerHTML += "<p><b>You:</b> " + message + "</p>";
            document.getElementById('chat-input').value = '';
        }
    };
    xhr.send("message=" + encodeURIComponent(message));
}
</script>