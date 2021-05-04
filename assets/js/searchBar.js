const selectElt = document.querySelector("select");
const buttonElt = document.querySelector("button");
selectElt.addEventListener("change", () =>{
    console.log({buttonElt, selectElt});
    buttonElt.click();
});
    
