<?php 
    //Initialisation
    $has_Error = false;
    $VariationsErr = '';
    $Variations = 0;
    $CSV_Imported_Name = '';
    $CSV_Imported_File = '';
    $CSV_Imported_Data = [];


    //Connect / Creates the Database
    $db = new PDO('sqlite:CSV.sqlite3');
    $Table=$db->prepare("CREATE TABLE IF NOT EXISTS csv_import(
        id  INTEGER PRIMARY KEY AUTOINCREMENT, 
        [name] TEXT NOT NULL, 
        surname TEXT NOT NULL, 
        intitals VARCHAR NOT NULL, 
        age INTEGER,
        dateOfBirth TEXT)");
    $Table -> execute();
    
    //Clears the database for new data.
    $Table = $db->prepare("DELETE FROM csv_import");
    $Table -> execute();
    
    //Declare the Names and Surnames to be used
    $NAMES = [
    "Joey","Aurelio","Evan","Donny","Foster","Dwayne","Grady","Quinton","Darin",
    "Mickey","Hank","Kim","Peter","Jeremy","Jess","Jimmie","Vern","Pasquale",
    "Romeo","Chris"];

    $SURNAMES = [
    "Davis","Henry","Sherman","Howells","Warner","Schroeder","King","Bright","Perry", 
    "Goodwin","Walton","Warren","Flynn","Dean","Torres","Chen","Dixon","Flowers","Jackson", 
    "Frank"];
    
    
    //Checks if the Create CSV File button was pressed.
    if(isset($_POST['CSubmit'])) {
        //Validation 
        if(empty($_POST['Variations'])) {
            $VariationsErr = 'Please input the number of variations needed';
            $has_Error = true;
        } else {
            $Variations = filter_input(INPUT_POST, 'Variations', FILTER_SANITIZE_NUMBER_INT);
        }

        //Creates the file 
        if(file_exists('Output\output.csv')){
            $CSV_Output_File = fopen("Output\output.csv", 'w');
        } else {
            $CSV_Output_File = fopen("Output\output.csv", 'w');
        }

        //Generates the unique combinations
        $Counter = 1;
        while($Counter <= $Variations) {
            $Client = '';


            $name = $NAMES[rand(0,19)];
            $surname = $SURNAMES[rand(0,19)];
            $initial = strtoupper($name[0]);
            $age = rand(18,60);
            
            
            $bYear = 2023 - $age;
            
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

            //Adds leading 0
            if($bMonth < 10) {
                $bMonth = "0$bMonth";
            }
            if($bday < 10) {
                $bday = "0$bday";
            }

            //Creates a "Date" string.
            $dob = $bday .'/'. $bMonth .'/'. $bYear;

            $Client = array($name, $surname, $initial, $age, $dob);
            
            //Inputs into CSV File
            if($Counter == 1) {
                array_unshift($Client, $Counter);
                fputcsv($CSV_Output_File, $Client);
                $Counter += 1;
            } else {
                //Checks for Duplicates
                $file = file_get_contents('Output\output.csv');

                if (! strpos($file,  implode(",", ($Client))) !== false) {
                    array_unshift($Client, $Counter);
                    fputcsv($CSV_Output_File, $Client);
                    $Counter += 1;    
                } 
            }
        }
        //Notification of completion
        echo "<script type=\"text/javascript\">
                alert(\"CSV file has been created.\");
                window.location = \"index.php\"
            </script>";
        
        fclose($CSV_Output_File);

    }
    
    //Checks if Import CSV File button was pressed
    if(isset($_POST['ISubmit'])) {
        $CSV_Imported_Name = $_FILES["Import"]["tmp_name"]; 

        //Checks if CSV file is empty.
        if($_FILES["Import"]["size"] > 0) {
            $CSV_Imported_File = fopen($CSV_Imported_Name, 'r');

            //Imports into SQLite Database.
            while(($CSV_Imported_Data = fgetcsv($CSV_Imported_File, 10000, ",")) !== FALSE) {
                array_unshift($CSV_Imported_Data);
                
                $SQL = "INSERT INTO csv_import ([name], surname, intitals, age, dateOfBirth) 
                VALUES ('" . $CSV_Imported_Data[1] . "', '" . $CSV_Imported_Data[2] . "', '" . ($CSV_Imported_Data[3]) . "'," . $CSV_Imported_Data[4] . ",'" . $CSV_Imported_Data[5] . "')";
                
                
                $result = $db->query($SQL);
                
                //Notification For input - Not used at the moment.
                
                // if(!isset($result))
                // {
                // echo "<script type=\"text/javascript\">
                //     alert(\"Invalid File:Please Upload CSV File.\");
                //     window.location = \"index.php\"
                //     </script>";    
                // }
                // else {
                //     echo "<script type=\"text/javascript\">
                //     alert(\"CSV File has been successfully Imported.\");
                //     window.location = \"index.php\"
                // </script>";
                // }
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