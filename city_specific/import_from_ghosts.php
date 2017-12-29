<?php

function create_page($title, $text) {
    $descriptorspec = array(
        0 => array("pipe", "r"),
        1 => array("pipe", "w"),
        2 => array("pipe", "w")
    );

    $cwd = '/tmp';
    $env = array();

    $username = getenv('CL_ADMIN_USERNAME');
    
    $process = proc_open("php /var/www/html/w/maintenance/edit.php -u $username \"$title\"", $descriptorspec, $pipes, $cwd, $env);

    if (is_resource($process)) {
        // $pipes now looks like this:
        // 0 => writeable handle connected to child stdin
        // 1 => readable handle connected to child stdout
        // Any error output will be appended to /tmp/error-output.txt

        fwrite($pipes[0], $text);
        fclose($pipes[0]);

        echo stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        echo stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        
        // It is important that you close any pipes before calling
        // proc_close in order to avoid a deadlock
        $return_value = proc_close($process);

        if($return_value !== 0) {
            echo "ERROR occurred creating page\n";
        }
    }
}

const GHOSTS_HTML = "/tmp/ghosts_seattle.html";

$page = "";
if(!file_exists(GHOSTS_HTML)) {
    $page = file_get_contents("http://www.seattleghosts.com/ghosts-of-seattle-past/maps/");
    file_put_contents(GHOSTS_HTML, $page);
} else {
    $page = file_get_contents(GHOSTS_HTML);
}

$lines = preg_split('/\n/', $page);

$raw = "";
foreach($lines as $line) {
    if(strpos($line, "var wpgmaps_localize_marker_data") !== false) {
        $raw = trim(substr($line, strpos($line, "{")), " ;\t\n\r");
        break;
    }
}

$data = json_decode($raw)->{"1"};

foreach(get_object_vars($data) as $record) {
    $title = $record->{"title"};
    // $title.gsub!(/\|/, "/");
    $title = str_replace("|", "/", $title);
    echo "$title\n";

    $buf = "";
    $buf .= "{{Infobox place\n";
    if($record->{'address'} && strpos($record->{'address'}, "47") !== 0) {
        $buf .= "|address = " . $record->{'address'} . "\n";
    }
    $lat = trim($record->{'lat'});
    $lng = trim($record->{'lng'});
    $buf .= "|coordinates = $lat, $lng\n";
    $buf .= "}}\n";
    $buf .= "\n";

    if($record->{'desc'} && count($record->{'desc'}) > 0) {
        $desc = $record->{'desc'};
        $buf .= "From [[Ghosts of Seattle Past]]: $desc\n";
        $buf .= "\n";
    } else {
        $buf .= "No description yet.\n";
    }

    $buf .= "== History ==\n";
    $buf .= "None yet.\n";
    $buf .= "\n";

    $buf .= "== Memories ==\n";
    $buf .= "None yet.\n";
    $buf .= "\n";

    $buf .= "== Links ==\n";
    if($record->{'linkd'} && count(trim($record->{'linkd'})) > 0) {
        $buf .= $record->{'linkd'} . "\n";
    } else {
        $buf .= "None yet.\n";
    }
    $buf .= "\n";

    $buf .= "[[Category:Place]]\n";
    $buf .= "[[Category:Lost]]\n";

    if(property_exists($record, 'category')) {
        $cats = preg_split('/,/', $record->{'category'});
    } else {
        $cats = array();
    }

    foreach($cats as $cat) {
        if($cat) {
            $cat_name = "";
            $cat = trim($cat);
            switch($cat) {
            case '1':
                $cat_name = "Venues, Theaters, Clubs"; break;
            case '2':
                $cat_name = "Restaurants, Bars, Coffee Shops"; break;
            case '3':
                $cat_name = "Art Galleries & Art Studios"; break;
            case '4':
                $cat_name = "Stores & Shops"; break;
            case '5':
                $cat_name = "Gathering Place"; break;
            case '6':
                $cat_name = "Other Business"; break;
            }
            $buf .= "[[Category:$cat_name]]\n";
        }
    }
        
    create_page($title, $buf);
    echo $buf;
}
