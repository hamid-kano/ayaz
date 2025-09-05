<?php

// Manual storage link creation for shared hosting
$target = __DIR__ . '/storage/app/public';
$link = __DIR__ . '/public/storage';

// Remove existing link if exists
if (file_exists($link)) {
    if (is_link($link)) {
        unlink($link);
    } else {
        rmdir($link);
    }
}

// Create symbolic link
if (symlink($target, $link)) {
    echo "Storage link created successfully!\n";
} else {
    echo "Failed to create storage link.\n";
}