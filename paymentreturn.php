<?php

switch($_GET['status']){
    case 'ok' :
        die('ok');
        break;
    case 'cancelled' :
        die('cancelled');
        break;
    case 'refused' :
        die('refused');
        break;
    case 'error' :
        die('error');
        break;
    default:
        break;


}
