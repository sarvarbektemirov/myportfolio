<?php
include_once('db.php');

// AUTO-FIX: Check if reply_to_id column exists
$check_col = $master_link->query("SHOW COLUMNS FROM system_messages LIKE 'reply_to_id'");
if ($check_col && $check_col->num_rows == 0) {
    $master_link->query("ALTER TABLE system_messages ADD COLUMN reply_to_id INT DEFAULT NULL AFTER subject");
}
if (empty($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id']);

// 1. Handle Deleting Message
if (isset($_GET['delete_msg'])) {
    $msg_id = (int)$_GET['delete_msg'];
    // Faqat o'z xabarini o'chirishi mumkin
    $master_link->query("DELETE FROM system_messages WHERE id = $msg_id AND user_id = $uid");
    header("Location: support.php");
    exit;
}

// 2. Handle Sending / Editing Message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message'])) {
    $message = trim($_POST['message']);
    $edit_id = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;
    $reply_to_id = isset($_POST['reply_to_id']) && $_POST['reply_to_id'] > 0 ? (int)$_POST['reply_to_id'] : null;

    if ($edit_id > 0) {
        $stmt = $master_link->prepare("UPDATE system_messages SET message = ? WHERE id = ? AND user_id = ? AND sender_role = 0");
        $stmt->bind_param("sii", $message, $edit_id, $uid);
    } else {
        $stmt = $master_link->prepare("INSERT INTO system_messages (user_id, sender_role, message, reply_to_id, subject) VALUES (?, 0, ?, ?, '')");
        $stmt->bind_param("isi", $uid, $message, $reply_to_id);
    }
    
    $stmt->execute();
    header("Location: support.php");
    exit;
}

// Fetch Chat History
$history = $master_link->query("
    SELECT m1.*, 
           (SELECT m2.message FROM system_messages m2 WHERE m2.id = m1.reply_to_id LIMIT 1) as replied_text
    FROM system_messages m1 
    WHERE m1.user_id = $uid 
    ORDER BY m1.created_at ASC
");
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin bilan muloqot</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/extra.css">
    <style>
        .chat-container {
            max-width: 900px;
            margin: 0 auto;
            background: var(--card-bg);
            backdrop-filter: blur(15px);
            border: 1px solid var(--border-color);
            border-radius: 30px;
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            height: 75vh;
            overflow: hidden;
            transition: background 0.3s, border 0.3s;
        }
        .chat-header {
            padding: 20px 25px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 15px;
            background: rgba(var(--text-primary), 0.02);
        }
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 30px;
            background: var(--bg-color);
            opacity: 0.98;
            display: flex;
            flex-direction: column;
            gap: 15px;
            background-image: url('https://www.transparenttextures.com/patterns/cubes.png');
        }
        .message-bubble {
            max-width: 75%;
            padding: 12px 20px;
            border-radius: 20px;
            font-size: 0.95rem;
            line-height: 1.5;
            position: relative;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: 0.2s;
        }
        .msg-user {
            align-self: flex-end;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            border-bottom-right-radius: 4px;
        }
        .msg-admin {
            align-self: flex-start;
            background: var(--card-bg);
            color: var(--text-primary);
            border-bottom-left-radius: 4px;
            border: 1px solid var(--border-color);
            backdrop-filter: blur(5px);
        }
        .msg-time {
            font-size: 0.7rem;
            margin-top: 5px;
            opacity: 0.6;
            display: block;
            text-align: right;
        }

        /* Reply Quote */
        .replied-quote {
            background: rgba(var(--accent-primary-rgb), 0.05); border-left: 3px solid #3b82f6;
            padding: 8px 12px; margin-bottom: 8px; border-radius: 8px; font-size: 0.8rem; 
            color: var(--text-secondary); font-style: italic; display: block;
        }
        .msg-user .replied-quote { 
            background: rgba(255,255,255,0.15); 
            border-left-color: rgba(255,255,255,0.5);
            color: rgba(255,255,255,0.95);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { 
            background: rgba(59, 130, 246, 0.3); 
            border-radius: 10px; 
        }
        ::-webkit-scrollbar-thumb:hover { 
            background: rgba(59, 130, 246, 0.5); 
        }

        .chat-container {
            scrollbar-width: thin;
            scrollbar-color: rgba(59, 130, 246, 0.3) transparent;
        }

        /* Hover Actions */
        .bubble-actions {
            position: absolute;
            top: -12px;
            right: 8px;
            display: none;
            gap: 6px;
            z-index: 10;
        }
        .msg-admin .bubble-actions { right: 8px; }
        .message-bubble:hover .bubble-actions { display: flex; }
        
        .btn-msg-edit, .btn-msg-delete, .btn-msg-reply {
            width: 32px; height: 32px; border-radius: 50%; border: 1px solid var(--border-color);
            background: var(--card-bg); color: var(--text-primary); display: flex; align-items: center;
            justify-content: center; font-size: 0.8rem; box-shadow: var(--shadow);
            transition: 0.3s;
        }
        .btn-msg-edit:hover, .btn-msg-reply:hover { background: #3b82f6; color: white; transform: translateY(-2px); }
        .btn-msg-delete:hover { background: #ef4444; color: white; transform: translateY(-2px); }

        .chat-input-area {
            padding: 25px 35px;
            border-top: 1px solid var(--border-color);
            position: relative;
            background: var(--card-bg);
        }
        .edit-indicator, .reply-indicator { 
            background: #3b82f6; color: white;
            padding: 6px 20px; border-radius: 12px 12px 0 0; position: absolute; top: -33px; left: 30px;
            font-size: 0.8rem; font-weight: 600; display: flex; align-items: center; gap: 12px;
            box-shadow: 0 -4px 15px rgba(59, 130, 246, 0.3);
        }
        .reply-indicator { background: var(--text-secondary); left: 160px; }

        .btn-cancel-edit { background: none; border: none; color: white; cursor: pointer; padding: 0; opacity: 0.8; }
        .btn-cancel-edit:hover { opacity: 1; }

        .chat-form { display: flex; gap: 15px; align-items: center; }
        .chat-input {
            flex: 1; background: var(--input-bg); border: 1px solid var(--border-color); 
            border-radius: 25px; padding: 12px 25px; outline: none; color: var(--text-primary); transition: 0.3s;
        }
        .chat-input::placeholder { color: var(--text-muted); opacity: 0.6; }
        .chat-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
        .btn-send {
            width: 50px; height: 50px; background: #3b82f6; color: white;
            border: none; border-radius: 50%; display: flex; align-items: center;
            justify-content: center; font-size: 1.2rem; transition: 0.3s;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
        }
        .btn-send:hover { transform: scale(1.05); box-shadow: 0 6px 20px rgba(59, 130, 246, 0.5); }
        
        .chat-header h6 { color: var(--text-primary); }
        .chat-header .text-success { color: #10b981 !important; }
    </style>
</head>
<body class="bg-light">
    <?php include_once("menu.php"); ?>

    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Super Admin bilan muloqot</h2>
            <p class="text-muted">Savol va takliflaringizni shu yerda qoldiring</p>
        </div>

        <div class="chat-container">
            <div class="chat-header">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; flex-shrink: 0;">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold">Super Admin</h6>
                    <span class="text-success small d-flex align-items-center gap-1">
                        <span style="width: 8px; height: 8px; background: #10b981; border-radius: 50%; display: inline-block;"></span> Online
                    </span>
                </div>
            </div>

            <div class="chat-messages" id="chatMessages">
                <?php if ($history->num_rows > 0): ?>
                    <?php while($msg = $history->fetch_assoc()): ?>
                        <div class="message-bubble <?= $msg['sender_role'] == 1 ? 'msg-admin' : 'msg-user' ?> fade-in" id="msg-<?= $msg['id'] ?>">
                            <div class="bubble-actions">
                                <button class="btn-msg-reply" onclick="replyMessage(<?= $msg['id'] ?>, '<?= addslashes(htmlspecialchars(mb_substr($msg['message'], 0, 50))) ?>...')">
                                    <i class="fa-solid fa-reply"></i>
                                </button>
                                <?php if ($msg['sender_role'] == 0): ?>
                                    <button class="btn-msg-edit" onclick="editMessage(<?= $msg['id'] ?>, '<?= addslashes(htmlspecialchars($msg['message'])) ?>')">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                <?php endif; ?>
                                <a href="?delete_msg=<?= $msg['id'] ?>" class="btn-msg-delete" onclick="return confirm('O\'chirilsinmi?')">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            </div>
                            <?php if (isset($msg['replied_text']) && $msg['replied_text'] !== ''): ?>
                                <div class="replied-quote">
                                    <i class="fa-solid fa-reply me-1 small"></i> <?= htmlspecialchars(mb_substr($msg['replied_text'], 0, 80)) ?>...
                                </div>
                            <?php endif; ?>
                            <div class="bubble-text"><?= nl2br(htmlspecialchars($msg['message'])) ?></div>
                            <span class="msg-time"><?= date('H:i', strtotime($msg['created_at'])) ?></span>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center text-muted my-5">
                        <i class="fas fa-comments fa-3x mb-3 opacity-20"></i>
                        <p>Hozircha xabarlar yo'q. Muloqotni boshlang!</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="chat-input-area">
                <form action="" method="post" class="chat-form" id="chatForm">
                    <input type="hidden" name="edit_id" id="edit_id" value="0">
                    <input type="hidden" name="reply_to_id" id="reply_to_id" value="0">

                    <div id="edit-indicator" class="edit-indicator" style="display: none;">
                        <span><i class="fa-solid fa-pen me-2"></i> Tahrirlash:</span>
                        <button type="button" onclick="cancelEdit()" class="btn-cancel-edit"><i class="fa-solid fa-xmark"></i></button>
                    </div>

                    <div id="reply-indicator" class="reply-indicator" style="display: none;">
                        <span id="reply-text-preview"><i class="fa-solid fa-reply me-2"></i> Javob: ...</span>
                        <button type="button" onclick="cancelReply()" class="btn-cancel-edit"><i class="fa-solid fa-xmark"></i></button>
                    </div>

                    <input type="text" name="message" id="messageInput" class="chat-input" placeholder="Xabar yozing..." autocomplete="off" required>
                    <button type="submit" class="btn-send" id="submitBtn">
                        <i class="fas fa-arrow-up"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const chatMessages = document.getElementById('chatMessages');
        if (chatMessages) chatMessages.scrollTop = chatMessages.scrollHeight;

        function editMessage(id, text) {
            cancelReply();
            document.getElementById('edit_id').value = id;
            document.getElementById('messageInput').value = text;
            document.getElementById('edit-indicator').style.display = 'flex';
            document.getElementById('submitBtn').innerHTML = '<i class="fa-solid fa-check"></i>';
            document.getElementById('messageInput').focus();
        }

        function cancelEdit() {
            document.getElementById('edit_id').value = '0';
            document.getElementById('messageInput').value = '';
            document.getElementById('edit-indicator').style.display = 'none';
            document.getElementById('submitBtn').innerHTML = '<i class="fas fa-arrow-up"></i>';
        }

        function replyMessage(id, text) {
            cancelEdit();
            document.getElementById('reply_to_id').value = id;
            document.getElementById('reply-text-preview').innerHTML = '<i class="fa-solid fa-reply me-2"></i> Javob: ' + text;
            document.getElementById('reply-indicator').style.display = 'flex';
            document.getElementById('messageInput').focus();
        }

        function cancelReply() {
            document.getElementById('reply_to_id').value = '0';
            document.getElementById('reply-indicator').style.display = 'none';
        }
    </script>
</body>
</html>
