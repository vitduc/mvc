<h2>Danh sach trong bang tinh thanh:</h2>

<?php

while ($row = $data['tinh']->fetch()){
    echo $row['tentinh'].'<br>';
}

?>