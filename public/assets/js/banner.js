document.addEventListener("DOMContentLoaded", function () {
	const ticketBanner = document.getElementById("ticket-banner-container");    
    const timeInfoContainer = document.querySelector('#time-info-container');
    console.log(timeInfoContainer);

	if (!ticketBanner || !timeInfoContainer) return;

	function checkVisibility() {
		const rect = timeInfoContainer.getBoundingClientRect();
		const halfVisible = rect.top + rect.height / 2 <= window.innerHeight;

		if (halfVisible) {
			ticketBanner.classList.add("visible");
		} else {
			ticketBanner.classList.remove("visible");
		}
	}

	// スクロール時に実行
	window.addEventListener("scroll", checkVisibility);

	// 初回チェック
	checkVisibility();
});

document.addEventListener("DOMContentLoaded", function () {
	const banner = document.querySelector("#ticket-banner-container");
	const closeButton = document.querySelector("#close-banner");

	if (closeButton) {
		closeButton.addEventListener("click", function () {
			banner.style.display = "none"; // バナーを非表示
		});
	}
});
