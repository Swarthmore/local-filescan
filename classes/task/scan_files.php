<?php

namespace local_filescan\task;
require(__DIR__.'/../../vendor/autoload.php');
use GuzzleHttp\Client;

ini_set('display_errors', 'On');
error_reporting(E_ALL);


class scan_files extends \core\task\scheduled_task {



    public function get_name() {
        // Shown in admin screens
        return get_string('filescan_task', 'local_filescan');
    }


    public function execute() {

		global $CFG, $DB;
        require_once($CFG->libdir.'/filelib.php');
		
		//$DB->set_debug(true);
		
        mtrace( "Filescan cron script is running" );
	/*
        $api_url = get_config('filescan', 'api_url');

		// Only proceed if config parameters are defined
		if (empty($api_url)) { mtrace("API key not defined");}
        if (empty($api_url)) {
        	return false;
        }
        */
        
		mtrace("Looking up files to scan"); 
    

        
        $query = "SELECT f.id, f.filename, f.contenthash, c.instanceid, c.path 
        		FROM {files} f, {context} c
        		WHERE c.id = f.contextid 
        			AND c.contextlevel = 70 
        			AND f.filesize <> 0 
        			AND f.mimetype = 'application/pdf' 
        			AND f.component != 'assignfeedback_editpdf' 
        			AND f.filearea != 'stamps'
        		";

        $files = $DB->get_records_sql($query);
        mtrace( print_r($files, true));
       
        if (!$files) {
        	mtrace("No files found");
            return false;
        }
    
    
		$client = new Client([
			// Base URI is used with relative requests
			'base_uri' => get_config('filescan', 'apiurl'),
			// You can set any number of default request options.
			'timeout'  => 10.0,
		]);    
    
        
        $fs = get_file_storage();
        foreach ($files as $f) {
            $file = $fs->get_file_by_id($f->id);
            mtrace("Now processing \"" . $f->filename ."\"");

            $promise = $client->requestAsync('POST', '/v1/file', [
				'multipart' => [
					[
						'name'     => 'upfile',
						'contents' => $file->get_content(),
						'filename' => $f->filename
					]
				]
			]);
			
			
			$promise->then(
				function (ResponseInterface $res) {
					$code = $response->getStatusCode();
					$results = json_decode($response->getBody(), true);
					if ($results['application/json']['hasText']) {
						echo "Has text\n";
					} else {
						echo "No text\n";
					}
				},
				function (RequestException $e) {
					echo $e->getMessage() . "\n";
					echo $e->getRequest()->getMethod();
				}
			);
			
			$promise->wait();

        }
       	
 	}
}
