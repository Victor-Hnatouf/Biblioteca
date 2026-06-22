<?php
$f = 'C:\Users\hugoh\Biblioteca\resources\views\livewire\chat-component.blade.php';
$c = file_get_contents($f);
$c = mb_convert_encoding($c, 'Windows-1252', 'UTF-8');
file_put_contents($f, $c);
echo "Done";
