<?php

function sanitizacion($data) {
    $data = trim($data); // Elimina espacio en blanco (u otro tipo de caracteres) del inicio y el final de la cadna
    $data = stripslashes($data); // (\) se convierte en () y Barras invertidas dobles (\\) se convierten en una sencilla (\).
    $data = htmlspecialchars($data); // ejemplo convierte <a href='test'>Test</a> en &lt;a href=&#039;test&#039;&gt;Test&lt;/a&gt
    return $data;
}
