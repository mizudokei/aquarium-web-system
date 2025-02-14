function openModal(ticketId, date, timeslot, type, qrCode, recipientLastname, recipientFirstname) {
    document.getElementById('modal-type').textContent = type;
    document.getElementById('modal-date').textContent = date;
    document.getElementById('modal-timeslot').textContent = timeslot;
    document.getElementById('modal-ticket-id').textContent = ticketId;
    document.getElementById('modal-qr').src = '/qr_load.php?file=' + encodeURIComponent(qrCode);
    document.getElementById('ticketModal').style.display = 'flex';

    let recipientElement = document.getElementById('modal-recipient');
    if (recipientLastname && recipientFirstname) {
        recipientElement.textContent = `${recipientLastname} ${recipientFirstname}`;
        recipientElement.style.color = "black"; // デフォルトの色
    } else {
        recipientElement.textContent = "※ このチケットは利用者未登録です";
        recipientElement.style.color = "red"; // 未登録の場合は赤文字
    }
}

function closeModal() {
    document.getElementById('ticketModal').style.display = 'none';
}

function saveModalAsImage() {
    html2canvas(document.getElementById('ticketModal'), {
        ignoreElements: function(element) {
            return element.classList.contains('close-button') || element.classList.contains('save-button');
        }
    }).then(function(canvas) {
        var link = document.createElement('a');
        link.href = canvas.toDataURL('image/png');
        link.download = 'ticket_modal.png';  // 画像として保存されるファイル名
        link.click();
    });
}

function openBindUsersModal() {
    document.getElementById('bindUsersModal').style.display = 'flex';
}

function closeBindUsersModal() {
    document.getElementById('bindUsersModal').style.display = 'none';
}

function openShareModal() {
    document.getElementById('shareModal').style.display = 'flex';
}

function closeShareModal() {
    document.getElementById('shareModal').style.display = 'none';
}

let selectedTicketId = null;


function selectTicketUserBind(ticketId, element) {
    document.querySelectorAll('.ticket-card').forEach(card => card.classList.remove('selected'));
    element.classList.add('selected');
    selectedTicketId = ticketId;
    document.getElementById('bindUsers').disabled = false;
    document.getElementById('bindUsers').classList.remove('disabled');
}

function bindUserToTicket() {
    if (!selectedTicketId) return;

    $.post("/?process=bind_users", { ticket_id: selectedTicketId }, function(response) {
        console.log(response);
        if (response === "success") {
            alert("チケットが自分の情報と紐づけられました！");
            closeBindUsersModal(); // 紐づけ後、モーダルを閉じる
        } else {
            alert("紐づけに失敗しました。再試行してください。");
        }
    });
}

function selectTicket(ticketId, element) {
    document.querySelectorAll('.ticket-card').forEach(card => card.classList.remove('selected'));
    element.classList.add('selected');
    selectedTicketId = ticketId;
    document.getElementById('generateShareURL').disabled = false;
    document.getElementById('generateShareURL').classList.remove('disabled');
}

function generateShareURL() {
    if (!selectedTicketId) return;
    $.post("/?process=generete_share_url", { ticket_id: selectedTicketId }, function(response) {
        document.getElementById('shareURL').value = response;
        document.getElementById('shareURL').disabled = false;
        document.getElementById('shareURL').classList.remove('disabled');
        document.getElementById('copy-icon').classList.remove('disabled');
    });
}

function copyToClipboard() {
    let input = document.getElementById("shareURL");
    if (input.value) {
        navigator.clipboard.writeText(input.value).then(() => {
        }).catch(err => {
            console.error("コピーに失敗しました: ", err);
        });
    }
}