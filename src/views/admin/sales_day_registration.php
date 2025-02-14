<?php
require_once('../models/db_connect.php');

// 営業日データを取得
$stmt = $pdo->prepare("SELECT date FROM sales_days");
$stmt->execute();
$sales_days = $stmt->fetchAll(PDO::FETCH_COLUMN);

// 営業日を配列で返す
$sales_days_array = array_map(function($date) {
    return (new DateTime($date, new DateTimeZone('Asia/Tokyo')))->format('Y-m-d'); // Y-m-d形式に変換（タイムゾーンを指定）
}, $sales_days);

// 現在の日付をPHPで取得（タイムゾーンを指定）
$current_date = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
$today = $current_date->format('Y-m-d');
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>架空水族館｜管理Web｜営業日登録</title>
    <link rel="stylesheet" href="../../styles/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .grayed-out {
            background-color: #e0e0e0;
            color: #a0a0a0;
            pointer-events: none;
        }
        .highlighted {
            background-color: #8BC34A; /* 緑色 */
        }
        .selected {
            background-color: yellow; /* 黄色 */
        }
    </style>
</head>
<body>
    <h1>営業日登録</h1>
    <div id="calendar"></div>
    <div id="settings">
        <h3>選択した日付: <span id="selected-date"></span></h3>
        <select id="working-hours">
            <option value="">営業時間区分を選択</option>
            <?php
            require_once('../models/db_connect.php');
            $stmt = $pdo->query("SELECT id, name, start_time, end_time FROM working_hours");
            while ($row = $stmt->fetch()) {
                echo "<option value='{$row['id']}'>{$row['name']} ({$row['start_time']} - {$row['end_time']})</option>";
            }
            ?>
        </select>
        <button id="register">登録</button>
    </div>
    <script>
        $(document).ready(function () {
            // 現在日付をタイムゾーンAsia/Tokyoで設定
            const today = new Date("<?php echo $today; ?>T00:00:00");
            let displayedYear = today.getFullYear();
            let displayedMonth = today.getMonth(); // 0-based index
            const maxDate = new Date(displayedYear, displayedMonth + 3, 0); // 3か月後の月末

            // 営業日データ（PHPから埋め込んだデータ）
            const salesDays = <?php echo json_encode($sales_days_array); ?>;

            let selectedDate = ""; // 選択された日付を管理

            function renderCalendar(year, month) {
                const firstDay = new Date(year, month, 1).getDay();
                const daysInMonth = new Date(year, month + 1, 0).getDate();
                const calendar = $("#calendar");
                calendar.empty();

                let html = `<div>
                                <button id="prev-month" ${year === today.getFullYear() && month <= today.getMonth() ? "disabled" : ""}>前月</button>
                                <span>${year}年${month + 1}月</span>
                                <button id="next-month" ${year === maxDate.getFullYear() && month >= maxDate.getMonth() ? "disabled" : ""}>次月</button>
                            </div>`;
                html += "<table><tr>";
                const days = ["日", "月", "火", "水", "木", "金", "土"];
                for (const day of days) {
                    html += `<th>${day}</th>`;
                }
                html += "</tr><tr>";

                for (let i = 0; i < firstDay; i++) {
                    html += "<td></td>";
                }

                for (let day = 1; day <= daysInMonth; day++) {
                    const date = new Date(year, month, day);
                    const isPast = date < today;
                    const isOutOfRange = date > maxDate;
                    const grayedOut = isPast || isOutOfRange ? "grayed-out" : "";
                    const formattedDate = date.toISOString().split('T')[0]; // YYYY-MM-DD形式

                    // 営業日があれば緑色のハイライト
                    const isSalesDay = salesDays.includes(formattedDate) ? "highlighted" : "";

                    // 選択された日付は黄色のハイライト
                    const isSelected = selectedDate === formattedDate ? "selected" : "";

                    html += `<td class="${grayedOut} ${isSalesDay} ${isSelected}" data-date="${formattedDate}">${day}</td>`;
                    if ((firstDay + day) % 7 === 0) {
                        html += "</tr><tr>";
                    }
                }
                html += "</tr></table>";
                calendar.html(html);
            }

            renderCalendar(displayedYear, displayedMonth);

            $(document).on("click", "#prev-month", function () {
                displayedMonth -= 1;
                if (displayedMonth < 0) {
                    displayedMonth = 11;
                    displayedYear -= 1;
                }
                renderCalendar(displayedYear, displayedMonth);
            });

            $(document).on("click", "#next-month", function () {
                displayedMonth += 1;
                if (displayedMonth > 11) {
                    displayedMonth = 0;
                    displayedYear += 1;
                }
                renderCalendar(displayedYear, displayedMonth);
            });

            $(document).on("click", "td:not(.grayed-out)", function () {
                $("td").removeClass("selected"); // 既存の選択状態をリセット
                $(this).addClass("selected"); // クリックしたセルを選択状態に

                selectedDate = $(this).data("date"); // 選択された日付を保存
                $("#selected-date").text(new Date(selectedDate).toLocaleDateString("ja-JP")); // 表示を更新
            });

            $(document).on("click", "#register", function () {
                if (!selectedDate) {
                    alert("日付を選択してください。");
                    return;
                }

                const workingHourId = $("#working-hours").val(); // 選択された営業時間区分を取得

                if (!workingHourId) {
                    alert("営業時間区分を選択してください。");
                    return;
                }

                $.ajax({
                    url: "../../src/controllers/process_sales_day_registration.php", // 登録処理用PHPのパス
                    type: "POST",
                    data: {
                        date: selectedDate,
                        working_hour_id: workingHourId
                    },
                    dataType: "json",
                    success: function (response) {
                        if (response.status === "success") {
                            alert(response.message);

                            // 登録した営業日をカレンダーにハイライト
                            const registeredDate = new Date(response.date);
                            const formattedDate = registeredDate.toISOString().split('T')[0]; // YYYY-MM-DD形式
                            salesDays.push(formattedDate);  // 営業日を追加
                            renderCalendar(displayedYear, displayedMonth); // カレンダーを再描画
                        } else {
                            alert("エラー: " + response.message);
                        }
                    },
                    error: function () {
                        alert("登録に失敗しました。サーバーとの通信に問題があります。");
                    }
                });
            });
        });
    </script>
</body>
</html>
