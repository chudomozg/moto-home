// import { createHooks } from '@wordpress/hooks';
// myObject.hooks = createHooks();
// myObject.hooks.doAction( 'carbon-fields.field-edit', Array('1'=>'1') )

wp.hooks.doAction( 'carbon-fields.field-edit',  1 );

// $(document).ready(function(){
//     carbon_set_post_meta( '48', 'motohome_city', 1 )
// })