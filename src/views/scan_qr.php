<body>
    <div id="wrapper">
        <video id="video" autoplay muted playsinline></video>
        <canvas id="camera-canvas"></canvas>
        <canvas id="rect-canvas"></canvas>
        <span id="qr-msg"></span>
    </div>
    <button id="resume-btn" class="btn-gra" disabled>スキャンを再開</button>

    <script src="assets/js/jsQR.js"></script>
    <script>
        // Webカメラの起動
        const video = document.getElementById('video');
        let contentWidth;
        let contentHeight;
        let scanning = true; // スキャン状態を管理するフラグ
        let requestId; // requestAnimationFrameのIDを保持
        const resumeBtn = document.getElementById('resume-btn'); // ボタン要素の取得

        const media = navigator.mediaDevices.getUserMedia({
                audio: false,
                video: {
                    width: 640,
                    height: 480
                }
            })
            .then((stream) => {
                video.srcObject = stream;
                video.onloadeddata = () => {
                    video.play();
                    contentWidth = video.clientWidth;
                    contentHeight = video.clientHeight;
                    canvasUpdate();
                    checkImage();
                }
            }).catch((e) => {
                console.log(e);
            });

        // カメラ映像のキャンバス表示
        const cvs = document.getElementById('camera-canvas');
        const ctx = cvs.getContext('2d');
        const canvasUpdate = () => {
            cvs.width = contentWidth;
            cvs.height = contentHeight;
            ctx.drawImage(video, 0, 0, contentWidth, contentHeight);
            if (scanning) {
                requestId = requestAnimationFrame(canvasUpdate); // 次のフレームを要求
            }
        }

        // QRコードの検出
        const rectCvs = document.getElementById('rect-canvas');
        const rectCtx = rectCvs.getContext('2d');
        // QRコードの検出
        const checkImage = () => {
            // スキャンが停止されていたら、処理を終了
            if (!scanning) return;

            // imageDataを作る
            const imageData = ctx.getImageData(0, 0, contentWidth, contentHeight);
            // jsQRに渡す
            const code = jsQR(imageData.data, contentWidth, contentHeight);

            // 検出結果に合わせて処理を実施
            if (code) {
                console.log("QRcodeが見つかりました", code);
                drawRect(code.location);
                document.getElementById('qr-msg').textContent = `QRコード：${code.data}`;

                // サーバーへ認証リクエスト
                if (!scanning) return; // もしスキャンが停止していれば処理を中断

                scanning = false; // 一度QRコードが検出されたらスキャンを停止

                fetch('/?process=verify_qr', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            qr_data: code.data
                        })
                    })
                    .then(response => response.text()) // JSONではなくテキストとして返ってきたものを取得
                    .then(result => {
                        console.log('Server response:', result); // レスポンス内容を確認
                        try {
                            const jsonResult = JSON.parse(result); // 手動でJSON解析
                            console.log(jsonResult.success);
                            if (jsonResult.success) {
                                document.getElementById('qr-msg').textContent = `認証成功: ${jsonResult.message}`;
                            } else {
                                document.getElementById('qr-msg').textContent = `認証失敗: ${jsonResult.message}`;
                            }
                            stopScanning();
                        } catch (error) {
                            console.error('Error parsing JSON:', error);
                            document.getElementById('qr-msg').textContent = 'サーバーエラーが発生しました';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        document.getElementById('qr-msg').textContent = 'リクエスト送信中にエラーが発生しました';
                    });
            } else {
                console.log("QRcodeが見つかりません…", code);
                rectCtx.clearRect(0, 0, contentWidth, contentHeight);
                document.getElementById('qr-msg').textContent = `QRコード: 見つかりません`;
            }
            // スキャンが停止されていなければ、再度チェック
            if (scanning) {
                setTimeout(() => {
                    checkImage();
                });
            }
        }

        // 四辺形の描画
        const drawRect = (location) => {
            rectCvs.width = contentWidth;
            rectCvs.height = contentHeight;
            drawLine(location.topLeftCorner, location.topRightCorner);
            drawLine(location.topRightCorner, location.bottomRightCorner);
            drawLine(location.bottomRightCorner, location.bottomLeftCorner);
            drawLine(location.bottomLeftCorner, location.topLeftCorner);
        }

        // 線の描画
        const drawLine = (begin, end) => {
            rectCtx.lineWidth = 4;
            rectCtx.strokeStyle = "#F00";
            rectCtx.beginPath();
            rectCtx.moveTo(begin.x, begin.y);
            rectCtx.lineTo(end.x, end.y);
            rectCtx.stroke();
        }

        // スキャンを停止する関数
        const stopScanning = () => {
            // スキャン停止
            cancelAnimationFrame(requestId);
            resumeBtn.disabled = false; // ボタンを有効化
        }

        // ボタンがクリックされた場合、スキャンを再開
        resumeBtn.addEventListener('click', () => {
            // ボタンを無効化（グレーアウト）
            resumeBtn.disabled = true;

            // スキャンを再開
            scanning = true;
            canvasUpdate();
            checkImage();
        });
    </script>
</body>
