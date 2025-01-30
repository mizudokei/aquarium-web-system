<?php
// db_connect.php でデータベース接続
include('../../config/db_connect.php');
session_start();

// 営業日データを取得
$stmt = $pdo->prepare("SELECT date, working_hour_id FROM sales_days WHERE is_operational = 1");
$stmt->execute();
$sales_days = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 営業日を配列で返す（フロントエンド用）
$sales_days_array = array_map(function($day) {
    $datetime = new DateTime($day['date'], new DateTimeZone('UTC'));
    return [
        'date' => $datetime->format('Y-m-d'),
        'working_hour_id' => $day['working_hour_id']
    ];
}, $sales_days);

// 営業時間データを取得
$working_hours_stmt = $pdo->prepare("SELECT * FROM working_hours");
$working_hours_stmt->execute();
$working_hours = $working_hours_stmt->fetchAll(PDO::FETCH_ASSOC);

// チケット種類データを取得
$admission_fee_types_stmt = $pdo->prepare("SELECT * FROM admission_fee_types");
$admission_fee_types_stmt->execute();
$admission_fee_types = $admission_fee_types_stmt->fetchAll(PDO::FETCH_ASSOC);

// 現在の日付をPHPで取得
$current_date = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
$today = $current_date->format('Y-m-d');
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>架空水族館｜入場eチケット予約</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="../../public/assets/css/admission_ticket_reservation.css">
</head>
<body>

    <header>
        <?php
            require_once '../../public/header.php'; // 共通ヘッダーを読み込む
        ?>
    </header>

    <main>
        <div id="calendar"></div>
        <div id="selected-date-info">
            <select id="time-slot" disabled></select> <!-- 初期状態では無効 -->
        </div>

        <div id="ticket-info" style="display:none;">
            <h3>券種・チケット枚数の入力</h3>
            <table class="ticket-table">
                <thead>
                    <tr>
                        <th>券種</th>
                        <th>価格</th>
                        <th>枚数</th>
                    </tr>
                </thead>
                <tbody id="ticket-types-list">
                    <!-- チケットの種類テーブルがここに動的に挿入される -->
                </tbody>
            </table>
        </div>

        <div class="summary_card">

            <!-- 選択された日 -->
            <span id="selected-date"></span>

            <!-- 選択された時間帯 -->
            <span id="selected-time"></span>

            <p>合計枚数: <span id="total-quantity">0</span></p>
            <p>合計金額: ¥<span id="total-price">0</span></p>
        </div>
    </main>

    <script>
        const days = ["日", "月", "火", "水", "木", "金", "土"];

        $(document).ready(function () {
            const today = new Date("<?php echo date('Y-m-d H:i:s'); ?>");            
            const salesDays = <?php echo json_encode($sales_days_array); ?>;
            const workingHours = <?php echo json_encode($working_hours); ?>;
            const ticketTypes = <?php echo json_encode($admission_fee_types); ?>;
            let selectedDate = ""; // 選択された日付を管理

            function renderCalendar(year, month) {
                const firstDay = new Date(year, month, 1).getDay();                
                const daysInMonth = new Date(year, month + 1, 0).getDate();                
                const calendar = $("#calendar");
                calendar.empty();

                let html = `<div>
                                <button id="prev-month">前月</button>
                                <span>${year}年${month + 1}月</span>
                                <button id="next-month">次月</button>
                            </div>`;
                html += "<table><tr>";
                for (const day of days) {
                    html += `<th>${day}</th>`;
                }
                html += "</tr><tr>";

                for (let i = 0; i < firstDay; i++) {
                    html += "<td></td>";
                }

                for (let day = 1; day <= daysInMonth; day++) {
                    const date = new Date(year, month, day);
                    const formattedDate = date.toISOString().split('T')[0];
                    const isSalesDay = salesDays.some(day => day.date === formattedDate);
                    console.log(isSalesDay);
                    

                    // 営業日だけを選択可能にする
                    html += `<td class="${isSalesDay ? 'highlighted' : 'grayed-out'}" data-date="${formattedDate}" ${isSalesDay ? '' : 'style="pointer-events: none;"'}>${day}</td>`;

                    if ((firstDay + day) % 7 === 0) {
                        html += "</tr><tr>";
                    }
                }
                html += "</tr></table>";
                calendar.html(html);
            }

            function renderTimeSlotOptions(selectedDate) {                
                const timeSlotSelect = $("#time-slot");
                timeSlotSelect.empty();
                timeSlotSelect.prop('disabled', false);

                const salesDay = salesDays.find(day => day.date === selectedDate);
                if (!salesDay) return;

                const workingHour = workingHours.find(hour => hour.id == salesDay.working_hour_id);
                if (!workingHour) return;

                const startTime = workingHour.start_time.split(":");
                const endTime = workingHour.end_time.split(":");
                const startHour = parseInt(startTime[0]);
                const startMinute = parseInt(startTime[1]);
                const endHour = parseInt(endTime[0]);
                const endMinute = parseInt(endTime[1]);

                let currentTime = startHour * 60 + startMinute;
                const endTimeInMinutes = endHour * 60 + endMinute;

                let timeSlots = [];
                while (currentTime < endTimeInMinutes) {
                    const hour = String(Math.floor(currentTime / 60)).padStart(2, "0");
                    const minute = String(currentTime % 60).padStart(2, "0");
                    const timeLabel = `${hour}:${minute}～${String(Math.floor((currentTime + 30) / 60)).padStart(2, "0")}:${String((currentTime + 30) % 60).padStart(2, "0")}`;
                    timeSlots.push(timeLabel);
                    currentTime += 30;
                }

                timeSlots.forEach(timeSlot => {
                    timeSlotSelect.append(new Option(timeSlot, timeSlot));
                });
            }

            // チケットの枚数入力UIを生成
            function renderTicketTypes() {
                const ticketTypesList = $("#ticket-types-list");
                ticketTypesList.empty();
                ticketTypes.forEach(ticket => {
                    const row = `<tr>
                                    <td>${ticket.type}</td>
                                    <td>¥${ticket.price}</td>
                                    <td>
                                        <div class="ticket-counter">
                                            <button class="decrease" data-id="${ticket.id}">-</button>
                                            <input type="text" value="0" id="ticket-quantity-${ticket.id}" readonly>
                                            <button class="increase" data-id="${ticket.id}">+</button>
                                        </div>
                                    </td>
                                </tr>`;
                    ticketTypesList.append(row);
                });
            }

            // チケット枚数の増減
            $(document).on("click", ".increase, .decrease", function () {
                const admissionFeeTypeId = $(this).data("id");
                const inputField = $(`#ticket-quantity-${admissionFeeTypeId}`);
                let quantity = parseInt(inputField.val());

                if ($(this).hasClass("increase")) {
                    quantity++;
                } else if ($(this).hasClass("decrease") && quantity > 0) {
                    quantity--;
                }

                inputField.val(quantity);
                updateTotal();
            });

            // 合計枚数と金額の更新
            function updateTotal() {
                let totalQuantity = 0;
                let totalPrice = 0;

                ticketTypes.forEach(ticket => {
                    const quantity = parseInt($(`#ticket-quantity-${ticket.id}`).val());
                    totalQuantity += quantity;
                    totalPrice += ticket.price * quantity;
                });

                $("#total-quantity").text(totalQuantity);
                $("#total-price").text(totalPrice);
            }

            // セレクトボックス変更時
			$("#time-slot").on("change", function () {
                // 選択された値の取得
				selectedTimeSlot = $(this).val();

				if (selectedTimeSlot) {
                    $("#selected-time").text(selectedTimeSlot);
                    renderTicketTypes();  // チケット種類の表示
                    $("#ticket-info").show();  // チケット情報を表示                   
				} else {
					$("#ticket-info").hide();
				}
			});

            // 日付が選択された時
            $(document).on("click", "#calendar td.highlighted", function () {
                $("td").removeClass("selected");
                $(this).addClass("selected");
                selectedDate = $(this).data("date");
                
                const dateObj = new Date(selectedDate);
                console.log(dateObj.toLocaleString("ja-JP", {timeZone: "Asia/Tokyo"}));
                
                const dayOfWeek = days[dateObj.getDay()];
                const formattedDate = `${dateObj.getFullYear()}年${String(dateObj.getMonth() + 1).padStart(2, '0')}月${String(dateObj.getDate()).padStart(2, '0')}日（${dayOfWeek}）`;
                $("#selected-date").text(formattedDate);
                renderTimeSlotOptions(selectedDate);
            });

            // 月の切り替え
            $(document).on("click", "#prev-month", function () {
                const currentMonth = new Date(today);
                currentMonth.setMonth(currentMonth.getMonth() - 1);
                renderCalendar(currentMonth.getFullYear(), currentMonth.getMonth());
            });

            $(document).on("click", "#next-month", function () {
                const currentMonth = new Date(today);
                currentMonth.setMonth(currentMonth.getMonth() + 1);
                renderCalendar(currentMonth.getFullYear(), currentMonth.getMonth());
            });

            // 初期カレンダーの表示
            renderCalendar(today.getFullYear(), today.getMonth());
        });
    </script>
</body>
</html>
