<h2>Danh sach trong bang tinh thanh:</h2>

<?php

while ($row = mysqli_fetch_array($data['tinh'])){
    echo $row['tentinh'].'<br>';
}

?>