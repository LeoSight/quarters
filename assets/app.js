/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

// start the Stimulus application
import './bootstrap';

import '@eastdesire/jscolor';

jscolor.presets.default = {
    format:'hex', backgroundColor:'rgba(48,48,48,1)',
    borderColor:'rgba(0,0,0,1)', borderRadius:0,
    controlBorderColor:'rgba(100,100,100,1)', shadow:false
};