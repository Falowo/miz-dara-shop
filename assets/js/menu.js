const secondLevelElts = document.querySelectorAll(".second-level");
const thirdLevelElts = document.querySelectorAll(".third-level");

window.addEventListener("DOMContentLoaded", (event) => {
	console.log("DOM fully loaded and parsed");

	setTimeout(
		() =>
			thirdLevelElts.forEach((element) => {
				element.classList.add("d-none");
			}),
		100
	);

	if (window.innerWidth < 768) {
		setTimeout(() => {
			secondLevelElts.forEach((element) => {
				element.classList.remove("d-flex");
				element.classList.add("d-none");
			});
		}, 200);
	}
});
