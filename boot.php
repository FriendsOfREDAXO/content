<?php

if (!rex_addon::get('minibar')->isAvailable()) {
    $inst = new rex_install();
    try {
        $inst->downloadAddon('minibar', '2.3.1');
    }
    catch (rex_exception $e) {
    }

$manager = rex_package_manager::factory(rex_package::get('minibar'));
$success = $manager->install();
//$success = $manager->delete();
}
//
//dump(rex_addon::get('minibar')->isAvailable());