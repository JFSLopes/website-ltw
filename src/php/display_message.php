<?php
function popup($message){ ?>
    <div class="popup">
        <div class="popup-content">
            <p><?= $message ?></p>
        </div>
        <button class="close-btn" onclick="hidePopup()">Close</button>
    </div>
    <script>
        function hidePopup() {
            document.querySelector('.popup').classList.add('hidden');
        }
    </script>
<?php }
?>
