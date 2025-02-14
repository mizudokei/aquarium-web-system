// PHPで取得したURIをJavaScriptに渡す
const requestUri = "<?php echo $_SERVER['REQUEST_URI']; ?>";
document.addEventListener('DOMContentLoaded', function () {
    const header = document.querySelector('header');
    const videoDiv = document.querySelector('.video-container');
    const body = document.body; // body 要素も取得
    const burger = document.querySelector('.burger');  // ハンバーガーメニューの要素を取得
    const logo = document.getElementById('header-logo');  // ヘッダーロゴの要素を取得

    // home.php以外の場合は背景色を#fdfdfdに設定
    if (!requestUri.includes('/home.php')) {
        if (header) {
            header.style.backgroundColor = '#fdfdfd';  // 背景色を #fdfdfd に設定
            header.style.boxShadow = '0 2px 5px rgba(0, 0, 0, 0.1)';  // ドロップシャドウを再適用
        }
        // ハンバーガーメニューの線の背景色を#2730d9に設定
        if (burger) {
            burger.classList.add('burger-color');  // クラスを追加して背景色を変更
        }
    }

    // home.phpのページでの動作は Intersection Observer を使用
    if (videoDiv && header && body) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // ヘッダーが透明化時のスタイル
                    header.style.backgroundColor = 'transparent';
                    header.style.boxShadow = 'none';
                    body.style.paddingTop = '0px';  // bodyのpadding-topを0に設定

                    // ロゴを透明にして、スペースを保持する
                    if (logo) {
                        logo.style.opacity = '0';  // 透明にする
                        logo.style.pointerEvents = 'none';  // クリックイベントを無効にする
                    }

                    // ハンバーガーメニューの線を透明化
                    if (burger) {
                        burger.classList.remove('burger-color');  // クラスを削除して背景色を透明に戻す
                    }
                } else {
                    // ヘッダーが透明化しない時のスタイル
                    header.style.backgroundColor = '#fdfdfd';
                    header.style.boxShadow = '0 2px 5px rgba(0, 0, 0, 0.1)';

                    // ロゴを表示する
                    if (logo) {
                        logo.style.opacity = '1';  // ロゴを表示
                        logo.style.pointerEvents = 'auto';  // クリックイベントを有効にする
                    }

                    // ハンバーガーメニューの線の背景色を#2730d9に設定
                    if (burger) {
                        burger.classList.add('burger-color');  // クラスを追加して背景色を変更
                    }
                }
            });
        });
        observer.observe(videoDiv);
    }
});

$(function () {
	$('.burger').click(function () {
		let $menu = $('.menu');
		let $button = $(this);
		let buttonOffset = $button.offset();
		let buttonCenterX = buttonOffset.left + $button.width() / 2;
		let buttonCenterY = buttonOffset.top + $button.height() / 2;

		// CSS変数にハンバーガーメニューの座標をセット
		$menu.css('--x', `${buttonCenterX}px`);
		$menu.css('--y', `${buttonCenterY}px`);

		// メニューの開閉状態を切り替える
		$('.menu').toggleClass('open');

		// オーバーレイの表示・非表示を同期
		$('#menu-overlay').toggleClass('open');

		if ($('.menu').hasClass('open')) {
			// メニューが開いている間は、メニュー内でのみスクロールを許可
			document.body.style.overflow = 'hidden';
			$('.menu').on('touchmove wheel', function (e) {
				e.stopPropagation(); // メニュー内のスクロールは許可
			});
		} else {
			// メニューが閉じたらスクロールを元に戻す
			document.body.style.overflow = '';
			$('.menu').off('touchmove wheel');
		}
	});
});


