/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../sass/app.css';

const Friends = require('./Friends');
Friends();
const Reaction = require('./Reactions');
Reaction();
const NewPassword = require('./newPWD');
NewPassword();

$('textarea').keyup(function () {
    $(this)[0].rows = 2;
    while($(this)[0].clientHeight < $(this)[0].scrollHeight){
        $(this)[0].rows = $(this)[0].rows+1;
    }
});

// import $ from 'jquery';
// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.


