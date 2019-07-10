<?php 

// $ch = curl_init();

// curl_setopt($ch, CURLOPT_URL,"http://tt.com/api/users");
// curl_setopt($ch, CURLOPT_POST, 1);
// curl_setopt($ch, CURLOPT_POSTFIELDS,"postvar1=value1&postvar2=value2&postvar3=value3");

// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// $server_output = curl_exec($ch);

// curl_close ($ch);

// var_dump($server_output );
?>

<script>
    fetch('http://tt.com/api/users').then(function(response){
        return response.json();
    }).then(function(data){
        console.log(data);
    })
</script>