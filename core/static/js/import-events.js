jQuery( document ).on( 'heartbeat-send', function ( event, data ) {
    // Add additional data to Heartbeat data.
    data.devmonsta_import = {
        foo: 'bar'
    };
});

jQuery( document ).on( 'heartbeat-tick', function ( event, data ) {
    // Check for our data, and use it.
    if ( ! data.devmonsta_import ) {
        return;
    }
 
    console.log(data.devmonsta_import);
});