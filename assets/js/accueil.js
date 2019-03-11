function onClickBtnLike(event){
    event.preventDefault();

    const url = this.href;
    const spanCount = this.querySelector('span.js-likes');
    const icone = this.querySelector('i');

    axios.get(url).then(function (response) {
        spanCount.textContent = response.data.likes;

        if (icone.classList.contains('fas')) {
            icone.classList.replace('fas', 'far');
        }
        else {
            icone.classList.replace('far', 'fas')
        }
    }).catch(function(error) {
        if(error.response.status === 403) {
            window.alert("Connecter vous pour like")
        } else {
            window.alert("Une erreur s'est produite, réessayez plus tard")
        }
    });
}
document.querySelectorAll('a.js-like').forEach(function (link) {
    link.addEventListener('click', onClickBtnLike);
})