const chatButton = document.getElementById("chat-button");
const chatContainer = document.getElementById("chat-container");
const closeChat = document.getElementById("close-chat");
const userInput = document.getElementById("user-input");
const sendButton = document.getElementById("send-button");
const chatMessages = document.getElementById("chat-messages");
const chatStatus = document.getElementById("chat-status");

let isBotTyping = false;

chatButton.addEventListener("click", () => {
    chatContainer.style.display = "flex";
    
    // Se for a primeira vez que o chat é aberto, iniciar a conversa
    if (chatMessages.children.length === 0) {
        showTypingIndicator();
        
        setTimeout(() => {
            fetch("chatbot.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" }
            })
            .then(response => response.text())
            .then(data => {
                hideTypingIndicator();
                displayBotMessageWithTypingEffect(data);
            });
        }, 1000);
    }
});

closeChat.addEventListener("click", () => {
    chatContainer.style.display = "none";
});

sendButton.addEventListener("click", sendMessage);
userInput.addEventListener("keypress", (e) => {
    if (e.key === "Enter") sendMessage();
});

function sendMessage() {
    let message = userInput.value.trim();
    if (message === "" || isBotTyping) return;
    
    addMessage(message, "user");
    userInput.value = "";
    
    // Mostrar indicador de digitação com animação
    showTypingIndicator();
    
    // Enviar mensagem para o backend
    fetch("chatbot.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "message=" + encodeURIComponent(message)
    })
    .then(response => response.text())
    .then(data => {
        // Mantemos o indicador de "digitando" por pelo menos 2 segundos
        // para dar a impressão de que alguém está realmente digitando
        setTimeout(() => {
            hideTypingIndicator();
            displayBotMessageWithTypingEffect(data);
        }, 2000);
    });
}

function addMessage(text, type) {
    let msg = document.createElement("div");
    msg.classList.add("message", type);

    if (type === "bot") {
        let img = document.createElement("img");
        img.src = "https://i.imgur.com/6RK7NQp.png";
        msg.appendChild(img);
    }

    let span = document.createElement("span");
    span.textContent = text;
    msg.appendChild(span);
    chatMessages.appendChild(msg);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Mostrar indicador de digitação com animação de pontos
function showTypingIndicator() {
    isBotTyping = true;
    chatStatus.textContent = "Digitando";
    
    // Criar animação dos pontos (...) no indicador de digitação
    let dots = 0;
    const typingInterval = setInterval(() => {
        if (!isBotTyping) {
            clearInterval(typingInterval);
            return;
        }
        
        dots = (dots + 1) % 4;
        let dotsText = "";
        for (let i = 0; i < dots; i++) {
            dotsText += ".";
        }
        chatStatus.textContent = "Digitando" + dotsText;
    }, 500);
}

function hideTypingIndicator() {
    isBotTyping = false;
    chatStatus.textContent = "Online agora";
}

function displayBotMessageWithTypingEffect(text) {
    let msg = document.createElement("div");
    msg.classList.add("message", "bot");
    
    let img = document.createElement("img");
    img.src = "https://i.imgur.com/6RK7NQp.png";
    msg.appendChild(img);
    
    let span = document.createElement("span");
    span.textContent = "";
    msg.appendChild(span);
    
    chatMessages.appendChild(msg);
    
    // Efeito de digitação caractere por caractere com velocidade variável
    let i = 0;
    
    function typeCharacter() {
        if (i < text.length) {
            span.textContent += text.charAt(i);
            i++;
            chatMessages.scrollTop = chatMessages.scrollHeight;
            
            // Velocidade de digitação variável para parecer mais humano
            // Mais rápido em partes do meio, mais lento no início e fim
            let typingSpeed;
            
            if (i < 5 || i > text.length - 10) {
                // Mais lento no início e fim da mensagem
                typingSpeed = Math.random() * 70 + 50; // 50-120ms
            } else {
                // Mais rápido no meio da mensagem
                typingSpeed = Math.random() * 40 + 30; // 30-70ms
            }
            
            // Pausa mais longa em pontuação
            if (['.', '!', '?', ',', ':'].includes(text.charAt(i - 1))) {
                typingSpeed += 300; // Pausa extra em pontuação
            }
            
            setTimeout(typeCharacter, typingSpeed);
        }
    }
    
    typeCharacter();
}