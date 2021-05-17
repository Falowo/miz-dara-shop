const secondLevelElts = document.querySelectorAll(".second-level");
const thirdLevelElts = document.querySelectorAll(".third-level");


setTimeout(
  () =>
    thirdLevelElts.forEach((element) => {
      element.classList.add("d-none");
    }),
  1000
);

if (window.innerWidth<768) {
  setTimeout(
    () => {       
      secondLevelElts.forEach((element) => {
        element.classList.remove("d-flex");
        element.classList.add("d-none");
      })
    },
    2000
  );
}
