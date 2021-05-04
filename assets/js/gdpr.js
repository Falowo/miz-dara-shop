const gdpr = document.querySelector( '.gdpr' );
const btn = document.querySelectorAll( '.gdpr button' );
const all = document.querySelector( '.gdpr .btn-success' );
const only = document.querySelector( '.gdpr .btn-warning' );
const none = document.querySelector( '.gdpr .btn-danger' );

if ( sessionStorage.getItem( 'cookiePref' )) {
    gdpr.classList.add( 'invisible' );
}

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
            console.log('default');
            break;
    }

    gdpr.classList.add( 'invisible' );
    window.scrollTo(top);
} ) );