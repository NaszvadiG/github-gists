<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Gists extends CI_Controller
{

    public function index()
    {
        //$data["gists"] = json_decode($this->get_gists(), TRUE);

        $data["gists"] = json_decode($this->get_gists_with_user_info(), TRUE);

        $this->load->view('gists', $data);
    }

    /**
     * connect to github api and return a list of gists
     * @return json $json_response
     */
    public function get_gists()
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

        return $json_response;
    }

    /**
     * Get Gists and also do a user look up to get more user information
     * @return type 
     */
    public function get_gists_with_user_info()
    {
        $gists_array = json_decode($this->get_gists(), TRUE);

        $new_gists_array = array();

        /**
         * loop through the gists, append user data 
         */
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

        return json_encode($new_gists_array);
    }

}

/* End of file gists.php */
/* Location: ./application/controllers/gists.php */