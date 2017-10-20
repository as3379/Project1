<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);





//initialize the main class
$obj = new main();

class Manage {
    public static function autoload($class) {
        //you can put any file name or directory here
        include $class . '.php';
    }
}

spl_autoload_register(array('Manage', 'autoload'));



class main
{
    function __construct()
    {
        //default page when no page is requested
        $pageRequest = 'uploadForm';

        //check if page is set
        if(isset($_REQUEST['page']))
        {
           $pageRequest = $_REQUEST['page'];

        }
        //call requested page
        $page = new $pageRequest;

            //check requested method and call function
        if($_SERVER['REQUEST_METHOD'] == 'GET') {
            $page->accept();
        } else {
            $page->display();
        }

    }
}

abstract class page
{
    protected $html;

    function __construct()
    {
        $this->html.='<html><head>';
        $this->html.='<link rel="stylesheet" href="styles.css" type="text/css">';
        $this->html.= '</head><body>';
    }


    function __destruct()
    {
        $this->html.='</body></html>';
        stringFunctions::echoString($this->html);

    }
}

class uploadForm extends page
{
        function accept()
        {
            $form = "<form action='index.php?page=uploadForm' method='post' enctype='multipart/form-data'>";
            $form .= '<h1>Select CSV file to upload</h1>';
            $form .= '<br>';
            $form .= '<br>';
            $form .= '<input type="file" name="fileToUpload" id="fileToUpload">';
            $form .= '<input type="submit" value="Upload File" name="submit">';
            $form.= '</form>';
            $this->html.=$form;
        }

    function display()
    {

        $target_dir = "Uploads/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);

        //check if file exist
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
        }
        else {
            //check file type
            if ($imageFileType != 'csv') {

                echo 'You can only upload a CSV file';
            } else {

                    //upload file
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {

                    echo "The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded.";
                    header("Location:index.php?page=htmlTable&file=$target_file");

                }
            }

        }
    }
}

class htmlTable extends page

{

    function accept()
    {

        $fileName = $_REQUEST['file'];

        $handle = fopen($fileName, "r");

        echo '<table>';
//display header row if true
        if (true) {
            $csvcontents = fgetcsv($handle);
            echo '<tr>';
            foreach ($csvcontents as $headercolumn) {
                echo "<th>$headercolumn</th>";
            }
            echo '</tr>';
        }
// displaying contents
        while ($csvcontents = fgetcsv($handle)) {
            echo '<tr>';
            foreach ($csvcontents as $column) {
                echo "<td>$column</td>";
            }
            echo '</tr>';
        }
        echo '</table>';
    }


}

class stringFunctions
{
    //print out whatever string is passed into it
    static function echoString($string)
    {
        echo $string;

    }



}



?>
