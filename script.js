document.getElementById("start-chat-btn").addEventListener("click", () => {
  document.getElementById("chat-container").style.display = "flex";
});

document.getElementById("send-btn").addEventListener("click", sendMessage);
document.getElementById("user-input").addEventListener("keypress", function (e) {
  if (e.key === "Enter") sendMessage();
});

async function sendMessage() {
  const inputField = document.getElementById("user-input");
  const message = inputField.value.trim();
  if (!message) return;

  addMessage("user", message);
  inputField.value = "";

  try {
    const res = await fetch("/chat", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ message }),
    });

    const data = await res.json();
    addMessage("bot", data.reply || "Bot didn't respond.");
  } catch (error) {
    addMessage("error", "Error: Could not reach the AI service.");
  }
}

function addMessage(sender, text) {
  const chatMessages = document.getElementById("chat-messages");
  const msg = document.createElement("div");
  msg.className = sender === "user" ? "message-user" :
                  sender === "bot" ? "message-bot" :
                  "message-error";
  msg.textContent = text;
  chatMessages.appendChild(msg);
  chatMessages.scrollTop = chatMessages.scrollHeight;
}
