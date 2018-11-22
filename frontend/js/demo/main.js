import demo from './demo';

demo();

import { a, c } from './demo2';

console.log(a, c);

import '../../css/demo/demo.css';

import '../../images/pcit.png';

import html from 'html-loader!../../html/demo/component.html';

$('body').append(html);
