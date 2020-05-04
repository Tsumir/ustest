<?php
    function is_Date($str){
        return is_numeric(strtotime($str));
    }
    if(!isset($_POST['date'])||!is_Date($_POST['date'])){
        echo 'Дата не выбрана или введена некорректно';
        return false;
    }
    $new_date = date('d.m.Y', strtotime($_POST['date']));
    $yesterday = date('d.m.Y', strtotime($new_date. " - 1 day"));
    $xml = new DOMDocument();  
    
    // ссылка на сайт банка  
    $url = 'http://www.cbr.ru/scripts/XML_daily.asp?date_req='.$new_date;  
    // получаем xml с курсами всех валют  
    if ($xml->load($url)){  
        // массив для хранения курсов валют  
        $result_today = array();   
        // разбираем xml  
        $root = $xml->documentElement;  
        // берем все теги 'Valute' и их содержимое  
        $items = $root->getElementsByTagName('Valute');  
        // переберем теги 'Valute' по одному        
        foreach ($items as $item){  
        // получаем код валюты  
            $code = $item->getElementsByTagName('CharCode')->item(0)->nodeValue;  
            // получаем значение курса валюты, относительно рубля  
            $value = $item->getElementsByTagName('Value')->item(0)->nodeValue;  
            // записываем в массив, предварительно заменив запятую на точку  
            $result_today[$code] = str_replace(',', '.', $value);           
        }         
    }
    
    $url = 'http://www.cbr.ru/scripts/XML_daily.asp?date_req='.$yesterday;  
    // получаем xml с курсами всех валют за предыдущий день 
    if ($xml->load($url)){  
        // массив для хранения курсов валют  
        $result_yday = array();   
        // разбираем xml  
        $root = $xml->documentElement;  
        // берем все теги 'Valute' и их содержимое  
        $items = $root->getElementsByTagName('Valute');  
        // переберем теги 'Valute' по одному 
        foreach ($items as $item){  
        // получаем код валюты  
            $code = $item->getElementsByTagName('CharCode')->item(0)->nodeValue;  
            // получаем значение курса валюты, относительно рубля  
            $value = $item->getElementsByTagName('Value')->item(0)->nodeValue;  
            // записываем в массив, предварительно заменив запятую на точку  
            $result_yday[$code] = str_replace(',', '.', $value);           
        }    
    }
    // определение динамики курса по отношению к предыдущему дню
    if($result_today['EUR']-$result_yday['EUR']>0){
        $dyn_eur = 1;
    } else if ($result_today['EUR']-$result_yday['EUR']==0) {
        $dyn_eur = 0;
    }    
    if($result_today['USD']-$result_yday['USD']>0){
        $dyn_usd = 1;
    } else if ($result_today['USD']-$result_yday['USD']==0) {
        $dyn_usd = 0;
    }
?>

<body>
    <form action="api_cbr.php" method="post">
        <div>
            <label>Выберите дату:</label>
            <input type="date" name="date" value="<?php echo date('Y-m-d');?>" >
        </div>
        <div>
            <input type="submit" id="get_currency">
        </div>
    </form>
    
    <label>Дата</label>
    <?php echo $new_date ?>
    <br>
    <label>EUR</label>
    <?php echo $result_today['EUR'] ?>
    <?php if (!isset($dyn_eur)):?>
        <img src="down48.png" style="width:15px; height: 15px;"> 
    <?php elseif ($dyn_eur==1):?>
        <img src="up48.png" style="width:15px; height: 15px;">   
    <?php elseif($dyn_eur==0):?>
        <img src="line.png" style="width:15px; height: 15px;">   
    <?php endif;?>
    <br>
    <label>USD</label>
    <?php echo $result_today['USD'] ?>
    <?php if (!isset($dyn_usd)):?>
        <img src="down48.png" style="width:15px; height: 15px;"> 
    <?php elseif ($dyn_usd==1):?>
        <img src="up48.png" style="width:15px; height: 15px;">   
    <?php elseif($dyn_usd==0):?>
        <img src="line.png" style="width:15px; height: 15px;">   
    <?php endif;?>
    
</body>








