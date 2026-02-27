<?php

use CodeIgniter\Events\Events;

Events::on('pre_system', function () {
    // Pre-system initialization code here
});

Events::on('post_controller_constructor', function () {
    // Post-controller constructor code here
});
