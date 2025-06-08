const express = require("express");
const axios = require("axios"); // Replace node-fetch with axios
const cors = require("cors");

const app = express();
const PORT = 3000;

// ✅ Use your DeepSeek/OpenAI API key here
const AI_API_KEY = "sk-258bdef8911e4c82a92c4bd901d0d5e4"; 
const AI_API_URL = "https://api.deepseek.com/v1/chat/completions"; // Example endpoint

app.use(cors());
app.use(express.json());

app.post("/chat", async (req, res) => {
  const userMessage = req.body.message;

  try {
    const response = await axios.post(AI_API_URL, {
      model: "gpt-3.5-turbo",
      messages: [{ role: "user", content: userMessage }],
      temperature: 0.7
    }, {
      headers: {
        "Authorization": `Bearer ${AI_API_KEY}`,
        "Content-Type": "application/json"
      }
    });

    const aiReply = response.data.choices[0].message.content;
    res.json({ reply: aiReply });

  } catch (err) {
    console.error("API Error:", err.response?.data || err.message);
    res.status(500).json({ 
      reply: "Sorry, I couldn't process your request. Please try again."
    });
  }
});

app.listen(PORT, () => {
  console.log(`Server running on http://localhost:${PORT}`);
});
