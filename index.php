<?php 
    $has_Error = false;
    $VariationsErr = '';
    $Variations = 0;
    $CSV_Imported_Name = '';
    $CSV_Imported_File = '';
    $CSV_Imported_Data = [];


    //Connect to SQLite Database
    $db = new SQLite3('mydb.sq3');
    
    $SQL = '';
    $result = $db->query($SQL);


    $NAMES = [
    "Joey","Aurelio","Evan","Donny","Foster","Dwayne","Grady","Quinton","Darin",
    "Mickey","Hank","Kim","Peter","Jeremy","Jess","Jimmie","Vern","Pasquale",
    "Romeo","Chris"];

    $SURNAMES = [
    "Davis","Henry","Sherman","Howells","Warner","Schroeder","King","Bright","Perry", 
    "Goodwin","Walton","Warren","Flynn","Dean","Torres","Chen","Dixon","Flowers","Jackson", 
    "Frank"];
    

    if(isset($_POST['CSubmit'])) {
        if(empty($_POST['Variations'])) {
            $VariationsErr = 'Please input the number of variations needed';
            $has_Error = true;
        } else {
            $Variations = filter_input(INPUT_POST, 'Variations', FILTER_SANITIZE_NUMBER_INT);
        }

        while($Counter <= $Variations) {
            $name = $NAMES[rand(1,20)];
            $surname = $SURNAMES[rand(1,20)];
            $initial = $name[1];
            $age = rand(18,60);
            
            $bYear = date('Y') - $age;
            
            $bMonth = rand(1,12);
            if($bMonth % 2 == 1 && $bMonth == 8) {
                $bday = rand(1,31);
            } else {
                if($bMonth == 2) {
                    $bday = rand(1,28);
                } else {
                    $bday = rand(1,30);
                }
            }

            $Client = "$name,$surname,$initial,$age,$bday/$bMonth/$bYear";
            
            //Create CSV
            //Add check for dupes
            //Expot to Output folder
            
        }
        

    }
    
    if(isset($_POST['ISubmit'])) {
        $CSV_Imported_Name = $_FILES["Import"]["tmp_name"]; 

        if($_FILES["Import"]["size"] > 0) {
            $CSV_Imported_File = fopen($CSV_Imported_Name, 'r');

            while(($CSV_Imported_Data = fgetcsv($CSV_Imported_File, 10000, ",")) !== FALSE) {
                // TODO: Finnish importing, Checking for correctness
            }
        }
        
    }


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Styles.css">
    <title>Code infinity Test 2</title>
</head>
<body>
    
    <div id="Headder">
        <img src="IMG\CodeInfinityLogo.jpg" alt="" id="Image">
        <h2>Unique Person Generator</h2> 
        
        <!-- Displays errors if any -->
    <?php
        if($has_Error) { ?>
            
            <div id="error_box" class="error_box" style=" background-color: #FFCCCB; padding: 10px"}>
            <?php echo $VariationsErr?>
            </div>

        <?php }
    ?>
    </div>

    <div class="Titles">
        
        <div class="Column">
            <h3>Create a CSV File</h3>
            <form action="index.php" method="post">

                <div id="TextBox">
                    <label for="Variations">Please input the number of variations</label>
                    <input type="text" name="Variations" placeholder="1 000 000">
                </div>


                <input type="submit" value="GO" name="CSubmit" id="CSubmit" class="Button">

            </form>
        </div>

        
        <div class="Column">
            <h3>Import a CSV File</h3>
            <form action="index.php" method="POST" enctype="multipart/form-data">

                <div id="Import">
                    <label for="Import">Please add a CSV File<br></label>
                    <input type="File" name="Import" id="Import">
                </div>


                <input type="submit" value="GO" name="ISubmit" id="ISubmit" class="Button">

            </form>
        </div>

    </div>
    



</body>
</html>