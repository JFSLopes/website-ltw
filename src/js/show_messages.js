document.addEventListener("DOMContentLoaded", function() {
    let userInfos = document.querySelectorAll(".user-info");
    const directMessagesSection = document.getElementById('direct-messages');
    const usersMessagesSection = document.getElementById('users-message');
    userInfos.forEach(function(userInfo) {
        userInfo.addEventListener("click", function() {
            let other_user_id = userInfo.getAttribute("data-user-id");
            if (window.innerWidth <= 576){
                usersMessagesSection.style.display = 'none';
                directMessagesSection.style.display = 'grid';
            }
            loadMessages(other_user_id);
            updateSections(false); 
        });
    });
    let change_user = document.getElementById('change-user');
    if(change_user){
        change_user.addEventListener("click", function() {
            updateSections(true); 
        });
    }

    function updateSections(displayUsersMessages) {
        if (window.innerWidth <= 576) {
            usersMessagesSection.style.display = displayUsersMessages ? 'block' : 'none';
            directMessagesSection.style.display = displayUsersMessages ? 'none' : 'grid';
        }
    }
    function loadMessages(other_user_id) {
        let xhr = new XMLHttpRequest();
        xhr.open("GET", "action_fetch_messages.php?other_user_id=" + other_user_id, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var messagesList = document.querySelector(".messages-list");
                messagesList.innerHTML = xhr.responseText;
                
                /// Scroll to the bottom
                let directMessagesContainer = document.getElementById("direct-messages");
                directMessagesContainer.scrollTop = directMessagesContainer.scrollHeight;
            }
        };
        xhr.send();
    }
});
