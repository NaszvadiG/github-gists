<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Gists extends CI_Controller
{

    public function index()
    {
        redirect('/gists/view/', 'refresh');
    }

    public function view($search_term = '')
    {
        if ($search_term != '')
        {
            $data["search_term"] = $search_term;
        }

        $data["gists"] = json_decode($this->get_gists_with_user_info($search_term), TRUE);

        $this->load->view('gists', $data);
    }

    public function search($search_term = '')
    {
        if ($search_term == '')
        {
            redirect('/gists/view/', 'refresh');
        }
        else
        {
            $this->view($search_term);
        }
    }

    /**
     * connect to github api and return a list of gists
     * @return json $json_response
     */
    public function get_gists($search_term = '')
    {
        /*
         * Create the full URL
         */
        $url = 'https://api.github.com/gists';

        /*
         * initialize a new curl object
         */
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        curl_setopt($curl, CURLOPT_HTTPGET, TRUE);

        $json_response = curl_exec($curl);

        // get result code
        $response_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        /**
         * Search for a keyword in the gist title or code 
         */
        if ($search_term != '')
        {
            $json_response = $this->filter_gists($json_response, $search_term);
        }

        return $json_response;
    }

    /**
     * Get Gists and also do a user look up to get more user information
     * @return type 
     */
    public function get_gists_with_user_info($search_term = '')
    {
        $gists_array = json_decode($this->get_gists($search_term), TRUE);

        $new_gists_array = array();

        /**
         * loop through the gists, append user data 
         */
        if (!empty($gists_array))
        {
            foreach ($gists_array as $gist)
            {
                $user = $gist["user"]["login"];

                if ($user != '')
                {

                    $url = 'https://api.github.com/users/' . $user;

                    /*
                     * initialize a new curl object
                     */
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
                    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
                    curl_setopt($curl, CURLOPT_HTTPGET, TRUE);

                    $json_response = curl_exec($curl);

                    $response_array = json_decode($json_response, TRUE);

                    $response_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

                    $gist["user_details"] = $response_array;
                }
                else
                {
                    $gists_array["user_details"] = array();
                }

                $new_gists_array[] = $gist;
            }
        }

        return json_encode($new_gists_array);
    }

    function filter_gists($json_response, $search_term)
    {
        $gists_array = json_decode($json_response, TRUE);

        $filtered_gists_array = array();

        /**
         * loop through the gists, look for the search terms in the title and/or description and/or the raw content 
         */
        foreach ($gists_array as $gist)
        {
            /*
             * get file information
             */
            $file_name = key($gist["files"]);
            $file_information_array = $gist["files"][$file_name];

            $file_information_array['code'] = file_get_contents($file_information_array['raw_url']);

            /*
             * look for the search term in the description
             */
            if (isset($gist["description"]) && $gist["description"] != '')
            {
                if (stristr($gist["description"], $search_term) == TRUE)
                {
                    $filtered_gists_array[] = $gist;
                }
            }

            /**
             * look for the search term in the file name 
             */
            if (isset($file_information_array["filename"]) && $file_information_array["filename"] != '')
            {
                if (stristr($file_information_array["filename"], $search_term) == TRUE)
                {
                    $filtered_gists_array[] = $gist;
                }
            }

            /**
             * look for the search term in the file content 
             */
            if (isset($file_information_array["code"]) && $file_information_array["code"] != '')
            {
                if (stristr($file_information_array["code"], $search_term) == TRUE)
                {
                    $filtered_gists_array[] = $gist;
                }
            }
        }

        return json_encode($filtered_gists_array);
    }

}

/* End of file gists.php */
/* Location: ./application/controllers/gists.php */