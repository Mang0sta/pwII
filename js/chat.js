// mensaje envio
const textarea = document.querySelector('.chatbox-message-input');
const chatboxForm = document.querySelector('.chatbox-message-form');

if (textarea) {
    textarea.addEventListener('input', function () {
        let line = textarea.value.split('\n').length;

        if (textarea.rows < 6 || line < 6) {
            textarea.rows = line;
        }

        if (textarea.rows > 1) {
            chatboxForm.style.alignItems = 'flex-end';
        } else {
            chatboxForm.style.alignItems = 'center';
        }
    });
}

// abrir chatbox
const chatboxToggle = document.querySelector('.chatbox-toggle');
const chatboxMessage = document.querySelector('.chatbox-message-wrapper');

if (chatboxToggle) {
    chatboxToggle.addEventListener('click', function () {
        chatboxMessage.classList.toggle('show');
    });
}

// dropdown
const dropdownToggle = document.querySelector('.chatbox-message-dropdown-toggle');
const dropdownMenu = document.querySelector('.chatbox-message-dropdown-menu');

if (dropdownToggle) {
    dropdownToggle.addEventListener('click', function () {
        dropdownMenu.classList.toggle('show');
    });
}

document.addEventListener('click', function (e) {
    if (dropdownMenu && !e.target.matches('.chatbox-message-dropdown, .chatbox-message-dropdown *')) {
        dropdownMenu.classList.remove('show');
    }
});

// chatbox mensaje (adaptado para el nuevo formulario de chat.php)
const chatboxMessageWrapper = document.querySelector('.message-area'); // 
const chatboxNoMessage = document.querySelector('.message-area p'); // 
const sendMessageForm = document.getElementById('send-message-form');
const messageInput = document.getElementById('message-input');
const imageInput = document.getElementById('image-input'); // 

function addZero(num) {
    return num < 10 ? '0' + num : num;
}

function isValid(value) {
    let text = value.replace(/\n/g, '');
    text = text.replace(/\s/g, '');
    return text.length > 0;
}

if (sendMessageForm) {
    sendMessageForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const currentReceiverId = document.getElementById('receiver-id').value;
        const messageText = messageInput.value.trim();
        const imageFile = imageInput ? imageInput.files[0] : null; // 

        if (!currentReceiverId) {
            alert('Por favor, selecciona un usuario para enviar el mensaje.');
            return;
        }

        if (messageText || imageFile) {
            const formData = new FormData();
            formData.append('receiver_id', currentReceiverId);
            if (messageText) {
                formData.append('message', messageText);
            }
            if (imageFile) {
                formData.append('image', imageFile);
            }

            fetch('send_message.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data === 'success') {
                    loadMessages(currentReceiverId);
                    if (messageInput) messageInput.value = '';
                    if (imageInput) imageInput.value = ''; // Limpiar el input de archivo
                } else {
                    alert('Error al enviar el mensaje o la imagen: ' + data);
                }
            })
            .catch(error => console.error('Error al enviar el mensaje/imagen:', error));
        } else {
            alert('Por favor, escribe un mensaje o selecciona una imagen.');
        }
    });
}


function scrollBottom() {
    if (chatboxMessageWrapper) {
        chatboxMessageWrapper.scrollTo(0, chatboxMessageWrapper.scrollHeight);
    }
}

// llamar a scrollBottom 
if (chatboxMessageWrapper) {
    scrollBottom();
}