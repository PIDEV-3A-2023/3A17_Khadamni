/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';


import 'flowbite';





// start the Stimulus application
import './bootstrap';



let toTopButton = document.getElementById('to-top-button');

 if (toTopButton !== null) {
     window.addEventListener('scroll', function () {
         if (document.body.scrollTop > 50 || document.documentElement.scrollTop > 50) {
             toTopButton.classList.remove('invisible');
         } else {
             toTopButton.classList.add('invisible');
         }
     });


     toTopButton.addEventListener('click', function () {
         window.scrollTo({ top: 0, behavior: 'smooth' });
     });
 }





