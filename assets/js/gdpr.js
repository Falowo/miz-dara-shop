const gdpr = document.querySelector( '.gdpr' );
const btn = document.querySelectorAll( '.gdpr button' );
const all = document.querySelector( '.gdpr .btn-success' );
const only = document.querySelector( '.gdpr .btn-warning' );
const none = document.querySelector( '.gdpr .btn-danger' );

if ( data = sessionStorage.getItem( 'cookiePref' )) {
    gdpr.classList.add( 'invisible' );
}
console.log( gdpr );
console.log( btn );
btn.forEach( b => b.addEventListener( 'click', e => {
    e.stopPropagation();
    switch ( b ) {
        case all:
            sessionStorage.setItem( 'cookiePref', 'all' );
            break;
        case only:
            sessionStorage.setItem( 'cookiePref', 'only' );
            break;
        case none:
            sessionStorage.setItem( 'cookiePref', 'none' );
            break;

        default:
            break;
    }
    gdpr.classList.add( 'invisible' );
} ) );